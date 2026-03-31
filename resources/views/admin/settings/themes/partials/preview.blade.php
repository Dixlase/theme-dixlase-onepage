{{--
This file is part of Dixlase.

Copyright (C) 2025 exc-D inc.
https://exc-d.com

Theme settings preview - Scaled container rendering of header + hero + footer
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
#preview-inner[data-preview-theme="dark"] .pv-edit-input { background: rgba(255,255,255,0.1) !important; color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-nav-item { color: #9ca3af !important; }
#preview-inner[data-preview-theme="dark"] .pv-contact { background: #111827 !important; border-color: #1f2937 !important; }
#preview-inner[data-preview-theme="dark"] .pv-contact-title { color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-contact-desc { color: #9ca3af !important; }
#preview-inner[data-preview-theme="dark"] .pv-contact-field { background: #374151 !important; }
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
#preview-inner[data-preview-theme="light"] .pv-btn-secondary { border-color: #d1d5db !important; color: #bfbfbf !important; }
#preview-inner[data-preview-theme="light"] .pv-edit-input { background: rgba(0,0,0,0.05) !important; color: #111827 !important; }
#preview-inner[data-preview-theme="light"] .pv-nav-item { color: #6b7280 !important; }
#preview-inner[data-preview-theme="light"] .pv-contact { background: #f9fafb !important; border-color: #e5e7eb !important; }
#preview-inner[data-preview-theme="light"] .pv-contact-title { color: #111827 !important; }
#preview-inner[data-preview-theme="light"] .pv-contact-desc { color: #6b7280 !important; }
#preview-inner[data-preview-theme="light"] .pv-contact-field { background: #e5e7eb !important; }
#preview-inner[data-preview-theme="light"] .pv-footer { background: #f3f4f6 !important; }
#preview-inner[data-preview-theme="light"] .pv-footer-title { color: #111827 !important; }
#preview-inner[data-preview-theme="light"] .pv-footer-text { color: #4b5563 !important; }
#preview-inner[data-preview-theme="light"] .pv-footer-border { border-color: #d1d5db !important; }

/* Front page content preview */
#preview-inner[data-preview-theme="dark"] .pv-content { background: #111827 !important; }
#preview-inner[data-preview-theme="dark"] .pv-content-text { color: #d1d5db !important; }
#preview-inner[data-preview-theme="dark"] .pv-content-text h1,
#preview-inner[data-preview-theme="dark"] .pv-content-text h2,
#preview-inner[data-preview-theme="dark"] .pv-content-text h3 { color: #f3f4f6 !important; }
#preview-inner[data-preview-theme="light"] .pv-content { background: #fff !important; }
#preview-inner[data-preview-theme="light"] .pv-content-text { color: #374151 !important; }
#preview-inner[data-preview-theme="light"] .pv-content-text h1,
#preview-inner[data-preview-theme="light"] .pv-content-text h2,
#preview-inner[data-preview-theme="light"] .pv-content-text h3 { color: #111827 !important; }

/* Hover badges for clickable preview sections */
.pv-hero .pv-edit-badge,
.pv-content .pv-edit-badge { opacity: 0; transition: opacity 0.2s ease; }
.pv-hero:hover .pv-edit-badge,
.pv-content:hover .pv-edit-badge { opacity: 1; }

/* Reset admin global section styles leaking into preview */
#preview-inner section { border-color: transparent !important; }
#preview-inner[data-preview-theme="light"] section { background-color: transparent !important; border-color: transparent !important; }
#preview-inner[data-preview-theme="dark"] section { background-color: transparent !important; border-color: transparent !important; }
</style>

<div class="relative">
    {{-- Preview Header --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
            <i class="fas fa-eye mr-1.5"></i>{{ __('themes::admin.settings.editor.preview_title') }}
        </h3>

        <div class="flex items-center gap-3">
            {{-- Device Toggle Buttons --}}
            <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded-lg p-0.5 gap-0.5">
                <button type="button" @click="setPreviewDevice('mobile')"
                    :class="previewDevice === 'mobile' ? 'bg-white dark:bg-gray-600 shadow-sm text-blue-600 dark:text-blue-400' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                    class="px-2.5 py-1.5 rounded-md transition-all text-xs" title="Mobile (375px)">
                    <i class="fas fa-mobile-alt"></i>
                </button>
                <button type="button" @click="setPreviewDevice('tablet')"
                    :class="previewDevice === 'tablet' ? 'bg-white dark:bg-gray-600 shadow-sm text-blue-600 dark:text-blue-400' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                    class="px-2.5 py-1.5 rounded-md transition-all text-xs" title="Tablet (768px)">
                    <i class="fas fa-tablet-alt"></i>
                </button>
                <button type="button" @click="setPreviewDevice('desktop')"
                    :class="previewDevice === 'desktop' ? 'bg-white dark:bg-gray-600 shadow-sm text-blue-600 dark:text-blue-400' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                    class="px-2.5 py-1.5 rounded-md transition-all text-xs" title="Desktop (1440px)">
                    <i class="fas fa-desktop"></i>
                </button>
            </div>

            <span class="text-xs text-gray-400 dark:text-gray-500" x-text="previewDeviceWidth + 'px'"></span>
        </div>
    </div>

    {{-- Scaled Preview Container --}}
    <div class="rounded-xl overflow-hidden shadow-2xl relative w-full border border-gray-100 dark:border-gray-700" id="preview-outer" style="min-height: 400px;">
        <div class="absolute top-0 left-0 transition-[width] duration-300" id="preview-inner"
             data-preview-theme="{{ ($settings->appearance_mode ?? '0') === '1' ? 'light' : 'dark' }}"
             @appearance-changed.window="$el.dataset.previewTheme = $event.detail.mode === '1' ? 'light' : 'dark'"
             :style="'width: ' + previewDeviceWidth + 'px; transform-origin: top left;'">

            {{-- ===== HEADER PREVIEW ===== --}}
            <header class="pv-header h-14 w-full flex items-center backdrop-blur-md shadow-lg px-6">
                <div class="flex items-center justify-between w-full">
                    {{-- Logo --}}
                    <div class="flex items-center space-x-3">
                        <div class="relative group cursor-pointer" @click="openMediaSelector('header_logo_id_selector', 'header_logo_id', 'header_logo_id_preview', false, 'original')">
                            <template x-if="headerLogoPreviewUrl">
                                <img :src="headerLogoPreviewUrl" alt="Logo" class="h-8 w-auto">
                            </template>
                            <template x-if="!headerLogoPreviewUrl">
                                <svg class="h-8 w-auto" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="40" height="40" rx="8" class="fill-blue-600"/>
                                    <text x="20" y="28" class="fill-white font-bold text-2xl" font-family="system-ui, sans-serif" text-anchor="middle">D</text>
                                </svg>
                            </template>
                            <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-image"></i></span>
                            </div>
                        </div>
                        <span class="pv-app-name text-xl font-bold">{{ config('app.name', 'Dixlase') }}</span>
                    </div>

                    {{-- Navigation - dynamic from selected menu --}}
                    <nav class="pv-menu-area relative group/headernav flex items-center space-x-4 cursor-pointer"
                         x-show="headerMenuId && allMenusData[headerMenuId]"
                         @click.stop="openModal('editHeaderMenuModal')">
                        {{-- Desktop/Tablet: show menu items --}}
                        <template x-if="previewDevice !== 'mobile'">
                            <template x-for="item in allMenusData[headerMenuId] || []" :key="item.title">
                                <span class="pv-nav-item px-3 py-1 text-sm" x-text="item.title"></span>
                            </template>
                        </template>
                        {{-- Mobile: show hamburger icon --}}
                        <template x-if="previewDevice === 'mobile'">
                            <span class="pv-nav-item text-xl cursor-pointer"><i class="fas fa-bars"></i></span>
                        </template>
                        {{-- Edit badge --}}
                        <div class="absolute -top-2 -right-2 opacity-0 group-hover/headernav:opacity-100 transition-opacity z-20">
                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full shadow-lg"><i class="fas fa-pencil-alt"></i></span>
                        </div>
                    </nav>
                </div>
            </header>

            {{-- ===== HERO PREVIEW ===== --}}
            <section class="pv-hero relative min-h-[600px] flex flex-col justify-center overflow-hidden cursor-pointer"
                @click="openMediaSelector('hero_background_image_id_selector', 'hero_background_image_id', 'hero_background_image_id_preview', false, 'hero')">
                {{-- Background edit indicator --}}
                <div class="pv-edit-badge absolute top-4 right-4 z-20">
                    <span class="bg-blue-500 text-white text-xs px-3 py-1.5 rounded-full shadow-lg"><i class="fas fa-image mr-1"></i>{{ __('themes::admin.settings.hero.select_background_image') }}</span>
                </div>
                {{-- Background glow --}}
                <div class="absolute inset-0 overflow-hidden z-0">
                    <div class="pv-glow-1 absolute top-1/4 left-10 w-72 h-72 rounded-full filter blur-3xl"></div>
                    <div class="pv-glow-2 absolute bottom-2/4 right-20 w-96 h-96 rounded-full filter blur-3xl"></div>
                </div>

                {{-- Hero background image --}}
                <template x-if="heroBgPreviewUrl">
                    <div class="absolute inset-0 z-0">
                        <img :src="heroBgPreviewUrl" alt="" class="w-full h-full object-cover opacity-40">
                    </div>
                </template>

                {{-- Content --}}
                <div class="container mx-auto px-8 py-20 relative z-10">
                    <div class="lg:w-1/2">
                        {{-- Main Title --}}
                        <div class="relative group cursor-pointer mb-6" @click.stop="startEdit('heroMainTitle')">
                            <h1 class="pv-title text-5xl lg:text-6xl font-bold leading-tight" x-show="editing !== 'heroMainTitle'">
                                <span x-text="heroMainTitle"></span>
                            </h1>
                            <input
                                x-show="editing === 'heroMainTitle'"
                                x-model="heroMainTitle"
                                @click.away="stopEdit()"
                                @keydown.enter="stopEdit()"
                                x-ref="editHeroMainTitle"
                                x-effect="if (editing === 'heroMainTitle') $nextTick(() => $refs.editHeroMainTitle?.focus())"
                                type="text"
                                class="pv-edit-input w-full text-5xl lg:text-6xl font-bold border border-blue-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="editing !== 'heroMainTitle'">
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-pencil-alt"></i></span>
                            </div>
                        </div>

                        {{-- Sub Title --}}
                        <div class="relative group cursor-pointer mb-8" @click.stop="startEdit('heroSubTitle')">
                            <p class="pv-subtitle text-lg max-w-lg" x-show="editing !== 'heroSubTitle'" x-text="heroSubTitle"></p>
                            <textarea
                                x-show="editing === 'heroSubTitle'"
                                x-model="heroSubTitle"
                                @click.away="stopEdit()"
                                x-ref="editHeroSubTitle"
                                x-effect="if (editing === 'heroSubTitle') $nextTick(() => $refs.editHeroSubTitle?.focus())"
                                rows="2"
                                class="pv-edit-input w-full max-w-lg text-lg border border-blue-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                            ></textarea>
                            <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="editing !== 'heroSubTitle'">
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-pencil-alt"></i></span>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-4">
                            {{-- Primary button --}}
                            <div class="relative group cursor-pointer" @click.stop="startEdit('heroButton')">
                                <div class="inline-flex items-center justify-center gap-2 h-11 rounded-xl bg-purple-600 text-white px-8 py-6" x-show="editing !== 'heroButton'">
                                    <span x-text="heroButtonText"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1 h-5 w-5"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                                </div>
                                <div x-show="editing === 'heroButton'" class="flex gap-2" @click.away="stopEdit()">
                                    <input x-model="heroButtonText" type="text" placeholder="{{ __('themes::admin.settings.hero.button_text') }}" class="pv-edit-input text-sm border border-blue-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-40"
                                        x-ref="editHeroButton" x-effect="if (editing === 'heroButton') $nextTick(() => $refs.editHeroButton?.focus())">
                                    <input x-model="heroButtonLink" type="text" placeholder="{{ __('themes::admin.settings.hero.button_link') }}" class="pv-edit-input text-sm border border-blue-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-40">
                                </div>
                                <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="editing !== 'heroButton'">
                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>

                            {{-- Secondary button --}}
                            <div class="relative group cursor-pointer" @click.stop="startEdit('heroButtonSecondary')">
                                <div class="pv-btn-secondary inline-flex items-center justify-center gap-2 h-11 rounded-xl px-8 border py-6" x-show="editing !== 'heroButtonSecondary'">
                                    <span x-text="heroButtonSecondaryText || '{{ __('themes::admin.settings.hero.button_secondary_text') }}'"></span>
                                </div>
                                <div x-show="editing === 'heroButtonSecondary'" class="flex gap-2" @click.away="stopEdit()">
                                    <input x-model="heroButtonSecondaryText" type="text" placeholder="{{ __('themes::admin.settings.hero.button_secondary_text') }}" class="pv-edit-input text-sm border border-blue-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-40"
                                        x-ref="editHeroSecondary" x-effect="if (editing === 'heroButtonSecondary') $nextTick(() => $refs.editHeroSecondary?.focus())">
                                    <input x-model="heroButtonSecondaryLink" type="text" placeholder="{{ __('themes::admin.settings.hero.button_secondary_link') }}" class="pv-edit-input text-sm border border-blue-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-40">
                                </div>
                                <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="editing !== 'heroButtonSecondary'">
                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ===== FRONT PAGE CONTENT PREVIEW ===== --}}
            @if(!empty($frontContentPreview))
            <section class="pv-content py-12 relative cursor-pointer"
                @click.stop="openModal('editFrontPageModal')">
                {{-- Edit badge --}}
                <div class="pv-edit-badge absolute top-4 right-4 z-20">
                    <span class="bg-blue-500 text-white text-xs px-3 py-1.5 rounded-full shadow-lg"><i class="fas fa-pencil-alt mr-1"></i>{{ __('themes::admin.settings.front_content.edit_badge') }}</span>
                </div>
                <div class="container mx-auto px-8">
                    <div class="prose max-w-none pv-content-text">
                        {!! $frontContentPreview !!}
                    </div>
                </div>
            </section>
            @endif

            {{-- ===== CONTACT FORM PREVIEW ===== --}}
            <section x-show="showInquiryForm === '1'" x-transition
                class="pv-contact py-16 border-t relative group/inquiry cursor-pointer"
                @click.stop="openModal('editInquiryFormModal')">
                {{-- Edit badge --}}
                <div class="absolute top-4 right-4 z-20 opacity-0 group-hover/inquiry:opacity-100 transition-opacity">
                    <span class="bg-blue-500 text-white text-xs px-3 py-1.5 rounded-full shadow-lg"><i class="fas fa-pencil-alt mr-1"></i>{{ __('themes::admin.settings.inquiry_form.edit_badge') }}</span>
                </div>
                <div class="container mx-auto px-8 text-center">
                    <h2 class="pv-contact-title text-3xl font-bold mb-3">
                        {{ $inquiryPreview?->title ?? __('themes::admin.settings.plugins.inquiry.title') }}
                    </h2>
                    @if($inquiryPreview?->description)
                        <p class="pv-contact-desc mb-8 max-w-lg mx-auto">{{ $inquiryPreview->description }}</p>
                    @endif
                    <div class="max-w-md mx-auto space-y-3 text-left">
                        @if($inquiryPreview?->isForm())
                            @php
                                $renderedGroups = [];
                                $fieldsByGroup = $inquiryPreview->getFieldsByGroup();
                            @endphp
                            @foreach($inquiryPreview->fields as $field)
                                @if($field->group)
                                    @if(!in_array($field->group, $renderedGroups))
                                        @php $renderedGroups[] = $field->group; @endphp
                                        @php $groupFields = $fieldsByGroup[$field->group]; @endphp
                                        <div>
                                            <div class="pv-contact-desc text-xs mb-1">{{ $groupFields[0]->label }}@if($groupFields[0]->required) <span class="text-red-500">*</span>@endif</div>
                                            <div class="flex gap-2">
                                                @foreach($groupFields as $gf)
                                                    <div class="flex-1">
                                                        <div class="pv-contact-field h-10 rounded-md"></div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @elseif($field->type === 'radio_card')
                                    <div>
                                        <div class="pv-contact-desc text-xs mb-1">{{ $field->label }}@if($field->required) <span class="text-red-500">*</span>@endif</div>
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
                                        <span class="pv-contact-desc text-xs leading-relaxed">{!! $field->label !!}@if($field->required) <span class="text-red-500">*</span>@endif</span>
                                    </div>
                                @else
                                    <div>
                                        <div class="pv-contact-desc text-xs mb-1">{{ $field->label }}@if($field->required) <span class="text-red-500">*</span>@endif</div>
                                        <div class="pv-contact-field {{ $field->type === 'textarea' ? 'h-24' : 'h-10' }} rounded-md"></div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            {{-- Fallback: static placeholders --}}
                            <div class="pv-contact-field h-10 rounded-md"></div>
                            <div class="pv-contact-field h-10 rounded-md"></div>
                            <div class="pv-contact-field h-24 rounded-md"></div>
                        @endif
                        <div class="h-10 bg-purple-600 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-medium">
                                @if($inquiryPreview?->submitIcon)<i class="{{ $inquiryPreview->submitIcon }} mr-1"></i>@endif
                                {{ $inquiryPreview?->submitLabel ?? 'Send' }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ===== FOOTER PREVIEW ===== --}}
            <footer class="pv-footer pt-12 pb-6">
                <div class="container mx-auto px-8">
                    <div class="flex flex-col items-center text-center pb-6 space-y-4">
                        {{-- Footer menu links --}}
                        <div class="pv-menu-area relative group/footernav cursor-pointer"
                             x-show="footerMenuId && allMenusData[footerMenuId]"
                             @click.stop="openModal('editFooterMenuModal')">
                            <div class="flex flex-wrap justify-center gap-4">
                                <template x-for="item in (footerMenuId && allMenusData[footerMenuId]) ? allMenusData[footerMenuId] : []" :key="item.title">
                                    <span class="pv-footer-text text-sm hover:underline cursor-default" x-text="item.title"></span>
                                </template>
                            </div>
                            {{-- Edit badge --}}
                            <div class="absolute -top-2 -right-2 opacity-0 group-hover/footernav:opacity-100 transition-opacity z-20">
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full shadow-lg"><i class="fas fa-pencil-alt"></i></span>
                            </div>
                        </div>
                        {{-- Site Name --}}
                        <h2 class="pv-footer-title text-2xl font-bold">{{ config('app.name', 'Dixlase') }}</h2>
                        {{-- Description (fixed) --}}
                        <p class="pv-footer-text">Powered by Dixlase</p>
                        {{-- SNS Links --}}
                        <div class="flex flex-wrap justify-center gap-3"
                             x-show="snsLinks.instagram || snsLinks.x || snsLinks.facebook || snsLinks.tiktok || snsLinks.bluesky || snsLinks.threads || snsLinks.linkedin || snsLinks.youtube || snsLinks.pinterest || snsLinks.discord">
                            <template x-if="snsLinks.instagram"><a class="pv-footer-text text-lg hover:opacity-75"><i class="fab fa-instagram"></i></a></template>
                            <template x-if="snsLinks.x"><a class="pv-footer-text text-lg hover:opacity-75"><i class="fab fa-x-twitter"></i></a></template>
                            <template x-if="snsLinks.facebook"><a class="pv-footer-text text-lg hover:opacity-75"><i class="fab fa-facebook"></i></a></template>
                            <template x-if="snsLinks.tiktok"><a class="pv-footer-text text-lg hover:opacity-75"><i class="fab fa-tiktok"></i></a></template>
                            <template x-if="snsLinks.bluesky"><a class="pv-footer-text text-lg hover:opacity-75"><i class="fab fa-bluesky"></i></a></template>
                            <template x-if="snsLinks.threads"><a class="pv-footer-text text-lg hover:opacity-75"><i class="fab fa-threads"></i></a></template>
                            <template x-if="snsLinks.linkedin"><a class="pv-footer-text text-lg hover:opacity-75"><i class="fab fa-linkedin"></i></a></template>
                            <template x-if="snsLinks.youtube"><a class="pv-footer-text text-lg hover:opacity-75"><i class="fab fa-youtube"></i></a></template>
                            <template x-if="snsLinks.pinterest"><a class="pv-footer-text text-lg hover:opacity-75"><i class="fab fa-pinterest"></i></a></template>
                            <template x-if="snsLinks.discord"><a class="pv-footer-text text-lg hover:opacity-75"><i class="fab fa-discord"></i></a></template>
                        </div>
                    </div>
                    {{-- Copyright --}}
                    <div class="pv-footer-border border-t pt-6 flex justify-center">
                        <div class="relative group cursor-pointer" @click.stop="startEdit('footerCopyright')">
                            <p class="pv-footer-text text-sm" x-show="editing !== 'footerCopyright'" x-text="footerCopyright"></p>
                            <input
                                x-show="editing === 'footerCopyright'"
                                x-model="footerCopyright"
                                @click.away="stopEdit()"
                                @keydown.enter="stopEdit()"
                                x-ref="editFooterCopyright"
                                x-effect="if (editing === 'footerCopyright') $nextTick(() => $refs.editFooterCopyright?.focus())"
                                type="text"
                                class="pv-edit-input w-full text-sm border border-blue-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="editing !== 'footerCopyright'">
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-pencil-alt"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
    </div>
</div>

{{-- フロントページ編集確認モーダル --}}
@if(!empty($frontContentPreview))
@push('modals')
    <x-ui-modal id="editFrontPageModal"
        :title="__('themes::admin.settings.front_content.confirm_title')"
        :message="__('themes::admin.settings.front_content.confirm_message')"
        :confirm-label="__('themes::admin.settings.front_content.confirm_ok')"
        :cancel-label="__('common.cancel')"
        icon-type="warning"
        confirm-color="blue"
        :form="null"
    >
        @slot('footer')
            <x-form-button
                type="button"
                variant="secondary"
                icon="fas fa-times"
                @click="close()"
                class="mx-2"
            >{{ __('common.cancel') }}</x-form-button>
            <x-form-button
                type="link"
                variant="primary"
                icon="fas fa-pencil-alt"
                :href="route('admin.front.edit')"
                class="mx-2"
            >{{ __('themes::admin.settings.front_content.confirm_ok') }}</x-form-button>
        @endslot
    </x-ui-modal>
@endpush
@endif

{{-- ヘッダーメニュー編集確認モーダル --}}
@if($menuPluginEnabled)
@push('modals')
    <x-ui-modal id="editHeaderMenuModal"
        :title="__('themes::admin.settings.header_menu.confirm_title')"
        :message="__('themes::admin.settings.header_menu.confirm_message')"
        :confirm-label="__('themes::admin.settings.header_menu.confirm_ok')"
        :cancel-label="__('common.cancel')"
        icon-type="warning"
        confirm-color="blue"
        :form="null"
    >
        @slot('footer')
            <x-form-button
                type="button"
                variant="secondary"
                icon="fas fa-times"
                @click="close()"
                class="mx-2"
            >{{ __('common.cancel') }}</x-form-button>
            <x-form-button
                type="button"
                variant="primary"
                icon="fas fa-pencil-alt"
                @click="window.location.href = headerMenuEditUrl"
                class="mx-2"
            >{{ __('themes::admin.settings.header_menu.confirm_ok') }}</x-form-button>
        @endslot
    </x-ui-modal>
@endpush
@endif

{{-- フッターメニュー編集確認モーダル --}}
@if($menuPluginEnabled)
@push('modals')
    <x-ui-modal id="editFooterMenuModal"
        :title="__('themes::admin.settings.footer_menu.confirm_title')"
        :message="__('themes::admin.settings.footer_menu.confirm_message')"
        :confirm-label="__('themes::admin.settings.footer_menu.confirm_ok')"
        :cancel-label="__('common.cancel')"
        icon-type="warning"
        confirm-color="blue"
        :form="null"
    >
        @slot('footer')
            <x-form-button
                type="button"
                variant="secondary"
                icon="fas fa-times"
                @click="close()"
                class="mx-2"
            >{{ __('common.cancel') }}</x-form-button>
            <x-form-button
                type="button"
                variant="primary"
                icon="fas fa-pencil-alt"
                @click="window.location.href = footerMenuEditUrl"
                class="mx-2"
            >{{ __('themes::admin.settings.footer_menu.confirm_ok') }}</x-form-button>
        @endslot
    </x-ui-modal>
@endpush
@endif

{{-- お問い合わせフォーム編集確認モーダル --}}
@if($inquiryPreview)
@push('modals')
    <x-ui-modal id="editInquiryFormModal"
        :title="__('themes::admin.settings.inquiry_form.confirm_title')"
        :message="__('themes::admin.settings.inquiry_form.confirm_message')"
        :confirm-label="__('themes::admin.settings.inquiry_form.confirm_ok')"
        :cancel-label="__('common.cancel')"
        icon-type="warning"
        confirm-color="blue"
        :form="null"
    >
        @slot('footer')
            <x-form-button
                type="button"
                variant="secondary"
                icon="fas fa-times"
                @click="close()"
                class="mx-2"
            >{{ __('common.cancel') }}</x-form-button>
            <x-form-button
                type="link"
                variant="primary"
                icon="fas fa-pencil-alt"
                :href="route('dixlase-inquiry::admin.inquiry.settings.form-basic')"
                class="mx-2"
            >{{ __('themes::admin.settings.inquiry_form.confirm_ok') }}</x-form-button>
        @endslot
    </x-ui-modal>
@endpush
@endif
