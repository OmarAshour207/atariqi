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
            $table->string('identity_number')->unique()->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_birth_hijri')->nullable();
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
            $table->dropColumn('identity_number');
            $table->dropColumn('date_of_birth');
            $table->dropColumn('date_of_birth_hijri');
        });
    }
};
