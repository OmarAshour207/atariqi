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
        Schema::create('day-ride-booking', function (Blueprint $table) {

            $table->id();

            $table->bigInteger('passenger-id');
            $table->foreign('passenger-id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('neighborhood-id');
            $table->foreign('neighborhood-id')->references('id')->on('neighborhoods')->onDelete('cascade');

            $table->bigInteger('service-id');
            $table->foreign('service-id')->references('id')->on('services')->onDelete('cascade');

            $table->date('date-of-ser')->nullable();
            $table->string('road-way')->nullable();
            $table->time('time-go')->nullable();
            $table->time('time-back')->nullable();
            $table->tinyInteger('action')->default(0);

            $table->timestamp('date-of-add')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('day-ride-booking');
    }
};
