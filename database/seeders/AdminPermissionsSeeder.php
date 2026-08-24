<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\WebPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->call(WebPagesSeeder::class);

        $guard = 'admin';
        $actions = Admin::availableActions();

        foreach ($actions as $action) {
            Permission::findOrCreate($action, $guard);
        }

        $adminRole = Role::findOrCreate(Admin::ROLE_ADMIN, $guard);
        $supportRole = Role::findOrCreate(Admin::ROLE_SUPPORT, $guard);
        $agentRole = Role::findOrCreate(Admin::ROLE_AGENT, $guard);

        $adminRole->syncPermissions($actions);
        $supportRole->syncPermissions($actions);
        $agentRole->syncPermissions([Admin::ACTION_VIEW]);

        $legacyPermissions = [];
        if (Schema::hasTable('legacy_permissions')) {
            $legacyPermissions = DB::table('legacy_permissions')->pluck('code', 'id')->all();
        }

        $legacyAssignments = collect();
        if (Schema::hasTable('legacy_employee_perm')) {
            $legacyAssignments = DB::table('legacy_employee_perm')->get()->groupBy('employee_id');
        }

        $allPageIds = WebPage::where('is_active', true)->pluck('id')->all();

        Admin::query()->each(function (Admin $admin) use ($legacyPermissions, $legacyAssignments, $allPageIds) {
            $role = Admin::normalizeRole($admin->role, $admin->type);
            $admin->role = $role;
            $admin->type = $role;
            $admin->save();

            $admin->syncRoles([$role]);

            if ($role === Admin::ROLE_ADMIN) {
                $admin->syncPermissions(Admin::availableActions());
                $admin->pages()->sync($allPageIds);

                return;
            }

            $mapped = [];
            foreach ($legacyAssignments->get($admin->id, collect()) as $row) {
                $code = $legacyPermissions[$row->permission_id] ?? null;
                $mapped[] = match ($code) {
                    'view' => Admin::ACTION_VIEW,
                    'delete' => Admin::ACTION_DELETE,
                    'edit', 'add', 'approve', 'reject' => Admin::ACTION_UPDATE,
                    default => null,
                };
            }

            $mapped = array_values(array_unique(array_filter($mapped)));

            if (! $mapped) {
                $mapped = $admin->getPermissionNames()->all() ?: [Admin::ACTION_VIEW];
            }

            if (! in_array(Admin::ACTION_VIEW, $mapped, true)) {
                $mapped[] = Admin::ACTION_VIEW;
            }

            $admin->syncPermissions($mapped);
        });

        if (! Admin::role(Admin::ROLE_ADMIN)->exists()) {
            $firstAdmin = Admin::query()->orderBy('id')->first();

            if ($firstAdmin) {
                $firstAdmin->role = Admin::ROLE_ADMIN;
                $firstAdmin->type = Admin::ROLE_ADMIN;
                $firstAdmin->save();
                $firstAdmin->syncRoles([Admin::ROLE_ADMIN]);
                $firstAdmin->syncPermissions(Admin::availableActions());
                $firstAdmin->pages()->sync($allPageIds);
            }
        }
    }
}
