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

return new class extends Migration
{
    /**
     * Drop the aggregate table that previously anchored the theme's
     * translatable settings against the polymorphic
     * `plg_dixlase_multilingual_translations` table.
     *
     * With DixlaseMultilingual's singleton cardinality and the theme's
     * `DixlaseOnePageSettingsProvider`, theme-settings translations are
     * now anchored on
     * `(translatable_type = 'dixlase-onepage:settings', translatable_id = 1)`
     * directly — no backing model row required. The provider reads
     * primary-locale values straight from `thm_dixlase_onepage_settings`,
     * and `dls_onepage_localized_setting()` walks the
     * SingletonTranslationResolver chain.
     *
     * Idempotent: `dropIfExists` is a no-op on installs that never had
     * the aggregate-create migration applied (the create migration was
     * never canonical — only present locally in a transitional state on
     * a few environments). Mirrors DixlaseInquiry's Phase-3 cleanup
     * (`plg_dixlase_inquiry_settings_aggregate`).
     *
     * `down()` recreates the table empty so a rollback does not strand a
     * legacy code path that still expects the table to exist.
     */
    public function up(): void
    {
        Schema::dropIfExists('thm_dixlase_onepage_settings_aggregate');
    }

    public function down(): void
    {
        Schema::create('thm_dixlase_onepage_settings_aggregate', function ($table) {
            $table->id();
            $table->timestamps();
        });
    }
};
