<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('thm_dixlase_default_theme_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('header_logo_id')->nullable()->after('id');
            $table->unsignedBigInteger('favicon_id')->nullable()->after('header_logo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thm_dixlase_default_theme_settings', function (Blueprint $table) {
            $table->dropColumn(['header_logo_id', 'favicon_id']);
        });
    }
};
