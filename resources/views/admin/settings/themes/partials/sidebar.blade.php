{{--
This file is part of Dixlase.

Copyright (C) 2025 exc-D inc.
https://exc-d.com

Theme settings right sidebar - non-visual settings
--}}

<x-admin.theme-preview-sidebar
    :openLabel="__('themes::admin.settings.editor.sidebar_open')"
    :closeLabel="__('themes::admin.settings.editor.sidebar_close')"
>
    {{-- ===== Appearance Mode ===== --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.appearance.title')" icon="fas fa-palette" :open="true">
        <select name="appearance_mode"
            @change="$dispatch('appearance-changed', { mode: $event.target.value })"
            class="input-common block w-full max-w-full p-2 pr-10 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white">
            <option value="0" {{ old('appearance_mode', $settings->appearance_mode ?? '0') === '0' ? 'selected' : '' }}>{{ __('themes::admin.settings.appearance.mode_auto') }}</option>
            <option value="1" {{ old('appearance_mode', $settings->appearance_mode ?? '0') === '1' ? 'selected' : '' }}>{{ __('themes::admin.settings.appearance.mode_light') }}</option>
            <option value="2" {{ old('appearance_mode', $settings->appearance_mode ?? '0') === '2' ? 'selected' : '' }}>{{ __('themes::admin.settings.appearance.mode_dark') }}</option>
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('themes::admin.settings.appearance.mode_help') }}</p>
    </x-admin.theme-preview-sidebar-section>

    {{-- ===== Primary Color ===== --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.primary_color.title')" icon="fas fa-swatchbook">
        <div class="grid grid-cols-4 gap-2">
            @php
                $colorOptions = [
                    '#3b82f6' => __('themes::admin.settings.primary_color.blue'),
                    '#8b5cf6' => __('themes::admin.settings.primary_color.purple'),
                    '#10b981' => __('themes::admin.settings.primary_color.green'),
                    '#ef4444' => __('themes::admin.settings.primary_color.red'),
                    '#f97316' => __('themes::admin.settings.primary_color.orange'),
                    '#ec4899' => __('themes::admin.settings.primary_color.pink'),
                    '#6366f1' => __('themes::admin.settings.primary_color.indigo'),
                    '#14b8a6' => __('themes::admin.settings.primary_color.teal'),
                ];
            @endphp
            @foreach($colorOptions as $hex => $label)
                <button type="button"
                    @click="primaryColor = '{{ $hex }}'"
                    :class="primaryColor === '{{ $hex }}' ? 'ring-2 ring-offset-2 ring-gray-900 dark:ring-white dark:ring-offset-gray-800 scale-110' : 'hover:scale-105'"
                    class="flex flex-col items-center gap-1 p-2 rounded-lg transition-all duration-150"
                    :title="'{{ $label }}'">
                    <span class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-600 shadow-sm" style="background-color: {{ $hex }}"></span>
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">{{ $label }}</span>
                </button>
            @endforeach
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('themes::admin.settings.primary_color.help') }}</p>
    </x-admin.theme-preview-sidebar-section>

    {{-- ===== Favicon ===== --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.header.favicon')" icon="fas fa-globe">
        <x-media.picker
            name="favicon_id"
            :value="$settings->favicon_id ?? null"
            :media="$favicon ?? null"
            :help="__('themes::admin.settings.header.favicon_help')"
            :error="$errors->first('favicon_id')"
            aspectRatio="square"
            :buttonText="__('themes::admin.settings.select_favicon_image')"
            :confirmUploadNavigation="true"
        />
    </x-admin.theme-preview-sidebar-section>

    {{-- ===== Header Logo ===== --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.header.header_logo')" icon="fas fa-heading">
        <x-media.picker
            name="header_logo_id"
            :value="$settings->header_logo_id ?? null"
            :media="$headerLogo ?? null"
            :help="__('themes::admin.settings.header.header_logo_help')"
            :error="$errors->first('header_logo_id')"
            :buttonText="__('themes::admin.settings.select_logo_image')"
            :confirmUploadNavigation="true"
        />
    </x-admin.theme-preview-sidebar-section>

    {{-- ===== Header Menu (Plugin: DixlaseMenus) ===== --}}
    @if($menuPluginEnabled)
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.plugins.menu.title')" icon="fas fa-bars">
        <select name="header_menu_id" x-model="headerMenuId"
            class="input-common block w-full max-w-full p-2 pr-10 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white">
            @foreach($menuOptions as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('themes::admin.settings.plugins.menu.help') }}</p>
    </x-admin.theme-preview-sidebar-section>
    @else
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.plugins.menu.title')" icon="fas fa-bars">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('themes::admin.settings.plugins.footer_menu.plugin_required') }}</p>
    </x-admin.theme-preview-sidebar-section>
    @endif

    {{-- ===== Hero Background Image ===== --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.hero.background_image')" icon="fas fa-image" :open="true">
        <x-media.picker
            name="hero_background_image_id"
            :value="$settings->hero_background_image_id ?? null"
            :media="$heroBackgroundImage ?? null"
            :help="__('themes::admin.settings.hero.background_image_help')"
            :error="$errors->first('hero_background_image_id')"
            aspectRatio="hero"
            :buttonText="__('themes::admin.settings.hero.select_background_image')"
            :confirmUploadNavigation="true"
        />
    </x-admin.theme-preview-sidebar-section>

    {{-- ===== Contact Form (Plugin: DixlaseInquiry) ===== --}}
    @if($inquiryPluginEnabled)
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.plugins.inquiry.title')" icon="fas fa-envelope">
        <input type="hidden" name="show_inquiry_form" :value="showInquiryForm">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" @change="showInquiryForm = $el.checked ? '1' : '0'"
                :checked="showInquiryForm === '1'"
                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('themes::admin.settings.plugins.inquiry.enable') }}</span>
        </label>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('themes::admin.settings.plugins.inquiry.help') }}</p>
    </x-admin.theme-preview-sidebar-section>
    @endif

    {{-- ===== Footer Menu ===== --}}
    @if($menuPluginEnabled)
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.plugins.footer_menu.title')" icon="fas fa-link">
        <select name="footer_menu_id" x-model="footerMenuId"
            class="input-common block w-full max-w-full p-2 pr-10 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white">
            @foreach($menuOptions as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('themes::admin.settings.plugins.footer_menu.help') }}</p>
    </x-admin.theme-preview-sidebar-section>
    @else
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.plugins.footer_menu.title')" icon="fas fa-link">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('themes::admin.settings.plugins.footer_menu.plugin_required') }}</p>
    </x-admin.theme-preview-sidebar-section>
    @endif

    {{-- ===== SNS Links ===== --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.footer.sns_title')" icon="fas fa-share-alt">
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
                @php $snsKey = str_replace('footer_sns_', '', $sns['name']); @endphp
                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-400">{{ $sns['label'] }}</label>
                    <div class="flex items-stretch">
                        <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400 mr-1 whitespace-nowrap">{{ $sns['prefix'] }}</span>
                        <input type="text" name="{{ $sns['name'] }}"
                               x-model="snsLinks.{{ $snsKey }}"
                               class="flex-1 min-w-0 block w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                               placeholder="{{ __('themes::admin.settings.footer.sns_username') }}">
                    </div>
                </div>
            @endforeach
        </div>
    </x-admin.theme-preview-sidebar-section>

    {{-- ===== Copyright ===== --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.footer.copyright_section')" icon="fas fa-copyright" :divider="false">
        <x-form-text name="footer_copyright" :label="__('themes::admin.settings.footer.copyright')"
                     x-model="footerCopyright"
                     :help="__('themes::admin.settings.footer.copyright_help')" />
    </x-admin.theme-preview-sidebar-section>
</x-admin.theme-preview-sidebar>
