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

namespace Themes\DixlaseOnePage\App\Multilingual;

use App\Contracts\Multilingual\TranslatableContentProvider;
use Themes\DixlaseOnePage\App\Models\ThemeSetting;

/**
 * Primary-locale value source for the DixlaseOnePage theme's translatable
 * hero settings (hero_main_title / hero_sub_title / hero_button_text /
 * hero_button_secondary_text).
 *
 * Registered in theme.json as the `provider` for the
 * `dixlase-onepage:settings` singleton type. DixlaseMultilingual calls
 * this when no per-locale translation exists for the requested locale,
 * so the front page falls back to the primary value stored in the
 * theme's existing key-value `thm_dixlase_onepage_settings` table.
 *
 * Mirrors `Plugins\DixlaseInquiry\App\Multilingual\InquirySettingsProvider`
 * — both are read-only and exist solely to bridge the central translation
 * resolver to extension-owned storage.
 */
class DixlaseOnePageSettingsProvider implements TranslatableContentProvider
{
    public function getPrimaryValue(string $field): ?string
    {
        $value = ThemeSetting::getValue($field);

        return $value === null ? null : (string) $value;
    }

    /**
     * The locale the primary-stored theme-settings values are written in.
     *
     * Sourced from the operator-configurable `default_locale` theme
     * setting:
     *
     * - An explicit locale code (e.g. 'en', 'ja') is returned as-is.
     *   The central translation manager UI excludes that locale from
     *   the locale selector (no "translating into the source"), and
     *   `dls_onepage_localized_setting()` short-circuits straight to
     *   the primary value when the current locale equals it.
     *
     * - The sentinel `'auto'` (or an unset/empty setting) returns
     *   `null` — the theme settings text is treated as a locale-neutral
     *   default and every enabled locale (including the site default)
     *   becomes translatable in the central UI. The runtime helper
     *   walks the full resolver chain for every locale and falls back
     *   to the primary value only when no translation row exists.
     */
    public function getPrimaryLocale(): ?string
    {
        $locale = ThemeSetting::getValue('default_locale');

        if (is_string($locale) && $locale !== '' && $locale !== 'auto') {
            return $locale;
        }

        return null;
    }
}
