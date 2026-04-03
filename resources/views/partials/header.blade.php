@php
    // テーマ設定はServiceProviderから自動的に渡される
    $logoText = config('app.name', 'Dixlase');
    $hasAdminBar = auth('member')->check();

    // ヘッダーの位置を計算（管理バーがある場合のみ下げる）
    $headerTopClass = $hasAdminBar ? 'top-12' : 'top-0';

    // ロゴサイズ設定（ヘッダー用は sm サイズ）
    $logoSize = 'sm';
    $sizeClasses = [
        'sm' => 'h-8',
        'md' => 'h-10',
        'lg' => 'h-12',
    ];
    $heightClass = $sizeClasses[$logoSize] ?? $sizeClasses['sm'];
@endphp

<header
    x-data="navigationMenu()"
    x-init="initObserver()"
    class="fixed h-14 items-center w-full z-50 transition-all duration-300 bg-white/50 dark:bg-gray-900/50 backdrop-blur-md shadow-lg dark:shadow-gray-700/10 {{ $headerTopClass }}"
>
    <div x-ref="navContainer" class="w-full h-full px-4 flex justify-between items-center">
        <div class="flex items-center justify-between w-full">
            {{-- Site Logo --}}
            <div x-ref="logo" class="flex-shrink-0">
                <a href="{{ url('/') }}" class="flex items-center space-x-3 group" aria-label="Home">
                    @if(!empty($themeSettings->headerLogoPath))
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

            {{-- Desktop Navigation --}}
            <nav
                x-ref="desktopNav"
                class="hidden lg:flex items-center space-x-1"
                :class="{ '!hidden': forceHamburger }"
            >
                @forelse($navigationItems ?? [] as $item)
                    @if(!empty($item['children']))
                        {{-- メガメニュートリガー --}}
                        @php
                            $childCount = count($item['children']);
                            $isMenuGroup = ($item['source_type'] ?? '') === 'menu_group';
                            $colClass = $childCount <= 4 ? 'sm:grid-cols-1' : ($childCount <= 8 ? 'sm:grid-cols-2' : 'sm:grid-cols-3');
                            $minWidthClass = $childCount <= 4 ? 'min-w-[20rem]' : ($childCount <= 8 ? 'min-w-[36rem]' : 'min-w-[52rem]');
                        @endphp
                        <div
                            class="relative"
                            x-data="{ open: false, timer: null }"
                            @mouseenter="clearTimeout(timer); open = true"
                            @mouseleave="timer = setTimeout(() => open = false, 150)"
                            @keydown.escape.prevent="open = false"
                        >
                            @if($isMenuGroup)
                                {{-- メニューグループ: URLなし、ボタンとして表示 --}}
                                <button
                                    type="button"
                                    class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
                                    :aria-expanded="open"
                                    aria-haspopup="true"
                                    @click="open = !open"
                                    @focusin="clearTimeout(timer); open = true"
                                >
                                    {{ $item['label'] ?? '' }}
                                    <svg class="ml-1 w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            @else
                                {{-- URLあり + 子あり: リンク兼ドロップダウントリガー --}}
                                <a
                                    href="{{ $item['url'] ?? '#' }}"
                                    target="{{ $item['target'] ?? '_self' }}"
                                    class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
                                    :aria-expanded="open"
                                    aria-haspopup="true"
                                    @focusin="clearTimeout(timer); open = true"
                                >
                                    {{ $item['label'] ?? '' }}
                                    <svg class="ml-1 w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </a>
                            @endif

                            {{-- メガメニューパネル --}}
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute left-0 mt-1 {{ $minWidthClass }} bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-black/5 dark:ring-white/10 z-50"
                                role="menu"
                                x-cloak
                                @click.outside="open = false"
                                @focusout.debounce.150ms="if (!$el.contains(document.activeElement)) open = false"
                            >
                                <div class="grid {{ $colClass }} gap-x-6 p-4">
                                    @foreach($item['children'] as $child)
                                        <a
                                            href="{{ $child['url'] ?? '#' }}"
                                            target="{{ $child['target'] ?? '_self' }}"
                                            role="menuitem"
                                            class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors"
                                        >
                                            @if(!empty($child['icon_class']))
                                                <i class="{{ $child['icon_class'] }} w-5 h-5 text-gray-400 dark:text-gray-500"></i>
                                            @endif
                                            <span>{{ $child['label'] ?? '' }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- 通常のリンク --}}
                        <a
                            href="{{ $item['url'] ?? '#' }}"
                            target="{{ $item['target'] ?? '_self' }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
                        >
                            {{ $item['label'] ?? '' }}
                        </a>
                    @endif
                @empty
                    {{-- メニューが空の場合 --}}
                @endforelse
            </nav>

            {{-- Desktop Extras (Auth Buttons + Language Switcher) --}}
            <div x-ref="navExtras" class="hidden lg:flex items-center" :class="{ '!hidden': forceHamburger }">
                {{-- User Auth Buttons (provided by DixlaseUsers plugin) --}}
                @if(view()->exists('dixlase-users::components.auth-buttons'))
                <div class="flex items-center ml-4">
                    @include('dixlase-users::components.auth-buttons')
                </div>
                @endif

                {{-- Desktop Language Switcher (provided by DixlaseMultilingual plugin) --}}
                @if(function_exists('dls_multilingual_switcher'))
                <div class="flex items-center ml-4">
                    {!! dls_multilingual_switcher('dropdown', ['showLabel' => false]) !!}
                </div>
                @endif
            </div>

            {{-- Mobile Menu Button --}}
            <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="lg:hidden text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                :class="{ '!flex': forceHamburger }"
                :aria-expanded="mobileMenuOpen"
                aria-label="Toggle menu"
            >
                {{-- ハンバーガーアイコン --}}
                <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" x2="20" y1="12" y2="12"></line>
                    <line x1="4" x2="20" y1="6" y2="6"></line>
                    <line x1="4" x2="20" y1="18" y2="18"></line>
                </svg>
                {{-- 閉じるアイコン --}}
                <svg x-show="mobileMenuOpen" x-cloak xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="absolute top-14 left-0 right-0 bg-white dark:bg-gray-900 shadow-lg border-t border-gray-200 dark:border-gray-700"
        :class="{ 'lg:hidden': !forceHamburger }"
        x-cloak
        @click.away="mobileMenuOpen = false"
    >
        <nav class="container mx-auto px-4 py-4 space-y-1">
            @forelse($navigationItems ?? [] as $item)
                @if(!empty($item['children']))
                    {{-- アコーディオンメニュー --}}
                    @php
                        $isMenuGroup = ($item['source_type'] ?? '') === 'menu_group';
                        $hasParentUrl = !$isMenuGroup && !empty($item['url']) && $item['url'] !== '#';
                    @endphp
                    <div x-data="{ expanded: false }">
                        <button
                            @click="expanded = !expanded"
                            class="w-full flex items-center justify-between px-4 py-3 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md"
                            :aria-expanded="expanded"
                        >
                            {{ $item['label'] ?? '' }}
                            <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="expanded" x-collapse class="pl-4 space-y-1">
                            {{-- URLあり + 子ありの場合: 親のリンクを先頭に表示 --}}
                            @if($hasParentUrl)
                                <a
                                    href="{{ $item['url'] }}"
                                    target="{{ $item['target'] ?? '_self' }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md"
                                    @click="mobileMenuOpen = false"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                    {{ $item['label'] ?? '' }}
                                </a>
                            @endif
                            @foreach($item['children'] as $child)
                                <a
                                    href="{{ $child['url'] ?? '#' }}"
                                    target="{{ $child['target'] ?? '_self' }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-blue-400 rounded-md"
                                    @click="mobileMenuOpen = false"
                                >
                                    @if(!empty($child['icon_class']))
                                        <i class="{{ $child['icon_class'] }} w-4 h-4"></i>
                                    @endif
                                    {{ $child['label'] ?? '' }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- 通常のリンク --}}
                    <a
                        href="{{ $item['url'] ?? '#' }}"
                        target="{{ $item['target'] ?? '_self' }}"
                        class="block px-4 py-3 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-blue-400 rounded-md"
                        @click="mobileMenuOpen = false"
                    >
                        {{ $item['label'] ?? '' }}
                    </a>
                @endif
            @empty
                <p class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('No menu items') }}
                </p>
            @endforelse

            {{-- Mobile User Auth (provided by DixlaseUsers plugin) --}}
            @if(view()->exists('dixlase-users::components.auth-buttons'))
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="px-4 py-2">
                    @include('dixlase-users::components.auth-buttons')
                </div>
            </div>
            @endif

            {{-- Mobile Language Switcher (provided by DixlaseMultilingual plugin) --}}
            @if(function_exists('dls_multilingual_switcher'))
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="px-4 py-2">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('common.language') }}
                    </span>
                </div>
                <div class="px-4 py-2">
                    {!! dls_multilingual_switcher('inline', ['showFlag' => true]) !!}
                </div>
            </div>
            @endif
        </nav>
    </div>
</header>
