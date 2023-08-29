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
        Schema::create('opening', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('title-ar', 50);
            $table->string('title-eng', 50);
            $table->text('contant-ar');
            $table->text('contant-eng');
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
        Schema::dropIfExists('opening');
    }
};
