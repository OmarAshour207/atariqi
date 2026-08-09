<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\WebPage;
use App\Services\ActionsLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function __construct(private ActionsLogService $actionsLog)
    {
    }

    public function index()
    {
        $employees = Admin::with(['permissions', 'pages'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('dashboard.employees.index', compact('employees'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('id')->get();
        $pages = WebPage::where('is_active', true)->orderBy('sort_order')->get();

        return view('dashboard.employees.create', compact('permissions', 'pages'));
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
                'type' => $data['role'] === 'admin' ? 'admin' : 'support',
                'is_active' => $request->boolean('is_active', true),
            ]);

            $employee->permissions()->sync($request->input('permission_ids', []));
            $employee->pages()->sync($request->input('page_ids', []));

            $this->actionsLog->logAdd('admins', $employee->id);
        });

        $message = __('Employee created successfully.');
        $flash = 'success';

        if (!$request->has('permission_ids')) {
            $message = __('Employee created successfully.') . ' ' . __('Employee saved without permissions. They will only have view access on assigned pages.');
            $flash = 'warning';
        }

        if (!$request->has('page_ids')) {
            $message = __('Employee created successfully.') . ' ' . __('Employee saved without pages. They will not be able to access the dashboard sections.');
            $flash = 'warning';
        }

        return redirect()->route('employees.index')->with($flash, $message);
    }

    public function edit(Admin $employee)
    {
        $permissions = Permission::orderBy('id')->get();
        $pages = WebPage::where('is_active', true)->orderBy('sort_order')->get();

        return view('dashboard.employees.edit', compact('employee', 'permissions', 'pages'));
    }

    public function update(Request $request, Admin $employee)
    {
        $data = $this->validateEmployee($request, $employee->id);

        $old = $employee->toArray();
        $changed = false;

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'type' => $data['role'] === 'admin' ? 'admin' : 'support',
            'is_active' => $request->boolean('is_active', true),
        ];

        $changed = $employee->name !== $updateData['name']
            || $employee->email !== $updateData['email']
            || ($employee->role ?? 'agent') !== $updateData['role']
            || (bool) $employee->is_active !== $updateData['is_active'];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
            $changed = true;
        }

        if (!$changed) {
            return back()->with('warning', __('No changes were made.'));
        }

        $employee->update($updateData);
        $this->actionsLog->logEdit('admins', $employee->id, $old);

        return redirect()->route('employees.index')->with('success', __('Employee updated successfully.'));
    }

    public function editPermissions(Admin $employee)
    {
        $permissions = Permission::orderBy('id')->get();
        $selected = $employee->permissions()->pluck('permissions.id')->all();

        return view('dashboard.employees.permissions', compact('employee', 'permissions', 'selected'));
    }

    public function updatePermissions(Request $request, Admin $employee)
    {
        $permissionIds = $request->input('permission_ids', []);

        if ($employee->permissions()->pluck('permissions.id')->sort()->values()->all() === collect($permissionIds)->sort()->values()->all()) {
            return back()->with('warning', __('No permission changes were made.'));
        }

        $employee->permissions()->sync($permissionIds);
        $this->actionsLog->logEdit('employee_perm', $employee->id);

        if (empty($permissionIds)) {
            return redirect()->route('employees.index')->with('warning', __('Employee saved without permissions. They will only have view access on assigned pages.'));
        }

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

        if ($employee->pages()->pluck('web_pages.id')->sort()->values()->all() === collect($pageIds)->sort()->values()->all()) {
            return back()->with('warning', __('No page changes were made.'));
        }

        $employee->pages()->sync($pageIds);
        $this->actionsLog->logEdit('employee_page', $employee->id);

        if (empty($pageIds)) {
            return redirect()->route('employees.index')->with('warning', __('Employee saved without pages. They will not be able to access the dashboard sections.'));
        }

        return redirect()->route('employees.index')->with('success', __('Employee pages updated successfully.'));
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
            'role' => 'required|in:agent,supervisor,admin',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
