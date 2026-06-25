<?php

/**
 * This file is part of Dixlase OnePage.
 *
 * Copyright (C) 2026 exc-D inc.
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

namespace Themes\DixlaseOnePage\App\Services;

use App\Contracts\Theme\SiteAppearanceProviderInterface;
use Themes\DixlaseOnePage\App\Models\ThemeSetting;

/**
 * OnePage's implementation of Core's
 * {@see SiteAppearanceProviderInterface}. Bridges the theme's
 * `appearance_mode` admin setting — stored in
 * `thm_dixlase_onepage_settings` and surfaced as a 0/1/2 selector in
 * the appearance sidebar — to the `auto`/`light`/`dark` vocabulary
 * Core surfaces (e.g. Turnstile) consume.
 *
 * Bound from `DixlaseOnePageServiceProvider::register()` so any
 * out-of-tree consumer that probes the contract sees the theme's
 * current mode without needing a separate config / env var.
 */
class DixlaseOnePageAppearanceProvider implements SiteAppearanceProviderInterface
{
    /**
     * Mapping between the theme's stored values (the same ones the
     * admin sidebar's `appearance_mode` select emits — see
     * `resources/views/admin/settings/themes/partials/sidebar.blade.php`)
     * and the vocabulary the contract declares.
     */
    private const MODE_MAP = [
        '0' => 'auto',   // Follow visitor's OS prefers-color-scheme
        '1' => 'light',  // Pin to light
        '2' => 'dark',   // Pin to dark
    ];

    public function getAppearanceMode(): string
    {
        // Wrap the DB read so a missing settings table (fresh install
        // before the seeder has run, or an environment where the
        // theme migrations have not been applied yet) cannot bubble
        // up to consumers — they treat exceptions as "no preference"
        // by falling back to `auto`, and so do we.
        try {
            $stored = ThemeSetting::getValue('appearance_mode', '0');
        } catch (\Throwable) {
            return 'auto';
        }

        return self::MODE_MAP[(string) $stored] ?? 'auto';
    }
}
