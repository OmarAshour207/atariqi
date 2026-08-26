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

        if ($supportRole->permissions()->count() === 0) {
            $supportRole->syncPermissions($defaultViews);
        } else {
            $supportRole->syncPermissions(
                $this->migratePermissionNames($supportRole->permissions->pluck('name')->all(), $allPermissions)
            );
        }

        if ($agentRole->permissions()->count() === 0) {
            $agentRole->syncPermissions($defaultViews);
        } else {
            $agentRole->syncPermissions(
                $this->migratePermissionNames($agentRole->permissions->pluck('name')->all(), $allPermissions)
            );
        }

        Role::where('guard_name', $guard)
            ->whereNotIn('name', Admin::systemRoleNames())
            ->each(function (Role $role) use ($allPermissions) {
                $role->syncPermissions(
                    $this->migratePermissionNames($role->permissions->pluck('name')->all(), $allPermissions)
                );
            });

        $allPageIds = WebPage::where('is_active', true)->pluck('id')->all();

        Admin::query()->each(function (Admin $admin) use ($allPageIds, $allPermissions, $adminRole) {
            $roleName = Admin::normalizeRole($admin->role, $admin->type);
            $admin->role = $roleName;
            $admin->type = $roleName === Admin::ROLE_ADMIN ? Admin::ROLE_ADMIN : $roleName;
            $admin->saveQuietly();

            $role = Role::findOrCreate($roleName, 'admin');
            $admin->syncRoles([$roleName]);
            $admin->syncPermissions([]);

            if ($roleName === Admin::ROLE_ADMIN) {
                $adminRole->syncPermissions($allPermissions);
                $admin->pages()->sync($allPageIds);

                return;
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
                $firstAdmin->saveQuietly();
                $firstAdmin->syncRoles([Admin::ROLE_ADMIN]);
                $firstAdmin->syncPermissions([]);
                $firstAdmin->pages()->sync($allPageIds);
            }
        }

        // Remove obsolete permission names (old view/update/delete-only set).
        Permission::where('guard_name', $guard)
            ->whereNotIn('name', $allPermissions)
            ->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function migratePermissionNames(array $existing, array $allPermissions): array
    {
        $mapped = [];

        foreach ($existing as $name) {
            if (in_array($name, $allPermissions, true)) {
                $mapped[] = $name;
                continue;
            }

            if (! str_contains($name, ' ')) {
                continue;
            }

            [$action, $resource] = explode(' ', $name, 2);
            $newAction = match ($action) {
                'view' => Admin::ACTION_VIEW,
                'delete', 'destroy' => Admin::ACTION_ADD_DELETE,
                'update', 'edit', 'add' => Admin::ACTION_UPDATE,
                default => null,
            };

            if (! $newAction) {
                continue;
            }

            // Legacy "update" covered approve/assign/ban — keep update only; admin reassigns decide/assign/ban.
            $candidate = Admin::permissionName($newAction, $resource);
            if (in_array($candidate, $allPermissions, true)) {
                $mapped[] = $candidate;
            }

            if ($action === 'delete') {
                $addDelete = Admin::permissionName(Admin::ACTION_ADD_DELETE, $resource);
                if (in_array($addDelete, $allPermissions, true)) {
                    $mapped[] = $addDelete;
                }
            }
        }

        return Admin::ensureViewWithMutations(array_values(array_unique($mapped)));
    }
}
