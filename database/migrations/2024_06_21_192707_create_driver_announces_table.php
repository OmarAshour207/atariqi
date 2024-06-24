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
        Schema::create('driver-announce', function (Blueprint $table) {
            $table->id();
            $table->string('title-ar');
            $table->string('title-eng');
            $table->text('content-ar');
            $table->text('content-eng');
            $table->timestamp('date-of-add')->nullable();
            $table->timestamp('date-of-edit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver-announce');
    }
};
