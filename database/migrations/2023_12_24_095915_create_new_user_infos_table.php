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
        Schema::create('new-users-info', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user-id');
            $table->foreign('user-id')->references('id')->on('users')->onDelete('cascade');

            $table->string('user-first-name', 20);
            $table->string('user-last-name', 20);

            $table->string('phone-no', 20);
            $table->string('gender', 50);
            $table->string('email');

            $table->string('user-type', 50);
            $table->string('image')->nullable();

            $table->bigInteger('call-key-id');
            $table->foreign('call-key-id')->references('id')->on('calling-key')->onDelete('cascade');

            $table->bigInteger('user-stage-id');
            $table->foreign('user-stage-id')->references('id')->on('stages')->onDelete('cascade');

            $table->bigInteger('university-id');
            $table->foreign('university-id')->references('id')->on('university')->onDelete('cascade');

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
        Schema::dropIfExists('new-users-info');
    }
};
