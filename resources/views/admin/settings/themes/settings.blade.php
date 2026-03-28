{{--
This file is part of Dixlase.

Copyright (C) 2025 exc-D inc.
https://exc-d.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program. If not, see <https://www.gnu.org/licenses/>.
--}}

@extends('layouts.admin')

@section('content')
<div x-data="themeSettingsEditor()" x-init="init()">
    <form id="theme-settings-form" action="{{ route('admin.settings.themes.settings.update') }}" method="POST">
        @csrf

        {{-- Hidden inputs synced from Alpine data --}}
        <input type="hidden" name="hero_main_title" :value="heroMainTitle">
        <input type="hidden" name="hero_sub_title" :value="heroSubTitle">
        <input type="hidden" name="hero_button_text" :value="heroButtonText">
        <input type="hidden" name="hero_button_link" :value="heroButtonLink">
        <input type="hidden" name="hero_button_secondary_text" :value="heroButtonSecondaryText">
        <input type="hidden" name="hero_button_secondary_link" :value="heroButtonSecondaryLink">
        <input type="hidden" name="footer_description" :value="footerDescription">
        <input type="hidden" name="footer_copyright" :value="footerCopyright">

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

@push('scripts')
<script @cspNonce>
function themeSettingsEditor() {
    return {
        // Hero settings
        heroMainTitle: @json(old('hero_main_title', $settings->hero_main_title ?? '')),
        heroSubTitle: @json(old('hero_sub_title', $settings->hero_sub_title ?? '')),
        heroButtonText: @json(old('hero_button_text', $settings->hero_button_text ?? '')),
        heroButtonLink: @json(old('hero_button_link', $settings->hero_button_link ?? '')),
        heroButtonSecondaryText: @json(old('hero_button_secondary_text', $settings->hero_button_secondary_text ?? '')),
        heroButtonSecondaryLink: @json(old('hero_button_secondary_link', $settings->hero_button_secondary_link ?? '')),

        // Footer settings
        footerDescription: @json(old('footer_description', $settings->footer_description ?? '')),
        footerCopyright: @json(old('footer_copyright', $settings->footer_copyright ?? '')),
        footerLinks: @json(old('footer_links', $settings->footer_links ?? [])),

        // Media preview URLs
        headerLogoPreviewUrl: @json($headerLogo ? asset(config('admin.mediaPath', 'media') . '/' . $headerLogo->path) : null),
        heroBgPreviewUrl: @json($heroBackgroundImage ? asset(config('admin.mediaPath', 'media') . '/' . $heroBackgroundImage->path) : null),

        // Device preview
        previewDevice: 'desktop',
        previewDeviceWidth: 1440,

        // Plugin preview data
        headerMenuId: @json(old('header_menu_id', $settings->header_menu_id ?? '')),
        allMenusData: @json($allMenusData ?? []),
        showInquiryForm: @json(old('show_inquiry_form', $settings->show_inquiry_form ?? '0')),

        // Editing state
        editing: null,

        init() {
            this.$dispatch('right-sidebar-active');

            // Recalculate preview scale whenever container size changes
            this.$nextTick(() => {
                const outer = document.getElementById('preview-outer');
                // Poll for container width changes (handles all sidebar toggles reliably)
                let lastWidth = outer ? outer.offsetWidth : 0;
                setInterval(() => {
                    if (outer && outer.offsetWidth !== lastWidth) {
                        lastWidth = outer.offsetWidth;
                        this.updatePreviewScale();
                    }
                }, 200);
                this.updatePreviewScale();
            });
        },

        setPreviewDevice(device) {
            const widths = { mobile: 375, tablet: 768, desktop: 1440 };
            this.previewDevice = device;
            this.previewDeviceWidth = widths[device];
            this.$nextTick(() => this.updatePreviewScale());
        },

        updatePreviewScale() {
            const outer = document.getElementById('preview-outer');
            const inner = document.getElementById('preview-inner');
            if (outer && inner) {
                const containerWidth = outer.offsetWidth;
                const deviceWidth = this.previewDeviceWidth;
                const scale = Math.min(containerWidth / deviceWidth, 1);
                inner.style.transform = 'scale(' + scale + ')';
                // Set container height to match scaled content
                const contentHeight = inner.scrollHeight || 900;
                outer.style.height = Math.max(contentHeight * scale, 300) + 'px';
                // Center horizontally when device is narrower than container
                const scaledWidth = deviceWidth * scale;
                const offsetX = (containerWidth - scaledWidth) / 2;
                inner.style.left = Math.max(offsetX, 0) + 'px';
            }
        },

        startEdit(field) {
            this.editing = field;
        },

        stopEdit() {
            this.editing = null;
        },

        addFooterLink() {
            this.footerLinks.push({ title: '', url: '' });
        },

        removeFooterLink(index) {
            this.footerLinks.splice(index, 1);
        },
    }
}
</script>
@endpush
