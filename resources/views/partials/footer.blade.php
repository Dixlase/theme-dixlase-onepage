@php
    // テーマ設定はServiceProviderから自動的に渡される
    $footerDescription = $themeSettings->footer_description ?? 'Powered by Dixlase CMS';
    $footerLinks = $themeSettings->footer_links ?? '[]';
    if (is_string($footerLinks)) {
        $footerLinks = json_decode($footerLinks, true) ?? [];
    }
    $footerCopyright = $themeSettings->footer_copyright ?? '© ' . date('Y') . ' ' . config('app.name', 'Dixlase') . '. All rights reserved.';
    // SNSリンクはServiceProviderで自動生成される
    $snsLinks = $themeSettings->snsLinks ?? [];
@endphp

<footer class="bg-[#12141C] pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 pb-8">
            {{-- About Section --}}
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-bold text-white mb-4">
                    {{ config('app.name', 'Dixlase') }}
                </h2>
                <p class="text-gray-400 mb-6 max-w-xs">
                    {{ $footerDescription }}
                </p>
                
                {{-- SNS Links --}}
                @if(array_filter($snsLinks))
                    <div class="flex space-x-4">
                        @if($snsLinks['facebook'])
                            <a href="{{ $snsLinks['facebook'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-500 transition-colors" aria-label="Facebook">
                                <i class="fa-brands fa-facebook text-xl"></i>
                            </a>
                        @endif
                        @if($snsLinks['x'])
                            <a href="{{ $snsLinks['x'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-500 transition-colors" aria-label="X (Twitter)">
                                <i class="fa-brands fa-x-twitter text-xl"></i>
                            </a>
                        @endif
                        @if($snsLinks['instagram'])
                            <a href="{{ $snsLinks['instagram'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-500 transition-colors" aria-label="Instagram">
                                <i class="fa-brands fa-instagram text-xl"></i>
                            </a>
                        @endif
                        @if($snsLinks['tiktok'])
                            <a href="{{ $snsLinks['tiktok'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-500 transition-colors" aria-label="TikTok">
                                <i class="fa-brands fa-tiktok text-xl"></i>
                            </a>
                        @endif
                        @if($snsLinks['bluesky'])
                            <a href="{{ $snsLinks['bluesky'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-500 transition-colors" aria-label="Bluesky">
                                <i class="fa-brands fa-bluesky text-xl"></i>
                            </a>
                        @endif
                        @if($snsLinks['threads'])
                            <a href="{{ $snsLinks['threads'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-500 transition-colors" aria-label="Threads">
                                <i class="fa-brands fa-threads text-xl"></i>
                            </a>
                        @endif
                        @if($snsLinks['linkedin'])
                            <a href="{{ $snsLinks['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-500 transition-colors" aria-label="LinkedIn">
                                <i class="fa-brands fa-linkedin text-xl"></i>
                            </a>
                        @endif
                        @if($snsLinks['youtube'])
                            <a href="{{ $snsLinks['youtube'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-500 transition-colors" aria-label="YouTube">
                                <i class="fa-brands fa-youtube text-xl"></i>
                            </a>
                        @endif
                        @if($snsLinks['pinterest'])
                            <a href="{{ $snsLinks['pinterest'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-500 transition-colors" aria-label="Pinterest">
                                <i class="fa-brands fa-pinterest text-xl"></i>
                            </a>
                        @endif
                        @if($snsLinks['discord'])
                            <a href="{{ $snsLinks['discord'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-500 transition-colors" aria-label="Discord">
                                <i class="fa-brands fa-discord text-xl"></i>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Links Sections --}}
            @if(!empty($footerLinks))
                @php
                    $linkChunks = array_chunk($footerLinks, ceil(count($footerLinks) / 3));
                @endphp
                @foreach($linkChunks as $index => $chunk)
                    <div>
                        <ul class="space-y-2">
                            @foreach($chunk as $link)
                                <li>
                                    <a href="{{ $link['url'] ?? '#' }}" class="text-gray-400 hover:text-purple-500 transition-colors">
                                        {{ $link['title'] ?? '' }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Copyright --}}
        <div class="border-t border-white/10 pt-8 flex justify-center">
            <p class="text-gray-400 text-sm mb-4 md:mb-0">
                {{ $footerCopyright }}
            </p>
        </div>
    </div>
</footer>
