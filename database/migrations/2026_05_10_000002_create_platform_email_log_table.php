<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('platform_email_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assigned_from_employee_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('driver_email')->nullable();
            $table->string('email_type');
            $table->string('status')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('platform_email_log');
    }
};
