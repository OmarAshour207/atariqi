<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_package_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assigned_from_employee_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('id_package_old')->nullable();
            $table->unsignedBigInteger('id_package_new')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_package_logs');
    }
};
