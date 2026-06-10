<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('drivers-banned', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assigned_from_employee_id')->nullable();
            $table->string('driver_identity');
            $table->string('driver_no');
            $table->string('driver_car_no');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drivers-banned');
    }
};
