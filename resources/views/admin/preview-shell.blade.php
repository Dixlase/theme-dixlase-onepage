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

{{-- Preview theme styles (isolated from admin dark mode) --}}
<style @cspNonce>
#preview-inner[data-preview-theme="dark"] { background: #030712 !important; }
#preview-inner[data-preview-theme="dark"] .pv-header { background: rgba(17,24,39,0.8) !important; }
#preview-inner[data-preview-theme="dark"] .pv-app-name { color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-hero { background: #030712 !important; }
#preview-inner[data-preview-theme="dark"] .pv-glow-1 { background: rgba(107,114,128,0.3) !important; }
#preview-inner[data-preview-theme="dark"] .pv-glow-2 { background: rgba(156,163,175,0.3) !important; }
#preview-inner[data-preview-theme="dark"] .pv-title span { background-image: linear-gradient(to right, #9ca3af, #9ca3af, #fff) !important; -webkit-background-clip: text !important; background-clip: text !important; color: transparent !important; }
#preview-inner[data-preview-theme="dark"] .pv-subtitle { color: #d1d5db !important; }
#preview-inner[data-preview-theme="dark"] .pv-btn-secondary { border-color: #374151 !important; color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-nav-item { color: #9ca3af !important; }
#preview-inner[data-preview-theme="dark"] .pv-content { background: #111827 !important; }
#preview-inner[data-preview-theme="dark"] .pv-content-text { color: #d1d5db !important; }
#preview-inner[data-preview-theme="dark"] .pv-content-text h1,
#preview-inner[data-preview-theme="dark"] .pv-content-text h2,
#preview-inner[data-preview-theme="dark"] .pv-content-text h3 { color: #f3f4f6 !important; }
#preview-inner[data-preview-theme="dark"] .pv-contact { background: #111827 !important; border-color: #1f2937 !important; }
#preview-inner[data-preview-theme="dark"] .pv-contact-title { color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-contact-desc { color: #9ca3af !important; }
#preview-inner[data-preview-theme="dark"] .pv-contact-field { background: #374151 !important; }
#preview-inner[data-preview-theme="dark"] .pv-required-badge { background: rgba(127,29,29,0.8) !important; color: #fecaca !important; }
#preview-inner[data-preview-theme="dark"] .pv-footer { background: #12141C !important; }
#preview-inner[data-preview-theme="dark"] .pv-footer-title { color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-footer-text { color: #9ca3af !important; }
#preview-inner[data-preview-theme="dark"] .pv-footer-border { border-color: rgba(255,255,255,0.1) !important; }

#preview-inner[data-preview-theme="light"] { background: #fff !important; }
#preview-inner[data-preview-theme="light"] .pv-header { background: rgba(255,255,255,0.9) !important; box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important; }
#preview-inner[data-preview-theme="light"] .pv-app-name { color: #111827 !important; }
#preview-inner[data-preview-theme="light"] .pv-hero { background: #f3f4f6 !important; }
#preview-inner[data-preview-theme="light"] .pv-glow-1 { background: rgba(216,180,254,0.3) !important; }
#preview-inner[data-preview-theme="light"] .pv-glow-2 { background: rgba(147,197,253,0.3) !important; }
#preview-inner[data-preview-theme="light"] .pv-title span { background-image: linear-gradient(to right, #374151, #374151, #111827) !important; -webkit-background-clip: text !important; background-clip: text !important; color: transparent !important; }
#preview-inner[data-preview-theme="light"] .pv-subtitle { color: #4b5563 !important; }
#preview-inner[data-preview-theme="light"] .pv-btn-secondary { border-color: #d1d5db !important; color: #111827 !important; }
#preview-inner[data-preview-theme="light"] .pv-nav-item { color: #6b7280 !important; }
#preview-inner[data-preview-theme="light"] .pv-content { background: #fff !important; }
#preview-inner[data-preview-theme="light"] .pv-content-text { color: #374151 !important; }
#preview-inner[data-preview-theme="light"] .pv-content-text h1,
#preview-inner[data-preview-theme="light"] .pv-content-text h2,
#preview-inner[data-preview-theme="light"] .pv-content-text h3 { color: #111827 !important; }
#preview-inner[data-preview-theme="light"] .pv-contact { background: #f9fafb !important; border-color: #e5e7eb !important; }
#preview-inner[data-preview-theme="light"] .pv-contact-title { color: #111827 !important; }
#preview-inner[data-preview-theme="light"] .pv-contact-desc { color: #6b7280 !important; }
#preview-inner[data-preview-theme="light"] .pv-contact-field { background: #e5e7eb !important; }
#preview-inner[data-preview-theme="light"] .pv-required-badge { background: #fee2e2 !important; color: #991b1b !important; }
#preview-inner[data-preview-theme="light"] .pv-footer { background: #f3f4f6 !important; }
#preview-inner[data-preview-theme="light"] .pv-footer-title { color: #111827 !important; }
#preview-inner[data-preview-theme="light"] .pv-footer-text { color: #4b5563 !important; }
#preview-inner[data-preview-theme="light"] .pv-footer-border { border-color: #d1d5db !important; }

#preview-inner section { border: none !important; border-radius: 0 !important; margin-bottom: 0 !important; box-shadow: none !important; padding: 0 !important; background-color: transparent !important; }
#preview-inner .pv-hero { min-height: 400px !important; padding: 0 !important; }
#preview-inner .pv-content { padding: 3rem 0 !important; }
#preview-inner .pv-contact { padding: 4rem 0 !important; }
#preview-inner .pv-footer { padding: 3rem 0 1.5rem !important; }
</style>

@php
    $shellSettings = $themeSettings ?? null;
    $shellAppearanceMode = $shellSettings->appearance_mode ?? '0';

    // ロゴ
    $shellLogoPath = null;
    if (!empty($shellSettings->header_logo_id)) {
        $shellLogo = \App\Models\Media::find($shellSettings->header_logo_id);
        if ($shellLogo) {
            $shellLogoPath = asset('storage/' . config('admin.files.mediaPath', 'media') . '/' . $shellLogo->path);
        }
    }

    // ヒーロー背景画像
    $shellHeroBgPath = null;
    if (!empty($shellSettings->hero_background_image_id)) {
        $shellHeroBg = \App\Models\Media::find($shellSettings->hero_background_image_id);
        if ($shellHeroBg) {
            $shellHeroBgPath = asset('storage/' . config('admin.files.mediaPath', 'media') . '/' . $shellHeroBg->path);
        }
    }

    // ヒーロー設定
    $shellHeroTitle = $shellSettings->hero_main_title ?? 'Welcome to ' . config('app.name');
    $shellHeroSubTitle = $shellSettings->hero_sub_title ?? '';
    $shellHeroButtonText = $shellSettings->hero_button_text ?? '';
    $shellHeroButtonSecondaryText = $shellSettings->hero_button_secondary_text ?? '';

    // メニュー・フッター
    $shellNavigationItems = $navigationItems ?? [];
    $shellFooterMenuItems = $footerMenuItems ?? [];
    $shellSnsLinks = $snsLinks ?? $shellSettings->snsLinks ?? [];
    $shellCopyright = $shellSettings->footer_copyright ?? '© ' . date('Y') . ' ' . config('app.name') . ' and Dixlase contributors';

    // お問い合わせ
    $shellShowInquiry = ($shellSettings->show_inquiry_form ?? '0') === '1';
    $shellInquiryPreview = null;
    if ($shellShowInquiry && \App\Helpers\PluginHelper::isEnabled('dixlase-inquiry')) {
        $resolver = app(\App\Services\Plugin\PluginServiceResolver::class);
        $result = $resolver->resolve(\App\Contracts\PluginIntegration\PreviewProviderInterface::class, 'dixlase-inquiry');
        if ($result->resolved && $result->instance) {
            $shellInquiryPreview = $result->instance->getPreview('inquiry_form');
        }
    }
@endphp

<div x-data="{ ...previewContainerMixin(), previewDevice: 'desktop', previewDeviceWidth: 1440 }" x-init="initPreviewContainer()">
    <x-admin.theme-preview-container
        :title="__('common.preview')"
        :appearanceMode="$shellAppearanceMode"
    >
        {{-- ===== HEADER ===== --}}
        <header class="pv-header h-14 w-full flex items-center backdrop-blur-md shadow-lg px-4">
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

        {{-- ===== HERO ===== --}}
        <section class="pv-hero relative min-h-[400px] flex flex-col justify-center overflow-hidden">
            <div class="absolute inset-0 overflow-hidden z-0">
                <div class="pv-glow-1 absolute top-1/4 left-10 w-72 h-72 rounded-full filter blur-3xl"></div>
                <div class="pv-glow-2 absolute bottom-2/4 right-20 w-96 h-96 rounded-full filter blur-3xl"></div>
            </div>
            @if($shellHeroBgPath)
            <div class="absolute inset-0 z-0">
                <img src="{{ $shellHeroBgPath }}" alt="" class="w-full h-full object-cover opacity-40">
            </div>
            @endif
            <div class="container mx-auto px-8 py-16 relative z-10">
                <div class="lg:w-1/2">
                    <h1 class="pv-title text-5xl lg:text-6xl font-bold leading-tight mb-4">
                        <span>{{ $shellHeroTitle }}</span>
                    </h1>
                    @if($shellHeroSubTitle)
                    <p class="pv-subtitle text-lg max-w-lg mb-6">{{ $shellHeroSubTitle }}</p>
                    @endif
                    <div class="flex gap-4">
                        @if($shellHeroButtonText)
                        <span class="inline-flex items-center gap-2 h-11 rounded-xl bg-purple-600 text-white px-8 py-6">
                            {{ $shellHeroButtonText }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                        </span>
                        @endif
                        @if($shellHeroButtonSecondaryText)
                        <span class="pv-btn-secondary inline-flex items-center gap-2 h-11 rounded-xl px-8 border py-6">
                            {{ $shellHeroButtonSecondaryText }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== CONTENT ===== --}}
        @if(!empty($previewContent))
        <section class="pv-content py-12">
            <div class="container mx-auto px-8">
                <div class="prose max-w-none pv-content-text">
                    {!! $previewContent !!}
                </div>
            </div>
        </section>
        @endif

        {{-- ===== INQUIRY FORM ===== --}}
        @if($shellShowInquiry && $shellInquiryPreview)
        <section class="pv-contact py-16 border-t">
            <div class="container mx-auto px-8 text-center">
                <h2 class="pv-contact-title text-3xl font-bold mb-3">{{ $shellInquiryPreview->title }}</h2>
                @if($shellInquiryPreview->description)
                <p class="pv-contact-desc mb-8 max-w-lg mx-auto">{{ $shellInquiryPreview->description }}</p>
                @endif
                <div class="max-w-md mx-auto space-y-3 text-left">
                    @if($shellInquiryPreview->isForm())
                        @php $renderedGroups = []; $fieldsByGroup = $shellInquiryPreview->getFieldsByGroup(); @endphp
                        @foreach($shellInquiryPreview->fields as $field)
                            @if($field->group)
                                @if(!in_array($field->group, $renderedGroups))
                                    @php $renderedGroups[] = $field->group; $groupFields = $fieldsByGroup[$field->group]; @endphp
                                    <div>
                                        <div class="pv-contact-desc text-xs mb-1">{{ $groupFields[0]->label }}@if($groupFields[0]->required) <span class="pv-required-badge inline-flex items-center px-1.5 py-0.5 ml-1 text-[10px] font-medium rounded">{{ __('common.required') }}</span>@endif</div>
                                        <div class="flex gap-2">
                                            @foreach($groupFields as $gf)
                                                <div class="flex-1"><div class="pv-contact-field h-10 rounded-md"></div></div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @elseif($field->type === 'radio_card')
                                <div>
                                    <div class="pv-contact-desc text-xs mb-1">{{ $field->label }}@if($field->required) <span class="pv-required-badge inline-flex items-center px-1.5 py-0.5 ml-1 text-[10px] font-medium rounded">{{ __('common.required') }}</span>@endif</div>
                                    <div class="flex gap-2 flex-wrap">
                                        @foreach($field->options as $val => $optLabel)
                                            <div class="pv-contact-field h-8 rounded-md px-3 flex items-center text-xs flex-1">
                                                <span class="pv-contact-desc">{{ $optLabel }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($field->type === 'checkbox')
                                <div class="flex items-start gap-2 mt-2">
                                    <div class="pv-contact-field w-4 h-4 rounded border flex-shrink-0 mt-0.5"></div>
                                    <span class="pv-contact-desc text-xs leading-relaxed">{!! $field->label !!}@if($field->required) <span class="pv-required-badge inline-flex items-center px-1.5 py-0.5 ml-1 text-[10px] font-medium rounded">{{ __('common.required') }}</span>@endif</span>
                                </div>
                            @else
                                <div>
                                    <div class="pv-contact-desc text-xs mb-1">{{ $field->label }}@if($field->required) <span class="pv-required-badge inline-flex items-center px-1.5 py-0.5 ml-1 text-[10px] font-medium rounded">{{ __('common.required') }}</span>@endif</div>
                                    <div class="pv-contact-field {{ $field->type === 'textarea' ? 'h-24' : 'h-10' }} rounded-md"></div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                    <div class="text-center mt-2">
                        <span class="inline-flex items-center h-10 px-6 bg-blue-600 rounded-md text-white text-sm font-medium">
                            @if($shellInquiryPreview->submitIcon)<i class="{{ $shellInquiryPreview->submitIcon }} mr-1"></i>@endif
                            {{ $shellInquiryPreview->submitLabel ?? 'Send' }}
                        </span>
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- ===== FOOTER ===== --}}
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
                        @foreach(['instagram' => 'fa-instagram', 'x' => 'fa-x-twitter', 'facebook' => 'fa-facebook', 'tiktok' => 'fa-tiktok', 'bluesky' => 'fa-bluesky', 'threads' => 'fa-threads', 'linkedin' => 'fa-linkedin', 'youtube' => 'fa-youtube', 'pinterest' => 'fa-pinterest', 'discord' => 'fa-discord', 'github' => 'fa-github'] as $platform => $icon)
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
