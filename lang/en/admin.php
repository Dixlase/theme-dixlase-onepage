<?php

/**
 * This file is part of Dixlase OnePage.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
 * https://exc-d.com
 *
 * Dixlase OnePage is dual-licensed. You may use this file under either:
 *
 *   (a) the GNU General Public License version 3 or later, as published
 *       by the Free Software Foundation; or
 *
 *   (b) a commercial license agreement obtained from exc-D inc.
 *
 * Unless you have entered into a commercial license agreement, this
 * file is governed by the GPL terms below.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Dixlase Default Theme Admin Language Lines (English)
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'title' => 'Theme Settings',
        'updated_successfully' => 'Theme settings updated successfully',
        'select_logo_image' => 'Select Logo Image',
        'select_favicon_image' => 'Select Favicon Image',

        // Header Section
        'header' => [
            'title' => 'Header & Favicon Settings',
            'header_logo' => 'Header Logo',
            'header_logo_help' => 'Set the logo image to be displayed in the site header.',
            'header_logo_dark' => 'Header Logo (Dark Mode)',
            'header_logo_dark_help' => 'Optional. Uploaded here, this logo is shown in dark mode instead of the light one above. If left empty, the light logo is auto-inverted (`filter: invert(1)`) as a fallback so single-colour marks stay visible on dark backgrounds — for multi-colour marks, upload an explicitly-authored dark version to avoid hue-flip.',
            'favicon' => 'Favicon',
            'favicon_help' => 'Set the icon to be displayed in the browser tab. (Recommended size: 32x32px or 64x64px)',
        ],

        // Hero Section
        'hero' => [
            'title' => 'Hero Section Settings',
            'basic_settings' => 'Basic Settings',
            'background_section' => 'Background Settings',
            'background_image' => 'Hero Background Image',
            'background_image_help' => 'Full-screen hero area background image. Also used as fallback when video cannot be played.',
            'select_background_image' => 'Select Background Image',
            'background_video' => 'Hero Background Video',
            'background_video_help' => 'Background video for the hero area. Plays automatically (muted) on supported browsers. Falls back to image if playback is blocked.',
            'select_background_video' => 'Select Background Video',
            'foreground_image' => 'Hero Foreground Image',
            'foreground_image_help' => 'Image rendered below the headline and buttons (e.g. a product or dashboard screenshot). When set, the hero text aligns to the top of the section so the image has room. Leave empty to keep the text vertically centered.',
            'select_foreground_image' => 'Select Foreground Image',
            'content_section' => 'Content',
            'main_title_label' => 'Main Title',
            'main_title' => 'Main Title',
            'main_title_help' => 'Hero area main heading',
            'gradient' => [
                'title' => 'Hero Background Color',
                'help' => 'Background fill behind the hero text. Shown only when neither a hero background image nor a background video is set (both win over this fill). With Primary color the second gradient stop auto-matches the page body colour (light / dark) so the hero blends seamlessly.',
                'shape_label' => 'Shape',
                'shape_radial' => 'Radial',
                'shape_linear_vertical' => 'Linear (vertical)',
                'shape_solid' => 'Solid',
                'color_label' => 'Color',
                'mode_primary' => 'Primary color',
                'mode_custom' => 'Custom color',
                'custom_color_label' => 'Color 1 (center / top)',
                'custom_color_2_label' => 'Color 2 (outside / bottom)',
                'custom_color_solid_label' => 'Color',
            ],
            'sub_title_label' => 'Sub Title',
            'sub_title' => 'Sub Title',
            'sub_title_help' => 'Hero area description text',
            'primary_button' => 'Primary Button',
            'button_text' => 'Button Text',
            'button_link' => 'Button Link',
            'secondary_button' => 'Secondary Button',
            'button_secondary_text' => 'Button Text',
            'button_secondary_link' => 'Button Link',
            'buttons' => [
                'title' => 'Hero Buttons',
                'primary_enable' => 'Display primary button',
                'secondary_enable' => 'Display secondary button',
                'help' => 'Toggle off to hide a button. The button text and link are kept and can be restored by toggling back on.',
                'primary_target' => 'Primary button target',
                'secondary_target' => 'Secondary button target',
                'target_self' => 'Same window',
                'target_blank' => 'New window',
            ],
            'edit_title' => 'Hero Text',
            'edit_help' => 'These fields edit the hero headline and sub-headline. The same values can also be edited inline by clicking them in the preview.',
        ],

        // Footer Section
        'footer' => [
            'title' => 'Footer Settings',
            'description' => 'Footer Description',
            'description_help' => 'Description text displayed in footer',
            'links' => 'Footer Links',
            'links_help' => 'List of links to display in footer',
            'link_title' => 'Title',
            'link_url' => 'URL',
            'add_link' => 'Add Link',
            'remove_link' => 'Remove Link',
            'copyright_section' => 'Copyright',
            'copyright' => 'Copyright',
            'copyright_help' => 'Copyright text displayed in footer. `©` and the current year are inserted automatically (auto-updates across year boundaries); edit only the text that follows.',
            'sns_title' => 'SNS Links',
            'sns_social_media' => 'Social Media',
            'sns_professional' => 'Professional',
            'sns_other' => 'Other',
            'sns_username' => 'Username',
            'sns_invite_code' => 'Invite Code',
            'sns_instagram' => 'Instagram URL',
            'sns_x' => 'X (Twitter) URL',
            'sns_facebook' => 'Facebook URL',
            'sns_tiktok' => 'TikTok URL',
            'sns_bluesky' => 'Bluesky URL',
            'sns_threads' => 'Threads URL',
            'sns_linkedin' => 'LinkedIn URL',
            'sns_youtube' => 'YouTube URL',
            'sns_pinterest' => 'Pinterest URL',
            'sns_discord' => 'Discord URL',
            'sns_github' => 'GitHub URL',
        ],

        // Primary Color
        'primary_color' => [
            'title' => 'Primary Color',
            'help' => 'Select the accent color used for buttons and links.',
            'blue' => 'Blue',
            'purple' => 'Purple',
            'green' => 'Green',
            'red' => 'Red',
            'orange' => 'Orange',
            'yellow' => 'Yellow',
            'brown' => 'Brown',
            'pink' => 'Pink',
            'indigo' => 'Indigo',
            'black' => 'Black',
            'gray' => 'Gray',
        ],

        // Typography Section
        'typography' => [
            'title' => 'Typography',
            'heading_font_family' => 'Heading Font',
            'heading_font_family_help' => 'Font applied to site name, hero title, and every heading (h1–h4) on the front. Body text stays on the system stack. Fonts are served over Bunny Fonts (privacy-friendly, cookie-free).',
            'font_cormorant' => 'Cormorant Garamond',
            'font_cormorant_description' => 'Elegant classical serif for a bookish, editorial feel. Latin only — Japanese glyphs render in the system Mincho.',
            'font_jost' => 'Jost',
            'font_jost_description' => 'Modern geometric sans, clean and technical. Latin only — Japanese glyphs render in the system Gothic.',
            'font_noto_sans_jp' => 'Noto Sans JP',
            'font_noto_sans_jp_description' => 'Versatile sans-serif with full Japanese coverage. The safe default.',
            'font_noto_serif_jp' => 'Noto Serif JP',
            'font_noto_serif_jp_description' => 'Traditional Japanese serif with clear stroke contrast.',
            'apply_to' => 'Apply to',
            'apply_to_help' => 'Turn a region off to leave it on the default system font. This lets you use, say, Mincho for the hero and content headings while keeping the header on a plain sans-serif.',
            'apply_header' => 'Header site name',
            'apply_hero' => 'Hero title',
            'apply_footer' => 'Footer site name',
            'apply_content' => 'Page headings (h1–h4)',
            'tracking' => 'Tracking (letter-spacing)',
            'tracking_help' => 'Per-region letter-spacing in em units. Negative values tighten the characters, positive values open them up. Range −0.1em to 0.3em.',
        ],

        // Appearance Section
        'appearance' => [
            'title' => 'Appearance Mode',
            'description' => 'Configure the theme appearance mode (light/dark).',
            'mode_auto' => 'Auto',
            'mode_light' => 'Light',
            'mode_dark' => 'Dark',
            'mode_help' => 'Auto: Follows user system settings / Light: Bright theme / Dark: Dark theme',
        ],

        // Default locale (primary language the theme settings are authored in)
        'default_locale' => [
            'title' => 'Default Language',
            'auto' => 'Auto (use site default)',
            'help' => 'The language the hero text and other translatable settings are written in. The translation manager excludes this language from its selector, and the front renders the primary values directly for viewers in this language.',
        ],

        // Multilingual switcher (DixlaseMultilingual integration)
        'multilingual' => [
            'title' => 'Multilingual switcher',
            'switcher_label' => 'Show language switcher in header / footer',
            'help' => 'Renders the language switcher in the header and footer when more than one locale is enabled. Picking the default locale jumps to the bare path (no locale prefix); picking another locale jumps to /{locale}/...',
            'requires_plugin' => 'Install and enable the DixlaseMultilingual plugin to surface the switcher.',
            'requires_enabled' => 'Enable multilingual in the plugin\'s settings to surface the switcher.',
        ],

        // Plugin Integration
        'plugins' => [
            'menu' => [
                'title' => 'Header Menu',
                'select_menu' => 'Select a menu',
                'none' => 'No menu',
                'help' => 'Select a menu to display in the header navigation.',
            ],
            'footer_menu' => [
                'title' => 'Footer Menu',
                'help' => 'Select a menu to display in the footer.',
                'plugin_required' => 'Install and enable the DixlaseMenus plugin to select a footer menu.',
            ],
            'inquiry' => [
                'title' => 'Contact Form',
                'enable' => 'Display contact form above footer',
                'help' => 'Display the inquiry form section above the footer.',
                'plugin_required' => 'Install and enable the DixlaseInquiry plugin to use the contact form.',
                'not_configured' => 'The contact form will not appear on the front page until the inquiry plugin is fully configured. Please set the admin email in the inquiry plugin settings.',
                'configure_link' => 'Open inquiry plugin settings',
            ],
        ],

        // Header Menu
        'header_menu' => [
            'edit_badge' => 'Edit Menu',
            'confirm_title' => 'Edit Header Menu?',
            'confirm_message' => 'You will be redirected to the menu editor.<br>Unsaved changes will be lost.',
            'confirm_ok' => 'OK',
        ],

        // Footer Menu
        'footer_menu' => [
            'edit_badge' => 'Edit Menu',
            'confirm_title' => 'Edit Footer Menu?',
            'confirm_message' => 'You will be redirected to the menu editor.<br>Unsaved changes will be lost.',
            'confirm_ok' => 'OK',
        ],

        // Front Page Content
        'front_content' => [
            'edit_badge' => 'Edit Front Page',
            'confirm_title' => 'Edit Front Page?',
            'confirm_message' => 'You will be redirected to the front page editor.<br>Unsaved changes will be lost.',
            'confirm_ok' => 'OK',
        ],

        // Inquiry Form
        'inquiry_form' => [
            'edit_badge' => 'Edit Inquiry Form',
            'confirm_title' => 'Edit Inquiry Form?',
            'confirm_message' => 'You will be redirected to the inquiry form settings.<br>Unsaved changes will be lost.',
            'confirm_ok' => 'OK',
        ],

        // Editor UI
        'editor' => [
            'preview_title' => 'Theme Preview',
            'click_to_edit' => 'Click to edit',
            'sidebar_open' => 'Open settings panel',
            'sidebar_close' => 'Close settings panel',
        ],
    ],

    'validation' => [
        'link_scheme_not_allowed' => 'This link uses a scheme that is not allowed. Use http, https, mailto, tel, or a path beginning with /.',
    ],
];
