<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dixlase Default Theme Admin Language Lines (Japanese)
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'title' => 'テーマ設定',
        'updated_successfully' => 'テーマ設定が更新されました',
        'select_logo_image' => 'ロゴ画像を選択',
        'select_favicon_image' => 'ファビコンを選択',

        // Header Section
        'header' => [
            'title' => 'ヘッダー・ファビコン設定',
            'header_logo' => 'ヘッダーロゴ',
            'header_logo_help' => 'サイトヘッダーに表示するロゴ画像を設定します。',
            'favicon' => 'ファビコン',
            'favicon_help' => 'ブラウザのタブに表示されるアイコンを設定します。（推奨サイズ: 32x32px または 64x64px）',
        ],

        // Hero Section
        'hero' => [
            'title' => 'ヒーローセクション設定',
            'basic_settings' => '基本設定',
            'background_section' => '背景設定',
            'background_image' => 'ヒーロー背景画像',
            'background_image_help' => '全画面ヒーローエリアの背景画像（未設定の場合はグラデーション表示）',
            'select_background_image' => '背景画像を選択',
            'content_section' => 'コンテンツ',
            'main_title_label' => 'メインタイトル',
            'main_title' => 'メインタイトル',
            'main_title_help' => 'ヒーローエリアの大見出し',
            'sub_title_label' => 'サブタイトル',
            'sub_title' => 'サブタイトル',
            'sub_title_help' => 'ヒーローエリアの説明文',
            'primary_button' => 'プライマリーボタン',
            'button_text' => 'ボタンテキスト',
            'button_link' => 'ボタンリンク',
            'secondary_button' => 'セカンダリーボタン',
            'button_secondary_text' => 'ボタンテキスト',
            'button_secondary_link' => 'ボタンリンク',
        ],

        // Footer Section
        'footer' => [
            'title' => 'フッター設定',
            'description' => 'フッター説明文',
            'description_help' => 'フッターに表示される説明文',
            'links' => 'フッターリンク',
            'links_help' => 'フッターに表示するリンク一覧',
            'link_title' => 'タイトル',
            'link_url' => 'URL',
            'add_link' => 'リンクを追加',
            'remove_link' => 'リンクを削除',
            'copyright_section' => 'コピーライト',
            'copyright' => 'コピーライト',
            'copyright_help' => 'フッターに表示するコピーライト文',
            'sns_title' => 'SNSリンク',
            'sns_social_media' => 'ソーシャルメディア',
            'sns_professional' => 'プロフェッショナル',
            'sns_other' => 'その他',
            'sns_username' => 'ユーザー名',
            'sns_invite_code' => '招待コード',
            'sns_instagram' => 'Instagram URL',
            'sns_x' => 'X (Twitter) URL',
            'sns_facebook' => 'Facebook URL',
            'sns_tiktok' => 'TikTok URL',
            'sns_bluesky' => 'Bluesky URL',
            'sns_threads' => 'Threads URL',
            'sns_linkedin' => 'LinkedIn URL',
            'sns_youtube' => 'YouTube URL',
            'sns_pinterest' => 'Pinterest URL',
            'sns_discord' => 'Discord URL',
        ],

        // Primary Color
        'primary_color' => [
            'title' => 'プライマリカラー',
            'help' => 'ボタンやリンクに使用するアクセントカラーを選択します。',
            'blue' => 'ブルー',
            'purple' => 'パープル',
            'green' => 'グリーン',
            'red' => 'レッド',
            'orange' => 'オレンジ',
            'pink' => 'ピンク',
            'indigo' => 'インディゴ',
            'teal' => 'ティール',
            'black' => 'ブラック',
            'gray' => 'グレー',
        ],

        // Appearance Section
        'appearance' => [
            'title' => '外観モード',
            'description' => 'テーマの外観モード（ライト/ダーク）を設定します。',
            'mode_auto' => '自動',
            'mode_light' => 'ライト',
            'mode_dark' => 'ダーク',
            'mode_help' => '自動: ユーザーのシステム設定に従います / ライト: 明るいテーマ / ダーク: 暗いテーマ',
        ],

        // Plugin Integration
        'plugins' => [
            'menu' => [
                'title' => 'ヘッダーメニュー',
                'select_menu' => 'メニューを選択',
                'none' => 'メニューなし',
                'help' => 'ヘッダーナビゲーションに表示するメニューを選択します。',
            ],
            'footer_menu' => [
                'title' => 'フッターメニュー',
                'help' => 'フッターに表示するメニューを選択します。',
                'plugin_required' => 'フッターメニューを選択するには、DixlaseMenusプラグインをインストール・有効化してください。',
            ],
            'inquiry' => [
                'title' => 'お問い合わせフォーム',
                'enable' => 'フッター上部にお問い合わせフォームを表示する',
                'help' => 'フッターの上にお問い合わせフォームセクションを表示します。',
            ],
        ],

        // Header Menu
        'header_menu' => [
            'edit_badge' => 'メニューを編集',
            'confirm_title' => 'ヘッダーメニューを編集しますか？',
            'confirm_message' => 'OKでメニュー編集に移動します。<br>保存していない内容は失われます。',
            'confirm_ok' => 'OK',
        ],

        // Footer Menu
        'footer_menu' => [
            'edit_badge' => 'メニューを編集',
            'confirm_title' => 'フッターメニューを編集しますか？',
            'confirm_message' => 'OKでメニュー編集に移動します。<br>保存していない内容は失われます。',
            'confirm_ok' => 'OK',
        ],

        // Front Page Content
        'front_content' => [
            'edit_badge' => 'フロントページを編集',
            'confirm_title' => 'フロントページを編集しますか？',
            'confirm_message' => 'OKでフロントページ編集に移動します。<br>保存していない内容は失われます。',
            'confirm_ok' => 'OK',
        ],

        // Inquiry Form
        'inquiry_form' => [
            'edit_badge' => 'お問い合わせフォームを編集',
            'confirm_title' => 'お問い合わせフォームを編集しますか？',
            'confirm_message' => 'OKでお問い合わせフォーム設定に移動します。<br>保存していない内容は失われます。',
            'confirm_ok' => 'OK',
        ],

        // Editor UI
        'editor' => [
            'preview_title' => 'テーマプレビュー',
            'click_to_edit' => 'クリックで編集',
            'sidebar_open' => '設定パネルを開く',
            'sidebar_close' => '設定パネルを閉じる',
        ],
    ],
];
