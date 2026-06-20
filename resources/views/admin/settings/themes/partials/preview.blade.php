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

{{-- Preview theme styles (isolated from admin dark mode).
     Colors use Tailwind v4 CSS custom properties (var(--color-*)) so they
     resolve to the same oklch() values as the surrounding outer container.
     Mixing hardcoded v3 hex values here would create visible color seams
     between the inline mocks (header / hero / etc.) and the iframe area
     that inherits from #preview-outer's bg-gray-* utility. --}}
<style @cspNonce>
#preview-inner[data-preview-theme="dark"] { background: var(--color-gray-950) !important; }
#preview-inner[data-preview-theme="dark"] .pv-header { background: color-mix(in oklab, var(--color-gray-900) 80%, transparent) !important; }
#preview-inner[data-preview-theme="dark"] .pv-app-name { color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-title { color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-subtitle { color: var(--color-gray-300) !important; }
#preview-inner[data-preview-theme="dark"] .pv-btn-secondary { border-color: var(--color-gray-600) !important; color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-hero-overlay { background: color-mix(in oklab, var(--color-gray-900) 70%, transparent) !important; }
#preview-inner[data-preview-theme="dark"] .pv-edit-input { background: rgba(255,255,255,0.1) !important; color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-nav-item { color: var(--color-gray-400) !important; }
#preview-inner[data-preview-theme="dark"] .pv-contact { background: var(--color-gray-800) !important; border-color: var(--color-gray-800) !important; }
#preview-inner[data-preview-theme="dark"] .pv-contact-title { color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-contact-desc { color: var(--color-gray-400) !important; }
#preview-inner[data-preview-theme="dark"] .pv-contact-field { background: var(--color-gray-700) !important; }
#preview-inner[data-preview-theme="dark"] .pv-required-badge { background: color-mix(in oklab, var(--color-red-900) 80%, transparent) !important; color: var(--color-red-200) !important; }
#preview-inner[data-preview-theme="dark"] .pv-footer { background: var(--color-gray-950) !important; }
#preview-inner[data-preview-theme="dark"] .pv-footer-title { color: #fff !important; }
#preview-inner[data-preview-theme="dark"] .pv-footer-text { color: var(--color-gray-400) !important; }
#preview-inner[data-preview-theme="dark"] .pv-footer-border { border-color: rgba(255,255,255,0.1) !important; }

#preview-inner[data-preview-theme="light"] { background: #fff !important; }
#preview-inner[data-preview-theme="light"] .pv-header { background: rgba(255,255,255,0.9) !important; box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important; }
#preview-inner[data-preview-theme="light"] .pv-app-name { color: var(--color-gray-900) !important; }
#preview-inner[data-preview-theme="light"] .pv-title { color: var(--color-gray-900) !important; }
#preview-inner[data-preview-theme="light"] .pv-subtitle { color: var(--color-gray-600) !important; }
#preview-inner[data-preview-theme="light"] .pv-btn-secondary { border-color: var(--color-gray-300) !important; color: var(--color-gray-700) !important; }
#preview-inner[data-preview-theme="light"] .pv-hero-overlay { background: rgba(255,255,255,0.6) !important; }
#preview-inner[data-preview-theme="light"] .pv-edit-input { background: rgba(0,0,0,0.05) !important; color: var(--color-gray-900) !important; }
#preview-inner[data-preview-theme="light"] .pv-nav-item { color: var(--color-gray-500) !important; }
#preview-inner[data-preview-theme="light"] .pv-contact { background: var(--color-gray-100) !important; border-color: var(--color-gray-200) !important; }
#preview-inner[data-preview-theme="light"] .pv-contact-title { color: var(--color-gray-900) !important; }
#preview-inner[data-preview-theme="light"] .pv-contact-desc { color: var(--color-gray-500) !important; }
#preview-inner[data-preview-theme="light"] .pv-contact-field { background: var(--color-gray-200) !important; }
#preview-inner[data-preview-theme="light"] .pv-required-badge { background: var(--color-red-100) !important; color: var(--color-red-800) !important; }
#preview-inner[data-preview-theme="light"] .pv-footer { background: var(--color-gray-100) !important; }
#preview-inner[data-preview-theme="light"] .pv-footer-title { color: var(--color-gray-900) !important; }
#preview-inner[data-preview-theme="light"] .pv-footer-text { color: var(--color-gray-600) !important; }
#preview-inner[data-preview-theme="light"] .pv-footer-border { border-color: var(--color-gray-300) !important; }

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

/* Front content iframe must be fully transparent so the surrounding
   preview bg shows through, blending the section seamlessly into the
   hero / inquiry / footer mocks above and below. */
#preview-inner .pv-content-iframe,
#preview-inner .pv-content-iframe iframe {
    background: transparent !important;
    border: 0 !important;
    color-scheme: normal;
}
</style>

<x-admin.theme-preview-container
    :title="__('themes::admin.settings.editor.preview_title')"
    :appearanceMode="$settings->appearance_mode ?? '0'"
    aspectRatio="16:9"
>

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
                         @click.stop="window.__menuEditUrl = headerMenuEditUrl; openModal('editHeaderMenuModal')">
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
            {{-- Vertical alignment mirrors the front-end hero.blade.php:
                 with a foreground image set, the text block is pushed
                 toward the top (justify-start + top padding) so the
                 image has room; without one, content stays centered. --}}
            <section class="pv-hero relative min-h-[600px] flex flex-col overflow-hidden"
                :class="heroForegroundPreviewUrl
                    ? 'justify-start pt-20 md:pt-28 pb-12'
                    : 'justify-center'">
                {{-- 動画設定インジケーター --}}
                <template x-if="heroVideoPreviewUrl">
                    <div class="absolute top-4 left-4 z-20">
                        <span class="bg-purple-600 text-white text-xs px-3 py-1.5 rounded-full shadow-lg"><i class="fas fa-video mr-1"></i>{{ __('themes::admin.settings.hero.background_video') }}</span>
                    </div>
                </template>

                {{-- 背景画像 --}}
                <template x-if="heroBgPreviewUrl">
                    <div class="absolute inset-0 z-0">
                        <img :src="heroBgPreviewUrl" alt="" class="w-full h-full object-cover">
                        <div class="pv-hero-overlay absolute inset-0"></div>
                    </div>
                </template>

                {{-- 背景なし時の Astro 風 mid-bottom 寄せ放射グラデーション
                     （フロントと 1:1） --}}
                <template x-if="!heroBgPreviewUrl && !heroVideoPreviewUrl">
                    <div class="absolute inset-0 z-0 pointer-events-none"
                        :style="'background: ' +
                            'radial-gradient(ellipse 75% 85% at 70% 60%, ' + primaryColor + '80 0%, transparent 60%),' +
                            'radial-gradient(ellipse 65% 75% at 25% 75%, ' + primaryColor + '55 0%, transparent 65%);'"></div>
                </template>

                {{-- コンテンツ --}}
                <div class="container mx-auto px-8 py-20 relative z-10">
                    <div class="max-w-4xl mx-auto text-center">
                        {{-- Main Title — textarea で改行入力可、表示は white-space:pre-line で改行を反映 --}}
                        <div class="relative group cursor-pointer mb-6" @click.stop="startEdit('heroMainTitle')">
                            <h1 class="pv-title text-5xl lg:text-6xl font-bold leading-tight whitespace-pre-line" x-show="editing !== 'heroMainTitle'" x-text="heroMainTitle">
                            </h1>
                            <textarea
                                x-show="editing === 'heroMainTitle'"
                                x-model="heroMainTitle"
                                @click.away="stopEdit()"
                                x-ref="editHeroMainTitle"
                                x-effect="if (editing === 'heroMainTitle') $nextTick(() => $refs.editHeroMainTitle?.focus())"
                                rows="2"
                                class="pv-edit-input w-full text-5xl lg:text-6xl font-bold border border-blue-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                            ></textarea>
                            <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="editing !== 'heroMainTitle'">
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-pencil-alt"></i></span>
                            </div>
                        </div>

                        {{-- Sub Title --}}
                        <div class="relative group cursor-pointer mb-8" @click.stop="startEdit('heroSubTitle')">
                            <p class="pv-subtitle text-lg max-w-lg mx-auto whitespace-pre-line" x-show="editing !== 'heroSubTitle'" x-text="heroSubTitle"></p>
                            <textarea
                                x-show="editing === 'heroSubTitle'"
                                x-model="heroSubTitle"
                                @click.away="stopEdit()"
                                x-ref="editHeroSubTitle"
                                x-effect="if (editing === 'heroSubTitle') $nextTick(() => $refs.editHeroSubTitle?.focus())"
                                rows="2"
                                class="pv-edit-input w-full max-w-lg mx-auto text-lg border border-blue-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                            ></textarea>
                            <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="editing !== 'heroSubTitle'">
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-pencil-alt"></i></span>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex flex-col sm:flex-row justify-center gap-4">
                            {{-- Primary button --}}
                            <div x-show="heroButtonEnabled === '1'" class="relative group cursor-pointer" @click.stop="startEdit('heroButton')">
                                <div class="inline-flex items-center justify-center gap-2 h-11 rounded-xl text-white px-8 py-6" x-show="editing !== 'heroButton'" :style="'background-color: ' + primaryColor">
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
                            <div x-show="heroButtonSecondaryEnabled === '1'" class="relative group cursor-pointer" @click.stop="startEdit('heroButtonSecondary')">
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

                    {{-- 前景画像（テキスト/ボタンの下に置くスクリーンショットなど）
                         クリックで媒体ピッカーを直接開く。サイドバーにも同じ
                         picker があるが、プレビュー上でその画像をクリックすると
                         即差し替えできるという UX のため。 --}}
                    <template x-if="heroForegroundPreviewUrl">
                        <div class="mt-12 md:mt-16 max-w-5xl mx-auto relative group cursor-pointer"
                            @click.stop="openMediaSelector('hero_foreground_image_id_selector', 'hero_foreground_image_id', 'hero_foreground_image_id_preview', false, 'hero')">
                            <img :src="heroForegroundPreviewUrl" alt="" class="w-full h-auto rounded-2xl shadow-2xl">
                            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="bg-blue-500 text-white text-xs px-3 py-1.5 rounded-full shadow-lg"><i class="fas fa-image mr-1"></i>{{ __('themes::admin.settings.hero.select_foreground_image') }}</span>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            {{-- ===== FRONT PAGE CONTENT PREVIEW (iframe with custom CSS/JS) ===== --}}
            @if(!empty($frontContentPreview))
            <section class="pv-content-iframe relative">
                {{-- The iframe is same-origin (allow-same-origin), so we read
                     contentDocument.scrollHeight directly instead of relying on
                     a postMessage round-trip from the inner page.
                     Iframe element + body are kept transparent so the
                     surrounding preview bg shows through and the section
                     blends with the hero / inquiry / footer mocks instead of
                     looking like a white card. --}}
                <iframe x-ref="frontPreviewIframe"
                        src="{{ route('admin.front.preview-frame', ['bare' => 1]) }}"
                        @load="
                            const iframe = $event.target;
                            const sync = () => {
                                try {
                                    const doc = iframe.contentDocument;
                                    if (!doc) return;
                                    const h = Math.max(
                                        doc.documentElement ? doc.documentElement.scrollHeight : 0,
                                        doc.body ? doc.body.scrollHeight : 0
                                    );
                                    if (h > 0 && Math.abs(iframe.clientHeight - h) > 1) {
                                        iframe.style.height = h + 'px';
                                        $nextTick(() => updatePreviewScale());
                                    }
                                } catch (e) {}
                            };
                            sync();
                            try {
                                const doc = iframe.contentDocument;
                                if (doc && doc.body && typeof ResizeObserver !== 'undefined') {
                                    new ResizeObserver(sync).observe(doc.body);
                                }
                                if (doc && doc.fonts && doc.fonts.ready) {
                                    doc.fonts.ready.then(sync);
                                }
                            } catch (e) {}
                        "
                        class="w-full block border-0"
                        style="height: 600px; background: transparent;"
                        sandbox="allow-scripts allow-same-origin"
                        title="{{ __('themes::admin.settings.editor.preview_title') }}"></iframe>
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
                    @if(!empty($inquiryPreview?->title))
                        <h2 class="pv-contact-title text-3xl font-bold mb-3">{{ $inquiryPreview->title }}</h2>
                    @endif
                    @if(!empty($inquiryPreview?->description))
                        <p class="pv-contact-desc mb-8 max-w-lg mx-auto">{!! nl2br(e($inquiryPreview->description)) !!}</p>
                    @endif
                    @if(!($inquiryPreview?->meta['use_single_page'] ?? true))
                        {{-- 別ページモード: リンクボタンのみ --}}
                        <div class="text-center mt-4">
                            <span class="inline-flex items-center h-10 px-6 bg-blue-600 rounded-md text-white text-sm font-medium">
                                <i class="fas fa-paper-plane mr-1"></i>
                                {{ __('dixlase-inquiry::front.form.go_to_form') }}
                            </span>
                        </div>
                    @else
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
                                            <div class="pv-contact-desc text-xs mb-1">{{ $groupFields[0]->label }}@if($groupFields[0]->required) <span class="pv-required-badge inline-flex items-center px-1.5 py-0.5 ml-1 text-[10px] font-medium rounded">{{ __('common.required') }}</span>@endif</div>
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
                        @else
                            {{-- Fallback: static placeholders --}}
                            <div class="pv-contact-field h-10 rounded-md"></div>
                            <div class="pv-contact-field h-10 rounded-md"></div>
                            <div class="pv-contact-field h-24 rounded-md"></div>
                        @endif
                        <div class="text-center mt-2">
                            <span class="inline-flex items-center h-10 px-6 bg-blue-600 rounded-md text-white text-sm font-medium">
                                @if($inquiryPreview?->submitIcon)<i class="{{ $inquiryPreview->submitIcon }} mr-1"></i>@endif
                                {{ $inquiryPreview?->submitLabel ?? 'Send' }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </section>

            {{-- ===== FOOTER PREVIEW ===== --}}
            <footer class="pv-footer pt-12 pb-6">
                <div class="container mx-auto px-8">
                    <div class="flex flex-col items-center text-center pb-6 space-y-4">
                        {{-- Footer menu links --}}
                        <div class="pv-menu-area relative group/footernav cursor-pointer"
                             x-show="footerMenuId && allMenusData[footerMenuId]"
                             @click.stop="window.__menuEditUrl = footerMenuEditUrl; openModal('editFooterMenuModal')">
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
                        {{-- SNS Links --}}
                        <div class="flex flex-wrap justify-center gap-3"
                             x-show="snsLinks.instagram || snsLinks.x || snsLinks.facebook || snsLinks.tiktok || snsLinks.bluesky || snsLinks.threads || snsLinks.linkedin || snsLinks.youtube || snsLinks.pinterest || snsLinks.discord || snsLinks.github">
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
                            <template x-if="snsLinks.github"><a class="pv-footer-text text-lg hover:opacity-75"><i class="fab fa-github"></i></a></template>
                        </div>
                        {{-- Site Name --}}
                        <h2 class="pv-footer-title text-2xl font-bold">{{ config('app.name', 'Dixlase') }}</h2>
                        {{-- Description (fixed) --}}
                        <p class="pv-footer-text">Powered by Dixlase</p>
                    </div>
                    {{-- Copyright: `© <year>` is auto-rendered (and stays
                         current across year boundaries); only the suffix
                         after the year is user-editable. --}}
                    <div class="pv-footer-border border-t pt-6 flex justify-center">
                        <div class="relative group cursor-pointer" @click.stop="startEdit('footerCopyright')">
                            <p class="pv-footer-text text-sm" x-show="editing !== 'footerCopyright'">
                                © <span x-text="copyrightYear"></span> <span x-text="footerCopyright"></span>
                            </p>
                            <div x-show="editing === 'footerCopyright'"
                                 class="flex flex-col gap-1"
                                 @click.away="stopEdit()">
                                <div class="flex items-center gap-1.5">
                                    <span class="pv-footer-text text-sm whitespace-nowrap">© <span x-text="copyrightYear"></span></span>
                                    <input
                                        x-model="footerCopyright"
                                        @keydown.enter="stopEdit()"
                                        x-ref="editFooterCopyright"
                                        x-effect="if (editing === 'footerCopyright') $nextTick(() => $refs.editFooterCopyright?.focus())"
                                        type="text"
                                        class="pv-edit-input flex-1 min-w-0 text-sm border border-blue-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                </div>
                                <p class="pv-footer-text text-[11px] opacity-70 text-left">
                                    {{ __('themes::admin.settings.footer.copyright_help') }}
                                </p>
                            </div>
                            <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="editing !== 'footerCopyright'">
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-pencil-alt"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>

</x-admin.theme-preview-container>

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
                @click="window.location.href = window.__menuEditUrl"
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
                @click="window.location.href = window.__menuEditUrl"
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
