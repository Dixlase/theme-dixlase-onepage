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

    // Without a foreground image the hero is a centered text block —
    // `justify-center` keeps the existing layout. With a foreground
    // image we pull the text/buttons toward the top so the image
    // (rendered below the buttons) has room to breathe.
    $heroVerticalAlign = $heroForegroundPath
        ? 'justify-start pt-20 md:pt-28 lg:pt-32 pb-12'
        : 'justify-center';
@endphp

<section class="relative min-h-screen flex flex-col {{ $heroVerticalAlign }} overflow-hidden bg-gray-100 dark:bg-gray-950"
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

    {{-- 背景画像（動画未設定 or 動画再生不可時のフォールバック） --}}
    @if($heroBackgroundPath)
        <div class="absolute inset-0 z-0"
            @if($heroVideoPath) x-show="!videoPlaying" @endif
        >
            <img src="{{ asset('storage/' . $heroBackgroundPath) }}" alt="" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-white/60 dark:bg-gray-950/70"></div>
        </div>
    @endif

    {{-- コンテンツ --}}
    <div class="container mx-auto px-4 py-20 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight text-gray-900 dark:text-white whitespace-pre-line">
                {{ $heroMainTitle }}
            </h1>

            @if($heroSubTitle)
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 max-w-lg mx-auto whitespace-pre-line">
                    {{ $heroSubTitle }}
                </p>
            @endif

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @if($heroButtonEnabled && $heroButtonText)
                    <a href="{{ $heroButtonLink }}"
                       target="{{ $heroButtonTarget }}"
                       @if($heroButtonTarget === '_blank') rel="noopener noreferrer" @endif
                       class="inline-flex items-center justify-center gap-2 h-11 rounded-xl text-white px-8 py-6 transition-opacity hover:opacity-90"
                       style="background-color: {{ $primaryColor }}">
                        {{ $heroButtonText }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-5 w-5">
                            <path d="M5 12h14"></path>
                            <path d="m12 5 7 7-7 7"></path>
                        </svg>
                    </a>
                @endif

                @if($heroButtonSecondaryEnabled && $heroButtonSecondaryText)
                    <a href="{{ $heroButtonSecondaryLink }}"
                       target="{{ $heroButtonSecondaryTarget }}"
                       @if($heroButtonSecondaryTarget === '_blank') rel="noopener noreferrer" @endif
                       class="hero-btn-secondary inline-flex items-center justify-center gap-2 h-11 rounded-xl px-8 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/5 py-6 transition-colors">
                        {{ $heroButtonSecondaryText }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-5 w-5">
                            <path d="M7 7h10v10"></path>
                            <path d="M7 17 17 7"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        {{-- 前景画像（設定時のみ。テキスト/ボタンより広い枠で中央寄せ） --}}
        @if($heroForegroundPath)
            <div class="mt-20 md:mt-28 lg:mt-32 max-w-5xl mx-auto">
                <img src="{{ asset('storage/' . $heroForegroundPath) }}"
                     alt=""
                     class="w-full h-auto rounded-2xl shadow-2xl">
            </div>
        @endif
    </div>
</section>
