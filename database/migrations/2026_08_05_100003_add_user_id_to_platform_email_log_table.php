<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_email_log', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('assigned_from_employee_id');
        });
    }

    public function down(): void
    {
        Schema::table('platform_email_log', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
