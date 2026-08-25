<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\WebPage;
use App\Services\ActionsLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function __construct(private ActionsLogService $actionsLog)
    {
    }

    public function index()
    {
        $roles = Role::where('guard_name', 'admin')
            ->withCount('users')
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('dashboard.roles.index', compact('roles'));
    }

    public function create()
    {
        $matrix = Admin::permissionsMatrix();
        $selected = [];

        return view('dashboard.roles.create', compact('matrix', 'selected'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRole($request);

        DB::transaction(function () use ($data, $request) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'admin',
            ]);

            $permissions = Admin::ensureViewWithMutations($request->input('permissions', []));
            $role->syncPermissions($permissions);

            if ($role->name === Admin::ROLE_ADMIN) {
                $role->syncPermissions(Admin::allPermissionNames());
            }

            $this->actionsLog->logAdd('roles', $role->id);
        });

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', __('Role created successfully.'));
    }

    public function edit(Role $role)
    {
        abort_unless($role->guard_name === 'admin', 404);

        $matrix = Admin::permissionsMatrix();
        $selected = $role->permissions->pluck('name')->all();

        return view('dashboard.roles.edit', compact('role', 'matrix', 'selected'));
    }

    public function update(Request $request, Role $role)
    {
        abort_unless($role->guard_name === 'admin', 404);

        $data = $this->validateRole($request, $role->id);

        DB::transaction(function () use ($data, $request, $role) {
            $oldName = $role->name;

            if ($role->name !== Admin::ROLE_ADMIN) {
                $role->name = $data['name'];
                $role->save();
            }

            $permissions = Admin::ensureViewWithMutations($request->input('permissions', []));

            if ($role->name === Admin::ROLE_ADMIN) {
                $permissions = Admin::allPermissionNames();
            }

            $role->syncPermissions($permissions);

            if ($oldName !== $role->name) {
                Admin::where('role', $oldName)->update([
                    'role' => $role->name,
                    'type' => $role->name === Admin::ROLE_ADMIN ? Admin::ROLE_ADMIN : $role->name,
                ]);
            }

            $this->syncEmployeesForRole($role);
            $this->actionsLog->logEdit('roles', $role->id);
        });

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', __('Role updated successfully.'));
    }

    public function destroy(Role $role)
    {
        abort_unless($role->guard_name === 'admin', 404);

        if ($role->name === Admin::ROLE_ADMIN) {
            return back()->with('error', __('The admin role cannot be deleted.'));
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', __('Cannot delete a role that is assigned to employees.'));
        }

        $roleId = $role->id;
        $role->delete();
        $this->actionsLog->logEdit('roles', $roleId);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', __('Role deleted successfully.'));
    }

    private function syncEmployeesForRole(Role $role): void
    {
        $pageIds = Admin::pageIdsFromPermissionNames($role->permissions()->pluck('name')->all());

        if ($role->name === Admin::ROLE_ADMIN) {
            $pageIds = WebPage::where('is_active', true)->pluck('id')->all();
        }

        Admin::role($role->name)->each(function (Admin $admin) use ($role, $pageIds) {
            $admin->role = $role->name;
            $admin->type = $role->name === Admin::ROLE_ADMIN ? Admin::ROLE_ADMIN : $role->name;
            $admin->save();
            $admin->syncPermissions([]);
            $admin->pages()->sync($pageIds);
        });
    }

    private function validateRole(Request $request, ?int $ignoreId = null): array
    {
        $request->merge([
            'name' => Admin::slugifyRoleName((string) $request->input('name')),
        ]);

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('roles', 'name')
                    ->where(fn ($q) => $q->where('guard_name', 'admin'))
                    ->ignore($ignoreId),
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => 'in:' . implode(',', Admin::allPermissionNames()),
        ], [
            'name.regex' => __('Role name must be lowercase letters, numbers and dashes only.'),
        ]);
    }
}
