<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateAlarmTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alarm', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('device_id')->index();
            $table->string('property')->nullable();
            $table->tinyInteger('property_index')->default(0);
            $table->string('message')->nullable();
            $table->tinyInteger('status')->default(1); // 0: Close, 1: Open
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alarm');
    }
}
