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
    '{{-- The theme\'s primary colour as a CSS variable, read from SCSS as' => '{{-- テーマプライマリカラーを CSS 変数化。SCSS 側 (var(--color-primary))',
    'var(--color-primary) for the header and footer menu hover colours.' => 'からヘッダー / フッターのメニュー hover 色などで参照する。',
    'The dark-mode hover colour is computed separately into' => 'ダークモードのホバー色は別計算で `--color-primary-dark-safe` を',
    '`--color-primary-dark-safe`:' => '算出する:',
    '1. An achromatic colour (grey / black: R≈G≈B) is unreadable on a' => '1. 無彩色 (グレー / ブラック: R≈G≈B) は明度に関わらず暗い',
    'dark hover background at any lightness, so it falls back to white.' => 'hover 背景上で読めないので白にフォールバック。',
    '2. Any other colour moves 30% of the way to white. The hue is kept' => '2. それ以外の有彩色は 30% だけ白に寄せて明度を上げる。',
    'and only the lightness rises, enough to read on a dark background. --}}' => '色相は保ったまま、ダーク背景でも視認しやすい明るさにする。 --}}',
    '// Achromatic: white' => '// 無彩色は白',
    '// 30% toward white, raising the lightness' => '// 30% 白寄せで明度を上げる',
];
