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
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // ビュー・翻訳・ルートはコアのThemeServiceProviderが読み込み済み
        // マイグレーションのみテーマ側で登録
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

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

                // 設定が空の場合はデフォルト値を使用
                if (! isset($themeSettings->hero_main_title)) {
                    $themeSettings = $this->getDefaultThemeSettings();
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
            'favicon_id' => null,
            'hero_background_image_id' => null,
            'hero_main_title' => 'Welcome to '.config('app.name', 'Dixlase'),
            'hero_sub_title' => 'Modern CMS Platform for Building Amazing Websites',
            'hero_button_text' => 'Get Started',
            'hero_button_link' => '#',
            'hero_button_secondary_text' => 'Learn More',
            'hero_button_secondary_link' => '#features',
            'footer_links' => '[]',
            'footer_copyright' => '© '.date('Y').' '.config('app.name', 'Dixlase').'. All rights reserved.',
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
            'appearance_mode' => '0', // 0: Auto, 1: Light, 2: Dark
            'multilingual_switcher_enabled' => '0',
            // メディア関連のプロパティ（loadMediaForThemeSettings()で設定されるが、デフォルトでも必要）
            'headerLogo' => null,
            'headerLogoPath' => null,
            'favicon' => null,
            'faviconPath' => null,
            'heroBackground' => null,
            'heroBackgroundPath' => null,
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
        ];
    }

    /**
     * 各SNSの完全なURLを生成
     */
    protected function generateSnsUrl(string $platform, ?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // 既に完全なURLの場合はそのまま返す
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // プラットフォームごとのURL生成
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
            'discord' => $value, // Discordは招待リンクなのでそのまま
            default => $value,
        };
    }
}
