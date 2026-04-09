<?php

/**
 * This file is part of Dixlase OnePage.
 *
 * Copyright (C) 2026 exc-D inc.
 * https://exc-d.com
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
            'content_section' => 'Content',
            'main_title_label' => 'Main Title',
            'main_title' => 'Main Title',
            'main_title_help' => 'Hero area main heading',
            'sub_title_label' => 'Sub Title',
            'sub_title' => 'Sub Title',
            'sub_title_help' => 'Hero area description text',
            'primary_button' => 'Primary Button',
            'button_text' => 'Button Text',
            'button_link' => 'Button Link',
            'secondary_button' => 'Secondary Button',
            'button_secondary_text' => 'Button Text',
            'button_secondary_link' => 'Button Link',
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
            'copyright_help' => 'Copyright text displayed in footer',
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
            'pink' => 'Pink',
            'indigo' => 'Indigo',
            'teal' => 'Teal',
            'black' => 'Black',
            'gray' => 'Gray',
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
];
