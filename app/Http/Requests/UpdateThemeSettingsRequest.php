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

namespace Themes\DixlaseOnePage\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateThemeSettingsRequest extends FormRequest
{
    /**
     * リクエストが認可されているか判定
     */
    public function authorize(): bool
    {
        // 管理者のみアクセス可能
        return true;
    }

    /**
     * バリデーションルールを取得
     */
    public function rules(): array
    {
        return [
            // Header & Favicon
            'header_logo_id' => 'nullable|integer|exists:media,id',
            'header_logo_dark_id' => 'nullable|integer|exists:media,id',
            'favicon_id' => 'nullable|integer|exists:media,id',

            // Hero Section
            'hero_background_image_id' => 'nullable|integer|exists:media,id',
            'hero_background_video_id' => 'nullable|integer|exists:media,id',
            'hero_foreground_image_id' => 'nullable|integer|exists:media,id',
            'hero_main_title' => 'required|string|max:255',
            'hero_sub_title' => 'nullable|string|max:1000',
            'hero_button_text' => 'nullable|string|max:100',
            'hero_button_link' => ['nullable', 'string', 'max:500', $this->safeLinkRule()],
            'hero_button_enabled' => 'nullable|in:0,1',
            'hero_button_target' => 'nullable|in:_self,_blank',
            'hero_button_secondary_text' => 'nullable|string|max:100',
            'hero_button_secondary_link' => ['nullable', 'string', 'max:500', $this->safeLinkRule()],
            'hero_button_secondary_enabled' => 'nullable|in:0,1',
            'hero_button_secondary_target' => 'nullable|in:_self,_blank',

            // Hero radial gradient (background-less hero only).
            // Legacy 'none' is still accepted for backward compat with sites
            // saved before the restructure; it is normalised at resolve-time
            // to shape='solid' + mode='primary' in partials/hero.blade.php.
            'hero_gradient_mode' => 'nullable|in:primary,custom,none',
            // 6-digit hex — narrow regex keeps CSS injection out of the
            // style attribute these values are interpolated into.
            'hero_gradient_color' => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
            'hero_gradient_color_2' => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
            // Dark-mode overrides (per-mode custom colors). Empty → dark
            // resolver falls back to the light value.
            'hero_gradient_color_dark' => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
            'hero_gradient_color_2_dark' => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
            // Shape whitelist — extendable to 'linear-horizontal', 'conic'
            // etc. later without opening the string.
            'hero_gradient_shape' => 'nullable|in:radial,linear-vertical,solid',

            // Footer
            'footer_copyright' => 'nullable|string|max:500',

            // SNS Links
            'footer_sns_instagram' => 'nullable|string|max:500',
            'footer_sns_x' => 'nullable|string|max:500',
            'footer_sns_facebook' => 'nullable|string|max:500',
            'footer_sns_tiktok' => 'nullable|string|max:500',
            'footer_sns_bluesky' => 'nullable|string|max:500',
            'footer_sns_threads' => 'nullable|string|max:500',
            'footer_sns_linkedin' => 'nullable|string|max:500',
            'footer_sns_youtube' => 'nullable|string|max:500',
            'footer_sns_pinterest' => 'nullable|string|max:500',
            'footer_sns_discord' => 'nullable|string|max:500',
            'footer_sns_github' => 'nullable|string|max:500',

            // Appearance Mode
            'appearance_mode' => 'required|in:0,1,2',

            // Heading font family (headings only — body stays on system stack).
            // Values:
            //   cormorant     → Cormorant Garamond (Latin serif display)
            //   jost          → Jost (Latin geometric sans)
            //   noto-sans-jp  → Noto Sans JP (JP sans)
            //   noto-serif-jp → Noto Serif JP (JP serif)
            // Legacy values 'gothic' / 'mincho' from the 2-choice era are
            // still accepted and normalised by the layout resolver so old
            // DB rows do not fail validation after upgrade.
            'heading_font_family' => 'nullable|in:cormorant,jost,noto-sans-jp,noto-serif-jp,gothic,mincho',

            // Per-region apply toggles. When '0', that region inherits the body
            // font (system stack); when '1', it uses --font-heading.
            'heading_font_apply_header' => 'nullable|in:0,1',
            'heading_font_apply_hero' => 'nullable|in:0,1',
            'heading_font_apply_footer' => 'nullable|in:0,1',
            'heading_font_apply_content' => 'nullable|in:0,1',

            // Per-region tracking (letter-spacing) in em units. Range
            // -0.1em to 0.3em covers idiomatic display-heading tightening
            // through open editorial-style tracking; extremes beyond the
            // range would break JP glyph shaping.
            'heading_font_tracking_header' => 'nullable|numeric|between:-0.1,0.3',
            'heading_font_tracking_hero' => 'nullable|numeric|between:-0.1,0.3',
            'heading_font_tracking_footer' => 'nullable|numeric|between:-0.1,0.3',
            'heading_font_tracking_content' => 'nullable|numeric|between:-0.1,0.3',

            // Multilingual: locale the primary-stored theme-settings values
            // are authored in. 'auto' resolves to the site default at runtime;
            // any other value must be a locale code recognised by Core's
            // LocaleHelper. The narrow rule keeps the request lean — Core's
            // LocaleHelper::supportedLocales() is the source of truth, but
            // listing them here would duplicate it; `string|max:10` blocks the
            // obvious abuse and any genuinely unsupported value is silently
            // ignored by the provider (falls back to site default).
            'default_locale' => 'nullable|string|max:10',

            // Primary Color
            'primary_color' => 'nullable|string|in:#3b82f6,#8b5cf6,#10b981,#ef4444,#f97316,#eab308,#92400e,#ec4899,#6366f1,#1f2937,#6b7280',

            // Plugin Integration
            'header_menu_id' => 'nullable|integer',
            'footer_menu_id' => 'nullable|integer',
            'show_inquiry_form' => 'nullable|in:0,1',

            // DixlaseMultilingual integration
            'multilingual_switcher_enabled' => 'nullable|in:0,1',
        ];
    }

    /**
     * Reject link schemes that execute rather than navigate.
     *
     * The hero button links are rendered into `href="{{ ... }}"` on the front
     * page, which every unauthenticated visitor sees. Blade escapes the HTML
     * but does nothing about the scheme, so `javascript:` runs for anyone who
     * clicks the call to action. These fields were validated only as
     * `nullable|string|max:500`.
     *
     * Deliberately not FILTER_VALIDATE_URL: it accepts
     * `javascript://%0aalert(1)` (verified on PHP 8.3), which is exactly the
     * payload this needs to stop. Control characters are stripped before the
     * scheme is read because browsers ignore them inside one -- `java\tscript:`
     * navigates just like `javascript:`.
     */
    protected function safeLinkRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (! is_string($value) || trim($value) === '') {
                return;
            }

            $candidate = preg_replace('/[\x00-\x20]/', '', trim($value)) ?? '';

            // No scheme: a relative path, query or fragment. Safe.
            // `//host` is the exception -- it inherits the page scheme and
            // leaves the origin, so it is treated as an absolute link.
            if (str_starts_with($candidate, '//')) {
                $fail(__('themes::admin.validation.link_scheme_not_allowed'));

                return;
            }

            if (preg_match('/^([A-Za-z][A-Za-z0-9+.\-]*):/', $candidate, $matches) !== 1) {
                return;
            }

            if (! in_array(strtolower($matches[1]), ['http', 'https', 'mailto', 'tel'], true)) {
                $fail(__('themes::admin.validation.link_scheme_not_allowed'));
            }
        };
    }

    /**
     * バリデーションエラーメッセージをカスタマイズ
     */
    public function messages(): array
    {
        return [
            'hero_main_title.required' => __('themes::admin.settings.hero.main_title').'は必須です。',
            'hero_background_image_id.exists' => '選択された画像が見つかりません。',
            'footer_links.*.title.required' => 'リンクタイトルは必須です。',
            'footer_links.*.url.required' => 'リンクURLは必須です。',
            'appearance_mode.required' => '外観モードは必須です。',
            'appearance_mode.in' => '外観モードは有効な値を選択してください。',
        ];
    }

    /**
     * バリデーション属性名をカスタマイズ
     */
    public function attributes(): array
    {
        return [
            'header_logo_id' => __('themes::admin.settings.header.header_logo'),
            'header_logo_dark_id' => __('themes::admin.settings.header.header_logo_dark'),
            'favicon_id' => __('themes::admin.settings.header.favicon'),
            'hero_background_image_id' => __('themes::admin.settings.hero.background_image'),
            'hero_background_video_id' => __('themes::admin.settings.hero.background_video'),
            'hero_foreground_image_id' => __('themes::admin.settings.hero.foreground_image'),
            'hero_main_title' => __('themes::admin.settings.hero.main_title'),
            'hero_sub_title' => __('themes::admin.settings.hero.sub_title'),
            'hero_button_text' => __('themes::admin.settings.hero.button_text'),
            'hero_button_link' => __('themes::admin.settings.hero.button_link'),
            'hero_button_secondary_text' => __('themes::admin.settings.hero.button_secondary_text'),
            'hero_button_secondary_link' => __('themes::admin.settings.hero.button_secondary_link'),
            'footer_copyright' => __('themes::admin.settings.footer.copyright'),
            'appearance_mode' => __('common.appearance_mode'),
            'heading_font_family' => __('themes::admin.settings.typography.heading_font_family'),
        ];
    }
}
