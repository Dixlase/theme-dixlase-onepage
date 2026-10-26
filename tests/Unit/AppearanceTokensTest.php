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
 * The live layout and the admin preview frame computed the same appearance
 * tokens twice: the primary colour and its dark-mode fallback, the heading
 * font stack, the per-region apply toggles and tracking, and the <style>
 * block that emits all of it (#106). A setting that grew a branch had to be
 * added in two places, or the preview quietly showed something a visitor
 * would not get — the failure #97 removed from the <head>, one layer in.
 *
 * The computation now lives in partials/appearance-tokens.blade.php and the
 * dark-mode script in partials/appearance-mode-script.blade.php. These tests
 * assert that both layouts reach for the partials and that neither has taken
 * a copy back, and that the tokens come after the stylesheets — the rules in
 * that partial share their specificity with Tailwind's preflight, so emitting
 * them earlier would hand the cascade to preflight and the heading font would
 * silently stop applying.
 */
class AppearanceTokensTest extends TestCase
{
    /**
     * Every layout that renders a <head> of its own.
     *
     * @return array<string, array{0: string}>
     */
    public static function headBearingLayouts(): array
    {
        return [
            'live layout' => ['resources/views/layouts/app.blade.php'],
            'preview layout' => ['resources/views/layouts/preview.blade.php'],
        ];
    }

    /**
     * The PHP variables the shared partial owns. A layout that names one of
     * these is computing a token for itself again.
     *
     * @return array<string, array{0: string}>
     */
    public static function sharedTokenVariables(): array
    {
        return [
            'dark-safe primary colour' => ['primaryColorDarkSafe'],
            'heading font stack' => ['headingFontStack'],
            'per-region tracking' => ['trackingRegions'],
        ];
    }

    private function path(string $relativePath): string
    {
        $path = dirname(__DIR__, 2).'/'.$relativePath;

        $this->assertFileExists($path);

        return $path;
    }

    private function compiledTemplate(string $relativePath): string
    {
        return Blade::compileString((string) file_get_contents($this->path($relativePath)));
    }

    #[DataProvider('headBearingLayouts')]
    public function test_a_head_bearing_layout_includes_the_shared_tokens(string $relativePath): void
    {
        $compiled = $this->compiledTemplate($relativePath);

        foreach (['appearance-mode-script', 'appearance-tokens'] as $partial) {
            $this->assertStringContainsString(
                "'themes::partials.{$partial}'",
                $compiled,
                "{$relativePath} must include partials/{$partial}.blade.php rather than carry its "
                .'own copy. A second copy drifts, and a setting added to one is then missing from '
                .'the other — the defect this test exists for.'
            );
        }
    }

    #[DataProvider('headBearingLayouts')]
    public function test_the_tokens_come_after_the_stylesheets(string $relativePath): void
    {
        $compiled = $this->compiledTemplate($relativePath);

        // The quotes matter: without them `themes::partials.head` also
        // matches `themes::partials.header`, which both layouts include.
        $head = strpos($compiled, "'themes::partials.head'");
        $tokens = strpos($compiled, "'themes::partials.appearance-tokens'");

        $this->assertNotFalse($head, "{$relativePath} must include partials/head.blade.php.");
        $this->assertNotFalse($tokens, "{$relativePath} must include partials/appearance-tokens.blade.php.");
        $this->assertGreaterThan(
            $head,
            $tokens,
            "In {$relativePath} the appearance tokens must come after partials/head.blade.php, "
            .'which loads the stylesheets. The token rules share their specificity with '
            ."Tailwind's preflight, so emitted earlier they lose the cascade and the heading "
            .'font stops applying.'
        );
    }

    #[DataProvider('sharedTokenVariables')]
    public function test_only_the_shared_partial_computes_a_token(string $variable): void
    {
        $partial = (string) file_get_contents($this->path('resources/views/partials/appearance-tokens.blade.php'));

        $this->assertStringContainsString(
            '$'.$variable,
            $partial,
            "partials/appearance-tokens.blade.php must own \${$variable}."
        );

        foreach (array_column(self::headBearingLayouts(), 0) as $layout) {
            $this->assertStringNotContainsString(
                '$'.$variable,
                (string) file_get_contents($this->path($layout)),
                "{$layout} must not compute \${$variable} itself — partials/appearance-tokens.blade.php "
                .'owns it, and a second copy is what #106 removed.'
            );
        }
    }

    public function test_the_shared_partial_emits_the_custom_properties(): void
    {
        $partial = (string) file_get_contents($this->path('resources/views/partials/appearance-tokens.blade.php'));

        foreach (['--color-primary:', '--color-primary-dark-safe:', '--font-heading:'] as $property) {
            $this->assertStringContainsString(
                $property,
                $partial,
                "partials/appearance-tokens.blade.php must declare {$property} — the theme's SCSS "
                .'reads it with var(), and a page without it falls back to the unstyled default.'
            );
        }
    }

    public function test_the_mode_script_ignores_a_stored_choice_unless_asked(): void
    {
        $partial = (string) file_get_contents($this->path('resources/views/partials/appearance-mode-script.blade.php'));

        $this->assertStringContainsString(
            "@php(\$honourStoredChoice = \$honourStoredChoice ?? false)",
            $partial,
            'The stored-choice branch must default to off. The admin preview frame includes this '
            .'partial with no argument and has to show the mode the operator configured, not the '
            ."one the browser it is being viewed in happens to have stored."
        );

        $this->assertStringContainsString(
            "@if (\$honourStoredChoice)",
            $partial,
            'The localStorage lookup must sit behind $honourStoredChoice.'
        );
    }
}
