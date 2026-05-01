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
>
<head>
    {{-- FOUC防止: ダークモードクラスを即時適用（Alpine.js不要） --}}
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

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Dixlase') }} @yield('title')</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    {{-- Font Awesome（フッターSNSアイコン等で使用） --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- CSSのみ読み込み（JSバンドルは一切含めない） --}}
    @if(app()->environment('local') && file_exists(public_path('hot')))
        @vite([
            'themes/DixlaseOnePage/resources/src/front/css/tailwind.css',
            'resources/src/front/scss/style.scss',
            'themes/DixlaseOnePage/resources/src/front/scss/style.scss'
        ])
    @else
        {!! load_front_assets(
            ['scss/style.scss'],
            ['css/tailwind.css', 'scss/style.scss']
        ) !!}
    @endif

    {{-- Alpine.js x-cloak: Alpine未読込のため常時非表示を維持 --}}
    {{-- ヘッダー位置: 管理バーがないため top-0 に固定 --}}
    <style @cspNonce>
        [x-cloak] { display: none !important; }
        header.fixed { top: 0 !important; }
        /* プレビュー専用: 全インタラクティブ要素を無効化 */
        a, button, [role="button"],
        input, textarea, select {
            pointer-events: none !important;
            cursor: default !important;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-col min-h-screen">
    @include('themes::partials.header')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('themes::partials.footer')

    @stack('scripts')
</body>
</html>
