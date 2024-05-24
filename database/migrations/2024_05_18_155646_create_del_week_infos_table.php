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
        Schema::create('del-week-info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sug-id');
            $table->foreign('sug-id')->references('id')->on('sug-week-drivers')->onDelete('cascade');
            $table->time('expect-arrived')->nullable();
            $table->time('arrived-location')->nullable();
            $table->time('arrived-destination')->nullable();
            $table->integer('passenger-rate')->nullable();
            $table->string('allow-disabilities', 25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('del-week-info');
    }
};
