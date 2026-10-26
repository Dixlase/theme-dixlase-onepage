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

{{-- Every appearance setting the operator can change, resolved once and
     emitted as CSS custom properties. Both head-bearing layouts include
     this, so the live page and the admin preview frame cannot drift: a new
     setting, a fifth heading face or a different achromatic cutoff is added
     here and reaches both.

     Include it *after* the stylesheets. The rules below share their
     specificity with Tailwind's preflight (`h1, h2, h3, h4`), so emitting
     them earlier would hand the cascade to preflight and the heading font
     would silently stop applying. --}}

{{-- Heading fonts via Bunny Fonts: two JP faces (Noto Sans JP,
     Noto Serif JP) and two Latin display faces (Cormorant Garamond,
     Jost). All four load eagerly so the operator can swap the
     theme setting and see it take effect immediately. The two
     Latin faces are tiny (~50KB gzip each); the two JP faces
     subset per `unicode-range` and typically only the base chunk
     (~300KB gzip) is downloaded for a JP page. `display=swap`
     prevents FOIT. The preconnect is in partials/head.blade.php,
     which every caller includes before this. --}}
<link rel="stylesheet" href="https://fonts.bunny.net/css?family=cormorant-garamond:400,700|jost:400,700|noto-sans-jp:400,700|noto-serif-jp:400,700&display=swap">

{{-- The theme's primary colour as a CSS variable, read from SCSS as
     var(--color-primary) for the header and footer menu hover colours.
     The dark-mode hover colour is computed separately into
     `--color-primary-dark-safe`:
       1. An achromatic colour (grey / black: R≈G≈B) is unreadable on a
          dark hover background at any lightness, so it falls back to white.
       2. Any other colour moves 30% of the way to white. The hue is kept
          and only the lightness rises, enough to read on a dark background. --}}
@php
    $primaryColor = $themeSettings->primary_color ?? '#3b82f6';
    $pcHex = ltrim($primaryColor, '#');
    if (strlen($pcHex) === 6) {
        $pcR = hexdec(substr($pcHex, 0, 2));
        $pcG = hexdec(substr($pcHex, 2, 2));
        $pcB = hexdec(substr($pcHex, 4, 2));
        if (max($pcR, $pcG, $pcB) - min($pcR, $pcG, $pcB) < 30) {
            // Achromatic: white
            $primaryColorDarkSafe = '#ffffff';
        } else {
            // 30% toward white, raising the lightness
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
