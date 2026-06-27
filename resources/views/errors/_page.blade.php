{{--
This file is part of Dixlase OnePage.

Copyright (C) 2026 exc-D inc.
https://exc-d.com

Dixlase OnePage is dual-licensed. You may use this file under either
the GNU General Public License v3 or later, or a commercial license
agreement obtained from exc-D inc. See LICENSE for details.
--}}

{{--
    Shared error page layout for the theme's branded 4xx / 5xx
    templates. Used by `errors/404.blade.php`, `errors/403.blade.php`,
    `errors/419.blade.php`, `errors/500.blade.php` — each one just
    `@include`s this with its own `$code`, `$title`, `$message`.

    Renders inside the theme's `layouts.app` so the header, footer,
    primary colour, and dark-mode wiring are all consistent with the
    rest of the site.

    Variables expected:
      $code    string|int    "404", "500", etc. (display only)
      $title   string        Headline (e.g. "Page Not Found")
      $message string        One-paragraph explanation
--}}
@extends('themes::layouts.app')

@section('title', ' - ' . $title)

@section('content')
@php
    $primaryColor = $themeSettings->primary_color ?? '#3b82f6';
    $hasAdminBar = auth('member')->check();
    // Match the hero's exact-height treatment so the error page fills
    // exactly one viewport — admin bar is subtracted when present.
    $sectionHeight = $hasAdminBar ? 'h-[calc(100vh-3rem)]' : 'h-screen';
@endphp

<section class="relative {{ $sectionHeight }} flex flex-col justify-center overflow-hidden bg-gray-100 dark:bg-gray-950">
    {{-- Primary-coloured radial glow, same recipe the hero uses when
         no background image is set. Keeps the error page visually
         continuous with the rest of the site. --}}
    <div class="absolute inset-0 z-0 pointer-events-none"
         style="background: radial-gradient(circle clamp(500px, 100vw, 2400px) at 50% 50%, {{ $primaryColor }}80 0%, transparent 65%);"></div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-2xl mx-auto text-center">
            {{-- Error code — oversized, semi-transparent so it reads as
                 a decorative numeral behind the message rather than the
                 main headline. --}}
            <p class="text-7xl md:text-8xl lg:text-9xl font-bold leading-none mb-4 text-gray-300 dark:text-gray-700 select-none">
                {{ $code }}
            </p>

            <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold mb-4 md:mb-6 leading-tight text-gray-900 dark:text-white"
                style="text-wrap: balance;">
                {{ $title }}
            </h1>

            <p class="text-base md:text-lg text-gray-600 dark:text-gray-300 mb-6 md:mb-8 max-w-md mx-auto"
               style="text-wrap: balance;">
                {{ $message }}
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center justify-center gap-2 h-11 rounded-xl text-white px-8 py-6 transition-opacity hover:opacity-90"
                   style="background-color: {{ $primaryColor }}">
                    {{ app()->getLocale() === 'ja' ? 'ホームへ戻る' : 'Back to Home' }}
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-5 w-5">
                        <path d="M5 12h14"></path>
                        <path d="m12 5 7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
