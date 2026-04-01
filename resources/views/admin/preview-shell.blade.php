{{--
This file is part of Dixlase.

Copyright (C) 2025 exc-D inc.
https://exc-d.com

Read-only theme preview shell for use by core admin pages (front page master, etc.)
Provides header + content slot + footer structure with theme-aware styling.
--}}

{{-- Preview theme styles (isolated from admin dark mode) --}}
<style @cspNonce>
#preview-inner[data-preview-theme="dark"] { background: #030712 !important; }
#preview-inner[data-preview-theme="dark"] .pv-header { background: rgba(17,24,39,0.8) !important; }
#preview-inner[data-preview-theme="dark"] .pv-app-name { color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-nav-item { color: #9ca3af !important; }
#preview-inner[data-preview-theme="dark"] .pv-content { background: #111827 !important; }
#preview-inner[data-preview-theme="dark"] .pv-content-text { color: #d1d5db !important; }
#preview-inner[data-preview-theme="dark"] .pv-content-text h1,
#preview-inner[data-preview-theme="dark"] .pv-content-text h2,
#preview-inner[data-preview-theme="dark"] .pv-content-text h3 { color: #f3f4f6 !important; }
#preview-inner[data-preview-theme="dark"] .pv-footer { background: #12141C !important; }
#preview-inner[data-preview-theme="dark"] .pv-footer-title { color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-footer-text { color: #9ca3af !important; }
#preview-inner[data-preview-theme="dark"] .pv-footer-border { border-color: rgba(255,255,255,0.1) !important; }

#preview-inner[data-preview-theme="light"] { background: #fff !important; }
#preview-inner[data-preview-theme="light"] .pv-header { background: rgba(255,255,255,0.9) !important; box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important; }
#preview-inner[data-preview-theme="light"] .pv-app-name { color: #111827 !important; }
#preview-inner[data-preview-theme="light"] .pv-nav-item { color: #6b7280 !important; }
#preview-inner[data-preview-theme="light"] .pv-content { background: #fff !important; }
#preview-inner[data-preview-theme="light"] .pv-content-text { color: #374151 !important; }
#preview-inner[data-preview-theme="light"] .pv-content-text h1,
#preview-inner[data-preview-theme="light"] .pv-content-text h2,
#preview-inner[data-preview-theme="light"] .pv-content-text h3 { color: #111827 !important; }
#preview-inner[data-preview-theme="light"] .pv-footer { background: #f3f4f6 !important; }
#preview-inner[data-preview-theme="light"] .pv-footer-title { color: #111827 !important; }
#preview-inner[data-preview-theme="light"] .pv-footer-text { color: #4b5563 !important; }
#preview-inner[data-preview-theme="light"] .pv-footer-border { border-color: #d1d5db !important; }

#preview-inner section { border-color: transparent !important; }
#preview-inner[data-preview-theme="light"] section { background-color: transparent !important; border-color: transparent !important; }
#preview-inner[data-preview-theme="dark"] section { background-color: transparent !important; border-color: transparent !important; }
</style>

@php
    $shellSettings = $themeSettings ?? null;
    $shellAppearanceMode = $shellSettings->appearance_mode ?? '0';
    $shellLogoPath = null;
    if (!empty($shellSettings->header_logo_id)) {
        $shellLogo = \App\Models\Media::find($shellSettings->header_logo_id);
        if ($shellLogo) {
            $shellLogoPath = asset('storage/' . config('admin.files.mediaPath', 'media') . '/' . $shellLogo->path);
        }
    }
    $shellNavigationItems = $navigationItems ?? [];
    $shellFooterMenuItems = $footerMenuItems ?? [];
    $shellSnsLinks = $snsLinks ?? [];
    $shellCopyright = $shellSettings->footer_copyright ?? '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.';
@endphp

<div x-data="{ ...previewContainerMixin(), previewDevice: 'desktop', previewDeviceWidth: 1440 }" x-init="initPreviewContainer()">
    <x-admin.theme-preview-container
        :title="__('common.preview')"
        :appearanceMode="$shellAppearanceMode"
    >
        {{-- Header --}}
        <header class="pv-header h-14 w-full flex items-center backdrop-blur-md shadow-lg px-6">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center space-x-3">
                    @if($shellLogoPath)
                        <img src="{{ $shellLogoPath }}" alt="Logo" class="h-8 w-auto">
                    @else
                        <svg class="h-8 w-auto" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="40" height="40" rx="8" class="fill-blue-600"/>
                            <text x="20" y="28" class="fill-white font-bold text-2xl" font-family="system-ui, sans-serif" text-anchor="middle">D</text>
                        </svg>
                    @endif
                    <span class="pv-app-name text-xl font-bold">{{ config('app.name', 'Dixlase') }}</span>
                </div>
                @if(!empty($shellNavigationItems))
                <nav class="flex items-center space-x-4">
                    @foreach($shellNavigationItems as $navItem)
                        <span class="pv-nav-item px-3 py-1 text-sm">{{ $navItem['label'] ?? $navItem['title'] ?? '' }}</span>
                    @endforeach
                </nav>
                @endif
            </div>
        </header>

        {{-- Content slot --}}
        <section class="pv-content py-12">
            <div class="container mx-auto px-8">
                <div class="prose max-w-none pv-content-text">
                    {!! $previewContent ?? '' !!}
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="pv-footer pt-12 pb-6">
            <div class="container mx-auto px-8">
                <div class="flex flex-col items-center text-center pb-6 space-y-4">
                    @if(!empty($shellFooterMenuItems))
                    <div class="flex flex-wrap justify-center gap-4">
                        @foreach($shellFooterMenuItems as $item)
                            <span class="pv-footer-text text-sm">{{ $item['label'] ?? $item['title'] ?? '' }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if(!empty(array_filter($shellSnsLinks)))
                    <div class="flex flex-wrap justify-center gap-3">
                        @foreach(['instagram' => 'fa-instagram', 'x' => 'fa-x-twitter', 'facebook' => 'fa-facebook', 'tiktok' => 'fa-tiktok', 'youtube' => 'fa-youtube'] as $platform => $icon)
                            @if(!empty($shellSnsLinks[$platform]))
                                <span class="pv-footer-text text-lg"><i class="fab {{ $icon }}"></i></span>
                            @endif
                        @endforeach
                    </div>
                    @endif
                    <h2 class="pv-footer-title text-2xl font-bold">{{ config('app.name', 'Dixlase') }}</h2>
                    <p class="pv-footer-text">Powered by Dixlase</p>
                </div>
                <div class="pv-footer-border border-t pt-6 flex justify-center">
                    <p class="pv-footer-text text-sm">{{ $shellCopyright }}</p>
                </div>
            </div>
        </footer>
    </x-admin.theme-preview-container>
</div>
