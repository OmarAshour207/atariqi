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
        Schema::table('users', function (Blueprint $table) {
            $table->foreign(['university-id'], 'users_ibfk_1')->references(['id'])->on('university');
            $table->foreign(['call-key-id'], 'users_ibfk_3')->references(['id'])->on('calling-key');
            $table->foreign(['user-stage-id'], 'users_ibfk_2')->references(['id'])->on('stages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_ibfk_1');
            $table->dropForeign('users_ibfk_3');
            $table->dropForeign('users_ibfk_2');
        });
    }
};
