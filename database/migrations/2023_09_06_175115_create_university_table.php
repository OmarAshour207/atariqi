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
        Schema::create('university', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->char('name-ar');
            $table->char('name-eng');
            $table->string('country', 100);
            $table->string('city', 100);
            $table->text('location');
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
        Schema::dropIfExists('university');
    }
};
