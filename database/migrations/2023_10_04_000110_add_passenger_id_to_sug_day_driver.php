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
        Schema::table('sug-day-drivers', function (Blueprint $table) {
            $table->bigInteger('passenger-id')->nullable();
            $table->foreign('passenger-id')->references('id')->on('users');

            $table->tinyInteger('viewed')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sug-day-drivers', function (Blueprint $table) {
            //
        });
    }
};
