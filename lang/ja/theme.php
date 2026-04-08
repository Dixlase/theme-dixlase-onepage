<?php

/**
 * This file is part of Dixlase OnePage.
 *
 * Copyright (C) 2026 exc-D inc.
 * https://exc-d.com
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

return [
    /*
    |--------------------------------------------------------------------------
    | Dixlase Default Theme Language Lines (Japanese)
    |--------------------------------------------------------------------------
    */

    'name' => 'Dixlase デフォルトテーマ',
    'description' => 'シンプルでモダンなデザインのデフォルトテーマ',

    // Navigation
    'navigation' => [
        'home' => 'ホーム',
        'about' => '私たちについて',
        'services' => 'サービス',
        'contact' => 'お問い合わせ',
        'toggle_menu' => 'メニューを開く',
    ],

    // Footer
    'footer' => [
        'about_title' => '私たちについて',
        'links' => 'リンク',
        'contact' => 'お問い合わせ',
        'copyright' => '© :year :name. All rights reserved.',
        'powered_by' => 'Powered by Dixlase',
    ],
    
    // Admin Settings
    'admin' => [
        'settings' => [
            'title' => 'テーマ設定',
            'updated_successfully' => 'テーマ設定が更新されました',
            
            // Header Section
            'header' => [
                'title' => 'ヘッダー設定',
                'logo_url' => 'ロゴURL',
                'logo_url_help' => 'ロゴ画像のURL（空欄の場合はテキストロゴを表示）',
                'logo_text' => 'ロゴテキスト',
                'logo_text_help' => 'サイト名として表示されるテキスト',
            ],
            
            // Hero Section
            'hero' => [
                'title' => 'ヒーローセクション設定',
                'background_image' => '背景画像URL',
                'background_image_help' => '全画面ヒーローエリアの背景画像（空欄の場合はグラデーション表示）',
                'main_title' => 'メインタイトル',
                'main_title_help' => 'ヒーローエリアの大見出し',
                'sub_title' => 'サブタイトル',
                'sub_title_help' => 'ヒーローエリアの説明文',
                'button_text' => 'メインボタンテキスト',
                'button_link' => 'メインボタンリンク',
                'button_secondary_text' => 'サブボタンテキスト',
                'button_secondary_link' => 'サブボタンリンク',
            ],
            
            // Footer Section
            'footer' => [
                'title' => 'フッター設定',
                'description' => 'フッター説明文',
                'description_help' => 'フッターに表示される説明文',
                'links' => 'フッターリンク',
                'links_help' => 'フッターに表示するリンク一覧',
                'link_title' => 'リンクタイトル',
                'link_url' => 'リンクURL',
                'add_link' => 'リンクを追加',
                'remove_link' => 'リンクを削除',
                'copyright' => 'コピーライト',
                'copyright_help' => 'フッターに表示するコピーライト文',
                'sns_title' => 'SNSリンク',
                'sns_facebook' => 'Facebook URL',
                'sns_twitter' => 'Twitter URL',
                'sns_instagram' => 'Instagram URL',
                'sns_linkedin' => 'LinkedIn URL',
                'sns_youtube' => 'YouTube URL',
            ],
            
            // Colors Section
            'colors' => [
                'title' => 'カラー設定',
                'primary' => 'プライマリーカラー',
                'secondary' => 'セカンダリーカラー',
                'accent' => 'アクセントカラー',
            ],
        ],
    ],

    // Buttons
    'buttons' => [
        'learn_more' => '詳しく見る',
        'get_started' => '始める',
        'contact_us' => 'お問い合わせ',
        'back_to_home' => 'ホームに戻る',
        'back_to_previous' => '前のページに戻る',
    ],

    // 404 Page
    '404' => [
        'title' => 'ページが見つかりません',
        'message' => 'お探しのページは存在しないか、移動した可能性があります。',
        'back_home' => 'ホームに戻る',
        'back_previous' => '前のページに戻る',
    ],

    // Front Page
    'frontpage' => [
        'welcome' => 'Welcome to :name',
        'description' => 'フロントページビルダーでこのページをカスタマイズできます',
        'go_to_admin' => '管理画面へ',
    ],
];
