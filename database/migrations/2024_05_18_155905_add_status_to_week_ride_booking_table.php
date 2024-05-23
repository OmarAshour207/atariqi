<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('week-ride-booking', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->after('action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('week-ride-booking', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
