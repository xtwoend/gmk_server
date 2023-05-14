<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateScoresTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('device_id');
            $table->date('date_score');
            $table->tinyInteger('number_of_shift')->default(3);
            $table->tinyInteger('hours_per_shift')->default(8);
            $table->tinyInteger('planned_shutdown_shift')->default(1);
            $table->float('downtime_loss', 4, 2)->nullable(); // from summary duration alarm
            $table->tinyInteger('ideal_cycle_time_seconds')->default(0);  // in seconds
            $table->integer('total_production')->nullable();
            $table->integer('good_production')->nullable();
            $table->float('availability', 5, 2)->nullable();
            $table->float('performance', 5, 2)->nullable();
            $table->float('quality', 5, 2)->nullable();
            $table->float('oee', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
}
