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
        Schema::create('ride-booking', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('passenger-id')->index('passenger-id');
            $table->bigInteger('neighborhood-id');
            $table->text('location');
            $table->bigInteger('service-id')->index('service-id');
            $table->integer('action');
            $table->dateTime('date-of-add');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ride-booking');
    }
};
