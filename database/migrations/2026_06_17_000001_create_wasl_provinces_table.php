<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wasl_provinces', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('province_id')->unique();
            $table->string('province_name');
            $table->string('region_name');
            $table->string('province_name_normalized')->index();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wasl_provinces');
    }
};
