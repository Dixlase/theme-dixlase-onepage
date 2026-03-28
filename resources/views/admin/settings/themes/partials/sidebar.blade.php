{{--
This file is part of Dixlase.

Copyright (C) 2025 exc-D inc.
https://exc-d.com

Theme settings right sidebar - non-visual settings
--}}

{{-- Toggle Button --}}
<button type="button"
        @click="toggleRightSidebar()"
        class="flex fixed top-14 right-0 z-50 items-center backdrop-blur-sm dark:bg-gray-900/75 bg-white/75 text-blue-400 dark:text-white px-1.5 py-4 rounded-l-lg shadow-md border border-r-0 border-gray-300 dark:border-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800"
        :class="{
            'translate-x-0': rightSidebarCollapsed,
            '-translate-x-80': !rightSidebarCollapsed
        }"
        :style="rightSidebarReady ? 'transition: transform 200ms ease-in-out' : ''"
        :aria-label="rightSidebarCollapsed
            ? '{{ __('themes::admin.settings.editor.sidebar_open') }}'
            : '{{ __('themes::admin.settings.editor.sidebar_close') }}'">
    <i class="fas text-sm" :class="rightSidebarCollapsed ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
</button>

{{-- Right Sidebar --}}
<div class="space-y-5 fixed top-12 right-0 bottom-0 w-80 z-50 overflow-y-auto bg-white/75 dark:bg-gray-900/75 backdrop-blur-sm border-l border-gray-200 dark:border-gray-600 shadow-md px-5 py-5"
     :class="{
         'translate-x-80': rightSidebarCollapsed,
         'translate-x-0': !rightSidebarCollapsed
     }"
     :style="rightSidebarReady ? 'transition: transform 300ms ease-in-out' : ''">

    {{-- ===== Appearance Mode ===== --}}
    <div x-data="{ open: true }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            <span><i class="fas fa-palette mr-1.5"></i>{{ __('themes::admin.settings.appearance.title') }}</span>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse>
            <select name="appearance_mode"
                @change="$dispatch('appearance-changed', { mode: $event.target.value })"
                class="input-common block w-full max-w-full p-2 pr-10 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white">
                <option value="0" {{ old('appearance_mode', $settings->appearance_mode ?? '0') === '0' ? 'selected' : '' }}>{{ __('themes::admin.settings.appearance.mode_auto') }}</option>
                <option value="1" {{ old('appearance_mode', $settings->appearance_mode ?? '0') === '1' ? 'selected' : '' }}>{{ __('themes::admin.settings.appearance.mode_light') }}</option>
                <option value="2" {{ old('appearance_mode', $settings->appearance_mode ?? '0') === '2' ? 'selected' : '' }}>{{ __('themes::admin.settings.appearance.mode_dark') }}</option>
            </select>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('themes::admin.settings.appearance.mode_help') }}</p>
        </div>
    </div>

    <hr class="border-gray-200 dark:border-gray-700">

    {{-- ===== Favicon ===== --}}
    <div x-data="{ open: false }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            <span><i class="fas fa-globe mr-1.5"></i>{{ __('themes::admin.settings.header.favicon') }}</span>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse>
            <x-media.picker
                name="favicon_id"
                :value="$settings->favicon_id ?? null"
                :media="$favicon ?? null"
                :help="__('themes::admin.settings.header.favicon_help')"
                :error="$errors->first('favicon_id')"
                aspectRatio="square"
                :buttonText="__('themes::admin.settings.select_favicon_image')"
            />
        </div>
    </div>

    <hr class="border-gray-200 dark:border-gray-700">

    {{-- ===== Header Logo ===== --}}
    <div x-data="{ open: false }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            <span><i class="fas fa-heading mr-1.5"></i>{{ __('themes::admin.settings.header.header_logo') }}</span>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse>
            <x-media.picker
                name="header_logo_id"
                :value="$settings->header_logo_id ?? null"
                :media="$headerLogo ?? null"
                :help="__('themes::admin.settings.header.header_logo_help')"
                :error="$errors->first('header_logo_id')"
                :buttonText="__('themes::admin.settings.select_logo_image')"
            />
        </div>
    </div>

    <hr class="border-gray-200 dark:border-gray-700">

    {{-- ===== Header Menu (Plugin: DixlaseMenus) ===== --}}
    @if($menuPluginEnabled)
    <div x-data="{ open: false }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            <span><i class="fas fa-bars mr-1.5"></i>{{ __('themes::admin.settings.plugins.menu.title') }}</span>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse>
            <select name="header_menu_id" x-model="headerMenuId"
                class="input-common block w-full max-w-full p-2 pr-10 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white">
                @foreach($menuOptions as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('themes::admin.settings.plugins.menu.help') }}</p>
        </div>
    </div>

    <hr class="border-gray-200 dark:border-gray-700">
    @endif

    {{-- ===== Hero Background Image ===== --}}
    <div x-data="{ open: true }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            <span><i class="fas fa-image mr-1.5"></i>{{ __('themes::admin.settings.hero.background_image') }}</span>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse>
            <x-media.picker
                name="hero_background_image_id"
                :value="$settings->hero_background_image_id ?? null"
                :media="$heroBackgroundImage ?? null"
                :help="__('themes::admin.settings.hero.background_image_help')"
                :error="$errors->first('hero_background_image_id')"
                aspectRatio="hero"
                :buttonText="__('themes::admin.settings.hero.select_background_image')"
            />
        </div>
    </div>

    <hr class="border-gray-200 dark:border-gray-700">

    {{-- ===== Contact Form (Plugin: DixlaseInquiry) ===== --}}
    @if($inquiryPluginEnabled)
    <div x-data="{ open: false }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            <span><i class="fas fa-envelope mr-1.5"></i>{{ __('themes::admin.settings.plugins.inquiry.title') }}</span>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse>
            <input type="hidden" name="show_inquiry_form" :value="showInquiryForm">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" @change="showInquiryForm = $el.checked ? '1' : '0'"
                    :checked="showInquiryForm === '1'"
                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('themes::admin.settings.plugins.inquiry.enable') }}</span>
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('themes::admin.settings.plugins.inquiry.help') }}</p>
        </div>
    </div>

    <hr class="border-gray-200 dark:border-gray-700">
    @endif

    {{-- ===== Footer Links ===== --}}
    <div x-data="{ open: false }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            <span><i class="fas fa-link mr-1.5"></i>{{ __('themes::admin.settings.footer.links') }}</span>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse>
            <div class="space-y-3">
                <template x-for="(link, index) in footerLinks" :key="index">
                    <div class="flex flex-col gap-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <input type="text" :name="'footer_links[' + index + '][title]'" x-model="link.title"
                               :placeholder="'{{ __('themes::admin.settings.footer.link_title') }}'"
                               class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <div class="flex gap-2">
                            <input type="text" :name="'footer_links[' + index + '][url]'" x-model="link.url"
                                   :placeholder="'{{ __('themes::admin.settings.footer.link_url') }}'"
                                   class="flex-1 text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <button type="button" @click="removeFooterLink(index)" class="px-2 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 text-xs">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </template>
                <button type="button" @click="addFooterLink()" class="w-full px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                    <i class="fas fa-plus mr-1"></i>{{ __('themes::admin.settings.footer.add_link') }}
                </button>
            </div>
        </div>
    </div>

    <hr class="border-gray-200 dark:border-gray-700">

    {{-- ===== Footer Description ===== --}}
    <div x-data="{ open: false }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            <span><i class="fas fa-align-left mr-1.5"></i>{{ __('themes::admin.settings.footer.description') }}</span>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse>
            <x-form-textarea name="footer_description" :label="__('themes::admin.settings.footer.description')"
                             x-model="footerDescription" rows="3"
                             :help="__('themes::admin.settings.footer.description_help')" />
        </div>
    </div>

    <hr class="border-gray-200 dark:border-gray-700">

    {{-- ===== SNS Links ===== --}}
    <div x-data="{ open: false }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            <span><i class="fas fa-share-alt mr-1.5"></i>{{ __('themes::admin.settings.footer.sns_title') }}</span>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse>
            <div class="space-y-3">
                @php
                    $snsFields = [
                        ['name' => 'footer_sns_instagram', 'label' => 'Instagram', 'prefix' => 'instagram.com/'],
                        ['name' => 'footer_sns_x', 'label' => 'X (Twitter)', 'prefix' => 'x.com/'],
                        ['name' => 'footer_sns_facebook', 'label' => 'Facebook', 'prefix' => 'facebook.com/'],
                        ['name' => 'footer_sns_tiktok', 'label' => 'TikTok', 'prefix' => 'tiktok.com/@'],
                        ['name' => 'footer_sns_bluesky', 'label' => 'Bluesky', 'prefix' => 'bsky.app/profile/'],
                        ['name' => 'footer_sns_threads', 'label' => 'Threads', 'prefix' => 'threads.net/@'],
                        ['name' => 'footer_sns_linkedin', 'label' => 'LinkedIn', 'prefix' => 'linkedin.com/in/'],
                        ['name' => 'footer_sns_youtube', 'label' => 'YouTube', 'prefix' => 'youtube.com/@'],
                        ['name' => 'footer_sns_pinterest', 'label' => 'Pinterest', 'prefix' => 'pinterest.com/'],
                        ['name' => 'footer_sns_discord', 'label' => 'Discord', 'prefix' => 'discord.gg/'],
                    ];
                @endphp
                @foreach($snsFields as $sns)
                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-400">{{ $sns['label'] }}</label>
                        <div class="flex items-stretch">
                            <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400 mr-1 whitespace-nowrap">{{ $sns['prefix'] }}</span>
                            <input type="text" name="{{ $sns['name'] }}"
                                   value="{{ old($sns['name'], $settings->{$sns['name']} ?? '') }}"
                                   class="flex-1 min-w-0 block w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                   placeholder="{{ __('themes::admin.settings.footer.sns_username') }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <hr class="border-gray-200 dark:border-gray-700">

    {{-- ===== Copyright ===== --}}
    <div x-data="{ open: false }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            <span><i class="fas fa-copyright mr-1.5"></i>{{ __('themes::admin.settings.footer.copyright_section') }}</span>
            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse>
            <x-form-text name="footer_copyright" :label="__('themes::admin.settings.footer.copyright')"
                         x-model="footerCopyright"
                         :help="__('themes::admin.settings.footer.copyright_help')" />
        </div>
    </div>

    {{-- Bottom spacing for save button --}}
    <div class="h-20"></div>
</div>
