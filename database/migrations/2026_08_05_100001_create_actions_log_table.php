<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actions_log', function (Blueprint $table) {
            $table->id();
            $table->string('action_type');
            $table->string('table_raw')->nullable();
            $table->json('old_data')->nullable();
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('decided_by_employee_id')->nullable()->index();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actions_log');
    }
};
