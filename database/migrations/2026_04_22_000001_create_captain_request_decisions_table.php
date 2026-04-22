<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('captain_request_decisions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('action_type'); // reject or approve
            $table->tinyInteger('old_approval');
            $table->tinyInteger('new_approval');

            $table->bigInteger('decided_by_employee_id');
            $table->foreign('decided_by_employee_id')->references('id')->on('users')->onDelete('cascade');

            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('captain_request_decisions');
    }
};
