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

    {{-- ===== Heading Font (Gothic / Mincho) ===== --}}
    {{-- Two-card picker. The label previews the actual font family via an --}}
    {{-- inline style="font-family: ..." span so the operator sees what     --}}
    {{-- they're choosing before saving. The Bunny <link> in the layout    --}}
    {{-- loads both families, so the sample is authentic even in the      --}}
    {{-- admin (not a system-font approximation).                          --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.typography.title')" icon="fas fa-font">
        <input type="hidden" name="heading_font_family" :value="headingFontFamily">
        {{-- 2x2 grid. Cormorant / Jost first (Latin display faces),
             then the two Noto JP families. Sample "Aa 見出し" lets the
             Latin faces show off their glyphs on the "Aa" and the JP
             fallback on the "見出し", so the operator sees exactly what
             each face will render for both scripts before saving. --}}
        <div class="grid grid-cols-2 gap-2">
            @php
                $fontOptions = [
                    'cormorant' => [
                        'label' => __('themes::admin.settings.typography.font_cormorant'),
                        'sample_family' => "'Cormorant Garamond', 'Hiragino Mincho ProN', 'Yu Mincho', serif",
                    ],
                    'jost' => [
                        'label' => __('themes::admin.settings.typography.font_jost'),
                        'sample_family' => "'Jost', 'Hiragino Kaku Gothic ProN', 'Yu Gothic Medium', sans-serif",
                    ],
                    'noto-sans-jp' => [
                        'label' => __('themes::admin.settings.typography.font_noto_sans_jp'),
                        'sample_family' => "'Noto Sans JP', 'Hiragino Kaku Gothic ProN', 'Yu Gothic Medium', sans-serif",
                    ],
                    'noto-serif-jp' => [
                        'label' => __('themes::admin.settings.typography.font_noto_serif_jp'),
                        'sample_family' => "'Noto Serif JP', 'Hiragino Mincho ProN', 'Yu Mincho', serif",
                    ],
                ];
            @endphp
            @foreach($fontOptions as $value => $opt)
                <button type="button"
                    @click="headingFontFamily = '{{ $value }}'"
                    :class="headingFontFamily === '{{ $value }}' ? 'border-indigo-500 ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'"
                    class="flex flex-col items-center gap-1 p-3 rounded-lg border transition-all duration-150 text-center">
                    <span class="text-2xl leading-none text-gray-900 dark:text-gray-100" style="font-family: {{ $opt['sample_family'] }};">Aa 見出し</span>
                    <span class="text-[11px] font-medium text-gray-900 dark:text-gray-100 mt-1 leading-tight">{{ $opt['label'] }}</span>
                </button>
            @endforeach
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('themes::admin.settings.typography.heading_font_family_help') }}</p>

        {{-- Per-region apply toggles. When a region is OFF, its
             --font-heading-{region} variable is not declared, so the
             region CSS rule falls back through var(name, inherit) to
             the parent (body) font stack. This lets the operator use
             one face for some regions and the system default for others.

             <x-form-toggle> emits its own hidden "0" input + checkbox
             "1" pair, so the field always POSTs '0' or '1'. xModel
             expects string values ('0'/'1'), not booleans — Alpine
             state in settings.blade.php matches that shape.
             --}}
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('themes::admin.settings.typography.apply_to') }}</div>
            <div class="flex flex-col gap-2">
                <x-form-toggle
                    id="heading_font_apply_header_toggle"
                    name="heading_font_apply_header"
                    :label="__('themes::admin.settings.typography.apply_header')"
                    xModel="headingFontApplyHeader"
                    color="blue"
                />
                <x-form-toggle
                    id="heading_font_apply_hero_toggle"
                    name="heading_font_apply_hero"
                    :label="__('themes::admin.settings.typography.apply_hero')"
                    xModel="headingFontApplyHero"
                    color="blue"
                />
                <x-form-toggle
                    id="heading_font_apply_footer_toggle"
                    name="heading_font_apply_footer"
                    :label="__('themes::admin.settings.typography.apply_footer')"
                    xModel="headingFontApplyFooter"
                    color="blue"
                />
                <x-form-toggle
                    id="heading_font_apply_content_toggle"
                    name="heading_font_apply_content"
                    :label="__('themes::admin.settings.typography.apply_content')"
                    xModel="headingFontApplyContent"
                    color="blue"
                />
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('themes::admin.settings.typography.apply_to_help') }}</p>
        </div>

        {{-- Per-region tracking (letter-spacing).

             Slider + number input share the same Alpine variable via
             x-model, so dragging the slider updates the number input
             and vice versa. Only the number input carries `name=` so
             POST payload has one value per region. Validation range
             mirrors the CSS var range (-0.1em to 0.3em, step 0.01).
             --}}
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('themes::admin.settings.typography.tracking') }}</div>
            @php
                $trackingRegions = [
                    'header'  => ['alpine' => 'headingFontTrackingHeader',  'name' => 'heading_font_tracking_header',  'label' => __('themes::admin.settings.typography.apply_header')],
                    'hero'    => ['alpine' => 'headingFontTrackingHero',    'name' => 'heading_font_tracking_hero',    'label' => __('themes::admin.settings.typography.apply_hero')],
                    'footer'  => ['alpine' => 'headingFontTrackingFooter',  'name' => 'heading_font_tracking_footer',  'label' => __('themes::admin.settings.typography.apply_footer')],
                    'content' => ['alpine' => 'headingFontTrackingContent', 'name' => 'heading_font_tracking_content', 'label' => __('themes::admin.settings.typography.apply_content')],
                ];
            @endphp
            @foreach($trackingRegions as $key => $reg)
                <div class="mb-3">
                    <label class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                        <span>{{ $reg['label'] }}</span>
                        <span class="font-mono text-gray-500 dark:text-gray-400" x-text="{{ $reg['alpine'] }} + 'em'"></span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="range" min="-0.1" max="0.3" step="0.01"
                            x-model="{{ $reg['alpine'] }}"
                            class="flex-1 accent-indigo-500 cursor-pointer">
                        <input type="number" min="-0.1" max="0.3" step="0.01"
                            x-model="{{ $reg['alpine'] }}"
                            name="{{ $reg['name'] }}"
                            class="w-16 text-xs bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded px-1 py-0.5 dark:text-white">
                    </div>
                </div>
            @endforeach
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('themes::admin.settings.typography.tracking_help') }}</p>
        </div>
    </x-admin.theme-preview-sidebar-section>

    {{-- ===== Default Locale (primary language of theme settings) ===== --}}
    {{-- Declares the locale the operator authored the hero text and other  --}}
    {{-- translatable settings in. The central translation manager UI       --}}
    {{-- (DixlaseMultilingual) excludes this locale from its language       --}}
    {{-- selector, and dls_onepage_localized_setting() short-circuits it to --}}
    {{-- the primary value instead of the resolver — matching the pattern  --}}
    {{-- already used by DixlasePages (per-row `lang`) and DixlaseInquiry.  --}}
    @php
        $localeOptions = \App\Helpers\LocaleHelper::supportedLocaleOptions();
        $currentDefaultLocale = old('default_locale', $settings->default_locale ?? 'auto');
    @endphp
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.default_locale.title')" icon="fas fa-language">
        <select name="default_locale"
            class="input-common block w-full max-w-full p-2 pr-10 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white">
            <option value="auto" {{ $currentDefaultLocale === 'auto' ? 'selected' : '' }}>{{ __('themes::admin.settings.default_locale.auto') }}</option>
            @foreach($localeOptions as $code => $label)
                <option value="{{ $code }}" {{ $currentDefaultLocale === $code ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('themes::admin.settings.default_locale.help') }}</p>
    </x-admin.theme-preview-sidebar-section>

    {{-- ===== Primary Color ===== --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.primary_color.title')" icon="fas fa-swatchbook">
        <div class="grid grid-cols-4 gap-1">
            @php
                $colorOptions = [
                    '#3b82f6' => __('themes::admin.settings.primary_color.blue'),
                    '#8b5cf6' => __('themes::admin.settings.primary_color.purple'),
                    '#10b981' => __('themes::admin.settings.primary_color.green'),
                    '#ef4444' => __('themes::admin.settings.primary_color.red'),
                    '#f97316' => __('themes::admin.settings.primary_color.orange'),
                    '#eab308' => __('themes::admin.settings.primary_color.yellow'),
                    '#92400e' => __('themes::admin.settings.primary_color.brown'),
                    '#ec4899' => __('themes::admin.settings.primary_color.pink'),
                    '#6366f1' => __('themes::admin.settings.primary_color.indigo'),
                    '#1f2937' => __('themes::admin.settings.primary_color.black'),
                    '#6b7280' => __('themes::admin.settings.primary_color.gray'),
                ];
            @endphp
            @foreach($colorOptions as $hex => $label)
                <button type="button"
                    @click="primaryColor = '{{ $hex }}'"
                    :class="primaryColor === '{{ $hex }}' ? 'ring-2 ring-offset-2 ring-gray-900 dark:ring-white dark:ring-offset-gray-800 scale-110' : 'hover:scale-105'"
                    class="flex flex-col items-center gap-1 p-2 rounded-lg transition-all duration-150 w-full"
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

    {{-- ===== Header Logo (Dark Mode) ===== --}}
    {{-- Optional. If left empty, the base header logo is shown in dark
         mode with a `filter: invert(1)` CSS fallback — that recovers
         single-colour SVG / PNG marks that would otherwise disappear
         against the dark header, at the cost of also flipping the
         hue on multi-colour marks. Uploading an explicitly-authored
         dark-mode file skips the fallback and gives pixel-perfect
         control. --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.header.header_logo_dark')" icon="fas fa-moon">
        <x-media.picker
            name="header_logo_dark_id"
            :value="$settings->header_logo_dark_id ?? null"
            :media="$headerLogoDark ?? null"
            :help="__('themes::admin.settings.header.header_logo_dark_help')"
            :error="$errors->first('header_logo_dark_id')"
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

    {{-- ===== Hero Text (title / sub-title — also editable inline in the preview) ===== --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.hero.edit_title')" icon="fas fa-heading" :open="true">
        <div class="space-y-3">
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('themes::admin.settings.hero.main_title') }}</label>
                <textarea x-model="heroMainTitle" rows="2"
                    class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('themes::admin.settings.hero.sub_title') }}</label>
                <textarea x-model="heroSubTitle" rows="3"
                    class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('themes::admin.settings.hero.edit_help') }}</p>
        </div>
    </x-admin.theme-preview-sidebar-section>

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

    {{-- ===== Hero Background Video ===== --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.hero.background_video')" icon="fas fa-video">
        <x-media.picker
            name="hero_background_video_id"
            :value="$settings->hero_background_video_id ?? null"
            :media="$heroBackgroundVideo ?? null"
            :help="__('themes::admin.settings.hero.background_video_help')"
            :error="$errors->first('hero_background_video_id')"
            aspectRatio="hero"
            :buttonText="__('themes::admin.settings.hero.select_background_video')"
            :confirmUploadNavigation="true"
            :allowedTypes="['video/mp4', 'video/webm', 'video/quicktime']"
        />
    </x-admin.theme-preview-sidebar-section>

    {{-- ===== Hero Background Color (shape + color mode + custom colors) =====
         Two orthogonal pickers:
           (1) Color mode — primary (auto) or custom (operator-picked)
           (2) Shape       — radial / linear-vertical / solid

         Both only take effect when neither a hero background image nor
         a background video is set (both above win); the front partial
         short-circuits then.

         Custom mode reveals color inputs:
           - shape='solid': one color slot (color1)
           - other shapes: two color slots (color1 = center/top, color2 = outside/bottom)

         Primary mode auto-picks a second endpoint that matches the
         page body colour per appearance mode (see partials/hero.blade.php
         resolver), so operators don't have to think about it. --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.hero.gradient.title')" icon="fas fa-circle-half-stroke">
        <input type="hidden" name="hero_gradient_mode" :value="heroGradientMode">
        <input type="hidden" name="hero_gradient_color" :value="heroGradientColor">
        <input type="hidden" name="hero_gradient_color_2" :value="heroGradientColor2">
        {{-- Dark-mode overrides. Empty string when unset — the front
             resolver falls back to the light value (backward-compat
             for sites saved before per-mode split). --}}
        <input type="hidden" name="hero_gradient_color_dark" :value="heroGradientColorDark">
        <input type="hidden" name="hero_gradient_color_2_dark" :value="heroGradientColor2Dark">
        <input type="hidden" name="hero_gradient_shape" :value="heroGradientShape">

        {{-- Color mode picker (2 buttons — primary / custom) --}}
        <div class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('themes::admin.settings.hero.gradient.color_label') }}</div>
        <div class="grid grid-cols-2 gap-2">
            @php
                $gradientModes = [
                    'primary' => ['label' => __('themes::admin.settings.hero.gradient.mode_primary'), 'icon' => 'fas fa-palette'],
                    'custom'  => ['label' => __('themes::admin.settings.hero.gradient.mode_custom'),  'icon' => 'fas fa-eye-dropper'],
                ];
            @endphp
            @foreach ($gradientModes as $value => $mode)
                <button type="button"
                    @click="heroGradientMode = '{{ $value }}'"
                    :class="heroGradientMode === '{{ $value }}' ? 'border-indigo-500 ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'"
                    class="flex flex-col items-center gap-1 p-2 rounded-lg border transition-all duration-150 text-center">
                    <i class="{{ $mode['icon'] }} text-gray-600 dark:text-gray-400"></i>
                    <span class="text-[11px] font-medium text-gray-900 dark:text-gray-100 leading-tight">{{ $mode['label'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Shape picker (radial / linear-vertical / solid) — placed
             above the color inputs since the shape decides how many
             color slots matter (solid → 1 slot, radial/linear → 2). --}}
        <div class="mt-4">
            <div class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('themes::admin.settings.hero.gradient.shape_label') }}</div>
            <div class="grid grid-cols-3 gap-2">
                @php
                    $gradientShapes = [
                        'radial'          => ['label' => __('themes::admin.settings.hero.gradient.shape_radial'),          'icon' => 'fas fa-circle'],
                        'linear-vertical' => ['label' => __('themes::admin.settings.hero.gradient.shape_linear_vertical'), 'icon' => 'fas fa-arrow-down'],
                        'solid'           => ['label' => __('themes::admin.settings.hero.gradient.shape_solid'),           'icon' => 'fas fa-square'],
                    ];
                @endphp
                @foreach ($gradientShapes as $value => $shape)
                    <button type="button"
                        @click="heroGradientShape = '{{ $value }}'"
                        :class="heroGradientShape === '{{ $value }}' ? 'border-indigo-500 ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'"
                        class="flex flex-col items-center gap-1 p-2 rounded-lg border transition-all duration-150 text-center">
                        <i class="{{ $shape['icon'] }} text-gray-600 dark:text-gray-400"></i>
                        <span class="text-[11px] font-medium text-gray-900 dark:text-gray-100 leading-tight">{{ $shape['label'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Custom color inputs — visible only in custom mode.
             Grouped by appearance (Light / Dark) rather than by slot so
             the operator picks two related colors together per theme.
             Empty dark input → the front resolver falls back to the
             light value at render time. Slot count depends on shape:
               solid           → 1 slot × 2 modes = 2 inputs
               radial / linear → 2 slots × 2 modes = 4 inputs --}}
        <div x-show="heroGradientMode === 'custom'" x-cloak class="mt-3">
            {{-- Light group --}}
            <div class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('themes::admin.settings.hero.gradient.custom_color_light') }}</div>
            <div class="text-[11px] text-gray-600 dark:text-gray-400 mb-1"
                x-text="heroGradientShape === 'solid'
                    ? '{{ __('themes::admin.settings.hero.gradient.custom_color_solid_label') }}'
                    : '{{ __('themes::admin.settings.hero.gradient.custom_color_label') }}'">
            </div>
            <div class="flex items-center gap-2">
                <input type="color"
                    id="hero_gradient_color_picker"
                    x-model="heroGradientColor"
                    class="h-7 w-10 rounded border border-gray-300 dark:border-gray-500 cursor-pointer bg-transparent">
                <input type="text"
                    x-model="heroGradientColor"
                    pattern="^#[0-9a-fA-F]{6}$"
                    maxlength="7"
                    class="input-common flex-1 p-1 text-xs font-mono bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded dark:text-white">
            </div>
            <div x-show="heroGradientShape !== 'solid'" x-cloak class="mt-2">
                <div class="text-[11px] text-gray-600 dark:text-gray-400 mb-1">
                    {{ __('themes::admin.settings.hero.gradient.custom_color_2_label') }}
                </div>
                <div class="flex items-center gap-2">
                    <input type="color"
                        id="hero_gradient_color_2_picker"
                        x-model="heroGradientColor2"
                        class="h-7 w-10 rounded border border-gray-300 dark:border-gray-500 cursor-pointer bg-transparent">
                    <input type="text"
                        x-model="heroGradientColor2"
                        pattern="^#[0-9a-fA-F]{6}$"
                        maxlength="7"
                        class="input-common flex-1 p-1 text-xs font-mono bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded dark:text-white">
                </div>
            </div>

            {{-- Dark group --}}
            <div class="text-xs font-medium text-gray-700 dark:text-gray-300 mt-3 mb-1">{{ __('themes::admin.settings.hero.gradient.custom_color_dark') }}</div>
            <div class="text-[11px] text-gray-600 dark:text-gray-400 mb-1"
                x-text="heroGradientShape === 'solid'
                    ? '{{ __('themes::admin.settings.hero.gradient.custom_color_solid_label') }}'
                    : '{{ __('themes::admin.settings.hero.gradient.custom_color_label') }}'">
            </div>
            <div class="flex items-center gap-2">
                <input type="color"
                    id="hero_gradient_color_dark_picker"
                    :value="heroGradientColorDark || heroGradientColor"
                    @input="heroGradientColorDark = $event.target.value"
                    class="h-7 w-10 rounded border border-gray-300 dark:border-gray-500 cursor-pointer bg-transparent">
                <input type="text"
                    x-model="heroGradientColorDark"
                    :placeholder="heroGradientColor"
                    pattern="^#[0-9a-fA-F]{6}$"
                    maxlength="7"
                    class="input-common flex-1 p-1 text-xs font-mono bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded dark:text-white">
            </div>
            <div x-show="heroGradientShape !== 'solid'" x-cloak class="mt-2">
                <div class="text-[11px] text-gray-600 dark:text-gray-400 mb-1">
                    {{ __('themes::admin.settings.hero.gradient.custom_color_2_label') }}
                </div>
                <div class="flex items-center gap-2">
                    <input type="color"
                        id="hero_gradient_color_2_dark_picker"
                        :value="heroGradientColor2Dark || heroGradientColor2"
                        @input="heroGradientColor2Dark = $event.target.value"
                        class="h-7 w-10 rounded border border-gray-300 dark:border-gray-500 cursor-pointer bg-transparent">
                    <input type="text"
                        x-model="heroGradientColor2Dark"
                        :placeholder="heroGradientColor2"
                        pattern="^#[0-9a-fA-F]{6}$"
                        maxlength="7"
                        class="input-common flex-1 p-1 text-xs font-mono bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded dark:text-white">
                </div>
            </div>

            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-2">{{ __('themes::admin.settings.hero.gradient.custom_color_dark_hint') }}</p>
        </div>

        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">{{ __('themes::admin.settings.hero.gradient.help') }}</p>
    </x-admin.theme-preview-sidebar-section>

    {{-- ===== Hero Foreground Image ===== --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.hero.foreground_image')" icon="fas fa-image">
        <x-media.picker
            name="hero_foreground_image_id"
            :value="$settings->hero_foreground_image_id ?? null"
            :media="$heroForegroundImage ?? null"
            :help="__('themes::admin.settings.hero.foreground_image_help')"
            :error="$errors->first('hero_foreground_image_id')"
            aspectRatio="hero"
            :buttonText="__('themes::admin.settings.hero.select_foreground_image')"
            :confirmUploadNavigation="true"
        />
    </x-admin.theme-preview-sidebar-section>

    {{-- ===== Hero Buttons (visibility + target) ===== --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.hero.buttons.title')" icon="fas fa-mouse-pointer">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox"
                @change="heroButtonEnabled = $el.checked ? '1' : '0'"
                :checked="heroButtonEnabled === '1'"
                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('themes::admin.settings.hero.buttons.primary_enable') }}</span>
        </label>
        <div class="ml-6 mt-1 mb-3" x-show="heroButtonEnabled === '1'">
            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('themes::admin.settings.hero.buttons.primary_target') }}</label>
            <select x-model="heroButtonTarget"
                class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                <option value="_self">{{ __('themes::admin.settings.hero.buttons.target_self') }}</option>
                <option value="_blank">{{ __('themes::admin.settings.hero.buttons.target_blank') }}</option>
            </select>
        </div>

        <label class="flex items-center gap-2 cursor-pointer mt-2">
            <input type="checkbox"
                @change="heroButtonSecondaryEnabled = $el.checked ? '1' : '0'"
                :checked="heroButtonSecondaryEnabled === '1'"
                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('themes::admin.settings.hero.buttons.secondary_enable') }}</span>
        </label>
        <div class="ml-6 mt-1 mb-2" x-show="heroButtonSecondaryEnabled === '1'">
            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('themes::admin.settings.hero.buttons.secondary_target') }}</label>
            <select x-model="heroButtonSecondaryTarget"
                class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                <option value="_self">{{ __('themes::admin.settings.hero.buttons.target_self') }}</option>
                <option value="_blank">{{ __('themes::admin.settings.hero.buttons.target_blank') }}</option>
            </select>
        </div>

        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('themes::admin.settings.hero.buttons.help') }}</p>
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
        @unless($inquiryReady)
        <div x-show="showInquiryForm === '1'" x-cloak
            class="mt-3 p-3 rounded-md border border-amber-300 dark:border-amber-600 bg-amber-50 dark:bg-amber-900/30">
            <p class="text-xs text-amber-800 dark:text-amber-200 flex items-start gap-2">
                <i class="fas fa-exclamation-triangle mt-0.5"></i>
                <span>{{ __('themes::admin.settings.plugins.inquiry.not_configured') }}</span>
            </p>
            <a href="{{ route('dixlase-inquiry::admin.inquiry.settings.admin-notification') }}"
                class="inline-flex items-center gap-1 mt-2 text-xs font-medium text-amber-900 dark:text-amber-100 underline hover:no-underline">
                <i class="fas fa-arrow-right"></i>
                {{ __('themes::admin.settings.plugins.inquiry.configure_link') }}
            </a>
        </div>
        @endunless
    </x-admin.theme-preview-sidebar-section>
    @else
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.plugins.inquiry.title')" icon="fas fa-envelope">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('themes::admin.settings.plugins.inquiry.plugin_required') }}</p>
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
                    ['name' => 'footer_sns_github', 'label' => 'GitHub', 'prefix' => 'github.com/'],
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

    {{-- ===== Copyright =====
         The front renders `© <current year>` + this suffix; the prefix
         is auto-rendered at request time and not editable. We show the
         same `© <year>` as a non-editable label to the left of the
         input so the user can see exactly what gets prepended. --}}
    <x-admin.theme-preview-sidebar-section :title="__('themes::admin.settings.footer.copyright_section')" icon="fas fa-copyright">
        <div class="flex items-center gap-2 my-2">
            <span class="text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap shrink-0">
                © <span x-text="copyrightYear"></span>
            </span>
            <input type="text"
                   id="footer_copyright"
                   name="footer_copyright"
                   x-model="footerCopyright"
                   value="{{ old('footer_copyright', $footerCopyrightSuffix ?? '') }}"
                   class="input-common input-full flex-1 min-w-0">
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {{ __('themes::admin.settings.footer.copyright_help') }}
        </p>
    </x-admin.theme-preview-sidebar-section>

</x-admin.theme-preview-sidebar>
