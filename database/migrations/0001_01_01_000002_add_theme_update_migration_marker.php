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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Round 2 verification migration shipped in v0.1.5.
 *
 * Purpose: prove that `dls:theme:update` runs theme migrations forward and
 * that `dls:theme:rollback` reverses them. `up()` writes a marker row into
 * this theme's own settings table; `down()` deletes it, so the row's absence
 * after a rollback is direct evidence that the schema step was rolled back.
 *
 * Read this together with `database/seeders/UpdateSeeder.php`, which writes a
 * sibling marker that rollback does NOT remove. The contrast between the two
 * is the point: theme migrations are reversed, seeder-written data is not.
 *
 * The row is inert — nothing in the theme reads `theme_update_migration_marker`
 * — and `updateOrInsert` keeps both directions safe to re-run.
 */
return new class extends Migration
{
    private const TABLE = 'thm_dixlase_onepage_settings';

    private const MARKER_NAME = 'theme_update_migration_marker';

    public function up(): void
    {
        DB::table(self::TABLE)->updateOrInsert(
            ['name' => self::MARKER_NAME],
            ['value' => '0.1.5', 'created_at' => now(), 'updated_at' => now()],
        );
    }

    public function down(): void
    {
        DB::table(self::TABLE)
            ->where('name', self::MARKER_NAME)
            ->delete();
    }
};
