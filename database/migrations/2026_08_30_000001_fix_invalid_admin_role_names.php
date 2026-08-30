<?php

use App\Models\Admin;
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

        // Custom Spatie role names must fit in a plain string column (not ENUM).
        DB::statement("ALTER TABLE `admins` MODIFY `role` VARCHAR(100) NOT NULL DEFAULT 'agent'");

        $guard = 'admin';
        $roles = DB::table('roles')->where('guard_name', $guard)->get();

        foreach ($roles as $role) {
            if (Admin::isValidRoleSlug($role->name)) {
                continue;
            }

            $newName = Admin::slugifyRoleName($role->name);

            if ($newName === '' || $newName === $role->name) {
                continue;
            }

            while (
                DB::table('roles')
                    ->where('guard_name', $guard)
                    ->where('name', $newName)
                    ->where('id', '!=', $role->id)
                    ->exists()
            ) {
                $newName .= '-role';
            }

            DB::table('admins')->where('role', $role->name)->update(['role' => $newName]);
            DB::table('admins')
                ->where('type', $role->name)
                ->where('role', '!=', Admin::ROLE_ADMIN)
                ->update(['type' => $newName]);

            DB::table('roles')->where('id', $role->id)->update(['name' => $newName]);
        }

        DB::table('admins')
            ->whereNotIn('role', DB::table('roles')->where('guard_name', $guard)->pluck('name'))
            ->update(['role' => Admin::ROLE_AGENT]);

        $validRoleNames = DB::table('roles')->where('guard_name', $guard)->pluck('name')->all();

        DB::table('admins')
            ->where('role', '!=', Admin::ROLE_ADMIN)
            ->whereNotIn('type', $validRoleNames)
            ->update(['type' => DB::raw('`role`')]);
    }

    public function down(): void
    {
        // Role renames are not safely reversible.
    }
};
