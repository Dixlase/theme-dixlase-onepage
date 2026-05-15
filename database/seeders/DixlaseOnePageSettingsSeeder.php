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
use Illuminate\Support\Facades\DB;

class DixlaseOnePageSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のデータがあるかチェック
        if (DB::table('thm_dixlase_one_page_settings')->exists()) {
            $this->command->info('Dixlase OnePage settings already exist. Skipping...');
            return;
        }

        $settings = [
            // Hero Section
            ['name' => 'hero_background_image_id', 'value' => null],
            ['name' => 'hero_main_title', 'value' => 'Welcome to ' . config('app.name', 'Dixlase')],
            ['name' => 'hero_sub_title', 'value' => 'Modern CMS Platform for Building Amazing Websites'],
            ['name' => 'hero_button_text', 'value' => 'Get Started'],
            ['name' => 'hero_button_link', 'value' => '#'],
            ['name' => 'hero_button_enabled', 'value' => '1'],
            ['name' => 'hero_button_secondary_text', 'value' => 'Learn More'],
            ['name' => 'hero_button_secondary_link', 'value' => '#features'],
            ['name' => 'hero_button_secondary_enabled', 'value' => '1'],
            
            // Footer
            ['name' => 'footer_description', 'value' => 'Powered by Dixlase CMS'],
            ['name' => 'footer_links', 'value' => json_encode([])],
            // `© <year>` is rendered automatically by the front (current
            // year); persist only the editable suffix.
            ['name' => 'footer_copyright', 'value' => config('app.name', 'Dixlase') . '. All rights reserved.'],
            
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
            
            // Appearance Mode
            ['name' => 'appearance_mode', 'value' => '0'], // 0: Auto, 1: Light, 2: Dark
        ];

        foreach ($settings as $setting) {
            DB::table('thm_dixlase_one_page_settings')->insert([
                'name' => $setting['name'],
                'value' => $setting['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Dixlase OnePage settings created successfully!');
    }
}
