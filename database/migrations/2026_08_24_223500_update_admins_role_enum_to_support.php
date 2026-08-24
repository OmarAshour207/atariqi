<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('admins', 'role')) {
            return;
        }

        // Expand ENUM so both old and new values are valid during conversion.
        DB::statement("ALTER TABLE `admins` MODIFY `role` ENUM('agent', 'supervisor', 'support', 'admin') NOT NULL DEFAULT 'agent'");

        DB::table('admins')->where('role', 'supervisor')->update(['role' => 'support']);

        DB::statement("ALTER TABLE `admins` MODIFY `role` ENUM('agent', 'support', 'admin') NOT NULL DEFAULT 'agent'");
    }

    public function down(): void
    {
        if (! Schema::hasColumn('admins', 'role')) {
            return;
        }

        DB::statement("ALTER TABLE `admins` MODIFY `role` ENUM('agent', 'support', 'supervisor', 'admin') NOT NULL DEFAULT 'agent'");

        DB::table('admins')->where('role', 'support')->update(['role' => 'supervisor']);

        DB::statement("ALTER TABLE `admins` MODIFY `role` ENUM('agent', 'supervisor', 'admin') NOT NULL DEFAULT 'agent'");
    }
};
