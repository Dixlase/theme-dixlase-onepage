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

@extends('layouts.admin')

@php
    // Strip any legacy `© YYYY ` prefix from the saved footer copyright
    // so the inline editor and the sidebar input only handle the
    // suffix. Computed once here so both the sidebar template and the
    // Alpine state at the bottom of this file can reuse it. Done in
    // PHP (rather than inline in @json) because Blade tokenises `{4}`
    // inside its directives.
    $footerCopyrightSuffix = preg_replace(
        '/^\s*©\s*\d{4}\s+/u',
        '',
        old('footer_copyright', $settings->footer_copyright ?? '')
    );
@endphp

@section('content')
<div x-data="themeSettingsEditor()" x-init="init()">
    <form id="theme-settings-form" action="{{ route('admin.settings.themes.settings.update') }}" method="POST">
        @csrf

        {{-- Hidden inputs synced from Alpine data --}}
        <input type="hidden" name="hero_main_title" :value="heroMainTitle">
        <input type="hidden" name="hero_sub_title" :value="heroSubTitle">
        <input type="hidden" name="hero_button_text" :value="heroButtonText">
        <input type="hidden" name="hero_button_link" :value="heroButtonLink">
        <input type="hidden" name="hero_button_enabled" :value="heroButtonEnabled">
        <input type="hidden" name="hero_button_secondary_text" :value="heroButtonSecondaryText">
        <input type="hidden" name="hero_button_secondary_link" :value="heroButtonSecondaryLink">
        <input type="hidden" name="hero_button_secondary_enabled" :value="heroButtonSecondaryEnabled">
        <input type="hidden" name="footer_copyright" :value="footerCopyright">
        <input type="hidden" name="footer_menu_id" :value="footerMenuId">
        <input type="hidden" name="primary_color" :value="primaryColor">

        {{-- Preview Area (center content) --}}
        <div class="mt-2">
            @include('themes::admin.settings.themes.partials.preview')
        </div>

        {{-- Right Sidebar --}}
        @include('themes::admin.settings.themes.partials.sidebar')
    </form>
</div>
@endsection

@section('save')
    <x-admin.save-button
        id_confirmation="confirmationModal"
        :label="__('common.save')"
        :title="__('common.save_confirmation_title')"
        :message="__('common.save_confirmation_message')"
        :confirm_label="__('common.save')"
        :cancel_label="__('common.cancel')"
        form="theme-settings-form"
    />
@endsection

@push('styles')
<style @cspNonce>
#admin-main-content { min-width: 0; }
</style>
@endpush

@push('scripts')
<script @cspNonce>
function themeSettingsEditor() {
    return {
        // プライマリカラー
        primaryColor: @json(old('primary_color', $settings->primary_color ?? '#3b82f6')),

        // Hero settings
        heroMainTitle: @json(old('hero_main_title', $settings->hero_main_title ?? '')),
        heroSubTitle: @json(old('hero_sub_title', $settings->hero_sub_title ?? '')),
        heroButtonText: @json(old('hero_button_text', $settings->hero_button_text ?? '')),
        heroButtonLink: @json(old('hero_button_link', $settings->hero_button_link ?? '')),
        heroButtonEnabled: @json(old('hero_button_enabled', $settings->hero_button_enabled ?? '1')),
        heroButtonSecondaryText: @json(old('hero_button_secondary_text', $settings->hero_button_secondary_text ?? '')),
        heroButtonSecondaryLink: @json(old('hero_button_secondary_link', $settings->hero_button_secondary_link ?? '')),
        heroButtonSecondaryEnabled: @json(old('hero_button_secondary_enabled', $settings->hero_button_secondary_enabled ?? '1')),

        // Footer settings
        // The `© <year>` prefix is rendered automatically at display time
        // (always current year), so the input only edits the suffix.
        // $footerCopyrightSuffix has any legacy `© YYYY ` stripped above.
        copyrightYear: @json(date('Y')),
        footerCopyright: @json($footerCopyrightSuffix),

        // Media preview URLs
        headerLogoPreviewUrl: @json($headerLogo ? asset('storage/' . config('admin.files.mediaPath', 'media') . '/' . $headerLogo->path) : null),
        heroBgPreviewUrl: @json($heroBackgroundImage ? asset('storage/' . config('admin.files.mediaPath', 'media') . '/' . $heroBackgroundImage->path) : null),
        heroVideoPreviewUrl: @json($heroBackgroundVideo ? asset('storage/' . config('admin.files.mediaPath', 'media') . '/' . $heroBackgroundVideo->path) : null),

        // Device preview (from previewContainerMixin)
        ...previewContainerMixin(),

        // SNS links
        snsLinks: {
            instagram: @json(old('footer_sns_instagram', $settings->footer_sns_instagram ?? '')),
            x: @json(old('footer_sns_x', $settings->footer_sns_x ?? '')),
            facebook: @json(old('footer_sns_facebook', $settings->footer_sns_facebook ?? '')),
            tiktok: @json(old('footer_sns_tiktok', $settings->footer_sns_tiktok ?? '')),
            bluesky: @json(old('footer_sns_bluesky', $settings->footer_sns_bluesky ?? '')),
            threads: @json(old('footer_sns_threads', $settings->footer_sns_threads ?? '')),
            linkedin: @json(old('footer_sns_linkedin', $settings->footer_sns_linkedin ?? '')),
            youtube: @json(old('footer_sns_youtube', $settings->footer_sns_youtube ?? '')),
            pinterest: @json(old('footer_sns_pinterest', $settings->footer_sns_pinterest ?? '')),
            discord: @json(old('footer_sns_discord', $settings->footer_sns_discord ?? '')),
            github: @json(old('footer_sns_github', $settings->footer_sns_github ?? '')),
        },

        // Plugin preview data
        headerMenuId: @json(old('header_menu_id', $settings->header_menu_id ?? '')),
        footerMenuId: @json(old('footer_menu_id', $settings->footer_menu_id ?? '')),
        allMenusData: @json($allMenusData ?? []),
        showInquiryForm: @json(old('show_inquiry_form', $settings->show_inquiry_form ?? '0')),
        menuEditBaseUrl: @json($menuPluginEnabled ? route('dixlase-menus::admin.menus.edit', ['id' => '__ID__']) : ''),
        get headerMenuEditUrl() { return this.menuEditBaseUrl.replace('__ID__', this.headerMenuId); },
        get footerMenuEditUrl() { return this.menuEditBaseUrl.replace('__ID__', this.footerMenuId); },

        // Editing state
        editing: null,

        init() {
            this.$dispatch('right-sidebar-active');

            // Update preview URLs when media is selected via picker
            const self = this;
            document.addEventListener('media-selected', (e) => {
                const { inputId, url } = e.detail;
                if (inputId === 'header_logo_id') {
                    self.headerLogoPreviewUrl = url || null;
                } else if (inputId === 'hero_background_image_id') {
                    self.heroBgPreviewUrl = url || null;
                } else if (inputId === 'hero_background_video_id') {
                    self.heroVideoPreviewUrl = url || null;
                }
            });

            // Update preview URLs when media is removed
            ['header_logo_id', 'hero_background_image_id', 'hero_background_video_id'].forEach(name => {
                const input = document.getElementById(name);
                if (input) {
                    input.addEventListener('change', () => {
                        if (!input.value) {
                            if (name === 'header_logo_id') {
                                self.headerLogoPreviewUrl = null;
                            } else if (name === 'hero_background_image_id') {
                                self.heroBgPreviewUrl = null;
                            } else if (name === 'hero_background_video_id') {
                                self.heroVideoPreviewUrl = null;
                            }
                        }
                    });
                }
            });

            // Initialize preview container (scaling, device toggle)
            this.initPreviewContainer();
            this.$watch('freeWidth', () => {
                if (this.previewDevice === 'free') {
                    this.previewDeviceWidth = this.freeWidth;
                    this.$nextTick(() => this.updatePreviewScale());
                }
            });
        },

        startEdit(field) {
            this.editing = field;
        },

        stopEdit() {
            this.editing = null;
        },
    }
}
</script>
@endpush
