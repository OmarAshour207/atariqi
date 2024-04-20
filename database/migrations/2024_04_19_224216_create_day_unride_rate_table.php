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
        Schema::create('day-unride-rate', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sug-id');
            $table->foreign('sug-id')->references('id')->on('sug-day-drivers')->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->tinyInteger('rate')->default(5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('day-unride-rate');
    }
};
