# Dixlase OnePage - Dixlase CMS テーマ

> **種別**: Dixlase テーマ | **バージョン**: 1.0.0
> **名前空間**: `Themes\DixlaseOnePage`

## テーマ概要

Dixlase OnePage テーマ。プラグイン連動型のシングルページ/LP用テーマ。

## アーキテクチャ

**Dixlase CMS** (Laravel 12) のテーマ。コアアプリケーションはこのテーマディレクトリから `../../` に配置。

### 主要パス
- **コアルート**: `../../` （`vendor/`, `artisan`, コア `app/` を含む）
- **このテーマ**: `themes/DixlaseOnePage/`
- **Artisan コマンド**: `docker exec -i dixlase-laravel.test-1 php artisan <command>`

### テーマ対応機能
- dark_mode
- responsive
- front_page_builder
- custom_colors

## 開発ルール

### 基本方針
- **テーマ内にビジネスロジックを含めない** — 表示のみ、ビュー内で Eloquent クエリ不可
- **ビュー名前空間**: テーマビューは全て `themes::`
- **翻訳名前空間**: テーマ翻訳は全て `themes::`
- **テーブル接頭辞**: テーマDBテーブルは `thm_`
- **ダークモード必須**: 全コンポーネントで `dark:` Tailwind バリアントをサポート
- **レスポンシブ必須**: モバイルファースト、全ブレークポイントでテスト
- **プラグイン対応**: プラグインと連携するが、なくても正常に動作すること

### PHP 標準
- PHP 8.3, Laravel 12, Livewire 4
- コンストラクタプロパティプロモーションを使用
- 全メソッドに明示的な戻り値型を宣言
- インラインコメントよりPHPDocブロックを優先
- `env()` は直接使わず `config()` を使用

### フロントエンドスタック
- **Tailwind CSS 3**: ユーティリティファースト、スペーシングは `gap-*`、ダークモードは `dark:`
- **Alpine.js 3**: `x-data`, `@click`, `x-show`, `x-transition`, `x-cloak`
- **Vite**: 開発は `npm run dev`、本番は `npm run build`

### Blade コンポーネント（コア提供）
利用可能: `x-ui-maintenance-banner`, `x-ui-admin-bar`, `x-form-text`,
`x-form-textarea`, `x-front.button`, `components.media-picker`, `components.save`

### 翻訳
- 翻訳ファイルは `en/` と `ja/` の両方を必ず用意する

### アセットバンドル
- 開発時はテーマディレクトリで `npm run dev` を実行
- アセット変更のコミット前に `npm run build` を実行

### テスト
- テスト実行: `docker exec -i dixlase-laravel.test-1 php artisan test`

### コードフォーマット
- Pint はフック経由で編集後に自動実行


## MCP ツール (Laravel Boost)
- `search-docs`: Laravel/Tailwind/Livewire ドキュメント検索
- `tinker`: デバッグ
- `database-query`: 読み取り専用データベースクエリ