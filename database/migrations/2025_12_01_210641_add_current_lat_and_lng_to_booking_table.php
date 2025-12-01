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
        Schema::table('ride-booking', function (Blueprint $table) {
            $table->string('current-lat')->nullable()->after('road-way');
            $table->string('current-lng')->nullable()->after('current-lat');
        });

        Schema::table('day-ride-booking', function (Blueprint $table) {
            $table->string('current-lat')->nullable();
            $table->string('current-lng')->nullable()->after('current-lat');
        });

        Schema::table('week-ride-booking', function (Blueprint $table) {
            $table->string('current-lat')->nullable()->after('status');
            $table->string('current-lng')->nullable()->after('current-lat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ride-booking', function (Blueprint $table) {
            $table->dropColumn('current-lat');
            $table->dropColumn('current-lng');
        });

        Schema::table('day-ride-booking', function (Blueprint $table) {
            $table->dropColumn('current-lat');
            $table->dropColumn('current-lng');
        });

        Schema::table('week-ride-booking', function (Blueprint $table) {
            $table->dropColumn('current-lat');
            $table->dropColumn('current-lng');
        });
    }
};
