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
        Schema::create('payment_reminders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('driver-id');
            $table->foreign('driver-id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('type', ['sms', 'notify'])->default('notify');
            $table->decimal('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_reminders');
    }
};
