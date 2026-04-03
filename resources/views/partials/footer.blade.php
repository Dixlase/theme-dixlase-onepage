@php
    // テーマ設定はServiceProviderから自動的に渡される
    $footerCopyright = $themeSettings->footer_copyright ?? '© ' . date('Y') . ' ' . config('app.name', 'Dixlase') . '. All rights reserved.';
    // SNSリンクはServiceProviderで自動生成される
    $snsLinks = $themeSettings->snsLinks ?? [];
@endphp

<footer class="bg-gray-100 dark:bg-[#12141C] pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col items-center text-center pb-8 space-y-6">
            {{-- Footer Menu (メニュープラグインから) --}}
            @if(!empty($footerMenuItems))
                @php
                    $hasHierarchy = collect($footerMenuItems)->contains(fn ($item) => !empty($item['children']));
                @endphp

                @if($hasHierarchy)
                    {{-- カラムグループ型: 階層メニューがある場合 --}}
                    <nav class="w-full">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 text-left">
                            @foreach($footerMenuItems as $item)
                                @if(!empty($item['children']))
                                    <div class="space-y-3">
                                        {{-- グループ見出し --}}
                                        @if(($item['source_type'] ?? '') === 'menu_group')
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                                                {{ $item['label'] ?? '' }}
                                            </h3>
                                        @else
                                            <h3 class="text-sm font-semibold uppercase tracking-wider">
                                                <a href="{{ $item['url'] ?? '#' }}" target="{{ $item['target'] ?? '_self' }}" class="text-gray-900 dark:text-white hover:text-purple-600 dark:hover:text-purple-500 transition-colors">
                                                    {{ $item['label'] ?? '' }}
                                                </a>
                                            </h3>
                                        @endif
                                        {{-- 子リンク --}}
                                        <ul class="space-y-2">
                                            @foreach($item['children'] as $child)
                                                <li>
                                                    <a href="{{ $child['url'] ?? '#' }}" target="{{ $child['target'] ?? '_self' }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors">
                                                        {{ $child['label'] ?? '' }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    {{-- 単体リンク --}}
                                    <div class="space-y-3">
                                        <h3 class="text-sm font-semibold uppercase tracking-wider">
                                            <a href="{{ $item['url'] ?? '#' }}" target="{{ $item['target'] ?? '_self' }}" class="text-gray-900 dark:text-white hover:text-purple-600 dark:hover:text-purple-500 transition-colors">
                                                {{ $item['label'] ?? '' }}
                                            </a>
                                        </h3>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </nav>
                @else
                    {{-- フラット型: 階層なしの場合は横並び --}}
                    <nav>
                        <ul class="flex flex-wrap justify-center gap-x-6 gap-y-2">
                            @foreach($footerMenuItems as $item)
                                <li>
                                    <a href="{{ $item['url'] ?? '#' }}" target="{{ $item['target'] ?? '_self' }}" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors text-sm">
                                        {{ $item['label'] ?? '' }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endif
            @endif

            {{-- SNS Links --}}
            @if(array_filter($snsLinks))
                <div class="flex space-x-4">
                    @if($snsLinks['facebook'])
                        <a href="{{ $snsLinks['facebook'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors" aria-label="Facebook">
                            <i class="fa-brands fa-facebook text-xl"></i>
                        </a>
                    @endif
                    @if($snsLinks['x'])
                        <a href="{{ $snsLinks['x'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors" aria-label="X (Twitter)">
                            <i class="fa-brands fa-x-twitter text-xl"></i>
                        </a>
                    @endif
                    @if($snsLinks['instagram'])
                        <a href="{{ $snsLinks['instagram'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors" aria-label="Instagram">
                            <i class="fa-brands fa-instagram text-xl"></i>
                        </a>
                    @endif
                    @if($snsLinks['tiktok'])
                        <a href="{{ $snsLinks['tiktok'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors" aria-label="TikTok">
                            <i class="fa-brands fa-tiktok text-xl"></i>
                        </a>
                    @endif
                    @if($snsLinks['bluesky'])
                        <a href="{{ $snsLinks['bluesky'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors" aria-label="Bluesky">
                            <i class="fa-brands fa-bluesky text-xl"></i>
                        </a>
                    @endif
                    @if($snsLinks['threads'])
                        <a href="{{ $snsLinks['threads'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors" aria-label="Threads">
                            <i class="fa-brands fa-threads text-xl"></i>
                        </a>
                    @endif
                    @if($snsLinks['linkedin'])
                        <a href="{{ $snsLinks['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors" aria-label="LinkedIn">
                            <i class="fa-brands fa-linkedin text-xl"></i>
                        </a>
                    @endif
                    @if($snsLinks['youtube'])
                        <a href="{{ $snsLinks['youtube'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors" aria-label="YouTube">
                            <i class="fa-brands fa-youtube text-xl"></i>
                        </a>
                    @endif
                    @if($snsLinks['pinterest'])
                        <a href="{{ $snsLinks['pinterest'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors" aria-label="Pinterest">
                            <i class="fa-brands fa-pinterest text-xl"></i>
                        </a>
                    @endif
                    @if($snsLinks['discord'])
                        <a href="{{ $snsLinks['discord'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors" aria-label="Discord">
                            <i class="fa-brands fa-discord text-xl"></i>
                        </a>
                    @endif
                </div>
            @endif

            {{-- Site Name --}}
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ config('app.name', 'Dixlase') }}
            </h2>

            {{-- Description --}}
            <p class="text-gray-600 dark:text-gray-400">
                Powered by Dixlase
            </p>
        </div>

        {{-- Copyright --}}
        <div class="border-t border-gray-300 dark:border-white/10 pt-8 flex justify-center">
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 md:mb-0">
                {{ $footerCopyright }}
            </p>
        </div>
    </div>
</footer>
