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
            'favicon_id' => 'nullable|integer|exists:media,id',

            // Hero Section
            'hero_background_image_id' => 'nullable|integer|exists:media,id',
            'hero_background_video_id' => 'nullable|integer|exists:media,id',
            'hero_foreground_image_id' => 'nullable|integer|exists:media,id',
            'hero_main_title' => 'required|string|max:255',
            'hero_sub_title' => 'nullable|string|max:1000',
            'hero_button_text' => 'nullable|string|max:100',
            'hero_button_link' => 'nullable|string|max:500',
            'hero_button_enabled' => 'nullable|in:0,1',
            'hero_button_secondary_text' => 'nullable|string|max:100',
            'hero_button_secondary_link' => 'nullable|string|max:500',
            'hero_button_secondary_enabled' => 'nullable|in:0,1',

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
        ];
    }
}
