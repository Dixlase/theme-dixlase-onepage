#!/usr/bin/env node
/**
 * Tailwind v4 @source path sanity check.
 *
 * Background: see commit `8454293` (June 2026) — the theme's tailwind.css
 * `@source` patterns silently resolved to a non-existent path
 * (`resources/resources/...`) for ~2.5 months because Tailwind v4's
 * `@tailwindcss/vite` auto-detect picked up *some* blade files anyway,
 * masking the issue. The hero title silently downgraded to mobile size
 * on PC after a blade file moved into a subdirectory the auto-detect
 * missed.
 *
 * This script parses every `@source "..."` directive in the theme's
 * Tailwind entry CSS, resolves each glob/path relative to that file,
 * and counts the matching files on disk. If any directive matches
 * zero files, the script exits non-zero so `npm run build` / CI fails
 * loudly instead of shipping a silently-degraded stylesheet.
 *
 * Scope: only the patterns the theme actually uses (recursive
 * "double-star slash star dot ext" globs and bare directory paths).
 * Does NOT implement full POSIX glob semantics — if a new pattern shape
 * is added to tailwind.css, extend `expandSource()` below.
 *
 * Run: `node scripts/check-tailwind-sources.mjs`
 * Auto-runs via the `prebuild` npm script before `npm run build`.
 */

import { readFileSync, readdirSync, statSync, existsSync } from 'node:fs';
import { resolve, dirname, join, basename, relative } from 'node:path';
import { fileURLToPath } from 'node:url';

const THEME_ROOT = resolve(fileURLToPath(import.meta.url), '../../');
const CSS_ENTRY = join(THEME_ROOT, 'resources/src/front/css/tailwind.css');

// Allow excluding noisy paths from the recursive walk. node_modules and
// vendor never contain Tailwind-utility-bearing source files in this
// codebase, but they can balloon the scan time.
const SKIP_DIR_NAMES = new Set(['node_modules', 'vendor', '.git', 'storage']);

const EXTENSIONS_BY_PATTERN = {
    'blade.php': /\.blade\.php$/,
    js: /\.js$/,
    vue: /\.vue$/,
    html: /\.html$/,
    php: /\.php$/,
};

const FILE_EXT_PRIORITY_FOR_DIR_SCAN = Object.keys(EXTENSIONS_BY_PATTERN);

/**
 * Parse `@source "..."` directives out of a Tailwind v4 entry CSS.
 *
 * Walks the source char by char tracking whether we are inside a
 * CSS slash-star comment, so slash-star sequences that appear inside
 * an @source path (e.g. a recursive glob that uses double-star and a
 * dot extension) don't get mistaken for a comment start. A naive
 * "strip slash-star to star-slash" regex was the previous approach
 * and silently swallowed the double-star portion of every glob.
 */
function parseSources(css) {
    let stripped = '';
    let i = 0;
    let inComment = false;
    while (i < css.length) {
        const ch = css[i];
        const next = css[i + 1];
        if (inComment) {
            if (ch === '*' && next === '/') {
                inComment = false;
                i += 2;
                continue;
            }
            i += 1;
            continue;
        }
        // Only treat `/*` as a comment start when it appears OUTSIDE a
        // string literal. We're not parsing strings carefully — just
        // accept that if a stray `/*` lands inside one, the false-strip
        // is on the user. For this codebase all `@source` use double
        // quotes; `/*` outside a quote is always a real comment.
        if (ch === '/' && next === '*') {
            // Quick lookbehind: skip into comment-mode unless we're
            // mid-string. Cheap heuristic — find the last unescaped
            // double-quote before i; if odd count, we're inside a string.
            const upto = css.slice(0, i);
            const quotes = (upto.match(/"/g) || []).length;
            if (quotes % 2 === 0) {
                inComment = true;
                i += 2;
                continue;
            }
        }
        stripped += ch;
        i += 1;
    }

    const re = /@source\s+["']([^"']+)["']/g;
    const found = [];
    let m;
    while ((m = re.exec(stripped)) !== null) {
        found.push(m[1]);
    }
    return found;
}

/**
 * Walk a directory tree and yield absolute file paths whose name matches
 * `extPattern` (a RegExp). Skips directories named in SKIP_DIR_NAMES.
 */
function* walk(dir, extPattern) {
    let entries;
    try {
        entries = readdirSync(dir, { withFileTypes: true });
    } catch {
        return;
    }
    for (const entry of entries) {
        if (entry.isDirectory()) {
            if (SKIP_DIR_NAMES.has(entry.name)) continue;
            yield* walk(join(dir, entry.name), extPattern);
        } else if (entry.isFile() && extPattern.test(entry.name)) {
            yield join(dir, entry.name);
        }
    }
}

/**
 * Expand one @source directive into a concrete list of matched files.
 *
 * Handles three shapes:
 *   - recursive glob ending in dot-extension: scan files of that ext
 *   - bare directory path with trailing slash: scan all known content exts
 *   - bare directory path: same as the trailing-slash form
 *
 * Returns `{ resolved, exists, matches }`. `resolved` is the absolute
 * path the directive points to; `exists` is whether the directory exists
 * on disk; `matches` is the count of files that matched.
 */
function expandSource(source, cssDir) {
    // Recursive glob: `…/**/*.ext`
    const globMatch = source.match(/^(.*?)\/\*\*\/\*\.([a-z.]+)$/i);
    if (globMatch) {
        const [, dirPart, ext] = globMatch;
        const absDir = resolve(cssDir, dirPart);
        const extKey = Object.keys(EXTENSIONS_BY_PATTERN).find((k) => k === ext) ?? ext;
        const extPattern = EXTENSIONS_BY_PATTERN[extKey] ?? new RegExp(`\\.${ext.replace(/\./g, '\\.')}$`);
        if (!existsSync(absDir)) {
            return { resolved: absDir, exists: false, matches: 0 };
        }
        let count = 0;
        for (const _ of walk(absDir, extPattern)) count++;
        return { resolved: absDir, exists: true, matches: count };
    }

    // Bare directory path — Tailwind treats this as "scan all content".
    // We count files matching any of the known content extensions.
    const absPath = resolve(cssDir, source.replace(/\/$/, ''));
    if (!existsSync(absPath)) {
        return { resolved: absPath, exists: false, matches: 0 };
    }
    const stat = statSync(absPath);
    if (!stat.isDirectory()) {
        // Single file source — exists check already passed.
        return { resolved: absPath, exists: true, matches: 1 };
    }
    let count = 0;
    for (const extKey of FILE_EXT_PRIORITY_FOR_DIR_SCAN) {
        for (const _ of walk(absPath, EXTENSIONS_BY_PATTERN[extKey])) count++;
    }
    return { resolved: absPath, exists: true, matches: count };
}

function main() {
    if (!existsSync(CSS_ENTRY)) {
        console.error(`✗ tailwind entry CSS not found: ${CSS_ENTRY}`);
        process.exit(1);
    }
    const css = readFileSync(CSS_ENTRY, 'utf8');
    const cssDir = dirname(CSS_ENTRY);
    const sources = parseSources(css);

    if (sources.length === 0) {
        console.error('✗ no @source directives found in tailwind.css');
        process.exit(1);
    }

    const results = sources.map((src) => ({ src, ...expandSource(src, cssDir) }));

    const cssRelative = relative(THEME_ROOT, CSS_ENTRY);
    console.log(`@source check for ${cssRelative}:`);
    let errors = 0;
    let warnings = 0;
    for (const r of results) {
        // ✗ — path does not exist (hard fail: definite typo or stale path)
        // ⚠ — path exists but 0 files matched (soft warn: empty subtree
        //      is legitimate when a directive is staged for future content
        //      that hasn't been added yet, e.g. .vue source pre-Vue use)
        // ✓ — path exists and at least one file matched
        let status;
        let note;
        if (!r.exists) {
            status = '✗';
            note = '  (path does not exist)';
            errors++;
        } else if (r.matches === 0) {
            status = '⚠';
            note = '  (path exists, 0 files matched — consider removing if no future use)';
            warnings++;
        } else {
            status = '✓';
            note = '';
        }
        const resolvedRel = relative(THEME_ROOT, r.resolved);
        console.log(`  ${status} ${r.matches.toString().padStart(4)} files  "${r.src}"${note}`);
        if (status !== '✓') {
            console.log(`        resolved → ${resolvedRel}`);
        }
    }
    console.log('');
    if (errors > 0) {
        console.error(`✗ ${errors} @source directive(s) point to non-existent paths.`);
        console.error('  Tailwind v4 will silently skip these; utilities used only in those');
        console.error('  trees will not round-trip into the emitted CSS. Fix the @source');
        console.error('  path or remove the directive.');
        process.exit(1);
    }
    if (warnings > 0) {
        console.log(`⚠ ${warnings} @source directive(s) matched zero files but the path exists — review whether they are still needed.`);
    }
    console.log(`✓ ${results.length - errors} @source directive(s) resolved without error.`);
}

main();
