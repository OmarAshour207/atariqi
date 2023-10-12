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
        Schema::table('suggestions-drivers', function (Blueprint $table) {
            $table->bigInteger('passenger-id')->nullable();
            $table->foreign('passenger-id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suggestions-drivers', function (Blueprint $table) {
            $table->dropColumn('passenger-id');
        });
    }
};
