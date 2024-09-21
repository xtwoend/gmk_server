<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddSpPpmSettings extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('score_settings', function (Blueprint $table) {
            $table->integer('sp_ppm_1')->default(0)->after('production_plan');
            $table->integer('sp_ppm_2')->default(0)->after('sp_ppm_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('score_settings', function (Blueprint $table) {
            $table->dropColumn('sp_ppm_1', 'sp_ppm_2');
        });
    }
}
