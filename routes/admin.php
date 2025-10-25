<?php

/**
 * This file is part of Dixlase.
 *
 * Copyright (C) 2025 exc-D inc.
 * Website: https://exc-d.com
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

use Illuminate\Support\Facades\Route;
use Themes\DixlaseDefaultTheme\App\Http\Controllers\Admin\Settings\AdminThemeSettingsController;

/*
|--------------------------------------------------------------------------
| テーマ管理画面ルート（自動読み込み）
|--------------------------------------------------------------------------
|
| このファイルはテーマが有効化されている場合、ThemeServiceProviderによって
| 自動的に読み込まれます。以下のミドルウェアが自動適用されます：
|
| - admin.ip: IPアドレスフィルタリング
| - auth:member: 管理メンバー認証
| - verified: メール認証済みチェック
| - log.admin.activity: 管理画面操作ログ
|
| ルートプレフィックス: /admin（動的に取得）
| ルート名プレフィックス: admin.
|
*/

// テーマ設定
Route::prefix('settings/themes')
    ->name('settings.themes.')
    ->group(function () {
        // 表示は閲覧権限のみ
        Route::get('/settings', [AdminThemeSettingsController::class, 'settings'])
            ->middleware('check.menu.access:settings.themes.settings')
            ->name('settings');
        
        // 更新は編集権限が必要
        Route::put('/settings', [AdminThemeSettingsController::class, 'update'])
            ->middleware(['check.menu.access:settings.themes.settings', 'check.menu.edit:settings.themes.settings'])
            ->name('settings.update');
    });
