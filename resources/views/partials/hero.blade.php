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
    // テーマ設定はServiceProviderから自動的に渡される
    // The 4 hero text fields go through the multilingual lookup chain
    // (current locale → site default → primary) via
    // dls_onepage_localized_setting(). The other fields are
    // non-translatable (URLs, booleans, media paths, colour) and read
    // straight from the primary $themeSettings object.
    $heroMainTitle = dls_onepage_localized_setting('hero_main_title') ?? 'Welcome to ' . config('app.name', 'Dixlase');
    $heroSubTitle = dls_onepage_localized_setting('hero_sub_title') ?? 'Modern CMS Platform for Building Amazing Websites';
    $heroButtonText = dls_onepage_localized_setting('hero_button_text') ?? 'Get Started';
    $heroButtonLink = $themeSettings->hero_button_link ?? '#';
    $heroButtonEnabled = ($themeSettings->hero_button_enabled ?? '1') === '1';
    $heroButtonTarget = in_array($themeSettings->hero_button_target ?? '_self', ['_self', '_blank'], true)
        ? ($themeSettings->hero_button_target ?? '_self')
        : '_self';
    $heroButtonSecondaryText = dls_onepage_localized_setting('hero_button_secondary_text');
    $heroButtonSecondaryLink = $themeSettings->hero_button_secondary_link ?? null;
    $heroButtonSecondaryEnabled = ($themeSettings->hero_button_secondary_enabled ?? '1') === '1';
    $heroButtonSecondaryTarget = in_array($themeSettings->hero_button_secondary_target ?? '_self', ['_self', '_blank'], true)
        ? ($themeSettings->hero_button_secondary_target ?? '_self')
        : '_self';
    $heroBackgroundPath = $themeSettings->heroBackgroundPath ?? null;
    $heroVideoPath = $themeSettings->heroBackgroundVideoPath ?? null;
    $heroForegroundPath = $themeSettings->heroForegroundPath ?? null;
    $primaryColor = $themeSettings->primary_color ?? '#3b82f6';

    // Hero gradient controls (see plumbing in SP / Request /
    // Controller / Seeder). Three knobs:
    //   $heroGradientMode  = color source; 'none' skips the div entirely.
    //   $heroGradientColor = resolved 6-hex tint.
    //   $heroGradientShape = 'radial' (default) | 'linear-vertical'.
    // All Request-validated so the values can be interpolated into
    // the inline style directly.
    $heroGradientMode = $themeSettings->hero_gradient_mode ?? 'primary';
    $heroGradientColor = $heroGradientMode === 'custom'
        ? ($themeSettings->hero_gradient_color ?? '#3b82f6')
        : $primaryColor;
    $heroGradientShape = $themeSettings->hero_gradient_shape ?? 'radial';
    $heroGradientCss = $heroGradientShape === 'linear-vertical'
        ? "linear-gradient(to bottom, {$heroGradientColor}80 0%, transparent 100%)"
        : "radial-gradient(circle clamp(500px, 100vw, 2400px) at 50% 50%, {$heroGradientColor}80 0%, transparent 65%)";

    // When the admin bar is present (member is logged in), it occupies
    // ~48px (Tailwind `top-12` = 3rem) at the top of the viewport.
    // Use exact `h-[…]` (not `min-h-…`) so the hero is pinned to one
    // viewport height — combined with the flex-1 image layout below,
    // this keeps "text + image" inside one screen even on very short
    // viewports (the image area shrinks to fit the remainder).
    $hasAdminBar = auth('member')->check();
    $heroHeight = $hasAdminBar ? 'h-[calc(100vh-3rem)]' : 'h-screen';

    // Without a foreground image the hero is a centred text block.
    // With one, we lay out as: text on top (natural height), image
    // area underneath taking the remaining viewport space — see the
    // `flex-1 min-h-0` block below.
    // Foreground-image branch: vh-based padding so the spacing scales
    // with viewport height instead of breakpoint. Tailwind v4's
    // arbitrary-value scanner in this dev pipeline doesn't generate
    // `pt-[3vh]` / `pb-[3vh]` (it does generate `max-h-[55vh]` —
    // utility-specific quirk), so we apply the padding via inline
    // `style="…"` below on the section. The class string here keeps
    // only the flex-alignment utility.
    //
    // Top padding has to clear the fixed theme header (`h-14` = 56 px
    // = 3.5 rem) that sits on top of the hero — otherwise the title
    // tucks under the header. `calc(3.5rem + 3vh)` gives header
    // offset + the same 3vh visual breathing room we want lower
    // viewports to scale with.
    $heroVerticalAlign = $heroForegroundPath ? 'justify-start' : 'justify-center';
    $heroSectionStyle = $heroForegroundPath ? 'padding-top: calc(3.5rem + 3vh); padding-bottom: 3vh;' : '';
@endphp

<section class="relative {{ $heroHeight }} flex flex-col {{ $heroVerticalAlign }} overflow-hidden bg-gray-100 dark:bg-gray-950"
    @if($heroSectionStyle) style="{{ $heroSectionStyle }}" @endif
    @if($heroVideoPath)
        x-data="{ videoPlaying: false }"
    @endif
>
    {{-- 背景動画（設定時のみ） --}}
    @if($heroVideoPath)
        <div class="absolute inset-0 z-0" x-show="videoPlaying" x-cloak>
            <video
                class="w-full h-full object-cover"
                autoplay muted loop playsinline
                x-ref="heroVideo"
                x-init="$refs.heroVideo.play().then(() => { videoPlaying = true }).catch(() => {})"
            >
                <source src="{{ asset('storage/' . $heroVideoPath) }}" type="video/mp4">
            </video>
            <div class="absolute inset-0 bg-white/60 dark:bg-gray-950/70"></div>
        </div>
    @endif

    {{-- 背景画像も動画も無いときの装飾: ヒーロー中央に 1 つの
         オーソドックスな放射グラデーション。形状は `circle` 固定で
         常に正円。半径は `clamp(500px, 100vw, 2400px)` でビューポート
         幅に応じてスケール — モバイルでは 500px、Desktop 1280px で
         1280px、Wide 1920px で 1920px、超ワイドでは 2400px に頭打ち。
         ユーザ操作対象ではないので `pointer-events-none`。
         8 桁 HEX 末尾 `80` = α 50%。 --}}
    @if(empty($heroBackgroundPath) && empty($heroVideoPath) && $heroGradientMode !== 'none')
        <div class="absolute inset-0 z-0 pointer-events-none"
             style="background: {{ $heroGradientCss }};"></div>
    @endif

    {{-- 背景画像（動画未設定 or 動画再生不可時のフォールバック） --}}
    @if($heroBackgroundPath)
        <div class="absolute inset-0 z-0"
            @if($heroVideoPath) x-show="!videoPlaying" @endif
        >
            <img src="{{ asset('storage/' . $heroBackgroundPath) }}" alt="" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-white/60 dark:bg-gray-950/70"></div>
        </div>
    @endif

    {{-- コンテンツ。
         前景画像があるときは flex-col のメイン軸を利用した「上=テキスト
         (自然高さ)、下=画像 (残り全部)」レイアウト。画像エリアが
         `flex-1 min-h-0` で section の残りスペースを吸収し、画像自身は
         `max-w-full max-h-full object-contain` で親に合わせて
         アスペクト比保持で縮む。viewport が短くても画像が押し出される
         ことがなく、常に "text + 画像 = 1 ビューポート" を保つ。
         前景画像が無いときは従来通り `py-20` + section 側の
         `justify-center` でテキストを中央寄せ。 --}}
    @if($heroForegroundPath)
        <div class="container mx-auto px-4 relative z-10 flex flex-col flex-1 min-h-0 w-full">
            <div class="max-w-4xl mx-auto text-center flex-shrink-0">
                @include('themes::partials.hero.text-block', [
                    'heroMainTitle' => $heroMainTitle,
                    'heroSubTitle' => $heroSubTitle,
                    'heroButtonEnabled' => $heroButtonEnabled,
                    'heroButtonText' => $heroButtonText,
                    'heroButtonLink' => $heroButtonLink,
                    'heroButtonTarget' => $heroButtonTarget,
                    'heroButtonSecondaryEnabled' => $heroButtonSecondaryEnabled,
                    'heroButtonSecondaryText' => $heroButtonSecondaryText,
                    'heroButtonSecondaryLink' => $heroButtonSecondaryLink,
                    'heroButtonSecondaryTarget' => $heroButtonSecondaryTarget,
                    'primaryColor' => $primaryColor,
                ])
            </div>
            <div class="max-w-[1488px] mx-auto flex-1 min-h-0 flex items-center justify-center w-full"
                 style="margin-top: 2vh;">
                <img src="{{ asset('storage/' . $heroForegroundPath) }}"
                     alt=""
                     class="max-w-full max-h-full w-auto h-auto object-contain rounded-2xl shadow-2xl">
            </div>
        </div>
    @else
        <div class="container mx-auto px-4 py-20 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                @include('themes::partials.hero.text-block', [
                    'heroMainTitle' => $heroMainTitle,
                    'heroSubTitle' => $heroSubTitle,
                    'heroButtonEnabled' => $heroButtonEnabled,
                    'heroButtonText' => $heroButtonText,
                    'heroButtonLink' => $heroButtonLink,
                    'heroButtonTarget' => $heroButtonTarget,
                    'heroButtonSecondaryEnabled' => $heroButtonSecondaryEnabled,
                    'heroButtonSecondaryText' => $heroButtonSecondaryText,
                    'heroButtonSecondaryLink' => $heroButtonSecondaryLink,
                    'heroButtonSecondaryTarget' => $heroButtonSecondaryTarget,
                    'primaryColor' => $primaryColor,
                ])
            </div>
        </div>
    @endif
</section>
