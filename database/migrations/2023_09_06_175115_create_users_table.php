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
        Schema::create('users', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('user-first-name', 20);
            $table->string('user-last-name', 20);
            $table->bigInteger('call-key-id')->index('call-key-id');
            $table->string('phone-no', 20)->unique();
            $table->string('gender', 50);
            $table->bigInteger('university-id')->index('university-id');
            $table->bigInteger('user-stage-id')->index('user-stage-id');
            $table->string('email');
            $table->boolean('approval')->default(true);
            $table->string('user-type', 50);
            $table->dateTime('date-of-add')->useCurrent();
            $table->dateTime('date-of-edit')->nullable();
            $table->string('code')->nullable();
            $table->string('fcm_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
