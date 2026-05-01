<?php

/**
 * This file is part of Dixlase OnePage.
 *
 * Copyright (C) 2026 exc-D inc.
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
    | Theme Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the Dixlase OnePage theme.
    | You can customize colors, fonts, and other theme settings here.
    |
    */

    'name' => 'Dixlase OnePage',
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Color Scheme
    |--------------------------------------------------------------------------
    */

    'colors' => [
        'primary' => '#3b82f6',
        'secondary' => '#6b7280',
        'accent' => '#10b981',
        'success' => '#10b981',
        'warning' => '#f59e0b',
        'error' => '#ef4444',
        'info' => '#3b82f6',
    ],

    /*
    |--------------------------------------------------------------------------
    | Typography
    |--------------------------------------------------------------------------
    */

    'fonts' => [
        'body' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
        'heading' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
        'mono' => 'ui-monospace, SFMono-Regular, "SF Mono", Menlo, Monaco, Consolas, monospace',
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    'layout' => [
        'container_max_width' => '1280px',
        'header_height' => '64px',
        'footer_height' => 'auto',
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    */

    'features' => [
        'dark_mode' => true,
        'responsive' => true,
        'lazy_loading' => true,
        'smooth_scroll' => true,
        'external_links_new_tab' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'navigation' => [
        'position' => 'top', // top, bottom, both
        'sticky' => true,
        'mobile_breakpoint' => '768px',
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    */

    'footer' => [
        'show_copyright' => true,
        'show_powered_by' => true,
        'columns' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    'seo' => [
        'meta_description' => 'Dixlase - Modern CMS Platform',
        'meta_keywords' => 'dixlase, cms, laravel, php',
        'og_image' => '/images/og-image.jpg',
    ],
];
