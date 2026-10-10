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

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="scroll-smooth"
    x-data="appearanceTheme('{{ $themeSettings->appearance_mode ?? '0' }}', {{ (string) ($themeSettings->appearance_toggle_enabled ?? '0') === '1' ? 'true' : 'false' }})"
    :class="{ 'dark': isDark, 'light': !isDark }"
>
<head>
    {{-- Apply the dark/light class before Alpine boots, so the page never
         paints in the wrong theme. The visitor's stored choice wins over the
         theme setting, but only while the front-end switcher is enabled — the
         same precedence appearanceTheme() applies in
         resources/src/front/js/app.js. Keep the two in step. --}}
    <script @cspNonce>
    (function(){
        var m = '{{ $themeSettings->appearance_mode ?? '0' }}';
        var toggleEnabled = {{ (string) ($themeSettings->appearance_toggle_enabled ?? '0') === '1' ? 'true' : 'false' }};
        if (toggleEnabled) {
            try {
                var stored = window.localStorage.getItem('dls-appearance-mode');
                if (stored === '0' || stored === '1' || stored === '2') {
                    m = stored;
                }
            } catch (e) {
                // Private mode or blocked storage: keep the theme setting.
            }
        }
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
    {{-- Heading fonts via Bunny Fonts: two JP faces (Noto Sans JP,
         Noto Serif JP) and two Latin display faces (Cormorant Garamond,
         Jost). All four load eagerly so the operator can swap the
         theme setting and see it take effect immediately. The two
         Latin faces are tiny (~50KB gzip each); the two JP faces
         subset per `unicode-range` and typically only the base chunk
         (~300KB gzip) is downloaded for a JP page. `display=swap`
         prevents FOIT. --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=cormorant-garamond:400,700|jost:400,700|noto-sans-jp:400,700|noto-serif-jp:400,700&display=swap">

    @php
        // Heading font stack resolves setting → CSS variable that any
        // rule can consume via var(--font-heading). Cormorant / Jost
        // are Latin-only faces, so their JP fallback is the system
        // Mincho / Gothic respectively — Japanese glyphs render in
        // the OS-native face while the Latin characters carry the
        // chosen web font's personality. Legacy 'gothic' / 'mincho'
        // values (from the 2-choice era) map onto the JP faces so
        // existing installs render as before.
        $headingFontStack = match ($themeSettings->heading_font_family ?? 'noto-sans-jp') {
            'cormorant' => "'Cormorant Garamond', 'Hiragino Mincho ProN', 'Yu Mincho', 'YuMincho', serif",
            'jost' => "'Jost', 'Hiragino Kaku Gothic ProN', 'Yu Gothic Medium', 'YuGothic', sans-serif",
            'noto-serif-jp', 'mincho' => "'Noto Serif JP', 'Hiragino Mincho ProN', 'Yu Mincho', 'YuMincho', serif",
            default => "'Noto Sans JP', 'Hiragino Kaku Gothic ProN', 'Yu Gothic Medium', 'YuGothic', sans-serif",
        };

        // Per-region toggles. Only regions whose toggle is ON get their
        // --font-heading-* variable declared, so region CSS rules fall
        // back through `var(name, inherit)` to the parent (body) font
        // stack when OFF.
        $applyHeader = (string) ($themeSettings->heading_font_apply_header ?? '1') === '1';
        $applyHero = (string) ($themeSettings->heading_font_apply_hero ?? '1') === '1';
        $applyFooter = (string) ($themeSettings->heading_font_apply_footer ?? '1') === '1';
        $applyContent = (string) ($themeSettings->heading_font_apply_content ?? '1') === '1';

        // Per-region tracking (letter-spacing) in em. Only emitted when
        // non-zero so the default rendering path stays on `normal`
        // letter-spacing without paying for an override lookup. Values
        // are numerically validated at the Request layer, so direct
        // interpolation is safe.
        $trackingRegions = [
            'header'  => (string) ($themeSettings->heading_font_tracking_header  ?? '0'),
            'hero'    => (string) ($themeSettings->heading_font_tracking_hero    ?? '0'),
            'footer'  => (string) ($themeSettings->heading_font_tracking_footer  ?? '0'),
            'content' => (string) ($themeSettings->heading_font_tracking_content ?? '0'),
        ];
    @endphp
    <style @cspNonce>
        :root {
            --color-primary: {{ $primaryColor }};
            --color-primary-dark-safe: {{ $primaryColorDarkSafe }};
            --font-heading: {!! $headingFontStack !!};
            @if ($applyHeader) --font-heading-header: var(--font-heading); @endif
            @if ($applyHero) --font-heading-hero: var(--font-heading); @endif
            @if ($applyFooter) --font-heading-footer: var(--font-heading); @endif
            @if ($applyContent) --font-heading-content: var(--font-heading); @endif
            @foreach ($trackingRegions as $region => $emValue)
                @if ($emValue !== '' && (float) $emValue !== 0.0) --font-heading-tracking-{{ $region }}: {{ $emValue }}em; @endif
            @endforeach
        }
        /* Global palt: proportional alternate widths for Japanese
           glyphs — closes the gap between kana / kanji / punctuation
           so headings and body read as designed rather than
           typewriter-spaced. No-op for Latin faces (they lack the
           feature), so applying globally is safe. */
        body { font-feature-settings: "palt"; }
        .font-heading-header { font-family: var(--font-heading-header, inherit); letter-spacing: var(--font-heading-tracking-header, normal); }
        .font-heading-hero   { font-family: var(--font-heading-hero, inherit);   letter-spacing: var(--font-heading-tracking-hero, normal); }
        .font-heading-footer { font-family: var(--font-heading-footer, inherit); letter-spacing: var(--font-heading-tracking-footer, normal); }
        h1, h2, h3, h4       { font-family: var(--font-heading-content, inherit); letter-spacing: var(--font-heading-tracking-content, normal); }
    </style>

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

         `appearance_toggle_enabled` is the master switch — it also decides
         whether a visitor's stored choice outranks `appearance_mode`, so the
         placement switches only choose where the control appears while that
         is on. The two placements are independent of each other. --}}
    @if ((string) ($themeSettings->appearance_toggle_enabled ?? '0') === '1'
        && (string) ($themeSettings->appearance_toggle_float ?? '0') === '1')
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
