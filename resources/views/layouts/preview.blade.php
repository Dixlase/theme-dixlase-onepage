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

    {{-- Fonts. All four heading families (Cormorant Garamond, Jost,
         Noto Sans JP, Noto Serif JP) are loaded so the operator sees
         an authentic preview no matter which setting they land on;
         see layouts/app.blade.php for the rationale. --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|cormorant-garamond:400,700|jost:400,700|noto-sans-jp:400,700|noto-serif-jp:400,700&display=swap" rel="stylesheet" />

    {{-- Font Awesome（フッターSNSアイコン等で使用） --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- CSSのみ読み込み（JSバンドルは一切含めない）。コア共通
         Tailwind (`resources/src/common/css/tailwind.css`) を必ず
         含めること（head.blade.php と同じ理由）。 --}}
    @if(app()->environment('local') && file_exists(public_path('hot')))
        @vite([
            'resources/src/common/css/tailwind.css',
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
    @php
        // テーマプライマリカラー + ダークモード可読フォールバック。
        // layouts/app.blade.php と同じロジック(無彩色は白、有彩色は
        // 30% 白寄せで明度アップ)。詳細は app.blade.php のコメント参照。
        $primaryColor = $themeSettings->primary_color ?? '#3b82f6';
        $pcHex = ltrim($primaryColor, '#');
        if (strlen($pcHex) === 6) {
            $pcR = hexdec(substr($pcHex, 0, 2));
            $pcG = hexdec(substr($pcHex, 2, 2));
            $pcB = hexdec(substr($pcHex, 4, 2));
            if (max($pcR, $pcG, $pcB) - min($pcR, $pcG, $pcB) < 30) {
                $primaryColorDarkSafe = '#ffffff';
            } else {
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
    @php
        // Preview layout mirrors app.blade.php's heading-font wiring so
        // the iframe reflects the same setting the front page will show.
        // See app.blade.php for the per-face rationale.
        $headingFontStack = match ($themeSettings->heading_font_family ?? 'noto-sans-jp') {
            'cormorant' => "'Cormorant Garamond', 'Hiragino Mincho ProN', 'Yu Mincho', 'YuMincho', serif",
            'jost' => "'Jost', 'Hiragino Kaku Gothic ProN', 'Yu Gothic Medium', 'YuGothic', sans-serif",
            'noto-serif-jp', 'mincho' => "'Noto Serif JP', 'Hiragino Mincho ProN', 'Yu Mincho', 'YuMincho', serif",
            default => "'Noto Sans JP', 'Hiragino Kaku Gothic ProN', 'Yu Gothic Medium', 'YuGothic', sans-serif",
        };

        $applyHeader = (string) ($themeSettings->heading_font_apply_header ?? '1') === '1';
        $applyHero = (string) ($themeSettings->heading_font_apply_hero ?? '1') === '1';
        $applyFooter = (string) ($themeSettings->heading_font_apply_footer ?? '1') === '1';
        $applyContent = (string) ($themeSettings->heading_font_apply_content ?? '1') === '1';
    @endphp
    <style @cspNonce>
        [x-cloak] { display: none !important; }
        header.fixed { top: 0 !important; }
        /* テーマプライマリカラー (preview-frame でも有効に) */
        :root {
            --color-primary: {{ $primaryColor }};
            --color-primary-dark-safe: {{ $primaryColorDarkSafe }};
            --font-heading: {!! $headingFontStack !!};
            @if ($applyHeader) --font-heading-header: var(--font-heading); @endif
            @if ($applyHero) --font-heading-hero: var(--font-heading); @endif
            @if ($applyFooter) --font-heading-footer: var(--font-heading); @endif
            @if ($applyContent) --font-heading-content: var(--font-heading); @endif
        }
        .font-heading-header { font-family: var(--font-heading-header, inherit); }
        .font-heading-hero   { font-family: var(--font-heading-hero, inherit); }
        .font-heading-footer { font-family: var(--font-heading-footer, inherit); }
        h1, h2, h3, h4       { font-family: var(--font-heading-content, inherit); }
        /* プレビュー専用: 全インタラクティブ要素を無効化 */
        a, button, [role="button"],
        input, textarea, select {
            pointer-events: none !important;
            cursor: default !important;
        }
    </style>

    @stack('styles')
</head>
<body
    class="text-gray-900 dark:text-gray-100 {{ ($bareContent ?? false) ? '' : 'bg-gray-50 dark:bg-gray-900 flex flex-col min-h-screen' }}"
    @if($bareContent ?? false) style="background: transparent;" @endif>
    @unless($bareContent ?? false)
        @include('themes::partials.header')
    @endunless

    <main class="{{ ($bareContent ?? false) ? '' : 'flex-grow' }}">
        @yield('content')
    </main>

    @unless($bareContent ?? false)
        @include('themes::partials.footer')
    @endunless

    @stack('scripts')
</body>
</html>
