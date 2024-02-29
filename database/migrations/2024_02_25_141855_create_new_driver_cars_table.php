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
        Schema::create('new-driver-car', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('driver-id');
            $table->foreign('driver-id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('driver-type-id');
            $table->foreign('driver-type-id')->references('id')->on('driver_type')->onDelete('cascade');

            $table->string('car_form_img')->nullable();
            $table->string('license_img')->nullable();
            $table->string('car_front_img')->nullable();
            $table->string('car_back_img')->nullable();
            $table->string('car_rside_img')->nullable();
            $table->string('car_lside_img')->nullable();
            $table->string('car_insideFront_img')->nullable();
            $table->string('car_insideBack_img')->nullable();
            $table->string('date_of_add')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new-driver-car');
    }
};
