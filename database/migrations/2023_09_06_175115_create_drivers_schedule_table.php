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
        Schema::create('drivers-schedule', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('driver-id')->index('user-id');
            $table->time('Saturday-to')->nullable();
            $table->time('Saturday-from')->nullable();
            $table->time('Sunday-to')->nullable();
            $table->time('Sunday-from')->nullable();
            $table->time('Monday-to')->nullable();
            $table->time('Monday-from')->nullable();
            $table->time('Tuesday-to')->nullable();
            $table->time('Tuesday-from')->nullable();
            $table->time('Wednesday-to')->nullable();
            $table->time('Wednesday-from')->nullable();
            $table->time('Thursday-to')->nullable();
            $table->time('Thursday-from')->nullable();
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
        Schema::dropIfExists('drivers-schedule');
    }
};
