<?php

/**
 * This file is part of Dixlase OnePage.
 *
 * Copyright (C) 2026 exc-D inc.
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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Rename `thm_dixlase_one_page_settings` to `thm_dixlase_onepage_settings`
 * so the theme's settings table matches the `dixlase-onepage` slug that
 * the core derives at runtime (FrontController converts the slug to
 * `thm_<snake_slug>_settings`). The earlier slug `dixlase-one-page` was
 * retired in theme PR #3 to align with the GitHub repository name
 * `theme-dixlase-onepage`, but the table name was still produced from
 * the old slug by the original `0001_01_01_000100_*` migration, causing
 * `Table 'dls_thm_dixlase_onepage_settings' doesn't exist` errors when
 * the core looked up theme settings via the new slug.
 *
 * Idempotent so fresh installs (where the original migration just
 * created `one_page` moments earlier) and existing installs
 * (where `one_page` has been live with data) both converge on
 * `onepage`.
 */
return new class extends Migration
{
    private const OLD_NAME = 'thm_dixlase_one_page_settings';

    private const NEW_NAME = 'thm_dixlase_onepage_settings';

    public function up(): void
    {
        $hasOld = Schema::hasTable(self::OLD_NAME);
        $hasNew = Schema::hasTable(self::NEW_NAME);

        if ($hasOld && ! $hasNew) {
            Schema::rename(self::OLD_NAME, self::NEW_NAME);

            return;
        }

        // Already renamed in a previous run, or both somehow exist:
        // drop the legacy one to avoid the wrong table being queried.
        if ($hasOld && $hasNew) {
            Schema::drop(self::OLD_NAME);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable(self::NEW_NAME) && ! Schema::hasTable(self::OLD_NAME)) {
            Schema::rename(self::NEW_NAME, self::OLD_NAME);
        }
    }
};
