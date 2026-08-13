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
    $logoText = config('app.name', 'Dixlase');
    $hasAdminBar = auth('member')->check();

    // <x-ui-front-banner-stack /> の ResizeObserver が
    // --front-banner-stack-height を計算する前の初期値。プラグインが
    // バナーを @push していない通常状態では 3rem (= 管理バー高さ) が
    // ぴったり合う。ログアウト時はスタックが空なので 0px。JS が値を
    // 設定したあとは CSS variable が優先される。
    $stackHeightFallback = $hasAdminBar ? '3rem' : '0px';

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
    x-data="{
        mobileMenuOpen: false,
        forceHamburger: false,
        _resizeObserver: null,
        _debounceTimer: null,
        initObserver() {
            this.$nextTick(() => {
                const nav = this.$refs.desktopNav;
                const container = this.$refs.navContainer;
                if (!nav || !container) return;
                this.checkOverflow(nav, container);
                this._resizeObserver = new ResizeObserver(() => {
                    clearTimeout(this._debounceTimer);
                    this._debounceTimer = setTimeout(() => {
                        this.checkOverflow(nav, container);
                    }, 100);
                });
                this._resizeObserver.observe(container);
            });
        },
        checkOverflow(nav, container) {
            const wasHidden = nav.offsetParent === null;
            if (wasHidden) {
                nav.style.position = 'absolute';
                nav.style.visibility = 'hidden';
                nav.style.display = 'flex';
            }
            const logo = this.$refs.logo;
            const extras = this.$refs.navExtras;
            const logoWidth = logo ? logo.offsetWidth : 0;
            const extrasWidth = extras ? extras.offsetWidth : 0;
            const padding = 64;
            const available = container.offsetWidth - logoWidth - extrasWidth - padding;
            this.forceHamburger = nav.scrollWidth > available;
            if (wasHidden) {
                nav.style.position = '';
                nav.style.visibility = '';
                nav.style.display = '';
            }
        },
        destroy() {
            if (this._resizeObserver) this._resizeObserver.disconnect();
            clearTimeout(this._debounceTimer);
        }
    }"
    x-init="initObserver()"
    class="fixed h-14 items-center w-full z-50 transition-all duration-300 bg-white/50 dark:bg-gray-900/50 backdrop-blur-md shadow-lg dark:shadow-gray-700/10"
    style="top: var(--front-banner-stack-height, {{ $stackHeightFallback }})"
>
    <div x-ref="navContainer" class="w-full h-full px-4 flex justify-between items-center">
        <div class="flex items-center justify-between w-full">
            {{-- Site Logo --}}
            <div x-ref="logo" class="flex-shrink-0">
                <a href="{{ url('/') }}" class="flex items-center space-x-3 group" aria-label="Home">
                    @if(!empty($themeSettings->headerLogoPath))
                        {{-- テーマ設定のロゴ画像。ダークモード用ロゴが別途
                             設定されていればモード毎に切り替え。指定が無ければ
                             ライト用ロゴを `dark:invert` で fallback 反転させて
                             真っ黒の単色マークがダーク背景で潰れるのを防ぐ。
                             `dark:invert` は多色マークだと色相まで反転して
                             ちぐはぐに見えるため、その場合はダーク用ロゴを
                             明示的にアップロードして fallback を無効化する。 --}}
                        @if(!empty($themeSettings->headerLogoDarkPath))
                            {{-- ライト: dark:hidden で隠す --}}
                            <img
                                src="{{ asset('storage/' . $themeSettings->headerLogoPath) }}"
                                alt="{{ $logoText }}"
                                class="{{ $heightClass }} w-auto block dark:hidden"
                            >
                            {{-- ダーク: 通常は hidden、dark:block で表示 --}}
                            <img
                                src="{{ asset('storage/' . $themeSettings->headerLogoDarkPath) }}"
                                alt="{{ $logoText }}"
                                class="{{ $heightClass }} w-auto hidden dark:block"
                            >
                        @else
                            {{-- ダーク用未指定 → `dark:invert` fallback --}}
                            <img
                                src="{{ asset('storage/' . $themeSettings->headerLogoPath) }}"
                                alt="{{ $logoText }}"
                                class="{{ $heightClass }} w-auto dark:invert"
                            >
                        @endif
                        {{-- サイト名 --}}
                        <span class="text-xl font-bold text-gray-900 dark:text-white font-heading-header">
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
                        <span class="text-xl font-bold text-gray-900 dark:text-white font-heading-header">
                            {{ $logoText }}
                        </span>
                    @endif
                </a>
            </div>

            {{-- Desktop Navigation --}}
            <nav
                x-ref="desktopNav"
                class="hidden lg:flex items-center space-x-1 ml-auto"
                :class="{ '!hidden': forceHamburger }"
            >
                @forelse($navigationItems ?? [] as $item)
                    @if(!empty($item['children']))
                        {{-- メガメニュートリガー --}}
                        @php
                            $childCount = count($item['children']);
                            $isMenuGroup = ($item['source_type'] ?? '') === 'menu_group';
                            $columnsClass = $childCount <= 4 ? 'sm:columns-1' : ($childCount <= 8 ? 'sm:columns-2' : 'sm:columns-3');
                            $minWidthClass = $childCount <= 4 ? 'min-w-[20rem]' : ($childCount <= 8 ? 'min-w-[36rem]' : 'min-w-[52rem]');
                        @endphp
                        <div
                            class="relative"
                            x-data="{
                                open: false,
                                timer: null,
                                init() {
                                    this.$watch('open', (val) => {
                                        if (val) this.$nextTick(() => this.fitPanel());
                                    });
                                },
                                fitPanel() {
                                    const panel = this.$refs.panel;
                                    if (!panel) return;
                                    // 既定: 右揃え
                                    panel.style.right = '0';
                                    panel.style.left = 'auto';
                                    const rect = panel.getBoundingClientRect();
                                    const vw = window.innerWidth;
                                    const m = 8;
                                    if (rect.left < m) {
                                        // 左にはみ出している → 右オフセットを負にして右へずらす
                                        const shift = m - rect.left;
                                        panel.style.right = `${-shift}px`;
                                    } else if (rect.right > vw - m) {
                                        // 右にはみ出している → 右オフセットを正にして左へずらす
                                        const shift = rect.right - (vw - m);
                                        panel.style.right = `${shift}px`;
                                    }
                                }
                            }"
                            @mouseenter="clearTimeout(timer); open = true"
                            @mouseleave="timer = setTimeout(() => open = false, 150)"
                            @keydown.escape.prevent="open = false"
                            @resize.window.debounce.150ms="if (open) fitPanel()"
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
                                    @if(! empty($item['icon_class']))
                                        <i class="{{ $item['icon_class'] }} mr-2"></i>
                                    @endif
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
                                    @if(! empty($item['icon_class']))
                                        <i class="{{ $item['icon_class'] }} mr-2"></i>
                                    @endif
                                    {{ $item['label'] ?? '' }}
                                    <svg class="ml-1 w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </a>
                            @endif

                            {{-- メガメニューパネル --}}
                            <div
                                x-ref="panel"
                                x-show="open"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute right-0 mt-1 {{ $minWidthClass }} max-w-[calc(100vw-1rem)] bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-black/5 dark:ring-white/10 z-50"
                                role="menu"
                                x-cloak
                                @click.outside="open = false"
                                @focusout.debounce.150ms="if (!$el.contains(document.activeElement)) open = false"
                            >
                                <div class="columns-1 {{ $columnsClass }} gap-x-6 p-4">
                                    @foreach($item['children'] as $child)
                                        @php
                                            $childIsGroup = ($child['source_type'] ?? '') === 'menu_group';
                                            $hasGrandchildren = ! empty($child['children']);
                                        @endphp
                                        @if($childIsGroup && $hasGrandchildren)
                                            {{-- カテゴリー列: 2層目メニューグループ + 3層目アイテム --}}
                                            <div class="break-inside-avoid mb-4 last:mb-0 space-y-1">
                                                <div class="px-3 pt-1 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700/60">
                                                    @if(! empty($child['icon_class']))
                                                        <i class="{{ $child['icon_class'] }} fa-fw mr-1.5"></i>
                                                    @endif
                                                    {{ $child['label'] ?? '' }}
                                                </div>
                                                @foreach($child['children'] as $grandchild)
                                                    <a
                                                        href="{{ $grandchild['url'] ?? '#' }}"
                                                        target="{{ $grandchild['target'] ?? '_self' }}"
                                                        role="menuitem"
                                                        class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors"
                                                    >
                                                        @if(! empty($grandchild['icon_class']))
                                                            <i class="{{ $grandchild['icon_class'] }} fa-fw shrink-0 text-sm text-gray-400 dark:text-gray-500"></i>
                                                        @endif
                                                        <span>{{ $grandchild['label'] ?? '' }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            {{-- 単独リンク（2層目で子を持たないアイテム） --}}
                                            <a
                                                href="{{ $child['url'] ?? '#' }}"
                                                target="{{ $child['target'] ?? '_self' }}"
                                                role="menuitem"
                                                class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors break-inside-avoid mb-1 last:mb-0"
                                            >
                                                @if(! empty($child['icon_class']))
                                                    <i class="{{ $child['icon_class'] }} fa-fw shrink-0 text-sm text-gray-400 dark:text-gray-500"></i>
                                                @endif
                                                <span>{{ $child['label'] ?? '' }}</span>
                                            </a>
                                        @endif
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
                            @if(! empty($item['icon_class']))
                                <i class="{{ $item['icon_class'] }} mr-2"></i>
                            @endif
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

    {{-- Mobile Drawer: x-teleport で body 直下に転送し stacking context を脱出 --}}
    <template x-teleport="body">
        {{-- Mobile Drawer Overlay. Persistent visible state is encoded
             in the base class (opacity + backdrop-blur-sm); Alpine
             removes enter/enter-end classes once the transition ends,
             so anything that must stay while the drawer is open MUST
             live on the base element — otherwise it snaps off the
             moment the animation finishes. Only enter-start / leave-end
             define the animated-away state. `transition-all` covers the
             backdrop-filter change so the blur animates in and out.
             Same recipe as the core admin bar overlay (see
             components/ui-admin-bar.blade.php). --}}
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition-all ease-out duration-300"
            x-transition:enter-start="opacity-0 backdrop-blur-none"
            x-transition:leave="transition-all ease-in duration-200"
            x-transition:leave-end="opacity-0 backdrop-blur-none"
            class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm"
            style="top: var(--front-banner-stack-height, {{ $stackHeightFallback }})"
            :class="{ 'lg:hidden': !forceHamburger }"
            x-cloak
            @click="mobileMenuOpen = false"
        ></div>
    </template>

    <template x-teleport="body">
        {{-- Mobile Drawer Panel (右からスライド) --}}
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            {{-- Translucent + backdrop blur — same glass recipe the
                 site header and cookie banner use, so the drawer reads
                 as part of the same translucent-overlay vocabulary and
                 lets the darkened page underneath show through. --}}
            class="fixed right-0 z-[70] w-80 max-w-[85vw] bg-white/70 dark:bg-gray-900/60 backdrop-blur-md shadow-2xl"
            style="top: var(--front-banner-stack-height, {{ $stackHeightFallback }}); height: calc(100% - var(--front-banner-stack-height, {{ $stackHeightFallback }}));"
            :class="{ 'lg:hidden': !forceHamburger }"
            x-cloak
        >
        {{-- ドロワーヘッダー --}}
        <div class="flex items-center justify-between px-5 h-14 border-b border-gray-200 dark:border-gray-700">
            @if(!empty($navigationMenuName))
                <span class="text-base font-semibold text-gray-900 dark:text-white">{{ $navigationMenuName }}</span>
            @else
                <span></span>
            @endif
            <button
                @click="mobileMenuOpen = false"
                class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                aria-label="Close menu"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        {{-- ドロワーコンテンツ（スクロール可能） --}}
        <nav class="overflow-y-auto h-[calc(100%-3.5rem)] px-3 py-4 space-y-1">
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
                            <span class="flex items-center gap-2">
                                @if(! empty($item['icon_class']))
                                    <i class="{{ $item['icon_class'] }} fa-fw shrink-0 text-sm"></i>
                                @endif
                                {{ $item['label'] ?? '' }}
                            </span>
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
                                @php
                                    $childIsGroup = ($child['source_type'] ?? '') === 'menu_group';
                                    $hasGrandchildren = ! empty($child['children']);
                                @endphp
                                @if($childIsGroup && $hasGrandchildren)
                                    {{-- 2層目メニューグループ: ネストアコーディオン --}}
                                    <div x-data="{ subExpanded: false }">
                                        <button
                                            @click="subExpanded = !subExpanded"
                                            class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md"
                                            :aria-expanded="subExpanded"
                                        >
                                            <span class="flex items-center gap-2">
                                                @if(! empty($child['icon_class']))
                                                    <i class="{{ $child['icon_class'] }} fa-fw shrink-0 text-sm"></i>
                                                @endif
                                                {{ $child['label'] ?? '' }}
                                            </span>
                                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': subExpanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div x-show="subExpanded" x-collapse class="pl-4 space-y-1">
                                            @foreach($child['children'] as $grandchild)
                                                <a
                                                    href="{{ $grandchild['url'] ?? '#' }}"
                                                    target="{{ $grandchild['target'] ?? '_self' }}"
                                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-blue-400 rounded-md"
                                                    @click="mobileMenuOpen = false"
                                                >
                                                    @if(! empty($grandchild['icon_class']))
                                                        <i class="{{ $grandchild['icon_class'] }} fa-fw shrink-0 text-sm"></i>
                                                    @endif
                                                    {{ $grandchild['label'] ?? '' }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    {{-- 単独リンク --}}
                                    <a
                                        href="{{ $child['url'] ?? '#' }}"
                                        target="{{ $child['target'] ?? '_self' }}"
                                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-blue-400 rounded-md"
                                        @click="mobileMenuOpen = false"
                                    >
                                        @if(! empty($child['icon_class']))
                                            <i class="{{ $child['icon_class'] }} fa-fw shrink-0 text-sm"></i>
                                        @endif
                                        {{ $child['label'] ?? '' }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- 通常のリンク --}}
                    <a
                        href="{{ $item['url'] ?? '#' }}"
                        target="{{ $item['target'] ?? '_self' }}"
                        class="flex items-center gap-2 px-4 py-3 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-blue-400 rounded-md"
                        @click="mobileMenuOpen = false"
                    >
                        @if(! empty($item['icon_class']))
                            <i class="{{ $item['icon_class'] }} fa-fw shrink-0 text-sm"></i>
                        @endif
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
        </nav>
        </div>
    </template>
</header>
