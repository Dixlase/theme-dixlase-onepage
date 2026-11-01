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

if (! function_exists('dls_onepage_primary_setting')) {
    /**
     * The primary-locale value of a theme setting.
     *
     * Normally the stored one. While the theme settings screen is
     * previewing values the operator has not saved, the posted value stands
     * in for it, so typing in the form is reflected in the preview.
     *
     * Deliberately the *primary* value and nothing more: a translation for
     * the locale being previewed still wins, exactly as it does on the live
     * page. Editing the primary field of a setting that the viewed locale
     * translates changes neither, which is the truth about that page.
     */
    function dls_onepage_primary_setting(string $key): ?string
    {
        $override = \Themes\DixlaseOnePage\App\Providers\DixlaseOnePageServiceProvider::PREVIEW_SETTINGS;

        if (app()->bound($override)) {
            $values = (array) app($override);
            if (array_key_exists($key, $values)) {
                return $values[$key] === null ? null : (string) $values[$key];
            }
        }

        $stored = ThemeSetting::getValue($key);

        return $stored === null ? null : (string) $stored;
    }
}

if (! function_exists('dls_onepage_localized_setting')) {
    /**
     * Read a translatable DixlaseOnePage theme setting in the current locale.
     *
     * Lookup chain:
     *
     *   1. The bound `SingletonTranslationResolver` for the current
     *      locale (anchored on
     *      `(translatable_type='dixlase-onepage:settings', translatable_id=1)`)
     *   2. Same resolver for the site's default locale
     *   3. The primary value from `thm_dixlase_onepage_settings`
     *
     * On a single-language site steps 1 and 2 are skipped because no
     * resolver is bound, and the helper returns the primary value
     * directly.
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
                $primary = dls_onepage_primary_setting($key);

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

            $primary = dls_onepage_primary_setting($key);

            return $primary === null ? null : (string) $primary;
        } catch (\Throwable) {
            // Defensive: never let a translation lookup break the page.
            try {
                $primary = dls_onepage_primary_setting($key);

                return $primary === null ? null : (string) $primary;
            } catch (\Throwable) {
                return null;
            }
        }
    }
}

if (! function_exists('dls_onepage_appearance_options')) {
    /**
     * The appearance switcher's three modes, in display order.
     *
     * Single source of truth for the stored value, the label and the icon
     * of each mode. Both placements read it: the option group rendered by
     * `partials/appearance-toggle.blade.php`, and the collapsed trigger of
     * the corner placement, which needs the icons on their own so it can
     * show the mode in effect.
     *
     * Keys are the stored values, matching `appearance_mode` and the
     * `dls-appearance-mode` localStorage entry ('0' auto / '1' light /
     * '2' dark). Labels resolve at call time so they follow the request's
     * locale.
     *
     * @return array<string, array{label: string, icon: string}>
     */
    function dls_onepage_appearance_options(): array
    {
        return [
            '0' => ['label' => __('themes::theme.appearance.auto'),  'icon' => 'fa-circle-half-stroke'],
            '1' => ['label' => __('themes::theme.appearance.light'), 'icon' => 'fa-sun'],
            '2' => ['label' => __('themes::theme.appearance.dark'),  'icon' => 'fa-moon'],
        ];
    }
}

/*
 * The narrowest and widest custom hero height, as a percentage of the window.
 *
 * Below the floor the height stops deciding the hero's size — the text inside
 * runs to roughly 260-340px on a desktop, taller than that on most screens, so
 * the slider would appear to do nothing. Above the ceiling it is not
 * meaningfully different from the full-screen mode, which additionally
 * subtracts the admin bar and pins an exact height; the gap is what makes the
 * two modes mean different things.
 *
 * Enforced twice: on the slider, and in UpdateThemeSettingsRequest. The
 * slider's bounds are a convenience, not a guarantee.
 */
if (! defined('DLS_ONEPAGE_HERO_HEIGHT_MIN')) {
    define('DLS_ONEPAGE_HERO_HEIGHT_MIN', 20);
}

if (! defined('DLS_ONEPAGE_HERO_HEIGHT_MAX')) {
    define('DLS_ONEPAGE_HERO_HEIGHT_MAX', 90);
}

/** The custom height a site gets before it has moved the slider. */
if (! defined('DLS_ONEPAGE_HERO_HEIGHT_DEFAULT')) {
    define('DLS_ONEPAGE_HERO_HEIGHT_DEFAULT', 55);
}

if (! function_exists('dls_onepage_hero_mode')) {
    /**
     * The hero height mode: 'none', 'custom' or 'full'.
     *
     * Anything unrecognised reads as 'full', which is both the default and
     * what every site had before this setting existed.
     */
    function dls_onepage_hero_mode(): string
    {
        $mode = (string) (dls_onepage_primary_setting('hero_height') ?? 'full');

        return in_array($mode, ['none', 'custom', 'full'], true) ? $mode : 'full';
    }
}

if (! function_exists('dls_onepage_hero_enabled')) {
    /**
     * Whether the page renders a hero at all.
     *
     * Read by the two views that include the hero partial rather than by the
     * partial itself, on purpose: a site that overrides
     * `partials/hero.blade.php` under `custom/` replaces it wholesale, and a
     * check living inside the partial would be lost with it. Gating the
     * include means "no hero" works even where the partial has been forked.
     */
    function dls_onepage_hero_enabled(): bool
    {
        return dls_onepage_hero_mode() !== 'none';
    }
}

if (! function_exists('dls_onepage_hero_custom_height')) {
    /**
     * The stored custom height, clamped to the bounds above.
     *
     * Clamped on read as well as on save, so a row written before the bounds
     * existed — or written by hand — cannot produce a hero taller than the
     * window.
     */
    function dls_onepage_hero_custom_height(): int
    {
        $stored = dls_onepage_primary_setting('hero_height_custom');

        $value = is_numeric($stored) ? (int) $stored : DLS_ONEPAGE_HERO_HEIGHT_DEFAULT;

        return max(DLS_ONEPAGE_HERO_HEIGHT_MIN, min(DLS_ONEPAGE_HERO_HEIGHT_MAX, $value));
    }
}

if (! function_exists('dls_onepage_hero_height_class')) {
    /**
     * The Tailwind class that gives the hero section its height.
     *
     * Full screen keeps an exact height and subtracts the admin bar when one
     * is shown: that mode means "one viewport", and the arithmetic is what
     * makes it exactly that. The comment in partials/hero.blade.php explains
     * why exact rather than minimum — the flex layout below absorbs the
     * remainder, which is what keeps text and image inside the screen
     * together.
     *
     * Empty for a custom height, which is a declaration rather than a class;
     * see dls_onepage_hero_height_style().
     */
    function dls_onepage_hero_height_class(): string
    {
        if (dls_onepage_hero_mode() === 'custom') {
            return '';
        }

        $hasAdminBar = app(\App\Services\Auth\SignedInMemberResolver::class)->check();

        return $hasAdminBar ? 'h-[calc(100vh-3rem)]' : 'h-screen';
    }
}

if (! function_exists('dls_onepage_hero_height_style')) {
    /**
     * The inline height declaration for a custom hero, or an empty string.
     *
     * A declaration rather than a Tailwind class on purpose. `min-h-[55vh]`
     * assembled from a stored number is invisible to Tailwind's scanner —
     * it reads source files, not the values a site happens to hold — so the
     * utility would simply not be in the emitted CSS and the hero would have
     * no height at all. Safelisting all 15 steps would work and would put 15
     * unused rules in every site's stylesheet. The value is data, so it goes
     * where data goes.
     *
     * A *minimum*, not an exact height. The exact height the full-screen
     * mode uses exists to pin the hero to one viewport; there is no single
     * screen to stay inside here, and the section is `overflow-hidden`, so
     * an exact 30% would cut a two-line title off on a laptop. As a minimum,
     * the chosen height is what nearly every screen shows and a short one
     * grows to fit rather than clipping. The admin bar is not subtracted for
     * the same reason — this mode is not trying to fill the screen.
     */
    function dls_onepage_hero_height_style(): string
    {
        if (dls_onepage_hero_mode() !== 'custom') {
            return '';
        }

        return 'min-height: '.dls_onepage_hero_custom_height().'vh;';
    }
}
