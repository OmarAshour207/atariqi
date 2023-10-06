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
        Schema::table('day-ride-booking', function (Blueprint $table) {
            $table->bigInteger('university-id')->after('neighborhood-id')->nullable();
            $table->foreign('university-id')->references('id')->on('university')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('day-ride-booking', function (Blueprint $table) {
            $table->dropColumn('university-id');
        });
    }
};
