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
        Schema::table('driver-info', function (Blueprint $table) {
            $table->string('sequence-number')->nullable()->after('car-letters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver-info', function (Blueprint $table) {
            $table->dropColumn('sequence-number');
        });
    }
};
