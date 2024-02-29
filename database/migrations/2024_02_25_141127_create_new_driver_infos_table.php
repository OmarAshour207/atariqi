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
        Schema::create('new-driver-info', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('driver-id');
            $table->foreign('driver-id')->references('id')->on('users')->onDelete('cascade');

            $table->string('car-brand')->nullable();

            $table->integer('car-model')->nullable();
            $table->integer('car-number')->nullable();

            $table->string('car-letters', 50)->nullable();
            $table->string('car-color', 50)->nullable();
            $table->string('driver-neighborhood')->nullable();
            $table->string('driver-license-link')->nullable();
            $table->string('allow-disabilities', 25)->nullable();

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
        Schema::dropIfExists('new-driver-info');
    }
};
