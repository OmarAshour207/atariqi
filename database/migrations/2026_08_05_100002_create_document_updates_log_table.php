<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_updates_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assigned_from_employee_id')->nullable()->index();
            $table->unsignedBigInteger('document_id');
            $table->string('document_link_old');
            $table->string('document_link_new');
            $table->string('status')->default('change');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_updates_log');
    }
};
