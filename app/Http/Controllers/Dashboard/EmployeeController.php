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
use Spatie\Permission\Models\Permission;

class EmployeeController extends Controller
{
    public function __construct(private ActionsLogService $actionsLog)
    {
    }

    public function index()
    {
        $employees = Admin::with(['roles', 'permissions', 'pages'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('dashboard.employees.index', compact('employees'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', 'admin')->orderBy('name')->get();
        $pages = WebPage::where('is_active', true)->orderBy('sort_order')->get();
        $roles = Admin::availableRoles();

        return view('dashboard.employees.create', compact('permissions', 'pages', 'roles'));
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

            $this->syncAccess($employee, $data['role'], $request->input('permissions', []), $request->input('page_ids', []));
            $this->actionsLog->logAdd('admins', $employee->id);
        });

        return redirect()->route('employees.index')->with('success', __('Employee created successfully.'));
    }

    public function edit(Admin $employee)
    {
        $permissions = Permission::where('guard_name', 'admin')->orderBy('name')->get();
        $pages = WebPage::where('is_active', true)->orderBy('sort_order')->get();
        $roles = Admin::availableRoles();

        return view('dashboard.employees.edit', compact('employee', 'permissions', 'pages', 'roles'));
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

        $employee->update($updateData);
        $this->actionsLog->logEdit('admins', $employee->id, $old);

        return redirect()->route('employees.index')->with('success', __('Employee updated successfully.'));
    }

    public function editPermissions(Admin $employee)
    {
        $permissions = Permission::where('guard_name', 'admin')->orderBy('name')->get();
        $selected = $employee->getPermissionNames()->all();

        return view('dashboard.employees.permissions', compact('employee', 'permissions', 'selected'));
    }

    public function updatePermissions(Request $request, Admin $employee)
    {
        $permissionNames = $request->input('permissions', []);
        $this->syncAccess($employee, $employee->role, $permissionNames, $employee->pages()->pluck('web_pages.id')->all());
        $this->actionsLog->logEdit('model_has_permissions', $employee->id);

        return redirect()->route('employees.index')->with('success', __('Employee permissions updated successfully.'));
    }

    public function editPages(Admin $employee)
    {
        $pages = WebPage::where('is_active', true)->orderBy('sort_order')->get();
        $selected = $employee->pages()->pluck('web_pages.id')->all();

        return view('dashboard.employees.pages', compact('employee', 'pages', 'selected'));
    }

    public function updatePages(Request $request, Admin $employee)
    {
        $pageIds = $request->input('page_ids', []);
        $this->syncAccess($employee, $employee->role, $employee->getPermissionNames()->all(), $pageIds);
        $this->actionsLog->logEdit('employee_page', $employee->id);

        return redirect()->route('employees.index')->with('success', __('Employee pages updated successfully.'));
    }

    private function syncAccess(Admin $employee, string $role, array $permissionNames, array $pageIds): void
    {
        $role = Admin::normalizeRole($role);
        $employee->syncRoles([$role]);

        if ($role === Admin::ROLE_ADMIN) {
            $employee->syncPermissions(Admin::availableActions());
            $employee->pages()->sync(WebPage::where('is_active', true)->pluck('id')->all());

            return;
        }

        $allowed = array_values(array_intersect($permissionNames, Admin::availableActions()));
        if (! in_array(Admin::ACTION_VIEW, $allowed, true) && $allowed) {
            $allowed[] = Admin::ACTION_VIEW;
        }

        $employee->syncPermissions($allowed);
        $employee->pages()->sync($pageIds);
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
            'role' => 'required|in:' . implode(',', Admin::availableRoles()),
            'is_active' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'in:' . implode(',', Admin::availableActions()),
            'page_ids' => 'nullable|array',
            'page_ids.*' => 'integer|exists:web_pages,id',
        ]);
    }
}
