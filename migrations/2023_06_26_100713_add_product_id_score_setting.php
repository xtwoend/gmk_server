<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddProductIdScoreSetting extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('score_settings', function (Blueprint $table) {
            $table->tinyInteger('product_id')->nullable()->after('device_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('score_settings', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
}
