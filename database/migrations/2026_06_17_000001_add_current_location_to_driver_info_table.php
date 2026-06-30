<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver-info', function (Blueprint $table) {
            $table->string('current-lat')->nullable()->after('sequence-number');
            $table->string('current-lng')->nullable()->after('current-lat');
            $table->dateTime('current-location-at')->nullable()->after('current-lng');
        });
    }

    public function down(): void
    {
        Schema::table('driver-info', function (Blueprint $table) {
            $table->dropColumn(['current-lat', 'current-lng', 'current-location-at']);
        });
    }
};
