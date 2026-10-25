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

return [
    '// The theme settings are passed in automatically by the ServiceProvider.' => '// テーマ設定はServiceProviderから自動的に渡される',
    '// Initial value, used before the ResizeObserver in' => '// <x-ui-front-banner-stack /> の ResizeObserver が',
    '// <x-ui-front-banner-stack /> computes --front-banner-stack-height.' => '// --front-banner-stack-height を計算する前の初期値。プラグインが',
    '// With no plugin pushing a banner, 3rem (the admin bar\'s height) is' => '// バナーを @push していない通常状態では 3rem (= 管理バー高さ) が',
    '// exact; signed out the stack is empty, so 0px. Once JS has set the' => '// ぴったり合う。ログアウト時はスタックが空なので 0px。JS が値を',
    '// value, the CSS variable wins.' => '// 設定したあとは CSS variable が優先される。',
];
