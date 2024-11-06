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
        Schema::create('immediate-group-info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ride-id');
            $table->foreign('ride-id')->references('id')->on('suggestions-drivers')->onDelete('cascade');
            $table->unsignedBigInteger('group-id');
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
        Schema::dropIfExists('immediate-group-info');
    }
};
