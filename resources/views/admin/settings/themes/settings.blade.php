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
        <input type="hidden" name="hero_button_target" :value="heroButtonTarget">
        <input type="hidden" name="hero_button_secondary_text" :value="heroButtonSecondaryText">
        <input type="hidden" name="hero_button_secondary_link" :value="heroButtonSecondaryLink">
        <input type="hidden" name="hero_button_secondary_enabled" :value="heroButtonSecondaryEnabled">
        <input type="hidden" name="hero_button_secondary_target" :value="heroButtonSecondaryTarget">
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
{{-- Load all four heading-font families here so the inline sidebar
     preview mock (admin/settings/themes/partials/preview.blade.php)
     and the two picker cards in the sidebar can render the actual
     faces the operator picks. The admin layout preconnects to
     fonts.bunny.net and loads only the figtree UI font; this <link>
     is additive and same-origin — the browser reuses the existing
     preconnect. --}}
<link rel="stylesheet" href="https://fonts.bunny.net/css?family=cormorant-garamond:400,700|jost:400,700|noto-sans-jp:400,700|noto-serif-jp:400,700&display=swap">
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

        // Heading font family: cormorant | jost | noto-sans-jp | noto-serif-jp
        // (Legacy gothic/mincho values are normalised to noto-sans-jp/noto-serif-jp
        // by the Controller before reaching Alpine, so strict equality against
        // the new slugs in the picker highlights the correct card.)
        headingFontFamily: @json(old('heading_font_family', $settings->heading_font_family ?? 'noto-sans-jp')),
        // Per-region apply toggles. The form-toggle component's xModel
        // expects string '0' / '1' (see components/form-toggle.blade.php:90),
        // not booleans.
        headingFontApplyHeader:  @json((string) old('heading_font_apply_header',  $settings->heading_font_apply_header  ?? '1')),
        headingFontApplyHero:    @json((string) old('heading_font_apply_hero',    $settings->heading_font_apply_hero    ?? '1')),
        headingFontApplyFooter:  @json((string) old('heading_font_apply_footer',  $settings->heading_font_apply_footer  ?? '1')),
        headingFontApplyContent: @json((string) old('heading_font_apply_content', $settings->heading_font_apply_content ?? '1')),
        // Per-region tracking (letter-spacing) in em. Kept as string so
        // <input type="number"> and <input type="range"> share the exact
        // Alpine value verbatim (no float rounding drift).
        headingFontTrackingHeader:  @json((string) old('heading_font_tracking_header',  $settings->heading_font_tracking_header  ?? '0')),
        headingFontTrackingHero:    @json((string) old('heading_font_tracking_hero',    $settings->heading_font_tracking_hero    ?? '0')),
        headingFontTrackingFooter:  @json((string) old('heading_font_tracking_footer',  $settings->heading_font_tracking_footer  ?? '0')),
        headingFontTrackingContent: @json((string) old('heading_font_tracking_content', $settings->heading_font_tracking_content ?? '0')),

        // Hero settings
        heroMainTitle: @json(old('hero_main_title', $settings->hero_main_title ?? '')),
        heroSubTitle: @json(old('hero_sub_title', $settings->hero_sub_title ?? '')),
        heroButtonText: @json(old('hero_button_text', $settings->hero_button_text ?? '')),
        heroButtonLink: @json(old('hero_button_link', $settings->hero_button_link ?? '')),
        heroButtonEnabled: @json(old('hero_button_enabled', $settings->hero_button_enabled ?? '1')),
        heroButtonTarget: @json(old('hero_button_target', $settings->hero_button_target ?? '_self')),
        heroButtonSecondaryText: @json(old('hero_button_secondary_text', $settings->hero_button_secondary_text ?? '')),
        heroButtonSecondaryLink: @json(old('hero_button_secondary_link', $settings->hero_button_secondary_link ?? '')),
        heroButtonSecondaryEnabled: @json(old('hero_button_secondary_enabled', $settings->hero_button_secondary_enabled ?? '1')),
        heroButtonSecondaryTarget: @json(old('hero_button_secondary_target', $settings->hero_button_secondary_target ?? '_self')),

        // Hero background fill — four independent knobs:
        //   mode   = 'primary' | 'custom'  (color source; legacy 'none'
        //             from the pre-restructure era is normalised at save
        //             + at render, but Alpine holds whatever DB gave us).
        //   color  = hex, custom mode's start / centre / solid color
        //   color2 = hex, custom mode's outside / bottom endpoint
        //             (ignored when shape='solid')
        //   shape  = 'radial' | 'linear-vertical' | 'solid'
        // All held in Alpine state at all times so the last-selected
        // shade / shape returns when the operator flips modes / shapes.
        heroGradientMode: @json(old('hero_gradient_mode', $settings->hero_gradient_mode ?? 'primary')),
        heroGradientColor: @json(old('hero_gradient_color', $settings->hero_gradient_color ?? '#3b82f6')),
        heroGradientColor2: @json(old('hero_gradient_color_2', $settings->hero_gradient_color_2 ?? '#ffffff')),
        // Dark-mode overrides. Empty string ('') when unset — the front
        // resolver falls back to the light value (matches the PHP resolver
        // in partials/hero.blade.php).
        heroGradientColorDark: @json(old('hero_gradient_color_dark', $settings->hero_gradient_color_dark ?? '')),
        heroGradientColor2Dark: @json(old('hero_gradient_color_2_dark', $settings->hero_gradient_color_2_dark ?? '')),
        heroGradientShape: @json(old('hero_gradient_shape', $settings->hero_gradient_shape ?? 'radial')),

        // Footer settings
        // The `© <year>` prefix is rendered automatically at display time
        // (always current year), so the input only edits the suffix.
        // $footerCopyrightSuffix has any legacy `© YYYY ` stripped above.
        copyrightYear: @json(date('Y')),
        footerCopyright: @json($footerCopyrightSuffix),

        // Media preview URLs
        headerLogoPreviewUrl: @json($headerLogo ? asset('storage/' . config('admin.files.mediaPath', 'media') . '/' . $headerLogo->path) : null),
        headerLogoDarkPreviewUrl: @json($headerLogoDark ? asset('storage/' . config('admin.files.mediaPath', 'media') . '/' . $headerLogoDark->path) : null),
        heroBgPreviewUrl: @json($heroBackgroundImage ? asset('storage/' . config('admin.files.mediaPath', 'media') . '/' . $heroBackgroundImage->path) : null),
        heroVideoPreviewUrl: @json($heroBackgroundVideo ? asset('storage/' . config('admin.files.mediaPath', 'media') . '/' . $heroBackgroundVideo->path) : null),
        heroForegroundPreviewUrl: @json($heroForegroundImage ? asset('storage/' . config('admin.files.mediaPath', 'media') . '/' . $heroForegroundImage->path) : null),

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

        // ===== Preview-mock helpers =====
        // The inline sidebar preview (partials/preview.blade.php) needs to
        // reflect hero_gradient + heading_font settings via Alpine :style
        // bindings. These helpers keep the resolution logic in one place
        // (mirrors layouts/app.blade.php's PHP resolvers) so per-element
        // bindings stay short and CSP-safe (no complex inline expressions).

        // Font-family stack lookup. Legacy 'gothic' / 'mincho' from the
        // 2-choice era map onto the JP faces, same as layouts/app.blade.php.
        resolvedHeadingFontStack() {
            const stacks = {
                cormorant: "'Cormorant Garamond', 'Hiragino Mincho ProN', 'Yu Mincho', 'YuMincho', serif",
                jost: "'Jost', 'Hiragino Kaku Gothic ProN', 'Yu Gothic Medium', 'YuGothic', sans-serif",
                'noto-sans-jp': "'Noto Sans JP', 'Hiragino Kaku Gothic ProN', 'Yu Gothic Medium', 'YuGothic', sans-serif",
                'noto-serif-jp': "'Noto Serif JP', 'Hiragino Mincho ProN', 'Yu Mincho', 'YuMincho', serif",
                gothic: "'Noto Sans JP', 'Hiragino Kaku Gothic ProN', 'Yu Gothic Medium', 'YuGothic', sans-serif",
                mincho: "'Noto Serif JP', 'Hiragino Mincho ProN', 'Yu Mincho', 'YuMincho', serif",
            };
            return stacks[this.headingFontFamily] || stacks['noto-sans-jp'];
        },

        // Per-region typography :style object. font-family only when the
        // region's apply toggle is ON (matches the live layout's
        // var(--font-heading-{region}, inherit) fallback). letter-spacing
        // is orthogonal: applies whenever non-zero, regardless of the apply
        // toggle — same as the live layout's letter-spacing rule.
        regionTypographyStyle(applyToggle, trackingValue) {
            const style = {};
            if (applyToggle === '1') style.fontFamily = this.resolvedHeadingFontStack();
            const t = parseFloat(trackingValue);
            if (!isNaN(t) && t !== 0) style.letterSpacing = trackingValue + 'em';
            return style;
        },

        // Hero fill gate. With the restructure there is no longer an
        // 'off entirely' state — 'solid' IS the "no gradient" case,
        // still rendered as a plain color fill. Only skip when a hero
        // background image or video takes over.
        showHeroGradient() {
            return !this.heroBgPreviewUrl && !this.heroVideoPreviewUrl;
        },

        // Resolve the fill CSS for a given preview theme ('light' | 'dark').
        // Mirrors the PHP resolver in partials/hero.blade.php:
        //   mode='primary'  → color1 = primaryColor, color2 = per-theme body bg
        //   mode='custom'   → color1 + color2 both operator-picked
        //   shape='solid'   → color1 only, no gradient
        // No alpha suffix — 2-color pair gives an explicit gradient that
        // matches what the operator picked. Legacy mode='none' is
        // normalised to primary+solid for backward compat.
        resolveHeroFillCss(themeMode) {
            const isDark = themeMode === 'dark';
            let mode = this.heroGradientMode;
            let shape = this.heroGradientShape;
            if (mode === 'none') { mode = 'primary'; shape = 'solid'; }

            // Custom mode carries per-appearance color pairs. Empty dark
            // → fall back to the light value (matches the PHP resolver
            // and keeps sites saved before the per-mode split working).
            let color1, color2;
            if (mode === 'custom') {
                color1 = isDark
                    ? (this.heroGradientColorDark || this.heroGradientColor)
                    : this.heroGradientColor;
                color2 = isDark
                    ? (this.heroGradientColor2Dark || this.heroGradientColor2)
                    : this.heroGradientColor2;
            } else {
                color1 = this.primaryColor;
                color2 = isDark ? '#030712' : '#f3f4f6'; // Tailwind gray-950 / gray-100 — matches body bg
            }

            switch (shape) {
                case 'solid':
                    return color1;
                case 'linear-vertical':
                    return 'linear-gradient(to bottom, ' + color1 + ' 0%, ' + color2 + ' 100%)';
                case 'radial':
                default:
                    return 'radial-gradient(circle clamp(500px, 100vw, 2400px) at 50% 50%, ' + color1 + ' 0%, ' + color2 + ' 65%)';
            }
        },

        // Emit both light + dark rules as a single stylesheet string.
        // Bound via x-text on a <style> element in the preview mock so
        // the fill reactively updates on every state change without any
        // per-element :style binding.
        heroFillStyleRules() {
            return '#preview-inner[data-preview-theme="light"] .pv-hero-fill { background: ' + this.resolveHeroFillCss('light') + '; }\n'
                + '#preview-inner[data-preview-theme="dark"]  .pv-hero-fill { background: ' + this.resolveHeroFillCss('dark') + '; }';
        },

        init() {
            this.$dispatch('right-sidebar-active');

            // Update preview URLs when media is selected via picker
            const self = this;
            document.addEventListener('media-selected', (e) => {
                const { inputId, url } = e.detail;
                if (inputId === 'header_logo_id') {
                    self.headerLogoPreviewUrl = url || null;
                } else if (inputId === 'header_logo_dark_id') {
                    self.headerLogoDarkPreviewUrl = url || null;
                } else if (inputId === 'hero_background_image_id') {
                    self.heroBgPreviewUrl = url || null;
                } else if (inputId === 'hero_background_video_id') {
                    self.heroVideoPreviewUrl = url || null;
                } else if (inputId === 'hero_foreground_image_id') {
                    self.heroForegroundPreviewUrl = url || null;
                }
            });

            // Update preview URLs when media is removed
            ['header_logo_id', 'header_logo_dark_id', 'hero_background_image_id', 'hero_background_video_id'].forEach(name => {
                const input = document.getElementById(name);
                if (input) {
                    input.addEventListener('change', () => {
                        if (!input.value) {
                            if (name === 'header_logo_id') {
                                self.headerLogoPreviewUrl = null;
                            } else if (name === 'header_logo_dark_id') {
                                self.headerLogoDarkPreviewUrl = null;
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

            // Session keep-alive: HEAD the current URL every 25 min so
            // the session cookie stays fresh and the CSRF token in the
            // pre-rendered form does not outlive its 120-min lifetime
            // while the operator is mid-edit. Any authenticated request
            // touches the session (Laravel routes HEAD as GET
            // internally); HEAD returns no body so this is cheap on
            // the wire. 25 < 30-min ping half-cycle < 60-min half of
            // the 120-min session lifetime, so a single missed ping
            // still leaves plenty of head-room.
            //
            // Scope note: this only covers the "left the settings page
            // open too long" cause of 419. Multi-tab logout /
            // browser-back-after-logout paths need a Core-side fix
            // (session refresh endpoint or better 419 UX handling).
            // The interval is auto-cleared on page unload.
            setInterval(() => {
                fetch(window.location.href, {
                    method: 'HEAD',
                    credentials: 'same-origin',
                    cache: 'no-store',
                }).catch(() => { /* silent — next tick retries */ });
            }, 25 * 60 * 1000);
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
