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
        Schema::create('driver-info', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('driver-id')->index('driver-id');
            $table->string('car-brand', 50);
            $table->bigInteger('car-model');
            $table->integer('car-number');
            $table->string('car-letters', 10);
            $table->string('car-color', 50);
            $table->string('driver-neighborhood', 50);
            $table->integer('driver-rate')->default(0);
            $table->string('driver-license-link', 225);
            $table->string('allow-disabilities', 25)->default('yes');
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
        Schema::dropIfExists('driver-info');
    }
};
