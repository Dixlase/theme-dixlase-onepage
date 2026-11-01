<?php

/**
 * This file is part of the Dixlase OnePage theme.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
 * https://exc-d.com
 */

namespace Themes\DixlaseOnePage\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Themes\DixlaseOnePage\App\Providers\DixlaseOnePageServiceProvider;

/**
 * The hero was always exactly one viewport tall (#115). It can now be left
 * out, kept at one viewport, or given a height of its own.
 *
 * Two decisions are worth holding still, because both are easy to undo by
 * accident and neither fails loudly:
 *
 * A custom height is a **minimum**, written as a declaration rather than a
 * Tailwind class. `min-h-[55vh]` assembled from a stored number is invisible
 * to Tailwind's scanner — it reads source files, not the values a site holds
 * — so the utility would not be in the emitted CSS and the hero would have no
 * height at all.
 *
 * "No hero" is decided **where the partial is included**, not inside it. A
 * site that overrides `partials/hero.blade.php` under `custom/` replaces it
 * wholesale, so a check living in there would be lost with the fork.
 */
class HeroHeightTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        require_once __DIR__.'/../../app/Helpers/DixlaseOnePageHelpers.php';

        // Theme migrations are not picked up by core's plugin migration
        // discovery, so the settings table is created here (same reason as
        // ThemePreviewSettingsOverrideTest).
        Artisan::call('migrate', [
            '--path' => 'themes/DixlaseOnePage/database/migrations',
            '--realpath' => false,
            '--force' => true,
        ]);

        $this->app->register(DixlaseOnePageServiceProvider::class);
    }

    protected function tearDown(): void
    {
        $this->app->forgetInstance(DixlaseOnePageServiceProvider::PREVIEW_SETTINGS);

        parent::tearDown();
    }

    /**
     * Stand values in for the stored ones, the way the settings screen does
     * while previewing what the operator has not saved.
     *
     * @param  array<string, string>  $values
     */
    private function previewing(array $values): void
    {
        $this->app->instance(DixlaseOnePageServiceProvider::PREVIEW_SETTINGS, $values);
    }

    private function contents(string $relativePath): string
    {
        $path = dirname(__DIR__, 2).'/'.$relativePath;

        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }

    public function test_a_site_that_has_never_set_it_gets_the_full_screen_hero(): void
    {
        $this->assertSame('full', dls_onepage_hero_mode());
        $this->assertTrue(dls_onepage_hero_enabled());
        $this->assertSame('', dls_onepage_hero_height_style());
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function unrecognisedModes(): array
    {
        return [
            'a mode that never existed' => ['wat'],
            'empty' => [''],
            'a number' => ['55'],
        ];
    }

    #[DataProvider('unrecognisedModes')]
    public function test_an_unrecognised_mode_reads_as_full_screen(string $stored): void
    {
        $this->previewing(['hero_height' => $stored]);

        $this->assertSame(
            'full',
            dls_onepage_hero_mode(),
            'Anything unrecognised has to fall back to what every site had before the setting '
            .'existed. Falling back to "none" would silently delete the hero.'
        );
    }

    public function test_none_turns_the_hero_off(): void
    {
        $this->previewing(['hero_height' => 'none']);

        $this->assertFalse(dls_onepage_hero_enabled());
    }

    public function test_a_custom_height_is_a_minimum_declaration(): void
    {
        $this->previewing(['hero_height' => 'custom', 'hero_height_custom' => '45']);

        $this->assertSame(
            'min-height: 45vh;',
            dls_onepage_hero_height_style(),
            'A minimum, not an exact height: the section is `overflow-hidden` and the hero text '
            .'runs to roughly 260-340px on a desktop, so an exact 30% cuts a two-line title off '
            .'on a laptop.'
        );
        $this->assertSame(
            '',
            dls_onepage_hero_height_class(),
            'The height is a declaration in this mode, so there must be no competing class.'
        );
    }

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    public static function outOfRangeHeights(): array
    {
        return [
            'far below the floor' => ['1', DLS_ONEPAGE_HERO_HEIGHT_MIN],
            'just below the floor' => [(string) (DLS_ONEPAGE_HERO_HEIGHT_MIN - 1), DLS_ONEPAGE_HERO_HEIGHT_MIN],
            'just above the ceiling' => [(string) (DLS_ONEPAGE_HERO_HEIGHT_MAX + 1), DLS_ONEPAGE_HERO_HEIGHT_MAX],
            'far above the ceiling' => ['999', DLS_ONEPAGE_HERO_HEIGHT_MAX],
            'not a number' => ['tall', DLS_ONEPAGE_HERO_HEIGHT_DEFAULT],
        ];
    }

    #[DataProvider('outOfRangeHeights')]
    public function test_a_custom_height_is_clamped_on_read(string $stored, int $expected): void
    {
        $this->previewing(['hero_height' => 'custom', 'hero_height_custom' => $stored]);

        $this->assertSame(
            $expected,
            dls_onepage_hero_custom_height(),
            'Clamped on read as well as on save, so a row written before the bounds existed — or '
            .'written by hand — cannot produce a hero taller than the window.'
        );
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function heroIncludingViews(): array
    {
        return [
            'front page' => ['resources/views/index.blade.php'],
            'admin preview frame' => ['resources/views/admin/preview-frame.blade.php'],
        ];
    }

    #[DataProvider('heroIncludingViews')]
    public function test_the_view_that_includes_the_hero_asks_whether_there_is_one(string $relativePath): void
    {
        $compiled = Blade::compileString($this->contents($relativePath));

        $this->assertStringContainsString(
            'dls_onepage_hero_enabled()',
            $compiled,
            "{$relativePath} includes the hero partial, so it has to gate that include. The check "
            .'cannot live inside the partial: a site that overrides it under `custom/` replaces '
            .'the file wholesale and would lose the check with it.'
        );
    }

    public function test_the_hero_partial_takes_its_height_from_the_helpers(): void
    {
        $hero = $this->contents('resources/views/partials/hero.blade.php');

        foreach (['dls_onepage_hero_height_class()', 'dls_onepage_hero_height_style()'] as $call) {
            $this->assertStringContainsString(
                $call,
                $hero,
                "partials/hero.blade.php must take its height from {$call} rather than deciding "
                .'it again, or the setting reaches the page in one mode and not another.'
            );
        }
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function settingWiring(): array
    {
        return [
            'seeder' => ['database/seeders/DixlaseOnePageSettingsSeeder.php'],
            'provider defaults' => ['app/Providers/DixlaseOnePageServiceProvider.php'],
            'validation rules' => ['app/Http/Requests/UpdateThemeSettingsRequest.php'],
            'admin controller' => ['app/Http/Controllers/Admin/Settings/AdminThemeSettingsController.php'],
            'settings form state' => ['resources/views/admin/settings/themes/settings.blade.php'],
            'settings sidebar' => ['resources/views/admin/settings/themes/partials/sidebar.blade.php'],
        ];
    }

    #[DataProvider('settingWiring')]
    public function test_the_hero_height_setting_is_wired(string $relativePath): void
    {
        $this->assertStringContainsString(
            'hero_height',
            $this->contents($relativePath),
            "{$relativePath} does not name `hero_height`. A theme setting missing from any one of "
            .'these fails silently — dropped on save, read as null, or with no row on a fresh '
            .'install — so each file is asserted separately.'
        );
    }
}
