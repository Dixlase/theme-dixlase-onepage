<?php

namespace Themes\DixlaseDefaultTheme\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DixlaseDefaultThemeSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のデータがあるかチェック
        if (DB::table('thm_dixlase_default_theme_settings')->exists()) {
            $this->command->info('Dixlase Default Theme settings already exist. Skipping...');
            return;
        }

        $settings = [
            // Hero Section
            ['name' => 'hero_background_image_id', 'value' => null],
            ['name' => 'hero_main_title', 'value' => 'Welcome to ' . config('app.name', 'Dixlase')],
            ['name' => 'hero_sub_title', 'value' => 'Modern CMS Platform for Building Amazing Websites'],
            ['name' => 'hero_button_text', 'value' => 'Get Started'],
            ['name' => 'hero_button_link', 'value' => '#'],
            ['name' => 'hero_button_secondary_text', 'value' => 'Learn More'],
            ['name' => 'hero_button_secondary_link', 'value' => '#features'],
            
            // Footer
            ['name' => 'footer_description', 'value' => 'Powered by Dixlase CMS'],
            ['name' => 'footer_links', 'value' => json_encode([])],
            ['name' => 'footer_copyright', 'value' => '© ' . date('Y') . ' ' . config('app.name', 'Dixlase') . '. All rights reserved.'],
            
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
            DB::table('thm_dixlase_default_theme_settings')->insert([
                'name' => $setting['name'],
                'value' => $setting['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Dixlase Default Theme settings created successfully!');
    }
}
