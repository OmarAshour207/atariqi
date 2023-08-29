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
        Schema::create('user-login', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('user-id')->index('user-id');
            $table->dateTime('date-time')->nullable();
            $table->boolean('login-logout');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user-login');
    }
};
