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
        Schema::table('drivers-schedule', function (Blueprint $table) {
            $table->foreign(['driver-id'], 'drivers-schedule_ibfk_1')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers-schedule', function (Blueprint $table) {
            $table->dropForeign('drivers-schedule_ibfk_1');
        });
    }
};
