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
        Schema::create('drivers-neighborhoods', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('driver-id')->index('user-id');
            $table->text('neighborhoods-to');
            $table->text('neighborhoods-from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers-neighborhoods');
    }
};
