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

namespace Themes\DixlaseOnePage\Tests\Feature;

use App\Services\Csp\CspComplianceScanner;
use Tests\TestCase;

/**
 * CI-time self-audit against core's own CSP detector.
 *
 * Replays what the admin theme audit dashboard runs for the CSP side.
 * If this fails, the dashboard would light up on a site that installs
 * the release. Pattern taken from `plugin-dixlase-inquiry` PR #72 and
 * the eight follow-up plugin PRs, scoped down for themes: only the CSP
 * check carries over, since `dls:plugin:audit` has no theme form.
 */
class ThemeSelfAuditTest extends TestCase
{
    private const SLUG = 'dixlase-onepage';

    public function test_no_csp_strict_mode_violations(): void
    {
        /** @var CspComplianceScanner $scanner */
        $scanner = app(CspComplianceScanner::class);
        $result = $scanner->scanTheme(self::SLUG);

        $blockers = array_values(array_filter(
            $result['violations'] ?? [],
            fn (array $v) => in_array($v['severity'] ?? '', ['warning', 'critical'], true),
        ));

        $this->assertSame(
            [],
            $blockers,
            "CSP strict-mode violations (warning/critical) detected:\n"
                .json_encode($blockers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
    }
}
