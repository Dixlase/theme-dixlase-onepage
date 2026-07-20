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
    // Copyright の `© <year>` は描画時に毎回現在の年で前置するので、
    // 保存値はそれ以降の編集可能サフィックスだけを期待する。古い
    // 保存値に `© 2026 ` 形式の prefix が残っていても二重表示にならない
    // よう、念のためここで剥がしてから前置し直す。
    $copyrightSuffix = $themeSettings->footer_copyright ?? config('app.name', 'Dixlase') . ' and Dixlase contributors';
    $copyrightSuffix = preg_replace('/^\s*©\s*\d{4}\s+/u', '', $copyrightSuffix);
    $footerCopyright = '© ' . date('Y') . ' ' . $copyrightSuffix;
    // SNSリンクはServiceProviderで自動生成される
    $snsLinks = $themeSettings->snsLinks ?? [];
@endphp

<footer class="bg-gray-100 dark:bg-gray-950 pt-16 pb-8">
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
                    @if($snsLinks['github'])
                        <a href="{{ $snsLinks['github'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-500 transition-colors" aria-label="GitHub">
                            <i class="fa-brands fa-github text-xl"></i>
                        </a>
                    @endif
                </div>
            @endif

            {{-- Site Name --}}
            <h2 class="font-heading-footer text-2xl font-bold text-gray-900 dark:text-white">
                {{ config('app.name', 'Dixlase') }}
            </h2>

            {{-- Platform + theme attribution.

                 Layout mirrors Core's <x-brand-attribution> on admin
                 auth screens: small Dixlase brand mark on top, Powered
                 by line below. <x-brand-logo> is a Core @api component
                 (SVG that inherits currentColor), sized to 20px so it
                 reads as an attribution glyph, not a hero mark — and
                 placed in the footer (not the header) so it can't be
                 mistaken for the site's own logo. `and` (rather than
                 the earlier `·`) reads naturally in a Powered-by line
                 that names two co-attribution targets. --}}
            <div class="flex flex-col items-center gap-2 text-gray-600 dark:text-gray-400">
                <x-brand-logo class="h-5 w-5" :aria-label="''" />
                <p>Powered by Dixlase and DixlaseOnePage</p>
            </div>
        </div>

    </div>

    {{-- Copyright — border-t spans the full window width. Placed outside
         the `.container` wrapper above so the rule isn't constrained by
         the container's max-width. `border-gray-200` (light) /
         `border-gray-800` (dark) keeps the line as a quiet separator,
         not a heavy rule. --}}
    <div class="border-t border-gray-200 dark:border-gray-800">
        <div class="container mx-auto px-4 pt-8 flex justify-center">
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 md:mb-0">
                {{ $footerCopyright }}
            </p>
        </div>
    </div>
</footer>
