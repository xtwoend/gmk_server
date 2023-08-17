<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class Lme8 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lme_8_202308', function (Blueprint $table) {
            $table->boolean('chilled_water_in_run')->default(false);
            $table->boolean('chilled_water_out_run')->default(false);
            $table->float('chilled_water_in', 10, 2)->default(0);
            $table->float('chilled_water_out', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('', function (Blueprint $table) {
        //     //
        // });
    }
}
