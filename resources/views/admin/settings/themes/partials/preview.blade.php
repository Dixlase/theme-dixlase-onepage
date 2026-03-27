{{--
This file is part of Dixlase.

Copyright (C) 2025 exc-D inc.
https://exc-d.com

Theme settings preview - Scaled container rendering of header + hero + footer
--}}

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
    <div class="rounded-xl overflow-hidden shadow-2xl relative w-full border border-gray-200 dark:border-gray-700" id="preview-outer" style="min-height: 400px;">
        <div class="absolute top-0 left-0 transition-[width] duration-300" id="preview-inner"
             :class="appearanceMode === '1' ? '' : 'dark'"
             :style="'width: ' + previewDeviceWidth + 'px; transform-origin: top left;'">

            {{-- ===== HEADER PREVIEW ===== --}}
            <header class="h-14 w-full flex items-center bg-white/80 dark:bg-gray-900/80 backdrop-blur-md shadow-lg dark:shadow-gray-700/10 px-6">
                <div class="flex items-center justify-between w-full">
                    {{-- Logo --}}
                    <div class="flex items-center space-x-3 cursor-pointer group" @click="$refs.headerLogoPickerTrigger && $refs.headerLogoPickerTrigger.click()">
                        <template x-if="headerLogoPreviewUrl">
                            <img :src="headerLogoPreviewUrl" alt="Logo" class="h-8 w-auto">
                        </template>
                        <template x-if="!headerLogoPreviewUrl">
                            <svg class="h-8 w-auto" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="40" height="40" rx="8" class="fill-blue-600"/>
                                <text x="20" y="28" class="fill-white font-bold text-2xl" font-family="system-ui, sans-serif" text-anchor="middle">D</text>
                            </svg>
                        </template>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">{{ config('app.name', 'Dixlase') }}</span>
                        <span class="opacity-0 group-hover:opacity-100 text-xs text-blue-400 transition-opacity">
                            <i class="fas fa-camera"></i>
                        </span>
                    </div>

                    {{-- Navigation placeholder --}}
                    <nav class="flex items-center space-x-4">
                        <span class="px-3 py-1 text-sm text-gray-500">Home</span>
                        <span class="px-3 py-1 text-sm text-gray-500">About</span>
                        <span class="px-3 py-1 text-sm text-gray-500">Contact</span>
                    </nav>
                </div>
            </header>

            {{-- ===== HERO PREVIEW ===== --}}
            <section class="relative min-h-[600px] flex flex-col justify-center overflow-hidden bg-gray-100 dark:bg-gray-950">
                {{-- Background glow --}}
                <div class="absolute inset-0 overflow-hidden z-0">
                    <div class="absolute top-1/4 left-10 w-72 h-72 bg-purple-300/30 dark:bg-gray-500/30 rounded-full filter blur-3xl"></div>
                    <div class="absolute bottom-2/4 right-20 w-96 h-96 bg-blue-300/30 dark:bg-gray-400/30 rounded-full filter blur-3xl"></div>
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
                            <h1 class="text-5xl lg:text-6xl font-bold leading-tight" x-show="editing !== 'heroMainTitle'">
                                <span class="bg-gradient-to-r from-gray-700 via-gray-700 to-gray-900 dark:from-gray-400 dark:via-gray-400 dark:to-white bg-clip-text text-transparent" x-text="heroMainTitle"></span>
                            </h1>
                            <input
                                x-show="editing === 'heroMainTitle'"
                                x-model="heroMainTitle"
                                @click.away="stopEdit()"
                                @keydown.enter="stopEdit()"
                                x-ref="editHeroMainTitle"
                                x-effect="if (editing === 'heroMainTitle') $nextTick(() => $refs.editHeroMainTitle?.focus())"
                                type="text"
                                class="w-full text-5xl lg:text-6xl font-bold bg-white/10 border border-blue-400 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="editing !== 'heroMainTitle'">
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-pencil-alt"></i></span>
                            </div>
                        </div>

                        {{-- Sub Title --}}
                        <div class="relative group cursor-pointer mb-8" @click.stop="startEdit('heroSubTitle')">
                            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-lg" x-show="editing !== 'heroSubTitle'" x-text="heroSubTitle"></p>
                            <textarea
                                x-show="editing === 'heroSubTitle'"
                                x-model="heroSubTitle"
                                @click.away="stopEdit()"
                                x-ref="editHeroSubTitle"
                                x-effect="if (editing === 'heroSubTitle') $nextTick(() => $refs.editHeroSubTitle?.focus())"
                                rows="2"
                                class="w-full max-w-lg text-lg bg-white/10 border border-blue-400 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
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
                                    <input x-model="heroButtonText" type="text" placeholder="{{ __('themes::admin.settings.hero.button_text') }}" class="text-sm bg-white/10 border border-blue-400 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 w-40"
                                        x-ref="editHeroButton" x-effect="if (editing === 'heroButton') $nextTick(() => $refs.editHeroButton?.focus())">
                                    <input x-model="heroButtonLink" type="text" placeholder="{{ __('themes::admin.settings.hero.button_link') }}" class="text-sm bg-white/10 border border-blue-400 rounded-lg px-3 py-2 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 w-40">
                                </div>
                                <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="editing !== 'heroButton'">
                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>

                            {{-- Secondary button --}}
                            <div class="relative group cursor-pointer" @click.stop="startEdit('heroButtonSecondary')">
                                <div class="inline-flex items-center justify-center gap-2 h-11 rounded-xl px-8 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white py-6" x-show="editing !== 'heroButtonSecondary'">
                                    <span x-text="heroButtonSecondaryText || '{{ __('themes::admin.settings.hero.button_secondary_text') }}'"></span>
                                </div>
                                <div x-show="editing === 'heroButtonSecondary'" class="flex gap-2" @click.away="stopEdit()">
                                    <input x-model="heroButtonSecondaryText" type="text" placeholder="{{ __('themes::admin.settings.hero.button_secondary_text') }}" class="text-sm bg-white/10 border border-blue-400 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 w-40"
                                        x-ref="editHeroSecondary" x-effect="if (editing === 'heroButtonSecondary') $nextTick(() => $refs.editHeroSecondary?.focus())">
                                    <input x-model="heroButtonSecondaryLink" type="text" placeholder="{{ __('themes::admin.settings.hero.button_secondary_link') }}" class="text-sm bg-white/10 border border-blue-400 rounded-lg px-3 py-2 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 w-40">
                                </div>
                                <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="editing !== 'heroButtonSecondary'">
                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ===== FOOTER PREVIEW ===== --}}
            <footer class="bg-gray-100 dark:bg-[#12141C] pt-12 pb-6">
                <div class="container mx-auto px-8">
                    <div class="pb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ config('app.name', 'Dixlase') }}</h2>
                        {{-- Footer description --}}
                        <div class="relative group cursor-pointer" @click.stop="startEdit('footerDescription')">
                            <p class="text-gray-600 dark:text-gray-400 max-w-xs" x-show="editing !== 'footerDescription'" x-text="footerDescription"></p>
                            <input
                                x-show="editing === 'footerDescription'"
                                x-model="footerDescription"
                                @click.away="stopEdit()"
                                @keydown.enter="stopEdit()"
                                x-ref="editFooterDescription"
                                x-effect="if (editing === 'footerDescription') $nextTick(() => $refs.editFooterDescription?.focus())"
                                type="text"
                                class="w-full max-w-xs text-sm bg-white/10 border border-blue-400 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="editing !== 'footerDescription'">
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><i class="fas fa-pencil-alt"></i></span>
                            </div>
                        </div>
                    </div>
                    {{-- Copyright --}}
                    <div class="border-t border-gray-300 dark:border-white/10 pt-6 flex justify-center">
                        <p class="text-gray-600 dark:text-gray-400 text-sm" x-text="footerCopyright"></p>
                    </div>
                </div>
            </footer>

        </div>
    </div>
</div>
