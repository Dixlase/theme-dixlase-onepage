<?php

/**
 * This file is part of Dixlase OnePage.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
 * https://exc-d.com
 *
 * Dixlase OnePage is dual-licensed. You may use this file under either:
 *
 *   (a) the GNU General Public License version 3 or later, as published
 *       by the Free Software Foundation; or
 *
 *   (b) a commercial license agreement obtained from exc-D inc.
 *
 * Unless you have entered into a commercial license agreement, this
 * file is governed by the GPL terms below.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Themes\DixlaseOnePage\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Guards that the front-page builder placeholder stays removed (#149).
 *
 * `supports.front_page_builder` and `resources/views/layouts/frontpage.blade.php`
 * were a placeholder from before core had a front-page body seam. Nothing ever
 * read either one: core does not look at the flag, and no code referenced the
 * layout. The seam exists now — a theme setting over core's
 * FrontPageBodyProviderInterface — so the placeholder advertised a second,
 * non-existent mechanism for the same job.
 *
 * `supports` is operator-facing metadata, read when deciding whether a theme can
 * do something, so a flag for a capability the theme does not have is a false
 * claim. This test exists because the cost of the placeholder was not that it
 * broke anything — it never ran — but that someone could build against it.
 *
 * Pure file reads: no framework boot, no database.
 */
class FrontPageBuilderStubRemovedTest extends TestCase
{
    private const REPO_ROOT = __DIR__.'/../..';

    private const STUB_LAYOUT = 'resources/views/layouts/frontpage.blade.php';

    /**
     * @return array<string, mixed>
     */
    private function manifest(): array
    {
        $decoded = json_decode((string) file_get_contents(self::REPO_ROOT.'/theme.json'), true);

        $this->assertIsArray($decoded, 'theme.json should be readable JSON');

        return $decoded;
    }

    public function test_the_manifest_does_not_declare_a_front_page_builder(): void
    {
        $supports = $this->manifest()['supports'] ?? [];

        $this->assertIsArray($supports);
        $this->assertArrayNotHasKey('front_page_builder', $supports);
    }

    /**
     * The removal must take the flag and nothing beside it: these three are
     * real and are read.
     */
    public function test_the_other_support_flags_are_untouched(): void
    {
        $supports = $this->manifest()['supports'] ?? [];

        foreach (['dark_mode', 'responsive', 'custom_colors'] as $flag) {
            $this->assertArrayHasKey($flag, $supports);
        }
    }

    public function test_the_stub_layout_is_gone(): void
    {
        $this->assertFileDoesNotExist(self::REPO_ROOT.'/'.self::STUB_LAYOUT);
    }

    /**
     * The manifest's `files` block is the signature list. An entry for a file
     * that is not there fails verification on an installed site, so the two
     * have to be removed together.
     */
    public function test_the_signature_list_does_not_name_the_stub_layout(): void
    {
        $files = $this->manifest()['files'] ?? [];

        $this->assertIsArray($files);
        $this->assertArrayNotHasKey(self::STUB_LAYOUT, $files);
    }

    /**
     * Every path the signature list names must exist, which is the general
     * form of the check above — a second deletion that forgets the manifest
     * fails here even if it is not this file.
     */
    public function test_every_file_in_the_signature_list_exists(): void
    {
        $missing = [];

        foreach (array_keys($this->manifest()['files'] ?? []) as $path) {
            if (! file_exists(self::REPO_ROOT.'/'.$path)) {
                $missing[] = $path;
            }
        }

        $this->assertSame([], $missing, 'the signature list names files that are not in the theme');
    }

    /**
     * Nothing may reference the removed layout or its slot. A leftover
     * `@extends('themes::layouts.frontpage')` would be a runtime error rather
     * than a dead flag.
     */
    public function test_no_view_references_the_removed_layout(): void
    {
        $offenders = [];

        foreach ($this->bladeFiles() as $path) {
            $contents = (string) file_get_contents($path);

            foreach (['layouts.frontpage', 'layouts/frontpage', 'builder-content'] as $needle) {
                if (str_contains($contents, $needle)) {
                    $offenders[] = basename($path).' → '.$needle;
                }
            }
        }

        $this->assertSame([], $offenders);
    }

    /**
     * @return array<int, string>
     */
    private function bladeFiles(): array
    {
        $directory = new \RecursiveDirectoryIterator(
            self::REPO_ROOT.'/resources/views',
            \FilesystemIterator::SKIP_DOTS
        );

        $files = [];

        foreach (new \RecursiveIteratorIterator($directory) as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                $files[] = $file->getPathname();
            }
        }

        $this->assertNotSame([], $files, 'no Blade files were scanned, so this test proves nothing');

        return $files;
    }
}
