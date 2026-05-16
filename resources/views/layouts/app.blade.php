{{--
This file is part of Dixlase OnePage.

Copyright (C) 2026 exc-D inc.
https://exc-d.com

Dixlase OnePage is dual-licensed. You may use this file under either:

  (a) the GNU General Public License version 3 or later, as published
      by the Free Software Foundation; or

  (b) a commercial license agreement obtained from exc-D inc.

Unless you have entered into a commercial license agreement, this
file is governed by the GPL terms below.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <https://www.gnu.org/licenses/>.
--}}

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="scroll-smooth"
    x-data="appearanceTheme('{{ $themeSettings->appearance_mode ?? '0' }}')"
    x-init="init()"
    :class="{ 'dark': isDark, 'light': !isDark }"
>
<head>
    {{-- FOUC防止: Alpine.js初期化前にダークモードクラスを即時適用 --}}
    <script @cspNonce>
    (function(){
        var m = '{{ $themeSettings->appearance_mode ?? '0' }}';
        if (m === '2' || (m === '0' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        } else {
            document.documentElement.classList.add('light');
            document.documentElement.classList.remove('dark');
        }
    })();
    </script>
    @include('themes::partials.head')

    {{-- テーマプライマリカラーを CSS 変数化。SCSS 側 (var(--color-primary))
         からヘッダー / フッターのメニュー hover 色などで参照する。
         ダークモードのホバー色は別計算で `--color-primary-dark-safe` を
         算出する:
           1. 無彩色 (グレー / ブラック: R≈G≈B) は明度に関わらず暗い
              hover 背景上で読めないので白にフォールバック。
           2. それ以外の有彩色は 30% だけ白に寄せて明度を上げる。
              色相は保ったまま、ダーク背景でも視認しやすい明るさにする。 --}}
    @php
        $primaryColor = $themeSettings->primary_color ?? '#3b82f6';
        $pcHex = ltrim($primaryColor, '#');
        if (strlen($pcHex) === 6) {
            $pcR = hexdec(substr($pcHex, 0, 2));
            $pcG = hexdec(substr($pcHex, 2, 2));
            $pcB = hexdec(substr($pcHex, 4, 2));
            if (max($pcR, $pcG, $pcB) - min($pcR, $pcG, $pcB) < 30) {
                // 無彩色は白
                $primaryColorDarkSafe = '#ffffff';
            } else {
                // 30% 白寄せで明度を上げる
                $primaryColorDarkSafe = sprintf('#%02x%02x%02x',
                    (int) round($pcR + (255 - $pcR) * 0.30),
                    (int) round($pcG + (255 - $pcG) * 0.30),
                    (int) round($pcB + (255 - $pcB) * 0.30)
                );
            }
        } else {
            $primaryColorDarkSafe = $primaryColor;
        }
    @endphp
    <style @cspNonce>
        :root {
            --color-primary: {{ $primaryColor }};
            --color-primary-dark-safe: {{ $primaryColorDarkSafe }};
        }
    </style>

    {{-- Font Awesome for SNS Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100 flex flex-col min-h-screen">
    {{-- 管理バー・メンテナンスバナー（管理者ログイン時のみ表示） --}}
    <div class="sticky top-0 z-[9999]">
        {{-- メンテナンスバナー（メンテナンス中のみ表示） --}}
        <x-ui-maintenance-banner />
        {{-- 管理バー --}}
        <x-ui-admin-bar />
    </div>
    
    @include('themes::partials.header')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('themes::partials.footer')

    {{-- Scripts --}}
    @if(app()->environment('local') && file_exists(public_path('hot')))
        @vite([
            'themes/DixlaseOnePage/resources/src/front/js/app.js',
            'resources/src/common/js/app.js'
        ])
    @else
        {{-- テーマJS（Alpine.start()前にappearanceTheme等を定義する必要がある） --}}
        {!! load_front_assets([], ['js/app.js']) !!}
        {{-- コア共通JS（Alpine.js + Alpine.start()） --}}
        {!! load_core_assets(['js/app.js'], 'common') !!}
    @endif

    {{-- プラグインJS（alpine:initイベントで自動登録） --}}
    @stack('scripts')

</body>
</html>
