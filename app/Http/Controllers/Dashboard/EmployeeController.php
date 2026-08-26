<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\WebPage;
use App\Services\ActionsLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class EmployeeController extends Controller
{
    public function __construct(private ActionsLogService $actionsLog)
    {
    }

    public function index()
    {
        $employees = Admin::with(['roles', 'pages'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('dashboard.employees.index', compact('employees'));
    }

    public function create()
    {
        $roles = Admin::availableRoles();

        return view('dashboard.employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $this->validateEmployee($request);

        DB::transaction(function () use ($data, $request) {
            $employee = Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            $this->assignRole($employee, $data['role']);
            $this->actionsLog->logAdd('admins', $employee->id);
        });

        return redirect()->route('employees.index')->with('success', __('Employee created successfully.'));
    }

    public function edit(Admin $employee)
    {
        $roles = Admin::availableRoles();

        return view('dashboard.employees.edit', compact('employee', 'roles'));
    }

    public function update(Request $request, Admin $employee)
    {
        $data = $this->validateEmployee($request, $employee->id);
        $old = $employee->toArray();

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active', false),
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        DB::transaction(function () use ($employee, $updateData, $data, $old) {
            $employee->fill($updateData);
            $this->assignRole($employee, $data['role']);
            $this->actionsLog->logEdit('admins', $employee->id, $old);
        });

        return redirect()->route('employees.index')->with('success', __('Employee updated successfully.'));
    }

    private function assignRole(Admin $employee, string $roleName): void
    {
        $role = Role::where('guard_name', 'admin')->where('name', $roleName)->first();

        if (! $role) {
            $normalized = Admin::normalizeRole($roleName);
            $role = Role::where('guard_name', 'admin')->where('name', $normalized)->first();
        }

        if (! $role) {
            throw ValidationException::withMessages([
                'role' => __('Selected role was not found.'),
            ]);
        }

        $employee->role = $role->name;
        $employee->type = $role->name === Admin::ROLE_ADMIN ? Admin::ROLE_ADMIN : $role->name;
        $employee->save();

        $employee->syncRoles([$role->name]);
        $employee->syncPermissions([]);

        if ($role->name === Admin::ROLE_ADMIN) {
            $employee->pages()->sync(WebPage::where('is_active', true)->pluck('id')->all());
        } else {
            $permissionNames = $role->permissions()->pluck('name')->all();
            $employee->pages()->sync(Admin::pageIdsFromPermissionNames($permissionNames));
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function validateEmployee(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('admins', 'email')->ignore($ignoreId),
            ],
            'password' => [$ignoreId ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')->where(fn ($q) => $q->where('guard_name', 'admin')),
            ],
            'is_active' => 'nullable|boolean',
        ]);
    }
}
