@php
    // テーマ設定はServiceProviderから自動的に渡される
    $heroMainTitle = $themeSettings->hero_main_title ?? 'Welcome to ' . config('app.name', 'Dixlase');
    $heroSubTitle = $themeSettings->hero_sub_title ?? 'Modern CMS Platform for Building Amazing Websites';
    $heroButtonText = $themeSettings->hero_button_text ?? 'Get Started';
    $heroButtonLink = $themeSettings->hero_button_link ?? '#';
    $heroButtonSecondaryText = $themeSettings->hero_button_secondary_text ?? null;
    $heroButtonSecondaryLink = $themeSettings->hero_button_secondary_link ?? null;
    $heroBackgroundPath = $themeSettings->heroBackgroundPath ?? null;
    $primaryColor = $themeSettings->primary_color ?? '#3b82f6';
@endphp

<section class="relative min-h-screen flex flex-col justify-center overflow-hidden bg-gray-100 dark:bg-gray-900">
    {{-- 背景画像 --}}
    @if($heroBackgroundPath)
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('storage/' . $heroBackgroundPath) }}" alt="" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-white/60 dark:bg-gray-900/70"></div>
        </div>
    @endif

    {{-- コンテンツ --}}
    <div class="container mx-auto px-4 py-20 relative z-10">
        <div class="max-w-2xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight text-gray-900 dark:text-white">
                {{ $heroMainTitle }}
            </h1>

            @if($heroSubTitle)
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 max-w-lg mx-auto">
                    {{ $heroSubTitle }}
                </p>
            @endif

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @if($heroButtonText)
                    <a href="{{ $heroButtonLink }}"
                       class="inline-flex items-center justify-center gap-2 h-11 rounded-xl text-white px-8 py-6 transition-opacity hover:opacity-90"
                       style="background-color: {{ $primaryColor }}">
                        {{ $heroButtonText }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-5 w-5">
                            <path d="M5 12h14"></path>
                            <path d="m12 5 7 7-7 7"></path>
                        </svg>
                    </a>
                @endif

                @if($heroButtonSecondaryText)
                    <a href="{{ $heroButtonSecondaryLink }}"
                       class="inline-flex items-center justify-center gap-2 h-11 rounded-xl px-8 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-white/5 py-6 transition-colors">
                        {{ $heroButtonSecondaryText }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-5 w-5">
                            <path d="M7 7h10v10"></path>
                            <path d="M7 17 17 7"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
