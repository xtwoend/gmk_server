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
            $table->unsignedBigInteger('shift_id')->nullable(); // shift 1, 2, 3
            $table->unsignedBigInteger('user_id')->nullable(); // relation to user but not now
            $table->date('production_date');
            $table->time('started_at')->nullable();
            $table->time('ended_at')->nullable();
            $table->float('ppm', 10, 4)->default(0); 
            $table->integer('run_time')->default(0); // in seconds
            $table->integer('stop_time')->default(0); // in seconds
            $table->integer('down_time')->default(0); // in seconds
            $table->integer('output')->default(0);
            $table->integer('reject')->default(0);
            $table->float('availability', 5, 4)->nullable();
            $table->float('performance', 5, 4)->nullable();
            $table->float('quality', 5, 4)->nullable();
            $table->float('oee', 5, 4)->nullable();
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
