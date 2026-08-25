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

        // Allow custom Spatie role names (not only enum values).
        DB::statement("ALTER TABLE `admins` MODIFY `role` VARCHAR(100) NOT NULL DEFAULT 'agent'");

        DB::table('admins')->where('role', 'supervisor')->update(['role' => 'support']);
    }

    public function down(): void
    {
        if (! Schema::hasColumn('admins', 'role')) {
            return;
        }

        DB::table('admins')
            ->whereNotIn('role', ['admin', 'support', 'agent'])
            ->update(['role' => 'agent']);

        DB::statement("ALTER TABLE `admins` MODIFY `role` ENUM('agent', 'support', 'admin') NOT NULL DEFAULT 'agent'");
    }
};
