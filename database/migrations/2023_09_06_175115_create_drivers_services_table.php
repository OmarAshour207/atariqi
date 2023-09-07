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
        Schema::create('drivers-services', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('driver-id')->index('driver-id');
            $table->bigInteger('service-id')->index('service-id');
            $table->dateTime('date-of-add')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers-services');
    }
};
