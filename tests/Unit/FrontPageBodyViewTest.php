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
 * How the two views that render the front page hand the body over (#146).
 *
 * Read from the templates rather than rendered, for the reason HeadStackTest
 * gives: rendering the real views drags in the Vite manifest and the
 * active-theme row, neither of which has anything to do with the structure
 * being asserted. The behaviour of the helper they call is covered by
 * FrontPageBodyTest, and AdminGetRoutesSmokeTest renders the admin screens
 * for real.
 *
 * Three things are pinned, all of them silent when undone:
 *
 * **A provider's body gets no `prose` and no `max-w-4xl`.** Those are right
 * for the Markdown an operator wrote and wrong for a plugin that brings its
 * own layout: a listing with a sidebar, or a grid of cards, does not fit a
 * narrow single column, and `prose` restyles everything inside it. This is
 * the one visible difference between the two kinds of body and the thing
 * most likely to be lost in a later edit of either view.
 *
 * **Core's content is still reachable.** Each view keeps a fallback branch,
 * so a provider that is gone, disabled or silent renders the front page as it
 * always was instead of blanking it.
 *
 * **The front page editor's live-update targets stay on core's branch.**
 * `preview-content-area` / `preview-content-section` are the ids the parent
 * window writes into through postMessage. They belong to the element holding
 * the content being edited, which in the provider branch is not what is on
 * screen.
 */
class FrontPageBodyViewTest extends TestCase
{
    /**
     * The two views that render the front page body.
     *
     * @return array<string, array{0: string}>
     */
    public static function bodyBearingViews(): array
    {
        return [
            'live front page' => ['resources/views/index.blade.php'],
            'preview frame' => ['resources/views/admin/preview-frame.blade.php'],
        ];
    }

    private function source(string $relativePath): string
    {
        $path = dirname(__DIR__, 2).'/'.$relativePath;

        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }

    /**
     * The provider branch: from its section tag to the end of that section.
     *
     * Located by the `front-page-body` class, which is what marks a section
     * as holding a plugin's body in both views.
     */
    private function providerBranch(string $relativePath): string
    {
        $source = $this->source($relativePath);

        $start = strpos($source, '<section class="front-page-body');
        $this->assertNotFalse(
            $start,
            "{$relativePath} must render a provider's body in a section marked `front-page-body`."
        );

        $end = strpos($source, '</section>', $start);
        $this->assertNotFalse($end, "{$relativePath}: the front-page-body section is not closed.");

        return substr($source, $start, $end - $start);
    }

    #[DataProvider('bodyBearingViews')]
    public function test_a_provider_body_is_not_wrapped_in_prose(string $relativePath): void
    {
        $this->assertStringNotContainsString(
            'prose',
            $this->providerBranch($relativePath),
            "In {$relativePath} a provider's body must not be given the `prose` typographic "
            .'styles: they are for the Markdown an operator wrote, and they restyle every '
            .'element of a layout the plugin brought with it.'
        );
    }

    #[DataProvider('bodyBearingViews')]
    public function test_a_provider_body_is_not_wrapped_in_the_narrow_column(string $relativePath): void
    {
        $this->assertStringNotContainsString(
            'max-w-4xl',
            $this->providerBranch($relativePath),
            "In {$relativePath} a provider's body must get the container width, not the "
            .'narrow single column: a listing with a sidebar, or a grid of cards, does not fit it.'
        );
    }

    #[DataProvider('bodyBearingViews')]
    public function test_cores_front_page_content_stays_reachable(string $relativePath): void
    {
        $source = $this->source($relativePath);

        $this->assertStringContainsString(
            'max-w-4xl mx-auto prose',
            $source,
            "{$relativePath} must still render core's front page content in the narrow prose "
            .'column — that is the fallback, and what every site without a provider sees.'
        );
    }

    #[DataProvider('bodyBearingViews')]
    public function test_the_two_branches_are_exclusive(string $relativePath): void
    {
        $compiled = Blade::compileString($this->source($relativePath));

        // One `if` reaching the other branch through `elseif` / `else`, not
        // two independent `if`s — which would print both bodies at once.
        $this->assertMatchesRegularExpression(
            '/\$hasProviderBody|\$hasPreviewProviderBody/',
            $compiled,
            "{$relativePath} must branch on whether a provider supplied a body."
        );
        $this->assertMatchesRegularExpression(
            '/(elseif|else:)/',
            $compiled,
            "In {$relativePath} the provider body and core's content must be alternatives, "
            .'not two sections that can both render.'
        );
    }

    public function test_the_preview_keeps_the_editors_live_update_targets_on_cores_branch(): void
    {
        $relativePath = 'resources/views/admin/preview-frame.blade.php';
        $branch = $this->providerBranch($relativePath);

        foreach (['preview-content-area', 'preview-content-section'] as $id) {
            $this->assertStringNotContainsString(
                $id,
                $branch,
                "In {$relativePath} `{$id}` must stay on core's branch: it is what the front "
                .'page editor writes into through postMessage, and it belongs to the element '
                .'holding the content being edited.'
            );
        }

        $source = $this->source($relativePath);
        foreach (['preview-content-area', 'preview-content-section'] as $id) {
            $this->assertStringContainsString(
                $id,
                $source,
                "{$relativePath} must still carry `{$id}`, or the front page editor's preview "
                .'stops updating as the operator types.'
            );
        }
    }

    public function test_the_preview_never_takes_the_provider_branch_in_bare_mode(): void
    {
        $relativePath = 'resources/views/admin/preview-frame.blade.php';

        $this->assertStringContainsString(
            '($bareContent ?? false)',
            $this->source($relativePath),
            "{$relativePath} must gate the provider body on bare mode: in bare mode this view "
            .'is the front page editor\'s preview, where the body being edited is core\'s own '
            .'content and a provider\'s body would show the operator something else.'
        );
    }

    public function test_the_live_page_passes_the_front_surface(): void
    {
        $this->assertStringContainsString(
            'DLS_ONEPAGE_FRONT_PAGE_SURFACE_FRONT',
            $this->source('resources/views/index.blade.php'),
            'The live front page must tell a provider it is rendering the live page.'
        );
    }

    public function test_the_preview_passes_the_preview_surface(): void
    {
        $this->assertStringContainsString(
            'DLS_ONEPAGE_FRONT_PAGE_SURFACE_PREVIEW',
            $this->source('resources/views/admin/preview-frame.blade.php'),
            'The preview must tell a provider it is rendering the settings preview, so a '
            .'provider can tell the two apart.'
        );
    }

    public function test_the_views_name_no_core_class_that_an_older_core_lacks(): void
    {
        // The contract arrived in core 0.2.18 and `requires.dixlase` is not
        // enforced at install time, so these views can compile on a core
        // without it. A constant read off a missing interface is a fatal at
        // compile time, not a graceful fallback — which is exactly why the
        // theme mirrors the two surface values as its own constants.
        foreach (array_keys(self::bodyBearingViews()) as $label) {
            $relativePath = self::bodyBearingViews()[$label][0];

            $this->assertStringNotContainsString(
                'FrontPageBodyProviderInterface::',
                $this->source($relativePath),
                "{$relativePath} must not read a constant off core's contract: on a core "
                .'without it that is a fatal error. Use the theme\'s mirrored constants.'
            );
        }
    }
}
