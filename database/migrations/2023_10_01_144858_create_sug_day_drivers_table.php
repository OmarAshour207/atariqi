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
        Schema::create('sug-day-drivers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('driver-id');
            $table->foreign('driver-id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('booking-id');
            $table->foreign('booking-id')->references('id')->on('day-ride-booking')->onDelete('cascade');

            $table->tinyInteger('action')->nullable();
            $table->timestamp('date-of-add')->useCurrent();
            $table->timestamp('date-of-edit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sug-day-drivers');
    }
};
