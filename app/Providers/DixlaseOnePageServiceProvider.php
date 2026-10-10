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

namespace Themes\DixlaseOnePage\App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Themes\DixlaseOnePage\App\Models\ThemeSetting;

class DixlaseOnePageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Load helper functions (dls_onepage_localized_setting etc.).
        // Kept here rather than in composer.json's autoload.files: core's
        // sync-local-autoload.php merges an extension's autoload.files into
        // composer.local.json, so declaring it there registered the same
        // file twice and composer warned about it. Loading it from the
        // provider keeps the blast radius inside the theme.
        require_once __DIR__.'/../Helpers/DixlaseOnePageHelpers.php';

        // Bind OnePage's appearance provider to Core's
        // SiteAppearanceProviderInterface contract so out-of-tree
        // consumers (Cloudflare Turnstile widget, third-party
        // embeds that need site-theme awareness, etc.) can read the
        // currently-configured light/dark/auto mode without coupling
        // to OnePage's own settings table.
        //
        // Guarded by `interface_exists` because the contract ships in
        // dixlase-core#104; Core builds that pre-date the contract
        // would autoload-fail when the binding fires, so we skip the
        // bind entirely on those (the consumer-side then falls back
        // to its own safe default — `auto` for Turnstile).
        if (interface_exists(\App\Contracts\Theme\SiteAppearanceProviderInterface::class)) {
            $this->app->bind(
                \App\Contracts\Theme\SiteAppearanceProviderInterface::class,
                \Themes\DixlaseOnePage\App\Services\DixlaseOnePageAppearanceProvider::class,
            );
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Views, translations and routes are already loaded by the core
        // ThemeServiceProvider. Migrations are deliberately NOT registered
        // here: they are applied by ThemeMigrator (dls:theme:install) and
        // recorded in the dls_theme_migrations ledger. Handing them to the
        // stock migrator makes a bare `php artisan migrate` try to
        // re-create tables that ThemeMigrator already created (SQLSTATE
        // 42S01). See PluginLoaderTrait::loadPluginMigrations() in core.

        // Auto-seed theme settings if table exists but is empty
        $this->autoSeedThemeSettings();

        // Share theme settings with all views
        $this->shareThemeSettings();
    }

    /**
     * テーマ設定を自動的にシード
     */
    protected function autoSeedThemeSettings(): void
    {
        // コンソールコマンド実行時のみ、かつマイグレーション後にチェック
        if ($this->app->runningInConsole()) {
            // データベース接続を試みる
            try {
                // テーブルが存在し、かつデータが存在しない場合のみシード
                if (DB::getSchemaBuilder()->hasTable('thm_dixlase_onepage_settings')) {
                    if (! DB::table('thm_dixlase_onepage_settings')->exists()) {
                        Artisan::call('db:seed', [
                            '--class' => 'Themes\\DixlaseOnePage\\Database\\Seeders\\DixlaseOnePageSettingsSeeder',
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // マイグレーション前など、テーブルがまだ存在しない場合は無視
            }
        }
    }

    /**
     * テーマ設定を全ビューに共有
     */
    protected function shareThemeSettings(): void
    {
        // View Composerを使用して、テーマのすべてのビューにテーマ設定を渡す
        // '*' はすべてのビュー（フロントエンドページ含む）
        View::composer('*', function ($view) {
            try {
                // ThemeSettingモデルで全設定を取得
                $themeSettings = ThemeSetting::getAllAsObject();

                // Merge defaults for any key the DB does not yet carry.
                // Critical for the update path: when a new theme release
                // introduces a setting field (e.g. hero_gradient_color_dark
                // in v0.1.2), existing installs have no corresponding
                // DB row, so `getAllAsObject()` returns a stdClass
                // missing that property. Accessing it in a Blade view
                // via `->prop ?: fallback` then triggers a PHP 8
                // "Undefined property" warning that production error
                // handling escalates to a 500. Merging defaults here
                // means the update flow does not require re-running
                // the seeder against an already-populated table.
                // property_exists() (not isset()) so an explicitly
                // saved null value is not clobbered by the default.
                $defaults = $this->getDefaultThemeSettings();
                foreach ((array) $defaults as $key => $value) {
                    if (! property_exists($themeSettings, $key)) {
                        $themeSettings->{$key} = $value;
                    }
                }

                // メディアオブジェクトとパスを取得して追加
                $this->loadMediaForThemeSettings($themeSettings);

                $view->with('themeSettings', $themeSettings);

                // メニュープロバイダーを解決
                $menuProvider = $this->resolveMenuProvider();

                // ヘッダーナビゲーションメニューを取得
                $headerMenuId = $themeSettings->header_menu_id ?? null;
                $headerMenuDTO = $this->getMenuDTO($menuProvider, $headerMenuId);
                $view->with('navigationItems', $headerMenuDTO ? array_map(fn ($item) => $this->mapMenuItem($item), $headerMenuDTO->items) : []);
                $view->with('navigationMenuName', $headerMenuDTO?->name);

                // フッターメニューアイテムを取得
                $footerMenuDTO = $this->getMenuDTO($menuProvider, $themeSettings->footer_menu_id ?? null);
                $view->with('footerMenuItems', $footerMenuDTO ? array_map(fn ($item) => $this->mapMenuItem($item), $footerMenuDTO->items) : []);
            } catch (\Exception $e) {
                // エラー時はデフォルト値を使用
                $view->with('themeSettings', $this->getDefaultThemeSettings());
                $view->with('navigationItems', []);
                $view->with('navigationMenuName', null);
                $view->with('footerMenuItems', []);
            }
        });
    }

    /**
     * メニュープロバイダーを解決
     */
    protected function resolveMenuProvider(): ?\App\Contracts\PluginIntegration\MenuProviderInterface
    {
        try {
            $resolver = app(\App\Services\Plugin\PluginServiceResolver::class);
            $result = $resolver->resolve(\App\Contracts\PluginIntegration\MenuProviderInterface::class, 'dixlase-menus');

            if ($result->resolved && $result->instance) {
                return $result->instance;
            }
        } catch (\Exception $e) {
            // プラグイン未インストール時は無視
        }

        return null;
    }

    /**
     * 指定メニューIDからMenuDTOを取得
     */
    protected function getMenuDTO(?\App\Contracts\PluginIntegration\MenuProviderInterface $menuProvider, int|string|null $menuId): ?\App\DTO\PluginIntegration\MenuDTO
    {
        if (! $menuProvider || empty($menuId)) {
            return null;
        }

        return $menuProvider->getMenu($menuId);
    }

    /**
     * MenuItemDTOをビュー用配列に再帰的に変換
     *
     * @return array{label: string, url: string, target: string, source_type: string|null, icon_class: string|null, css_class: string|null, children: array}
     */
    protected function mapMenuItem(\App\DTO\PluginIntegration\MenuItemDTO $item): array
    {
        return [
            'label' => $item->label,
            'url' => $item->url,
            'target' => $item->target,
            'source_type' => $item->sourceType,
            'icon_class' => $item->iconClass,
            'css_class' => $item->cssClass,
            'children' => array_map(fn ($child) => $this->mapMenuItem($child), $item->children),
        ];
    }

    /**
     * デフォルトのテーマ設定を取得
     */
    protected function getDefaultThemeSettings(): object
    {
        return (object) [
            'header_logo_id' => null,
            'header_logo_dark_id' => null,
            'favicon_id' => null,
            'hero_background_image_id' => null,
            'hero_foreground_image_id' => null,
            'hero_main_title' => 'Welcome to '.config('app.name', 'Dixlase'),
            'hero_sub_title' => 'Modern CMS Platform for Building Amazing Websites',
            'hero_button_text' => 'Get Started',
            'hero_button_link' => '#',
            'hero_button_target' => '_self',
            'hero_button_secondary_text' => 'Learn More',
            'hero_button_secondary_link' => '#features',
            'hero_button_secondary_target' => '_self',
            // Hero background-fill controls. Renders only when the hero has
            // neither a background image nor a background video (image /
            // video win). Three knobs:
            //   mode  'primary'|'custom' — color source
            //     'primary': color1 = --color-primary; color2 auto-picked to
            //                match the page body bg (light: #f3f4f6, dark:
            //                #030712) so the hero fills blend seamlessly.
            //     'custom' : color1 = hero_gradient_color; color2 =
            //                hero_gradient_color_2 (only used when
            //                shape != 'solid').
            //   shape 'radial'|'linear-vertical'|'solid'
            //     'radial'          — classic centred circular gradient.
            //     'linear-vertical' — top→bottom linear gradient.
            //     'solid'           — 単色 (color1 only, no gradient).
            //   color / color_2 — hex, used when mode='custom'.
            //
            // Legacy value 'none' from the pre-restructure era is migrated
            // by the front resolver to shape='solid' + mode='primary'
            // (see partials/hero.blade.php).
            'hero_gradient_mode' => 'primary',
            // Light-mode colors (default for both modes; dark can override).
            'hero_gradient_color' => '#3b82f6',
            'hero_gradient_color_2' => '#ffffff',
            // Dark-mode overrides. Blank / null → fall back to the light
            // value at resolve time (backward-compatible for sites that
            // saved custom colors before per-mode split landed).
            'hero_gradient_color_dark' => null,
            'hero_gradient_color_2_dark' => null,
            'hero_gradient_shape' => 'radial',
            'footer_links' => '[]',
            // The footer render strips any leading `© YYYY ` prefix and
            // prepends the current year, so the persisted suffix does
            // not carry a year of its own. `and Dixlase contributors`
            // matches the Dixlase-brand attribution pattern used on
            // admin surfaces.
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
            // Whether the front end offers a control to change the mode above.
            // Off by default so an existing site keeps the fixed appearance
            // the operator already chose.
            'appearance_toggle_enabled' => '0',
            // Where the switcher appears while the toggle above is on. The
            // two placements are independent; the footer one defaults to on
            // so an existing site renders exactly as before.
            'appearance_toggle_footer' => '1',
            'appearance_toggle_float' => '0',
            'heading_font_family' => 'noto-sans-jp', // cormorant | jost | noto-sans-jp | noto-serif-jp
            // Per-region apply toggles. When '0', that region inherits the body
            // font (system stack); when '1', it uses --font-heading.
            'heading_font_apply_header' => '1',
            'heading_font_apply_hero' => '1',
            'heading_font_apply_footer' => '1',
            'heading_font_apply_content' => '1',
            // Per-region tracking (letter-spacing) in em units. Stored as
            // a decimal string; the layout only emits the CSS variable
            // when non-zero to keep the inline <style> small.
            'heading_font_tracking_header' => '0',
            'heading_font_tracking_hero' => '0',
            'heading_font_tracking_footer' => '0',
            'heading_font_tracking_content' => '0',
            'default_locale' => 'auto', // primary-locale for theme settings; 'auto' = site default
            'multilingual_switcher_enabled' => '0',
            // メディア関連のプロパティ（loadMediaForThemeSettings()で設定されるが、デフォルトでも必要）
            'headerLogo' => null,
            'headerLogoPath' => null,
            'headerLogoDark' => null,
            'headerLogoDarkPath' => null,
            'favicon' => null,
            'faviconPath' => null,
            'heroBackground' => null,
            'heroBackgroundPath' => null,
            'heroForeground' => null,
            'heroForegroundPath' => null,
        ];
    }

    /**
     * テーマ設定にメディアオブジェクトとパスを追加
     */
    protected function loadMediaForThemeSettings(object $themeSettings): void
    {
        $mediaPath = config('admin.mediaPath', 'media');

        // ヘッダーロゴ
        if (! empty($themeSettings->header_logo_id)) {
            $headerLogo = \App\Models\Media::find($themeSettings->header_logo_id);
            $themeSettings->headerLogo = $headerLogo;
            $themeSettings->headerLogoPath = $headerLogo ? $mediaPath.'/'.$headerLogo->path : null;
        } else {
            $themeSettings->headerLogo = null;
            $themeSettings->headerLogoPath = null;
        }

        // ヘッダーロゴ（ダークモード用）— 未指定なら null。テーマ側は
        // headerLogoDarkPath があれば dark:block で切り替え、無ければ
        // fallback として単色 SVG に自動 invert を当てる。
        if (! empty($themeSettings->header_logo_dark_id)) {
            $headerLogoDark = \App\Models\Media::find($themeSettings->header_logo_dark_id);
            $themeSettings->headerLogoDark = $headerLogoDark;
            $themeSettings->headerLogoDarkPath = $headerLogoDark ? $mediaPath.'/'.$headerLogoDark->path : null;
        } else {
            $themeSettings->headerLogoDark = null;
            $themeSettings->headerLogoDarkPath = null;
        }

        // ファビコン
        if (! empty($themeSettings->favicon_id)) {
            $favicon = \App\Models\Media::find($themeSettings->favicon_id);
            $themeSettings->favicon = $favicon;
            $themeSettings->faviconPath = $favicon ? $mediaPath.'/'.$favicon->path : null;
        } else {
            $themeSettings->favicon = null;
            $themeSettings->faviconPath = null;
        }

        // ヒーロー背景画像
        if (! empty($themeSettings->hero_background_image_id)) {
            $heroBackground = \App\Models\Media::find($themeSettings->hero_background_image_id);
            $themeSettings->heroBackground = $heroBackground;
            $themeSettings->heroBackgroundPath = $heroBackground ? $mediaPath.'/'.$heroBackground->path : null;
        } else {
            $themeSettings->heroBackground = null;
            $themeSettings->heroBackgroundPath = null;
        }

        // ヒーロー前景画像（テキスト/ボタンの下に配置するイメージ）
        if (! empty($themeSettings->hero_foreground_image_id)) {
            $heroForeground = \App\Models\Media::find($themeSettings->hero_foreground_image_id);
            $themeSettings->heroForeground = $heroForeground;
            $themeSettings->heroForegroundPath = $heroForeground ? $mediaPath.'/'.$heroForeground->path : null;
        } else {
            $themeSettings->heroForeground = null;
            $themeSettings->heroForegroundPath = null;
        }

        // ヒーロー背景動画
        if (! empty($themeSettings->hero_background_video_id)) {
            $heroVideo = \App\Models\Media::find($themeSettings->hero_background_video_id);
            $themeSettings->heroBackgroundVideo = $heroVideo;
            $themeSettings->heroBackgroundVideoPath = $heroVideo ? $mediaPath.'/'.$heroVideo->path : null;
        } else {
            $themeSettings->heroBackgroundVideo = null;
            $themeSettings->heroBackgroundVideoPath = null;
        }

        // SNSリンクのURL生成
        $themeSettings->snsLinks = $this->generateSnsLinks($themeSettings);
    }

    /**
     * SNSリンクのURLを生成
     * アカウント名やIDから完全なURLを生成する
     */
    protected function generateSnsLinks(object $themeSettings): array
    {
        return [
            'instagram' => $this->generateSnsUrl('instagram', $themeSettings->footer_sns_instagram ?? null),
            'x' => $this->generateSnsUrl('x', $themeSettings->footer_sns_x ?? null),
            'facebook' => $this->generateSnsUrl('facebook', $themeSettings->footer_sns_facebook ?? null),
            'tiktok' => $this->generateSnsUrl('tiktok', $themeSettings->footer_sns_tiktok ?? null),
            'bluesky' => $this->generateSnsUrl('bluesky', $themeSettings->footer_sns_bluesky ?? null),
            'threads' => $this->generateSnsUrl('threads', $themeSettings->footer_sns_threads ?? null),
            'linkedin' => $this->generateSnsUrl('linkedin', $themeSettings->footer_sns_linkedin ?? null),
            'youtube' => $this->generateSnsUrl('youtube', $themeSettings->footer_sns_youtube ?? null),
            'pinterest' => $this->generateSnsUrl('pinterest', $themeSettings->footer_sns_pinterest ?? null),
            'discord' => $this->generateSnsUrl('discord', $themeSettings->footer_sns_discord ?? null),
            'github' => $this->generateSnsUrl('github', $themeSettings->footer_sns_github ?? null),
        ];
    }

    /**
     * Build the full URL for one SNS platform (null when the value is not safe to link)
     */
    protected function generateSnsUrl(string $platform, ?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Return an already-complete URL as-is, but only when it is one the
        // browser will navigate to rather than execute.
        //
        // FILTER_VALIDATE_URL alone is not that check: it accepts
        // `javascript://%0aalert(1)` (verified on PHP 8.3). Since the result
        // goes straight into `href="{{ $snsLinks[...] }}"` in the footer of
        // every page, that early return handed an executable link to every
        // visitor. Scheme is read after stripping control characters, which
        // browsers ignore inside a scheme.
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $scheme = strtolower((string) parse_url(
                preg_replace('/[\x00-\x20]/', '', $value) ?? '',
                PHP_URL_SCHEME
            ));

            return in_array($scheme, ['http', 'https'], true) ? $value : null;
        }

        // Build the URL for each platform from a handle or ID.
        return match ($platform) {
            'instagram' => 'https://www.instagram.com/'.ltrim($value, '@').'/',
            'x' => 'https://twitter.com/'.ltrim($value, '@'),
            'facebook' => 'https://www.facebook.com/'.ltrim($value, '@'),
            'tiktok' => 'https://www.tiktok.com/@'.ltrim($value, '@'),
            'bluesky' => 'https://bsky.app/profile/'.ltrim($value, '@'),
            'threads' => 'https://www.threads.net/@'.ltrim($value, '@'),
            'linkedin' => str_starts_with($value, 'company/')
                ? 'https://www.linkedin.com/'.$value
                : 'https://www.linkedin.com/in/'.$value,
            'youtube' => str_starts_with($value, '@')
                ? 'https://www.youtube.com/'.$value
                : 'https://www.youtube.com/@'.$value,
            'pinterest' => 'https://www.pinterest.com/'.ltrim($value, '@').'/',
            // A Discord link is an invite. A full URL was handled above (and
            // only when it is http/https); anything else must be a bare invite
            // code. Returning the value unchanged let `javascript:alert(1)` --
            // which FILTER_VALIDATE_URL rejects, so it never reached the scheme
            // check -- into the footer of every page.
            'discord' => preg_match('/\A[A-Za-z0-9-]{2,64}\z/', $value) === 1
                ? 'https://discord.gg/'.$value
                : null,
            'github' => 'https://github.com/'.ltrim($value, '@'),
            // Unknown platform: never echo a raw value into an href.
            default => null,
        };
    }
}
