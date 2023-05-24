<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateScoreSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('score_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('device_id');
            $table->tinyInteger('number_of_shift')->default(3);
            $table->tinyInteger('hours_per_shift')->default(8);
            $table->tinyInteger('planned_shutdown_shift')->default(1);
            $table->tinyInteger('ideal_cycle_time_seconds')->default(0);  // in seconds
            $table->integer('total_production')->nullable();
            $table->integer('good_production')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('score_settings');
    }
}
