<?php

/**
 * This file is part of the Dixlase OnePage theme.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
 * https://exc-d.com
 */

namespace Themes\DixlaseOnePage\Tests\Unit;

use Tests\TestCase;

/**
 * The settings preview used to measure the document inside the frame and
 * write the result back as the frame's own height. That cannot converge
 * (#121): the page inside is sized in viewport units — `min-h-screen` on the
 * body, the hero at `h-screen` — and inside an iframe the viewport *is* the
 * frame. Growing the frame grew the page, which grew the measurement, which
 * grew the frame, a pass at a time, until the panel was a header above an
 * enormous empty column.
 *
 * It was invisible until #118 let the browser render the frame at all, so it
 * sat in the code from #80 with nothing to show for it. That is the reason
 * for a test: the defect has no symptom until something unrelated changes.
 *
 * The rule it encodes is the narrow one — do not derive the frame's height
 * from the frame's content — rather than the shape of today's fix.
 */
class PreviewFrameHeightTest extends TestCase
{
    private const PARTIAL = 'resources/views/admin/settings/themes/partials/preview.blade.php';

    private function partial(): string
    {
        $path = dirname(__DIR__, 2).'/'.self::PARTIAL;

        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }

    public function test_the_frame_does_not_measure_the_document_it_sizes(): void
    {
        $this->assertStringNotContainsString(
            'scrollHeight',
            $this->partial(),
            'The preview must not take its height from the document inside it. The page there '
            .'is sized in viewport units and the viewport is the frame, so the measurement is '
            .'an input to the thing being measured and the frame grows without stopping (#121).'
        );
    }

    public function test_the_frame_does_not_resize_itself_from_its_own_content(): void
    {
        $this->assertStringNotContainsString(
            'ResizeObserver(',
            $this->partial(),
            'Observing the framed body and resizing the frame in response is the loop in #121. '
            .'The container runs its own observer to rescale; this partial must not add one.'
        );
    }

    public function test_the_frame_takes_its_height_from_the_chosen_device(): void
    {
        $this->assertStringContainsString(
            'previewDevice ===',
            $this->partial(),
            'The height has to come from the device the operator picked, so that it does not '
            .'depend on what the page happens to contain — and so that `h-screen` inside the '
            .'frame means one screen, as it does on the real page.'
        );
    }
}
