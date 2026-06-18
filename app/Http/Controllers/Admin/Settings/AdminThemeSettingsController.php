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

namespace Themes\DixlaseOnePage\App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Admin\AdminLoggedInController;
use App\Models\Media;
use Themes\DixlaseOnePage\App\Http\Requests\UpdateThemeSettingsRequest;
use Themes\DixlaseOnePage\App\Models\ThemeSetting;

class AdminThemeSettingsController extends AdminLoggedInController
{
    /**
     * テーマ設定画面を表示
     */
    public function settings()
    {
        // すべての設定をキーバリュー形式で取得
        $settingsData = ThemeSetting::getValues([
            'header_logo_id',
            'favicon_id',
            'hero_background_image_id',
            'hero_background_video_id',
            'hero_foreground_image_id',
            'hero_main_title',
            'hero_sub_title',
            'hero_button_text',
            'hero_button_link',
            'hero_button_enabled',
            'hero_button_secondary_text',
            'hero_button_secondary_link',
            'hero_button_secondary_enabled',
            'footer_links',
            'footer_copyright',
            'footer_sns_instagram',
            'footer_sns_x',
            'footer_sns_facebook',
            'footer_sns_tiktok',
            'footer_sns_bluesky',
            'footer_sns_threads',
            'footer_sns_linkedin',
            'footer_sns_youtube',
            'footer_sns_pinterest',
            'footer_sns_discord',
            'footer_sns_github',
            'appearance_mode',
            'default_locale',
            'header_menu_id',
            'footer_menu_id',
            'show_inquiry_form',
            'primary_color',
            'multilingual_switcher_enabled',
        ]);

        // デフォルト値を設定
        $defaults = [
            'header_logo_id' => null,
            'favicon_id' => null,
            'hero_background_image_id' => null,
            'hero_background_video_id' => null,
            'hero_foreground_image_id' => null,
            'hero_main_title' => 'Welcome to '.config('app.name', 'Dixlase'),
            'hero_sub_title' => 'Modern CMS Platform for Building Amazing Websites',
            'hero_button_text' => 'Get Started',
            'hero_button_link' => '#',
            'hero_button_enabled' => '1',
            'hero_button_secondary_text' => 'Learn More',
            'hero_button_secondary_link' => '#features',
            'hero_button_secondary_enabled' => '1',
            'footer_links' => '[]',
            // `© <year>` is auto-rendered (current year) at display time;
            // only the editable suffix is persisted.
            'footer_copyright' => config('app.name', 'Dixlase').'. All rights reserved.',
            'footer_sns_instagram' => null,
            'footer_sns_x' => null,
            'footer_sns_facebook' => null,
            'footer_sns_tiktok' => null,
            'footer_sns_bluesky' => null,
            'footer_sns_threads' => null,
            'footer_sns_linkedin' => null,
            'footer_sns_youtube' => null,
            'footer_sns_pinterest' => null,
            'footer_sns_discord' => null,
            'footer_sns_github' => null,
            'appearance_mode' => '0', // 0: Auto, 1: Light, 2: Dark
            'default_locale' => 'auto', // 'auto' resolves to site default at runtime
            'header_menu_id' => null,
            'footer_menu_id' => null,
            'show_inquiry_form' => '0',
            'primary_color' => '#3b82f6',
            'multilingual_switcher_enabled' => '0',
        ];

        // デフォルト値とマージ
        $settingsData = array_merge($defaults, array_filter($settingsData, fn ($v) => $v !== null));

        // オブジェクトに変換
        $settings = (object) $settingsData;

        // JSON文字列をデコード
        if (isset($settings->footer_links) && is_string($settings->footer_links)) {
            $settings->footer_links = json_decode($settings->footer_links, true) ?? [];
        }

        // メディアを取得
        $headerLogo = null;
        if (isset($settings->header_logo_id)) {
            $headerLogo = Media::find($settings->header_logo_id);
        }

        $favicon = null;
        if (isset($settings->favicon_id)) {
            $favicon = Media::find($settings->favicon_id);
        }

        $heroBackgroundImage = null;
        if (isset($settings->hero_background_image_id)) {
            $heroBackgroundImage = Media::find($settings->hero_background_image_id);
        }

        $heroForegroundImage = null;
        if (isset($settings->hero_foreground_image_id)) {
            $heroForegroundImage = Media::find($settings->hero_foreground_image_id);
        }

        $heroBackgroundVideo = null;
        if (isset($settings->hero_background_video_id)) {
            $heroBackgroundVideo = Media::find($settings->hero_background_video_id);
        }

        $this->viewParams['settings'] = $settings;
        $this->viewParams['headerLogo'] = $headerLogo;
        $this->viewParams['favicon'] = $favicon;
        $this->viewParams['heroBackgroundImage'] = $heroBackgroundImage;
        $this->viewParams['heroBackgroundVideo'] = $heroBackgroundVideo;
        $this->viewParams['heroForegroundImage'] = $heroForegroundImage;

        // Plugin integration via Contract+DTO (no direct plugin references)
        $resolver = app(\App\Services\Plugin\PluginServiceResolver::class);

        $menuResult = $resolver->resolve(\App\Contracts\PluginIntegration\MenuProviderInterface::class, 'dixlase-menus');
        $menuPluginEnabled = $menuResult->resolved;
        $inquiryPluginEnabled = \App\Helpers\PluginHelper::isEnabled('dixlase-inquiry');

        $this->viewParams['menuPluginEnabled'] = $menuPluginEnabled;
        $this->viewParams['inquiryPluginEnabled'] = $inquiryPluginEnabled;

        // Build menu options and items via MenuProviderInterface
        $menuOptions = ['' => __('themes::admin.settings.plugins.menu.none')];
        $allMenusData = [];
        if ($menuPluginEnabled && $menuResult->instance) {
            $menuOptions += $menuResult->instance->getMenuOptions();
            foreach ($menuResult->instance->getMenus() as $menuDTO) {
                $allMenusData[$menuDTO->id] = array_map(fn ($item) => [
                    'title' => $item->label,
                    'url' => $item->url,
                ], $menuDTO->items);
            }
        }
        $this->viewParams['menuOptions'] = $menuOptions;
        $this->viewParams['allMenusData'] = $allMenusData;

        // Check if inquiry form should show in preview
        $this->viewParams['showInquiryForm'] = $settings->show_inquiry_form ?? '0';

        // Resolve inquiry preview via Contract+DTO (no direct plugin reference)
        $inquiryPreview = null;
        if ($inquiryPluginEnabled) {
            $resolver = app(\App\Services\Plugin\PluginServiceResolver::class);
            $result = $resolver->resolve(\App\Contracts\PluginIntegration\PreviewProviderInterface::class, 'dixlase-inquiry');
            if ($result->resolved && $result->instance) {
                $inquiryPreview = $result->instance->getPreview('inquiry_form');
            }
        }
        $this->viewParams['inquiryPreview'] = $inquiryPreview;

        // Detect whether the inquiry plugin is fully configured.
        // dls_inquiry_section() silently returns null when admin_email is empty
        // or accepting_inquiries is off, so we surface that to the editor.
        $inquiryReady = false;
        if ($inquiryPluginEnabled && function_exists('dls_inquiry_enabled')) {
            $inquiryReady = (bool) dls_inquiry_enabled();
        }
        $this->viewParams['inquiryReady'] = $inquiryReady;

        // フロントページコンテンツをプレビュー用に取得
        $frontContentPreview = null;
        $frontPage = \App\Models\FrontPage::findByTypeAndLang('main_content', app()->getLocale());
        if ($frontPage && $frontPage->isPublished()) {
            $contentService = app(\App\Services\FrontPageContentService::class);
            $rawContent = $contentService->getContent($frontPage, app()->getLocale());
            if ($rawContent) {
                $editorType = $frontPage->editor_type;
                if ($editorType === \App\Enums\ContentEditorType::GUI) {
                    $renderedHtml = app(\App\Services\Editor\EditorManager::class)->renderContent('gui', $rawContent);
                } elseif ($editorType === \App\Enums\ContentEditorType::MARKDOWN) {
                    $renderedHtml = \Illuminate\Support\Str::markdown($rawContent);
                } elseif ($editorType === \App\Enums\ContentEditorType::BLADE) {
                    $renderedHtml = \Illuminate\Support\Facades\Blade::render($rawContent);
                } else {
                    $renderedHtml = $rawContent;
                }
                $frontContentPreview = shortcode_parse($renderedHtml);
            }
        }
        $this->viewParams['frontContentPreview'] = $frontContentPreview;

        return view('themes::admin.settings.themes.settings', $this->viewParams);
    }

    /**
     * テーマ設定を更新
     */
    public function update(UpdateThemeSettingsRequest $request)
    {
        $validated = $request->validated();

        // The footer's `© <year>` prefix is rendered automatically from
        // the current year on every request, so the DB only stores the
        // editable suffix. Strip any prefix the user may have typed in
        // (or that came through from legacy data) before persisting.
        if (isset($validated['footer_copyright'])) {
            $validated['footer_copyright'] = preg_replace(
                '/^\s*©\s*\d{4}\s+/u',
                '',
                $validated['footer_copyright']
            );
        }

        // キーバリュー形式で保存
        ThemeSetting::setValues($validated);

        // キャッシュをクリア
        ThemeSetting::clearAllCache();

        return redirect()
            ->route('admin.settings.themes.settings')
            ->with('success', __('themes::admin.settings.updated_successfully'));
    }
}
