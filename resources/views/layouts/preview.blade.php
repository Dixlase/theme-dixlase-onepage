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

    {{-- The same <head> the live page renders, so a tag added there reaches
         the preview with no second copy to keep in step. `stylesOnly` leaves
         the JavaScript bundle out: this frame shows the page's appearance and
         runs no front-end JS. --}}
    @include('themes::partials.head', ['stylesOnly' => true])

    {{-- Heading fonts. All four families (Cormorant Garamond, Jost, Noto
         Sans JP, Noto Serif JP) load eagerly so the operator sees an
         authentic preview whatever the setting lands on; see
         layouts/app.blade.php for the per-face rationale. The partial above
         carries the preconnect and the body face. --}}
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=cormorant-garamond:400,700|jost:400,700|noto-sans-jp:400,700|noto-serif-jp:400,700&display=swap">

    {{-- Font Awesome (footer SNS icons) comes from the theme's bundled
         style.scss (FA 7.x); see layouts/app.blade.php for why the cdnjs
         6.4.0 stylesheet must not be loaded here. --}}

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

        $trackingRegions = [
            'header'  => (string) ($themeSettings->heading_font_tracking_header  ?? '0'),
            'hero'    => (string) ($themeSettings->heading_font_tracking_hero    ?? '0'),
            'footer'  => (string) ($themeSettings->heading_font_tracking_footer  ?? '0'),
            'content' => (string) ($themeSettings->heading_font_tracking_content ?? '0'),
        ];
    @endphp
    <style @cspNonce>
        /* Header position: fixed at the top, since the frame has no admin bar. */
        header.fixed { top: 0 !important; }
        /* Theme primary colour, so it applies in the preview frame too. */
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
        body { font-feature-settings: "palt"; }
        .font-heading-header { font-family: var(--font-heading-header, inherit); letter-spacing: var(--font-heading-tracking-header, normal); }
        .font-heading-hero   { font-family: var(--font-heading-hero, inherit);   letter-spacing: var(--font-heading-tracking-hero, normal); }
        .font-heading-footer { font-family: var(--font-heading-footer, inherit); letter-spacing: var(--font-heading-tracking-footer, normal); }
        h1, h2, h3, h4       { font-family: var(--font-heading-content, inherit); letter-spacing: var(--font-heading-tracking-content, normal); }
        /* Preview only: every interactive element is disabled. */
        a, button, [role="button"],
        input, textarea, select {
            pointer-events: none !important;
            cursor: default !important;
        }
    </style>

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

        {{-- Appearance switcher fixed in the corner, included here for the
             same reason layouts/app.blade.php includes it rather than the
             footer partial: a `custom/` override replaces a view wholesale,
             so a site that has forked the footer would never receive a
             control added inside it.

             A preview that leaves it out is a preview of a different page,
             which is the whole failure this layout exists to avoid. It does
             not operate here — this layout disables interactive elements —
             but it is shown, in the position and at the size the visitor
             gets. The footer placement needs nothing extra: it lives inside
             partials/footer.blade.php, which is included above. --}}
        @if ((string) ($themeSettings->appearance_toggle_float ?? '0') === '1')
            @include('themes::partials.appearance-float')
        @endif
    @endunless

    @stack('scripts')
</body>
</html>
