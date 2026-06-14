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
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the theme's settings table.
 *
 * Consolidated from three earlier migrations that, in order, (a) created
 * `thm_dixlase_one_page_settings`, (b) dropped the deprecated
 * `thm_dixlase_onepage_settings_aggregate` helper table, and (c) renamed
 * the table to `thm_dixlase_onepage_settings` to match the slug Core
 * derives at runtime. The end state of that chain is baked into this
 * single `Schema::create` so fresh installs converge on it in one step.
 *
 * Existing installs that already ran the previous three-file chain have
 * those rows in `dls_theme_migrations` (or, on older sites, in
 * `dls_migrations` because of the install-time stock-migrator path that
 * was fixed in dixlase-core #69). After consolidation the obsolete
 * `drop_*_aggregate_table` and `rename_*_to_onepage` rows become
 * orphans on `dls:migration:resync` and have to be deleted from the
 * ledger with a one-line SQL; the data tables themselves are already
 * in their target shape and need no further touching.
 *
 * The filename is kept as `..._create_thm_dixlase_one_page_settings_table`
 * so the existing ledger row's suffix still matches and resync sees no
 * realignment for the surviving row — only the two delete-able orphans.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thm_dixlase_onepage_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Setting key name');
            $table->text('value')->nullable()->comment('Setting value');
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thm_dixlase_onepage_settings');
    }
};
