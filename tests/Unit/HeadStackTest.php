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
 * The theme's <head> rendered @stack('styles') and no general-purpose stack,
 * so a plugin that pushed a <link rel="alternate"> or a page-level <meta> had
 * nowhere to render it and Blade dropped it without a word (#66).
 *
 * Two things depended on that stack existing: a translation provider pushes
 * its hreflang alternates to `head`, so other-language URLs were never
 * announced to search engines, and DixlaseBlog had to borrow the stylesheet
 * stack for its feed link.
 *
 * Both head-bearing templates have to carry the stack. layouts/preview.blade.php
 * keeps its own copy of the <head> rather than including partials/head.blade.php,
 * so adding it to only one of them leaves the preview frame behaving
 * differently from the live page.
 */
class HeadStackTest extends TestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public static function headTemplates(): array
    {
        return [
            'front head partial' => ['resources/views/partials/head.blade.php'],
            'preview layout' => ['resources/views/layouts/preview.blade.php'],
        ];
    }

    /**
     * Compile a template straight from disk. Compiling rather than rendering
     * keeps the assertion on the directive itself: rendering the real partial
     * would drag in the Vite manifest and the active-theme row, neither of
     * which has anything to do with whether the stack is declared.
     */
    private function compiledTemplate(string $relativePath): string
    {
        $path = dirname(__DIR__, 2).'/'.$relativePath;

        $this->assertFileExists($path);

        return Blade::compileString((string) file_get_contents($path));
    }

    #[DataProvider('headTemplates')]
    public function test_head_template_declares_the_head_stack(string $relativePath): void
    {
        $this->assertStringContainsString(
            "yieldPushContent('head')",
            $this->compiledTemplate($relativePath),
            "{$relativePath} must declare @stack('head'), or pushed <head> tags are silently dropped."
        );
    }

    #[DataProvider('headTemplates')]
    public function test_the_head_stack_comes_after_the_styles_stack(string $relativePath): void
    {
        $compiled = $this->compiledTemplate($relativePath);

        $styles = strpos($compiled, "yieldPushContent('styles')");
        $head = strpos($compiled, "yieldPushContent('head')");

        $this->assertNotFalse($styles, "{$relativePath} must still declare @stack('styles').");
        $this->assertNotFalse($head, "{$relativePath} must declare @stack('head').");
        $this->assertGreaterThan(
            $styles,
            $head,
            "In {$relativePath} the head stack must come after the styles stack, so a stylesheet "
            .'pushed to `styles` still loads before a tag pushed to `head`.'
        );
    }

    public function test_a_pushed_tag_is_rendered_where_the_stack_sits(): void
    {
        $tag = '<link rel="alternate" hreflang="en" href="https://example.test/en/">';

        app('view')->startPush('head', $tag);

        $html = Blade::render("<head>@stack('styles')@stack('head')</head>", [], true);

        $this->assertSame("<head>{$tag}</head>", trim($html));
    }

    public function test_the_head_stack_adds_nothing_when_no_view_pushed_to_it(): void
    {
        $this->assertSame(
            '<head></head>',
            trim(Blade::render("<head>@stack('styles')@stack('head')</head>", [], true)),
            'A page that pushes nothing must render exactly as it did before the stack existed.'
        );
    }
}
