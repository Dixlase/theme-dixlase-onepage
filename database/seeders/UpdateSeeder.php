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

namespace Themes\DixlaseOnePage\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder that `dls:theme:update` runs after theme migrations, when the class
 * is present. Core resolves it by convention as
 * `Themes\{Directory}\Database\Seeders\UpdateSeeder` and invokes it through
 * `dls:theme:seed --class=UpdateSeeder`; when the class is absent the step is
 * silently skipped, so this is opt-in.
 *
 * It is deliberately NOT registered in DatabaseSeeder, which keeps it out of
 * the install path — only an update ever runs it.
 *
 * Round 2 (v0.1.5) uses it as a verification marker. `dls:theme:rollback`
 * reverses theme migrations but never re-runs or undoes seeders, so this
 * marker survives a rollback while the row written by
 * `0001_01_01_000002_add_theme_update_migration_marker` disappears. That
 * contrast is what the sandbox asserts.
 *
 * Everything here must stay safe to re-run: the seeder fires on every theme
 * update, so use updateOrInsert / insertOrIgnore rather than plain inserts.
 */
class UpdateSeeder extends Seeder
{
    private const TABLE = 'thm_dixlase_onepage_settings';

    private const MARKER_NAME = 'theme_update_seeder_marker';

    public function run(): void
    {
        DB::table(self::TABLE)->updateOrInsert(
            ['name' => self::MARKER_NAME],
            ['value' => '0.1.5', 'created_at' => now(), 'updated_at' => now()],
        );
    }
}
