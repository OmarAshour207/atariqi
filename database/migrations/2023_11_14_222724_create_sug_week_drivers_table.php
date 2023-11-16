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
        Schema::create('sug-week-drivers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('booking-id');
            $table->foreign('booking-id')->references('id')->on('week-ride-booking')->onDelete('cascade');

            $table->bigInteger('driver-id');
            $table->foreign('driver-id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('passenger-id');
            $table->foreign('passenger-id')->references('id')->on('users')->onDelete('cascade');

            $table->tinyInteger('action')->default(0);
            $table->tinyInteger('viewed')->default(0);
            $table->dateTime('date-of-add')->useCurrent();
            $table->dateTime('date-of-edit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sug-week-drivers');
    }
};
