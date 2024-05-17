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
        Schema::create('passenger-rate', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user-id');
            $table->foreign('user-id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('passenger-rate');
    }
};
