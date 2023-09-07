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
        Schema::create('uni-driving-services', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('university-id');
            $table->bigInteger('service-id');
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
        Schema::dropIfExists('uni-driving-services');
    }
};
