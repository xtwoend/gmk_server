<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddLme3Table extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lme_9_202309', function (Blueprint $table) {
            $table->float('cooling_tank', 10, 2)->default(0);
            $table->float('holding_tank', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
}
