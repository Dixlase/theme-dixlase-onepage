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

namespace Themes\DixlaseOnePage\App\Multilingual;

use App\Contracts\Multilingual\TranslatableContentProvider;
use App\Helpers\LocaleHelper;
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
     * setting. An explicit locale (e.g. 'en', 'ja') is returned as-is.
     * The sentinel value 'auto' (or an unset/empty setting) falls back
     * to the site's default locale.
     *
     * The central translation manager UI excludes this locale from the
     * locale selector (so the operator cannot accidentally translate
     * "into" the primary), and `dls_onepage_localized_setting()`
     * short-circuits the primary locale straight to the primary value
     * instead of going through the resolver.
     *
     * Returns null only when LocaleHelper is unavailable; the helper /
     * editor treat null as "no primary locale configured" (= pre-Phase-B
     * behaviour with every enabled locale editable).
     */
    public function getPrimaryLocale(): ?string
    {
        $locale = ThemeSetting::getValue('default_locale');

        if (is_string($locale) && $locale !== '' && $locale !== 'auto') {
            return $locale;
        }

        try {
            return LocaleHelper::getSiteDefaultLocale();
        } catch (\Throwable) {
            return null;
        }
    }
}
