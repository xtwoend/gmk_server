<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddPpm2Score extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            //
            $table->float('ppm2', 10, 4)->default(0)->after('ppm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn('ppm2');
        });
    }
}
