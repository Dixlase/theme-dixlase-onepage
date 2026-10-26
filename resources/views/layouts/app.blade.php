{{--
This file is part of Dixlase OnePage.

Copyright (C) 2026 exc-D inc. and Dixlase contributors
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

@php
    // The two placements of the front-end appearance switcher, resolved once
    // for the whole layout. A visitor's stored choice outranks
    // `appearance_mode` while either placement is on; with both off no
    // switcher is rendered, stored choices are ignored and every visitor
    // returns to the operator's mode.
    $appearanceFooter = (string) ($themeSettings->appearance_toggle_footer ?? '0') === '1';
    $appearanceFloat = (string) ($themeSettings->appearance_toggle_float ?? '0') === '1';
    $appearanceSwitchable = $appearanceFooter || $appearanceFloat;
@endphp
<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="scroll-smooth"
    x-data="appearanceTheme('{{ $themeSettings->appearance_mode ?? '0' }}', {{ $appearanceSwitchable ? 'true' : 'false' }})"
    :class="{ 'dark': isDark, 'light': !isDark }"
>
<head>
    @include('themes::partials.appearance-mode-script', ['honourStoredChoice' => $appearanceSwitchable])
    @include('themes::partials.head')

    @include('themes::partials.appearance-tokens')

    {{-- Font Awesome (brand icons for the footer SNS row) is bundled by
         front/scss/style.scss (@fortawesome/fontawesome-free 7.x, fonts
         emitted by the Vite build). Do not add the cdnjs 6.4.0 stylesheet
         here again: it loaded after the bundle, overrode `.fa-brands` to
         the 6.4.0 font, which lacks the X / Threads / Bluesky glyphs. --}}
</head>
<body class="bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100 flex flex-col min-h-screen">
    {{-- 管理バー / メンテナンスバナー / プラグインが @push('front-banners') で
         差し込む通知をまとめて管理バーの上に積み上げるスタック。 --}}
    <x-ui-front-banner-stack />
    {{-- 本テーマのヘッダー / モバイルドロワーは position: fixed なので、
         スタックの実高さを CSS variable に出してヘッダー側 (partials/header)
         が var(--front-banner-stack-height) で追従できるようにする。
         Core 側 (<x-ui-front-banner-stack />) は構造のみを提供し、計測は
         fixed ヘッダーを使う本テーマが受け持つ。 --}}
    <script @cspNonce>
        (function () {
            var stack = document.getElementById('front-banner-stack');
            if (!stack) return;
            var root = document.documentElement;
            var update = function () {
                root.style.setProperty('--front-banner-stack-height', (stack.offsetHeight || 0) + 'px');
            };
            update();
            new ResizeObserver(update).observe(stack);
            window.addEventListener('resize', update);
        })();
    </script>

    @include('themes::partials.header')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('themes::partials.footer')

    {{-- Appearance switcher, fixed in the corner.

         Included here rather than from partials/footer.blade.php: a `custom/`
         override replaces a view wholesale, so a site that has forked the
         footer would never receive a control added inside it. The layout is
         also what owns the `appearanceTheme` data this partial drives.

         The two placements are independent of each other; `$appearanceFloat`
         is resolved at the top of this file alongside the footer one. --}}
    @if ($appearanceFloat)
        @include('themes::partials.appearance-float')
    @endif

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
