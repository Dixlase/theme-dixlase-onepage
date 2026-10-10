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

namespace Themes\DixlaseOnePage\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DixlaseOnePageSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のデータがあるかチェック
        if (DB::table('thm_dixlase_onepage_settings')->exists()) {
            $this->command->info('Dixlase OnePage settings already exist. Skipping...');

            return;
        }

        $settings = [
            // Hero Section
            ['name' => 'hero_background_image_id', 'value' => null],
            ['name' => 'hero_foreground_image_id', 'value' => null],
            ['name' => 'hero_main_title', 'value' => 'Welcome to '.config('app.name', 'Dixlase')],
            ['name' => 'hero_sub_title', 'value' => 'Modern CMS Platform for Building Amazing Websites'],
            ['name' => 'hero_button_text', 'value' => 'Get Started'],
            ['name' => 'hero_button_link', 'value' => '#'],
            ['name' => 'hero_button_target', 'value' => '_self'],
            ['name' => 'hero_button_enabled', 'value' => '1'],
            ['name' => 'hero_button_secondary_text', 'value' => 'Learn More'],
            ['name' => 'hero_button_secondary_link', 'value' => '#features'],
            ['name' => 'hero_button_secondary_enabled', 'value' => '1'],
            ['name' => 'hero_button_secondary_target', 'value' => '_self'],

            // Footer
            ['name' => 'footer_description', 'value' => 'Powered by Dixlase CMS'],
            ['name' => 'footer_links', 'value' => json_encode([])],
            // `© <year>` is rendered automatically by the front (current
            // year); persist only the editable suffix. `and Dixlase
            // contributors` matches the Dixlase-brand attribution
            // pattern used on admin surfaces.
            ['name' => 'footer_copyright', 'value' => config('app.name', 'Dixlase').' and Dixlase contributors'],

            // SNS Links
            ['name' => 'footer_sns_instagram', 'value' => null],
            ['name' => 'footer_sns_x', 'value' => null],
            ['name' => 'footer_sns_facebook', 'value' => null],
            ['name' => 'footer_sns_tiktok', 'value' => null],
            ['name' => 'footer_sns_bluesky', 'value' => null],
            ['name' => 'footer_sns_threads', 'value' => null],
            ['name' => 'footer_sns_linkedin', 'value' => null],
            ['name' => 'footer_sns_youtube', 'value' => null],
            ['name' => 'footer_sns_pinterest', 'value' => null],
            ['name' => 'footer_sns_discord', 'value' => null],
            ['name' => 'footer_sns_github', 'value' => null],

            // Appearance Mode
            ['name' => 'appearance_mode', 'value' => '0'], // 0: Auto, 1: Light, 2: Dark
            ['name' => 'appearance_toggle_enabled', 'value' => '0'], // 0: hidden, 1: shown on the front end
            ['name' => 'appearance_toggle_placement', 'value' => 'footer'], // footer | float | both

            // Multilingual
            // The locale the primary-stored theme-settings values are written
            // in. 'auto' resolves to the site default at runtime. The central
            // translation manager UI excludes this locale from its language
            // selector; the localized helper short-circuits it to the primary.
            ['name' => 'default_locale', 'value' => 'auto'],
        ];

        foreach ($settings as $setting) {
            DB::table('thm_dixlase_onepage_settings')->insert([
                'name' => $setting['name'],
                'value' => $setting['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Dixlase OnePage settings created successfully!');
    }
}
