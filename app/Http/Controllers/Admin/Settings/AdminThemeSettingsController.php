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

namespace Themes\DixlaseOnePage\App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Admin\AdminLoggedInController;
use App\Models\Media;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Themes\DixlaseOnePage\App\Http\Requests\UpdateThemeSettingsRequest;
use Themes\DixlaseOnePage\App\Models\ThemeSetting;
use Themes\DixlaseOnePage\App\Providers\DixlaseOnePageServiceProvider;

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
            'header_logo_dark_id',
            'favicon_id',
            'hero_background_image_id',
            'hero_background_video_id',
            'hero_foreground_image_id',
            'hero_main_title',
            'hero_sub_title',
            'hero_button_text',
            'hero_button_link',
            'hero_button_enabled',
            'hero_button_target',
            'hero_button_secondary_text',
            'hero_button_secondary_link',
            'hero_button_secondary_enabled',
            'hero_button_secondary_target',
            'hero_gradient_mode',
            'hero_gradient_color',
            'hero_gradient_color_2',
            'hero_gradient_color_dark',
            'hero_gradient_color_2_dark',
            'hero_gradient_shape',
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
            'heading_font_family',
            'heading_font_apply_header',
            'heading_font_apply_hero',
            'heading_font_apply_footer',
            'heading_font_apply_content',
            'heading_font_tracking_header',
            'heading_font_tracking_hero',
            'heading_font_tracking_footer',
            'heading_font_tracking_content',
            'default_locale',
            'header_menu_id',
            'footer_menu_id',
            'show_inquiry_form',
            'primary_color',
            'multilingual_switcher_enabled',
            'appearance_toggle_enabled',
            'appearance_toggle_footer',
            'appearance_toggle_float',
        ]);

        // デフォルト値を設定
        $defaults = [
            'header_logo_id' => null,
            'header_logo_dark_id' => null,
            'favicon_id' => null,
            'hero_background_image_id' => null,
            'hero_background_video_id' => null,
            'hero_foreground_image_id' => null,
            'hero_main_title' => 'Welcome to '.config('app.name', 'Dixlase'),
            'hero_sub_title' => 'Modern CMS Platform for Building Amazing Websites',
            'hero_button_text' => 'Get Started',
            'hero_button_link' => '#',
            'hero_button_enabled' => '1',
            'hero_button_target' => '_self',
            'hero_button_secondary_text' => 'Learn More',
            'hero_button_secondary_link' => '#features',
            'hero_button_secondary_enabled' => '1',
            'hero_button_secondary_target' => '_self',
            'hero_gradient_mode' => 'primary',
            'hero_gradient_color' => '#3b82f6',
            'hero_gradient_color_2' => '#ffffff',
            'hero_gradient_color_dark' => null,
            'hero_gradient_color_2_dark' => null,
            'hero_gradient_shape' => 'radial',
            'footer_links' => '[]',
            // `© <year>` is auto-rendered (current year) at display time;
            // only the editable suffix is persisted. `and Dixlase
            // contributors` matches the Dixlase-brand attribution
            // pattern used on admin surfaces.
            'footer_copyright' => config('app.name', 'Dixlase').' and Dixlase contributors',
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
            'heading_font_family' => 'noto-sans-jp', // see UpdateThemeSettingsRequest for the value set
            'heading_font_apply_header' => '1',
            'heading_font_apply_hero' => '1',
            'heading_font_apply_footer' => '1',
            'heading_font_apply_content' => '1',
            'heading_font_tracking_header' => '0',
            'heading_font_tracking_hero' => '0',
            'heading_font_tracking_footer' => '0',
            'heading_font_tracking_content' => '0',
            'default_locale' => 'auto', // 'auto' resolves to site default at runtime
            'header_menu_id' => null,
            'footer_menu_id' => null,
            'show_inquiry_form' => '0',
            'primary_color' => '#3b82f6',
            'multilingual_switcher_enabled' => '0',
            'appearance_toggle_footer' => '0', // 0: hidden, 1: shown in the footer
            'appearance_toggle_float' => '0', // 0: hidden, 1: fixed in the corner
        ];

        // Legacy (0.2.0): one switch for the whole front-end switcher, now
        // one per placement. Mirrors DixlaseOnePageServiceProvider's merge so
        // the form shows what the front end renders. `appearance_toggle_enabled`
        // stays in the list above only to be read here; it is not a form field
        // any more and is never written back.
        if (($settingsData['appearance_toggle_footer'] ?? null) === null
            && ($settingsData['appearance_toggle_enabled'] ?? null) !== null) {
            $settingsData['appearance_toggle_footer'] = (string) $settingsData['appearance_toggle_enabled'];
        }
        unset($settingsData['appearance_toggle_enabled']);

        // デフォルト値とマージ
        $settingsData = array_merge($defaults, array_filter($settingsData, fn ($v) => $v !== null));

        // Normalise legacy heading_font_family values from the 2-choice
        // era so the sidebar picker highlights the correct card. The
        // layout resolver also accepts legacy values, but the picker
        // Alpine state uses strict equality against the new slugs.
        $settingsData['heading_font_family'] = match ($settingsData['heading_font_family'] ?? null) {
            'gothic' => 'noto-sans-jp',
            'mincho' => 'noto-serif-jp',
            'cormorant', 'jost', 'noto-sans-jp', 'noto-serif-jp' => $settingsData['heading_font_family'],
            default => 'noto-sans-jp',
        };

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

        $headerLogoDark = null;
        if (isset($settings->header_logo_dark_id)) {
            $headerLogoDark = Media::find($settings->header_logo_dark_id);
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
        $this->viewParams['headerLogoDark'] = $headerLogoDark;
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

        return view('themes::admin.settings.themes.settings', $this->viewParams);
    }

    /**
     * Let this response be framed by the admin panel.
     *
     * The admin area sends `frame-ancestors 'none'` (config/csp/admin.php), so
     * a preview iframe is refused by the browser and the panel shows an empty
     * frame with no error anywhere. Core's ContentSecurityPolicy middleware
     * rewrites the directive to `'self'` for a response whose request carries
     * this attribute, which is how core's own front page preview is framed
     * (AdminFrontController::previewFrame). Every action that renders into the
     * preview iframe has to call this, the POST one included: without it the
     * frame goes blank again on the operator's first keystroke.
     *
     * Only visible where the admin CSP is enforced. In development mode the
     * policy goes out as Content-Security-Policy-Report-Only, which browsers
     * do not act on, so the same code renders the frame perfectly there.
     */
    private function permitAdminFraming(): void
    {
        request()->attributes->set('csp_frame_ancestors_self', true);
    }

    /**
     * Render the preview frame with the settings currently stored.
     *
     * This is the `src` of the preview iframe, so the first paint needs no
     * JavaScript. It renders `themes::admin.preview-frame` — the same view
     * the front page editor's preview uses — with `bareContent` false, so
     * the header, hero, content, inquiry section and footer all come from
     * the theme's own partials and a site's `custom/` overrides apply
     * exactly as they do on the live page.
     */
    public function previewFrame(): View
    {
        $this->permitAdminFraming();

        return view('themes::admin.preview-frame', $this->previewFrameParams());
    }

    /**
     * Render the preview frame with settings that have not been saved.
     *
     * The settings form posts itself here, targeting the preview iframe, so
     * the operator sees the real theme rendered with the values in the form.
     * Nothing is written: the values are bound for this request only and the
     * view composer merges them over the stored ones.
     *
     * The rules are the ones the save path uses, so a value that would be
     * rejected on save is not previewed as if it were acceptable. They are
     * applied leniently though: a field that fails is dropped and the stored
     * value shows through, rather than the request being rejected. Injecting
     * the form request instead would redirect on the first invalid field,
     * and since the form posts itself while the operator is still typing,
     * that redirect would land in the preview frame — the settings page
     * rendered inside its own preview.
     */
    public function preview(Request $request): View
    {
        $this->permitAdminFraming();

        $valid = Validator::make($request->all(), (new UpdateThemeSettingsRequest())->rules())->valid();

        app()->instance(
            DixlaseOnePageServiceProvider::PREVIEW_SETTINGS,
            $this->normalizeCopyright($valid)
        );

        return view('themes::admin.preview-frame', $this->previewFrameParams());
    }

    /**
     * The non-setting half of the preview frame: the front page body, and
     * the custom CSS / JS the frame links as subresources.
     *
     * Theme settings do not reach the front page body, so this part is the
     * same whether the preview is of saved or unsaved settings.
     */
    private function previewFrameParams(): array
    {
        // Keyed on the row rather than on the admin's locale, matching the
        // front page editor's preview (`AdminFrontController::previewFrame`).
        // Looking the page up by `app()->getLocale()` meant the frame went
        // blank whenever the operator's locale differed from the page's.
        $frontPage = \App\Models\FrontPage::findByType('main_content');

        if (! $frontPage || ! $frontPage->isPublished()) {
            // No published front page for this locale. The frame still
            // renders: header, hero and footer are what this preview is
            // mostly about, and an empty body is the truth about the page.
            return [
                'initialRenderedContent' => '',
                'hasCustomCss' => false,
                'hasCustomJs' => false,
                'customAssetVersion' => '0',
                'bareContent' => false,
            ];
        }

        $contentService = app(\App\Services\FrontPageContentService::class);
        $locale = $frontPage->lang;

        $rawContent = $contentService->getContent($frontPage, $locale);
        $renderedContent = $rawContent
            ? shortcode_parse($this->renderFrontContent($frontPage, $rawContent))
            : '';

        // Custom CSS / JS belong to the HTML editor only, matching core's
        // own preview frame.
        $customCss = null;
        $customJs = null;
        if ($frontPage->editor_type === \App\Enums\ContentEditorType::HTML) {
            $customCss = $contentService->getCssContent($frontPage, $locale);
            $customJs = $contentService->getJsContent($frontPage, $locale);
        }

        return [
            'initialRenderedContent' => $renderedContent,
            'hasCustomCss' => ! empty($customCss),
            'hasCustomJs' => ! empty($customJs),
            'customAssetVersion' => \App\Http\Controllers\Front\FrontWelcomeController::customAssetVersion(
                $frontPage,
                (string) $customCss,
                (string) $customJs
            ),
            'bareContent' => false,
        ];
    }

    /**
     * Turn stored front page content into HTML, by editor type.
     */
    private function renderFrontContent(\App\Models\FrontPage $frontPage, string $rawContent): string
    {
        return match ($frontPage->editor_type) {
            \App\Enums\ContentEditorType::GUI => app(\App\Services\Editor\EditorManager::class)
                ->renderContent('gui', $rawContent),
            \App\Enums\ContentEditorType::MARKDOWN => (string) \Illuminate\Support\Str::markdown($rawContent),
            \App\Enums\ContentEditorType::BLADE => \Illuminate\Support\Facades\Blade::render($rawContent),
            default => $rawContent,
        };
    }

    /**
     * テーマ設定を更新
     */
    public function update(UpdateThemeSettingsRequest $request)
    {
        $validated = $this->normalizeCopyright($request->validated());

        // キーバリュー形式で保存
        ThemeSetting::setValues($validated);

        // キャッシュをクリア
        ThemeSetting::clearAllCache();

        return redirect()
            ->route('admin.settings.themes.settings')
            ->with('success', __('themes::admin.settings.updated_successfully'));
    }

    /**
     * Strip the `© <year>` prefix from the footer copyright.
     *
     * The footer renders that prefix from the current year on every
     * request, so only the editable suffix is stored. A prefix the operator
     * typed in, or one carried over from legacy data, would otherwise show
     * twice.
     *
     * Shared by the save path and the preview path so the preview shows
     * what saving would produce.
     *
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function normalizeCopyright(array $values): array
    {
        if (isset($values['footer_copyright'])) {
            $values['footer_copyright'] = preg_replace(
                '/^\s*©\s*\d{4}\s+/u',
                '',
                (string) $values['footer_copyright']
            );
        }

        return $values;
    }
}
