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
        $allPermissions = Admin::allPermissionNames();

        foreach ($allPermissions as $permission) {
            Permission::findOrCreate($permission, $guard);
        }

        $adminRole = Role::findOrCreate(Admin::ROLE_ADMIN, $guard);
        $supportRole = Role::findOrCreate(Admin::ROLE_SUPPORT, $guard);
        $agentRole = Role::findOrCreate(Admin::ROLE_AGENT, $guard);

        $adminRole->syncPermissions($allPermissions);

        $defaultViews = [
            Admin::permissionName(Admin::ACTION_VIEW, 'dashboard'),
        ];

        // Starter roles: view dashboard only unless already customized.
        if ($supportRole->permissions()->count() === 0) {
            $supportRole->syncPermissions($defaultViews);
        }

        if ($agentRole->permissions()->count() === 0) {
            $agentRole->syncPermissions($defaultViews);
        }

        $legacyPermissions = [];
        if (Schema::hasTable('legacy_permissions')) {
            $legacyPermissions = DB::table('legacy_permissions')->pluck('code', 'id')->all();
        }

        $legacyAssignments = collect();
        if (Schema::hasTable('legacy_employee_perm')) {
            $legacyAssignments = DB::table('legacy_employee_perm')->get()->groupBy('employee_id');
        }

        $allPageIds = WebPage::where('is_active', true)->pluck('id')->all();

        Admin::query()->each(function (Admin $admin) use (
            $legacyPermissions,
            $legacyAssignments,
            $allPageIds,
            $allPermissions,
            $adminRole,
            $supportRole,
            $agentRole,
            $defaultViews
        ) {
            $roleName = Admin::normalizeRole($admin->role, $admin->type);
            $admin->role = $roleName;
            $admin->type = $roleName === Admin::ROLE_ADMIN ? Admin::ROLE_ADMIN : $roleName;
            $admin->save();

            $role = Role::findOrCreate($roleName, 'admin');
            $admin->syncRoles([$roleName]);
            $admin->syncPermissions([]);

            if ($roleName === Admin::ROLE_ADMIN) {
                $adminRole->syncPermissions($allPermissions);
                $admin->pages()->sync($allPageIds);

                return;
            }

            // First-time migration: expand old global actions onto role if role empty.
            if ($role->permissions()->count() === 0) {
                $actions = $this->resolveLegacyActions($admin, $legacyPermissions, $legacyAssignments);
                $pageRoutes = $admin->pages()->pluck('web_pages.route')->all();

                if (! $actions) {
                    $actions = [Admin::ACTION_VIEW];
                }

                $resourcePermissions = [];
                foreach ($pageRoutes as $route) {
                    $resource = Admin::resourceKeyFromRoute($route);
                    if (! $resource) {
                        continue;
                    }
                    foreach ($actions as $action) {
                        $resourcePermissions[] = Admin::permissionName($action, $resource);
                    }
                }

                if ($resourcePermissions) {
                    $role->syncPermissions(Admin::ensureViewWithMutations($resourcePermissions));
                } elseif (in_array($roleName, [Admin::ROLE_SUPPORT, Admin::ROLE_AGENT], true)) {
                    $role->syncPermissions($defaultViews);
                }
            }

            $admin->pages()->sync(
                Admin::pageIdsFromPermissionNames($role->fresh()->permissions->pluck('name')->all())
            );
        });

        if (! Admin::role(Admin::ROLE_ADMIN)->exists()) {
            $firstAdmin = Admin::query()->orderBy('id')->first();

            if ($firstAdmin) {
                $firstAdmin->role = Admin::ROLE_ADMIN;
                $firstAdmin->type = Admin::ROLE_ADMIN;
                $firstAdmin->save();
                $firstAdmin->syncRoles([Admin::ROLE_ADMIN]);
                $firstAdmin->syncPermissions([]);
                $firstAdmin->pages()->sync($allPageIds);
            }
        }

        // Drop obsolete global-only permissions.
        Permission::where('guard_name', $guard)
            ->whereIn('name', Admin::availableActions())
            ->whereNotIn('name', $allPermissions)
            ->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function resolveLegacyActions(Admin $admin, array $legacyPermissions, $legacyAssignments): array
    {
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

        return array_values(array_unique(array_filter($mapped)));
    }
}
