<?php

/**
 * This file is part of the Dixlase OnePage theme.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
 * https://exc-d.com
 */

namespace Themes\DixlaseOnePage\Tests\Unit;

use Illuminate\Support\Facades\Blade;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Both controls that float in the bottom-right corner — back to top (#114)
 * and the appearance switcher in its fixed placement — are laid out by
 * partials/corner-stack.blade.php, which owns the position.
 *
 * They cannot fix themselves to the corner individually: the switcher's
 * height changes at the `md` breakpoint and again when it opens on a phone,
 * so a second control offset by a hardcoded amount overlaps it at some width.
 * A layout that goes back to including a control directly is therefore a
 * defect, not a style choice, and the second test here catches it.
 *
 * The last test is about a different hazard. A theme setting is wired through
 * seven files, and a value missing from one of them fails quietly: a setting
 * absent from the validation rules is dropped on save, absent from the
 * provider defaults it reads as null on a site that has never saved, absent
 * from the seeder it has no row on a fresh install. Nothing errors; the
 * control simply never appears.
 */
class CornerStackTest extends TestCase
{
    private const STACK = 'themes::partials.corner-stack';

    /**
     * Every layout that renders a <head> of its own, and therefore a page.
     *
     * @return array<string, array{0: string}>
     */
    public static function layouts(): array
    {
        return [
            'live layout' => ['resources/views/layouts/app.blade.php'],
            'preview layout' => ['resources/views/layouts/preview.blade.php'],
        ];
    }

    /**
     * Every file that has to know the setting's key for it to work.
     *
     * @return array<string, array{0: string}>
     */
    public static function settingWiring(): array
    {
        return [
            'seeder (a fresh install gets a row)' => ['database/seeders/DixlaseOnePageSettingsSeeder.php'],
            'provider defaults (a site that never saved)' => ['app/Providers/DixlaseOnePageServiceProvider.php'],
            'validation rules (the value survives a save)' => ['app/Http/Requests/UpdateThemeSettingsRequest.php'],
            'admin controller (read and defaulted)' => ['app/Http/Controllers/Admin/Settings/AdminThemeSettingsController.php'],
            'settings form state' => ['resources/views/admin/settings/themes/settings.blade.php'],
            'settings sidebar (the operator can set it)' => ['resources/views/admin/settings/themes/partials/sidebar.blade.php'],
            'corner stack (the front end reads it)' => ['resources/views/partials/corner-stack.blade.php'],
        ];
    }

    private function contents(string $relativePath): string
    {
        $path = dirname(__DIR__, 2).'/'.$relativePath;

        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }

    private function compiled(string $relativePath): string
    {
        return Blade::compileString($this->contents($relativePath));
    }

    #[DataProvider('layouts')]
    public function test_a_layout_renders_the_corner_stack(string $relativePath): void
    {
        $this->assertStringContainsString(
            "'".self::STACK."'",
            $this->compiled($relativePath),
            "{$relativePath} must include partials/corner-stack.blade.php, which is what puts "
            .'the floating controls in the corner and stacks them when more than one is on.'
        );
    }

    #[DataProvider('layouts')]
    public function test_a_layout_does_not_place_a_floating_control_itself(string $relativePath): void
    {
        $this->assertStringNotContainsString(
            "'themes::partials.appearance-float'",
            $this->compiled($relativePath),
            "{$relativePath} must reach the floating appearance switcher through "
            .'partials/corner-stack.blade.php. Included directly it fixes itself to the corner '
            .'again, and back-to-top then sits on top of it rather than above it.'
        );
    }

    public function test_the_stack_holds_both_controls(): void
    {
        $stack = $this->compiled('resources/views/partials/corner-stack.blade.php');

        foreach (['back-to-top', 'appearance-float'] as $partial) {
            $this->assertStringContainsString(
                "'themes::partials.{$partial}'",
                $stack,
                "partials/corner-stack.blade.php must include partials/{$partial}.blade.php. "
                .'It is the only thing that positions them.'
            );
        }
    }

    #[DataProvider('settingWiring')]
    public function test_the_back_to_top_setting_is_wired(string $relativePath): void
    {
        $this->assertStringContainsString(
            'back_to_top_enabled',
            $this->contents($relativePath),
            "{$relativePath} does not name `back_to_top_enabled`. A theme setting missing from "
            .'any one of these fails silently — the control just never appears — so each file '
            .'is asserted separately rather than trusting that the feature works somewhere.'
        );
    }
}
