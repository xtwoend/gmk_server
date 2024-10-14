<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddEnableIdleSetting extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('score_settings', function (Blueprint $table) {
            $table->boolean('enable_idle')->default(true)->after('sp_ppm_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('score_settings', function (Blueprint $table) {
            $table->dropColumn('enable_idle');
        });
    }
}
