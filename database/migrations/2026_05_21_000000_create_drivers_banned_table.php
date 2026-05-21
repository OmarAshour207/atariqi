<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('drivers-banned', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver-id');
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('banned-by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drivers-banned');
    }
};
