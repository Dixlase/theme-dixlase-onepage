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

use Themes\DixlaseOnePage\App\Models\ThemeSetting;

if (! function_exists('dls_onepage_localized_setting')) {
    /**
     * Read a translatable DixlaseOnePage theme setting in the current locale.
     *
     * Lookup chain:
     *
     *   1. DixlaseMultilingual's SingletonTranslationResolver for the
     *      current locale (anchored on
     *      `(translatable_type='dixlase-onepage:settings', translatable_id=1)`)
     *   2. Same resolver for the site's default locale
     *   3. The primary value from `thm_dixlase_onepage_settings`
     *
     * On single-language sites (no DixlaseMultilingual installed) steps
     * 1 and 2 are skipped because no resolver is bound, and the helper
     * returns the primary value directly.
     *
     * Intended fields: hero_main_title, hero_sub_title, hero_button_text,
     * hero_button_secondary_text. Callers outside this set get the
     * primary value via the same path.
     */
    function dls_onepage_localized_setting(string $key): ?string
    {
        try {
            // Step 0: when the current locale equals the provider's
            // primary locale (derived from the `default_locale` theme
            // setting, falling back to the site default when 'auto' or
            // unset), skip the resolver entirely — the primary IS the
            // value for that locale. This also avoids the empty-row
            // trap when the operator opens a primary-locale tab in the
            // central translation manager and saves it blank.
            try {
                $primaryLocale = (new \Themes\DixlaseOnePage\App\Multilingual\DixlaseOnePageSettingsProvider())->getPrimaryLocale();
            } catch (\Throwable) {
                $primaryLocale = null;
            }
            if ($primaryLocale !== null && $primaryLocale === app()->getLocale()) {
                $primary = ThemeSetting::getValue($key);

                return $primary === null ? null : (string) $primary;
            }

            if (app()->bound(\App\Contracts\Multilingual\SingletonTranslationResolver::class)) {
                /** @var \App\Contracts\Multilingual\SingletonTranslationResolver $resolver */
                $resolver = app(\App\Contracts\Multilingual\SingletonTranslationResolver::class);

                // Treat an empty string the same as "no translation". The
                // central translation manager UI stores every field of a
                // locale row even when the operator leaves them blank, so
                // a brand-new locale tab saves `""` for each field on
                // first save. Without this guard, the helper would return
                // `""` and the blade would render an empty hero block
                // instead of falling back to the primary value.
                $value = $resolver->resolve('dixlase-onepage:settings', $key, app()->getLocale());
                if ($value !== null && $value !== '') {
                    return (string) $value;
                }

                try {
                    $siteDefault = \App\Helpers\LocaleHelper::getSiteDefaultLocale();
                } catch (\Throwable) {
                    $siteDefault = null;
                }

                if (is_string($siteDefault) && $siteDefault !== app()->getLocale()) {
                    $value = $resolver->resolve('dixlase-onepage:settings', $key, $siteDefault);
                    if ($value !== null && $value !== '') {
                        return (string) $value;
                    }
                }
            }

            $primary = ThemeSetting::getValue($key);

            return $primary === null ? null : (string) $primary;
        } catch (\Throwable) {
            // Defensive: never let a translation lookup break the page.
            try {
                $primary = ThemeSetting::getValue($key);

                return $primary === null ? null : (string) $primary;
            } catch (\Throwable) {
                return null;
            }
        }
    }
}
