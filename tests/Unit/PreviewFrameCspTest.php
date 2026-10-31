<?php

/**
 * This file is part of the Dixlase OnePage theme.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
 * https://exc-d.com
 */

namespace Themes\DixlaseOnePage\Tests\Unit;

use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;
use Themes\DixlaseOnePage\App\Http\Controllers\Admin\Settings\AdminThemeSettingsController;

/**
 * The admin area sends `frame-ancestors 'none'` (core's config/csp/admin.php),
 * so a preview iframe is refused by the browser: the panel shows an empty
 * frame, nothing is logged, and nothing in the theme reports a problem. Core
 * gives a screen that legitimately needs an iframe a way out — a request
 * attribute its ContentSecurityPolicy middleware rewrites the directive for —
 * and the theme's preview actions did not use it (#117).
 *
 * It only shows where the admin CSP is enforced. In development mode the
 * policy goes out report-only, which browsers do not act on, so the identical
 * code renders the frame perfectly and the defect hides.
 *
 * So this asserts the invariant rather than the two call sites: every action
 * that renders into the preview iframe opts into being framed. A third one
 * added without it fails here instead of shipping a blank panel.
 */
class PreviewFrameCspTest extends TestCase
{
    /** The view every preview action renders. */
    private const PREVIEW_VIEW = 'themes::admin.preview-frame';

    /** The request attribute core's middleware reads. */
    private const ATTRIBUTE = 'csp_frame_ancestors_self';

    /**
     * The source lines of one method, as written.
     */
    private function methodSource(ReflectionMethod $method): string
    {
        $lines = file($method->getFileName());

        return implode('', array_slice(
            $lines,
            $method->getStartLine() - 1,
            $method->getEndLine() - $method->getStartLine() + 1
        ));
    }

    /**
     * @return array<int, ReflectionMethod>
     */
    private function previewActions(): array
    {
        $class = new ReflectionClass(AdminThemeSettingsController::class);

        return array_values(array_filter(
            $class->getMethods(ReflectionMethod::IS_PUBLIC),
            fn (ReflectionMethod $m) => $m->getDeclaringClass()->getName() === AdminThemeSettingsController::class
                && str_contains($this->methodSource($m), self::PREVIEW_VIEW)
        ));
    }

    public function test_the_controller_still_has_preview_actions(): void
    {
        $names = array_map(fn (ReflectionMethod $m) => $m->getName(), $this->previewActions());

        sort($names);

        $this->assertSame(
            ['preview', 'previewFrame'],
            $names,
            'Expected the GET action behind the iframe src and the POST action the settings '
            .'form targets at the frame. If this changed, the rest of this test is checking '
            .'the wrong methods.'
        );
    }

    public function test_every_preview_action_opts_into_being_framed(): void
    {
        foreach ($this->previewActions() as $action) {
            $this->assertStringContainsString(
                'permitAdminFraming()',
                $this->methodSource($action),
                "AdminThemeSettingsController::{$action->getName()}() renders "
                .self::PREVIEW_VIEW.' but does not call permitAdminFraming(). The admin CSP '
                ."sends frame-ancestors 'none', so the browser will refuse to display the "
                .'result in the preview iframe and the panel will be blank with no error.'
            );
        }
    }

    public function test_the_helper_sets_the_attribute_core_reads(): void
    {
        $class = new ReflectionClass(AdminThemeSettingsController::class);

        $this->assertTrue(
            $class->hasMethod('permitAdminFraming'),
            'permitAdminFraming() is what the preview actions call; without it they cannot opt in.'
        );

        $this->assertStringContainsString(
            self::ATTRIBUTE,
            $this->methodSource($class->getMethod('permitAdminFraming')),
            'permitAdminFraming() must set the '.self::ATTRIBUTE.' request attribute. That exact '
            ."name is what core's ContentSecurityPolicy middleware looks for before rewriting "
            ."frame-ancestors to 'self'; any other name silently does nothing."
        );
    }
}
