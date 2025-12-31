<?php

/**
 * This file is part of Dixlase.
 *
 * Copyright (C) 2025 exc-D inc.
 * https://exc-d.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Themes\DixlaseDefaultTheme\App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Themes\DixlaseDefaultTheme\App\Models\ThemeSetting;
use App\Helpers\AdminHelper;

class DixlaseDefaultThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge admin navigation config
        AdminHelper::mergeAdminNavigation(
            'DixlaseDefaultTheme',
            __DIR__ . '/../../config/admin.php'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/admin.php');
        
        // Load views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'themes');
        
        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'themes');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        
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
                if (DB::getSchemaBuilder()->hasTable('thm_dixlase_default_theme_settings')) {
                    if (!DB::table('thm_dixlase_default_theme_settings')->exists()) {
                        Artisan::call('db:seed', [
                            '--class' => 'Themes\\DixlaseDefaultTheme\\Database\\Seeders\\DixlaseDefaultThemeSettingsSeeder'
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
                if (!isset($themeSettings->hero_main_title)) {
                    $themeSettings = $this->getDefaultThemeSettings();
                }
                
                // メディアオブジェクトとパスを取得して追加
                $this->loadMediaForThemeSettings($themeSettings);
                
                $view->with('themeSettings', $themeSettings);
                
                // ナビゲーションアイテムを取得（メニュープラグインから）
                $navigationItems = $this->getNavigationItems();
                $view->with('navigationItems', $navigationItems);
            } catch (\Exception $e) {
                // エラー時はデフォルト値を使用
                $view->with('themeSettings', $this->getDefaultThemeSettings());
                $view->with('navigationItems', []);
            }
        });
    }
    
    /**
     * ナビゲーションアイテムを取得
     * メニュープラグインが有効な場合はそこから取得、なければデフォルト
     * 
     * @return array
     */
    protected function getNavigationItems(): array
    {
        // メニュープラグインのヘルパー関数が存在するか確認
        if (function_exists('dls_menu_items')) {
            // ヘッダーロケーションまたはデフォルトメニューからアイテムを取得
            $items = dls_menu_items(true);
            
            if (!empty($items)) {
                return $items;
            }
        }
        
        // メニュープラグインがない場合やメニューが空の場合はデフォルト
        return $this->getDefaultNavigationItems();
    }
    
    /**
     * デフォルトのナビゲーションアイテムを取得
     * 
     * @return array
     */
    protected function getDefaultNavigationItems(): array
    {
        return [
            ['label' => 'Home', 'url' => url('/')],
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
            'hero_main_title' => 'Welcome to ' . config('app.name', 'Dixlase'),
            'hero_sub_title' => 'Modern CMS Platform for Building Amazing Websites',
            'hero_button_text' => 'Get Started',
            'hero_button_link' => '#',
            'hero_button_secondary_text' => 'Learn More',
            'hero_button_secondary_link' => '#features',
            'footer_description' => 'Powered by Dixlase CMS',
            'footer_links' => '[]',
            'footer_copyright' => '© ' . date('Y') . ' ' . config('app.name', 'Dixlase') . '. All rights reserved.',
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
     * 
     * @param object $themeSettings
     * @return void
     */
    protected function loadMediaForThemeSettings(object $themeSettings): void
    {
        $mediaPath = config('admin.mediaPath', 'media');
        
        // ヘッダーロゴ
        if (!empty($themeSettings->header_logo_id)) {
            $headerLogo = \App\Models\Media::find($themeSettings->header_logo_id);
            $themeSettings->headerLogo = $headerLogo;
            $themeSettings->headerLogoPath = $headerLogo ? $mediaPath . '/' . $headerLogo->path : null;
        } else {
            $themeSettings->headerLogo = null;
            $themeSettings->headerLogoPath = null;
        }
        
        // ファビコン
        if (!empty($themeSettings->favicon_id)) {
            $favicon = \App\Models\Media::find($themeSettings->favicon_id);
            $themeSettings->favicon = $favicon;
            $themeSettings->faviconPath = $favicon ? $mediaPath . '/' . $favicon->path : null;
        } else {
            $themeSettings->favicon = null;
            $themeSettings->faviconPath = null;
        }
        
        // ヒーロー背景画像
        if (!empty($themeSettings->hero_background_image_id)) {
            $heroBackground = \App\Models\Media::find($themeSettings->hero_background_image_id);
            $themeSettings->heroBackground = $heroBackground;
            $themeSettings->heroBackgroundPath = $heroBackground ? $mediaPath . '/' . $heroBackground->path : null;
        } else {
            $themeSettings->heroBackground = null;
            $themeSettings->heroBackgroundPath = null;
        }
        
        // SNSリンクのURL生成
        $themeSettings->snsLinks = $this->generateSnsLinks($themeSettings);
    }
    
    /**
     * SNSリンクのURLを生成
     * アカウント名やIDから完全なURLを生成する
     * 
     * @param object $themeSettings
     * @return array
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
     * 
     * @param string $platform
     * @param string|null $value
     * @return string|null
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
        return match($platform) {
            'instagram' => 'https://www.instagram.com/' . ltrim($value, '@') . '/',
            'x' => 'https://twitter.com/' . ltrim($value, '@'),
            'facebook' => 'https://www.facebook.com/' . ltrim($value, '@'),
            'tiktok' => 'https://www.tiktok.com/@' . ltrim($value, '@'),
            'bluesky' => 'https://bsky.app/profile/' . ltrim($value, '@'),
            'threads' => 'https://www.threads.net/@' . ltrim($value, '@'),
            'linkedin' => str_starts_with($value, 'company/') 
                ? 'https://www.linkedin.com/' . $value 
                : 'https://www.linkedin.com/in/' . $value,
            'youtube' => str_starts_with($value, '@') 
                ? 'https://www.youtube.com/' . $value 
                : 'https://www.youtube.com/@' . $value,
            'pinterest' => 'https://www.pinterest.com/' . ltrim($value, '@') . '/',
            'discord' => $value, // Discordは招待リンクなのでそのまま
            default => $value,
        };
    }
}
