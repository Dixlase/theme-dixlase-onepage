import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
    // Emit asset URLs relative to the file that references them, instead of
    // Vite's default `base: '/'`. The built CSS lives at
    // `css/style-<hash>.css` and the fonts at `fonts/<name>.woff2`, so with
    // the default the stylesheet asked for `/fonts/fa-solid-900.woff2` —
    // an absolute site-root path that 404s, because the theme is served from
    // `/assets/themes/DixlaseOnePage/`. Relative `../fonts/...` resolves
    // correctly wherever the theme is mounted, so this does not have to be
    // kept in step with the mount path.
    //
    // Safe for this theme specifically: the entry URLs are resolved by PHP
    // from `manifest.json` (AssetHelper), which stores outDir-relative paths
    // that `base` does not touch, and the JS build is a single entry with no
    // dynamic imports, so there is no chunk loading to mis-resolve.
    base: './',
    build: {
        // ビルド出力先（コア側がシンボリックリンクで public/assets/themes/DixlaseOnePage として公開する）
        outDir: resolve(__dirname, 'resources/assets'),
        // ビルドごとに出力先をクリアして残骸ファイルを排除する
        // (テーマサムネイルはリポジトリ直下 `thumbnail.png` に置き、
        // ビルド対象外 — Dixlase Core は拡張機能ルート直下の
        // `thumbnail.{ext}` を最優先で probe するため、outDir wipe に
        // 巻き込まれない。dixlase-core PR #287 の推奨レイアウト)
        emptyOutDir: true,

        // ソースマップ
        sourcemap: process.env.NODE_ENV === 'development',

        // ロールアップオプション
        rollupOptions: {
            input: {
                // Tailwind CSS v4
                'tailwind': resolve(__dirname, 'resources/src/front/css/tailwind.css'),

                // SCSS (開発時)
                'style': resolve(__dirname, 'resources/src/front/scss/style.scss'),

                // JavaScript (Alpine.js含む)
                'app': resolve(__dirname, 'resources/src/front/js/app.js'),
            },
            output: {
                // Output filename patterns.
                //
                // Content-hash the JS/CSS entries so the URL changes
                // on every build whose bytes changed — automatic cache
                // busting without touching Cache-Control headers. The
                // AssetHelper (`load_assets_from_manifest`) resolves
                // requests through manifest.json's `file` mapping, so
                // it picks up the hashed filename with no changes on
                // the PHP side. Identical builds still produce
                // identical filenames (Vite's `[hash]` is a content
                // hash), so this does not force spurious re-fetches.
                //
                // Fonts and images stay stable — Font Awesome's
                // woff2 blobs are ~110KB each and rarely change; a
                // stable URL keeps them in the browser cache across
                // rebuilds. If a font file ever does change, its
                // reference in the built CSS carries a new hash (via
                // Vite's asset-URL rewriting), so stale font content
                // cannot silently ship.
                //
                // See dixlase-brand history around Sep 2026: the
                // previous stable `css/style.css` URL let iOS Safari
                // cache pre-refactor CSS indefinitely, breaking the
                // footer layout on returning visitors even after
                // Settings > Safari > Clear History.
                entryFileNames: 'js/[name]-[hash].js',
                chunkFileNames: 'js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    // CSSファイル
                    if (assetInfo.name.endsWith('.css')) {
                        return 'css/[name]-[hash][extname]';
                    }
                    // 画像ファイル
                    if (/\.(png|jpe?g|gif|svg|webp|ico)$/.test(assetInfo.name)) {
                        return 'images/[name][extname]';
                    }
                    // フォントファイル
                    if (/\.(woff2?|eot|ttf|otf)$/.test(assetInfo.name)) {
                        return 'fonts/[name][extname]';
                    }
                    // その他
                    return 'assets/[name][extname]';
                },
            },
        },

        // マニフェストファイル生成（AssetHelperが manifest.json を参照するため明示指定）
        manifest: 'manifest.json',

        // 最小化
        minify: process.env.NODE_ENV === 'production' ? 'terser' : false,

        // Terserオプション
        terserOptions: {
            compress: {
                drop_console: process.env.NODE_ENV === 'production',
            },
        },

        // CSS の minify は lightningcss を使う。
        // @tailwindcss/typography 0.5.x は Tailwind v4 配下で空の
        // :where() を含む CSS を出力することがあり、esbuild
        // (Vite の CSS minify デフォルト) はこれを valid と認識できず
        // 毎ビルドで "Unexpected \")\"" の warning を出す。
        // lightningcss は forgiving-selector-list を許容し、圧縮率も
        // esbuild より高い (Tailwind v4 公式推奨)。
        cssMinify: 'lightningcss',

        // lightningcss に対するターゲット指定。明示しないと保守的な
        // browserslist 既定で動き、Tailwind v4 が出す oklch() / nesting
        // / `:where()` などのモダン CSS に対して fallback を展開する
        // 結果、ファイルサイズが大きく膨らむ。Tailwind v4 自身が前提と
        // するモダンブラウザ (Chrome / Edge / Safari / Firefox 直近) に
        // 揃え、不要な変換を避けることで出力サイズを抑える。
        cssTarget: ['chrome111', 'edge111', 'safari16.4', 'firefox128'],
    },

    // 開発サーバー設定
    server: {
        host: '0.0.0.0',
        port: 5174, // デフォルトテーマ専用ポート
        strictPort: false,
        hmr: {
            host: 'localhost',
        },
    },

    // プレビューサーバー設定
    preview: {
        port: 5174,
    },

    // 依存関係の最適化
    optimizeDeps: {
        include: [],
    },

    // CSS設定
    css: {
        // Anchor PostCSS to this repo so Vite does not walk up and
        // pick up the Dixlase Core `postcss.config.js` (a different
        // Tailwind bundle configured for core; when building from a
        // Core-mounted checkout, Vite would otherwise find core's
        // file first and mix incompatible plugin registrations).
        // Point at this theme's own postcss.config.js explicitly.
        postcss: resolve(__dirname, 'postcss.config.js'),
        devSourcemap: true,
        preprocessorOptions: {
            scss: {
                // Use the modern Sass JS API so Vite stops printing the
                // "legacy-js-api" deprecation warning. The legacy API will be
                // removed in Dart Sass 2.0.
                api: 'modern-compiler',
            },
        },
    },
});
