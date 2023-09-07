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
        Schema::table('drivers-services', function (Blueprint $table) {
            $table->foreign(['driver-id'], 'drivers-services_ibfk_1')->references(['id'])->on('users');
            $table->foreign(['service-id'], 'drivers-services_ibfk_2')->references(['id'])->on('services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers-services', function (Blueprint $table) {
            $table->dropForeign('drivers-services_ibfk_1');
            $table->dropForeign('drivers-services_ibfk_2');
        });
    }
};
