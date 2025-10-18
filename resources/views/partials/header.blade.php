@php
    // テーマ設定はServiceProviderから自動的に渡される
    $logoText = config('app.name', 'Dixlase');
    $hasAdminBar = auth('member')->check();
    
    // ロゴサイズ設定（ヘッダー用は sm サイズ）
    $logoSize = 'sm';
    $sizeClasses = [
        'sm' => 'h-8',
        'md' => 'h-10',
        'lg' => 'h-12',
    ];
    $heightClass = $sizeClasses[$logoSize] ?? $sizeClasses['sm'];
@endphp

<header class="fixed h-14 items-center w-full z-50 transition-all duration-300 bg-white/50 dark:bg-gray-900/50 backdrop-blur-md shadow-lg dark:shadow-gray-700/10 {{ $hasAdminBar ? 'top-12' : 'top-0' }}">
    <div class="container h-full mx-auto px-4 flex justify-between items-center">
        <div class="flex items-center justify-between w-full">
            {{-- Site Logo --}}
            <div class="flex-shrink-0">
                <a href="{{ url('/') }}" class="flex items-center space-x-3 group" aria-label="Home">
                    @if($themeSettings->headerLogo && $themeSettings->headerLogoPath)
                        {{-- テーマ設定のロゴ画像 --}}
                        <img 
                            src="{{ asset('storage/' . $themeSettings->headerLogoPath) }}" 
                            alt="{{ $logoText }}" 
                            class="{{ $heightClass }} w-auto"
                        >
                        {{-- サイト名 --}}
                        <span class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ $logoText }}
                        </span>
                    @else
                        {{-- デフォルトロゴ (SVG + テキスト) --}}
                        <svg class="{{ $heightClass }} w-auto" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="40" height="40" rx="8" class="fill-blue-600 dark:fill-blue-500"/>
                            <text x="20" y="28" class="fill-white font-bold text-2xl" font-family="system-ui, sans-serif" text-anchor="middle">
                                {{ substr($logoText, 0, 1) }}
                            </text>
                        </svg>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ $logoText }}
                        </span>
                    @endif
                </a>
            </div>

            {{-- Navigation --}}
            <ul class="hidden lg:flex items-center space-x-8">
                <x-front.navigation :items="$navigationItems ?? []" />
            </ul>

            {{-- Mobile Menu Button --}}
            <button class="lg:hidden text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu">
                    <line x1="4" x2="20" y1="12" y2="12"></line>
                    <line x1="4" x2="20" y1="6" y2="6"></line>
                    <line x1="4" x2="20" y1="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
</header>
