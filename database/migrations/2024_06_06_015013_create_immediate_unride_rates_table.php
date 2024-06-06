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
        Schema::create('immediate-unride-rate', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sug-id');
            $table->foreign('sug-id')->references('id')->on('suggestions-drivers')->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->decimal('rate', 5, 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('immediate-unride-rate');
    }
};
