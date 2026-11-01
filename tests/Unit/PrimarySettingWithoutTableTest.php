<?php

/**
 * This file is part of the Dixlase OnePage theme.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
 * https://exc-d.com
 */

namespace Themes\DixlaseOnePage\Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Themes\DixlaseOnePage\App\Models\ThemeSetting;

/**
 * The settings read through dls_onepage_primary_setting() render with their
 * defaults when the settings table is missing (#126).
 *
 * Core's own test suite renders the bundled theme on a database without the
 * theme's tables. The settings added in 0.5.0 failed there with a query
 * exception, which turned the footer and the settings preview into a 500.
 */
class PrimarySettingWithoutTableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        require_once __DIR__.'/../../app/Helpers/DixlaseOnePageHelpers.php';

        Schema::dropIfExists((new ThemeSetting())->getTable());
    }

    public function test_a_missing_table_reads_as_not_set(): void
    {
        $this->assertNull(dls_onepage_primary_setting('hero_height'));
        $this->assertNull(dls_onepage_primary_setting('sns_links_order'));
    }

    public function test_the_settings_added_in_0_5_0_fall_back_to_their_defaults(): void
    {
        $this->assertTrue(dls_onepage_hero_enabled(), 'With nothing readable the hero must stay full screen, not disappear.');
        $this->assertSame(array_keys(dls_onepage_sns_services()), dls_onepage_sns_order());
    }
}
