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

namespace Themes\DixlaseOnePage\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;
use Themes\DixlaseOnePage\App\Models\ThemeSetting;
use Themes\DixlaseOnePage\App\Providers\DixlaseOnePageServiceProvider;

/**
 * Preview override for unsaved theme settings.
 *
 * The theme settings screen previews the real theme by rendering it with
 * the values currently in the form. Those values are not in the database,
 * and the theme's `View::composer('*')` rebuilds `$themeSettings` from the
 * database for every view, so a controller cannot pass them down — they are
 * bound in the container instead and the composer merges them on top.
 *
 *   - **In scope**: that the binding wins over stored values, that it does
 *     not leak once forgotten, and that previewing writes nothing.
 *   - **Out of scope**: the HTTP layer, the iframe wiring, and how media
 *     ids resolve to paths (unchanged by this).
 */
class ThemePreviewSettingsOverrideTest extends TestCase
{
    use RefreshDatabase;

    /** Reads whatever the composer put on `$themeSettings`. */
    private const TEMPLATE = '{{ $themeSettings->hero_main_title ?? "MISSING" }}';

    protected function setUp(): void
    {
        parent::setUp();

        require_once __DIR__.'/../../app/Helpers/DixlaseOnePageHelpers.php';

        // Theme migrations are not picked up by core's plugin migration
        // discovery, so the settings table is created here (same reason as
        // DixlaseOnePageSettingsLocalizationTest).
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--path' => 'themes/DixlaseOnePage/database/migrations',
            '--realpath' => false,
            '--force' => true,
        ]);

        // The composer under test is registered in the provider's boot(),
        // which the test environment does not necessarily run.
        $this->app->register(DixlaseOnePageServiceProvider::class);
    }

    protected function tearDown(): void
    {
        $this->app->forgetInstance(DixlaseOnePageServiceProvider::PREVIEW_SETTINGS);

        parent::tearDown();
    }

    private function render(): string
    {
        return trim(Blade::render(self::TEMPLATE, [], true));
    }

    public function test_stored_value_is_used_when_nothing_is_bound(): void
    {
        ThemeSetting::setValue('hero_main_title', '保存済みの見出し');
        ThemeSetting::clearAllCache();

        $this->assertSame('保存済みの見出し', $this->render());
    }

    public function test_bound_value_overrides_the_stored_one(): void
    {
        ThemeSetting::setValue('hero_main_title', '保存済みの見出し');
        ThemeSetting::clearAllCache();

        $this->app->instance(
            DixlaseOnePageServiceProvider::PREVIEW_SETTINGS,
            ['hero_main_title' => '未保存の見出し']
        );

        $this->assertSame('未保存の見出し', $this->render());
    }

    public function test_the_override_does_not_outlive_the_binding(): void
    {
        ThemeSetting::setValue('hero_main_title', '保存済みの見出し');
        ThemeSetting::clearAllCache();

        $this->app->instance(
            DixlaseOnePageServiceProvider::PREVIEW_SETTINGS,
            ['hero_main_title' => '未保存の見出し']
        );
        $this->assertSame('未保存の見出し', $this->render());

        $this->app->forgetInstance(DixlaseOnePageServiceProvider::PREVIEW_SETTINGS);

        $this->assertSame('保存済みの見出し', $this->render());
    }

    public function test_previewing_writes_nothing_to_the_settings_table(): void
    {
        ThemeSetting::setValue('hero_main_title', '保存済みの見出し');
        ThemeSetting::clearAllCache();

        $this->app->instance(
            DixlaseOnePageServiceProvider::PREVIEW_SETTINGS,
            ['hero_main_title' => '未保存の見出し']
        );
        $this->render();

        ThemeSetting::clearAllCache();
        $this->assertSame('保存済みの見出し', ThemeSetting::getValue('hero_main_title'));
    }

    public function test_the_primary_setting_reader_sees_the_override(): void
    {
        // The hero and other localized strings never read `$themeSettings`;
        // they go through dls_onepage_localized_setting(), whose primary
        // value comes from dls_onepage_primary_setting(). The override has
        // to reach that reader too, or typing in the form moves nothing.
        ThemeSetting::setValue('hero_main_title', '保存済みの見出し');
        ThemeSetting::clearAllCache();

        $this->assertSame('保存済みの見出し', dls_onepage_primary_setting('hero_main_title'));

        $this->app->instance(
            DixlaseOnePageServiceProvider::PREVIEW_SETTINGS,
            ['hero_main_title' => '未保存の見出し']
        );

        $this->assertSame('未保存の見出し', dls_onepage_primary_setting('hero_main_title'));
    }

    public function test_an_unrelated_key_is_not_affected_by_the_override(): void
    {
        ThemeSetting::setValue('hero_main_title', '保存済みの見出し');
        ThemeSetting::setValue('hero_sub_title', '保存済みの説明');
        ThemeSetting::clearAllCache();

        $this->app->instance(
            DixlaseOnePageServiceProvider::PREVIEW_SETTINGS,
            ['hero_main_title' => '未保存の見出し']
        );

        $this->assertSame('保存済みの説明', dls_onepage_primary_setting('hero_sub_title'));
    }

    public function test_a_key_absent_from_the_database_can_still_be_previewed(): void
    {
        // The settings screen posts every field, including ones a site has
        // never saved. Those arrive with no row behind them, so the merge
        // has to add the property rather than only replace it.
        $this->app->instance(
            DixlaseOnePageServiceProvider::PREVIEW_SETTINGS,
            ['hero_main_title' => '行のない値']
        );

        $this->assertSame('行のない値', $this->render());
    }
}
