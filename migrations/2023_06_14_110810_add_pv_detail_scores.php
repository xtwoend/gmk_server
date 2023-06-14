<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddPvDetailScores extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->float('ppm_pv', 10, 3)->nullable()->after('ppm');
            $table->float('ppm_sv', 10, 3)->nullable()->after('ppm_pv');
            $table->float('ppm2_pv', 10, 3)->nullable()->after('ppm2');
            $table->float('ppm2_sv', 10, 3)->nullable()->after('ppm2_pv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn('ppm_pv', 'ppm_sv', 'ppm2_pv', 'ppm2_sv');
        });
    }
}
