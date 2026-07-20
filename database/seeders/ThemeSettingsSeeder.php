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

namespace Themes\DixlaseOnePage\Database\Seeders;

use Illuminate\Database\Seeder;
use Themes\DixlaseOnePage\App\Models\ThemeSetting;

class ThemeSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Header & Favicon
            'header_logo_id' => null,
            'header_logo_dark_id' => null,
            'favicon_id' => null,

            // Hero Section
            'hero_background_image_id' => null,
            'hero_foreground_image_id' => null,
            'hero_main_title' => 'Welcome to '.config('app.name', 'Dixlase'),
            'hero_sub_title' => 'Modern CMS Platform for Building Amazing Websites',
            'hero_button_text' => 'Get Started',
            'hero_button_link' => '#',
            'hero_button_enabled' => '1',
            'hero_button_target' => '_self',
            'hero_button_secondary_text' => 'Learn More',
            'hero_button_secondary_link' => '#features',
            'hero_button_secondary_enabled' => '1',
            'hero_button_secondary_target' => '_self',

            // Footer
            'footer_description' => 'Powered by Dixlase CMS',
            'footer_links' => json_encode([]),
            // `© <year>` is rendered automatically by the front (current
            // year); persist only the editable suffix. `and Dixlase
            // contributors` matches the Dixlase-brand attribution
            // pattern used on admin surfaces.
            'footer_copyright' => config('app.name', 'Dixlase').' and Dixlase contributors',

            // SNS Links
            'footer_sns_instagram' => null,
            'footer_sns_x' => null,
            'footer_sns_facebook' => null,
            'footer_sns_tiktok' => null,
            'footer_sns_bluesky' => null,
            'footer_sns_threads' => null,
            'footer_sns_linkedin' => null,
            'footer_sns_youtube' => null,
            'footer_sns_pinterest' => null,
            'footer_sns_discord' => null,
            'footer_sns_github' => null,

            // Colors
            'primary_color' => '#3b82f6',
            'secondary_color' => '#6b7280',
            'accent_color' => '#10b981',

            // Multilingual: locale the primary settings are authored in.
            // 'auto' resolves to the site default at runtime.
            'default_locale' => 'auto',

            // Heading font family. See UpdateThemeSettingsRequest for the value set.
            'heading_font_family' => 'noto-sans-jp',
            // Per-region apply toggles (default: apply everywhere).
            'heading_font_apply_header' => '1',
            'heading_font_apply_hero' => '1',
            'heading_font_apply_footer' => '1',
            'heading_font_apply_content' => '1',
            // Per-region tracking (letter-spacing) in em, default 0 (no tracking).
            'heading_font_tracking_header' => '0',
            'heading_font_tracking_hero' => '0',
            'heading_font_tracking_footer' => '0',
            'heading_font_tracking_content' => '0',
        ];

        foreach ($settings as $name => $value) {
            ThemeSetting::updateOrCreate(
                ['name' => $name],
                ['value' => $value]
            );
        }

        $this->command->info('テーマ設定のデフォルト値を作成しました。');
    }
}
