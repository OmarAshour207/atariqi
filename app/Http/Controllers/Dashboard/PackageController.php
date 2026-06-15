<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\NewPackageNotificationMail;
use App\Models\Package;
use App\Models\PlatformEmailLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = Package::with('features');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by name (English or Arabic)
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('name_en', 'like', '%' . $request->name . '%')
                  ->orWhere('name_ar', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('monthly_price')) {
            $query->where('price_monthly', '=', $request->monthly_price);
                //   ->orWhere('price_annual', '=', $request->monthly_price);
        }

        if ($request->filled('annual_price')) {
            $query->where('price_annual', '=', $request->annual_price);
                //   ->orWhere('price_monthly', '<=', $request->annual_price);
        }

        // Filter by features
        if ($request->filled('has_features')) {
            if ($request->has_features == '1') {
                $query->has('features');
            } elseif ($request->has_features == '0') {
                $query->doesntHave('features');
            }
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $query->orderBy($sortBy, $sortDirection);

        $packages = $query->paginate(20)->appends($request->query());

        return view('dashboard.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('dashboard.packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_annual' => 'required|numeric|min:0',
            'status' => 'required|in:0,1,2,3',
            'features' => 'nullable|array',
            'features.*' => 'exists:features,id'
        ]);

        $adminId = auth()->guard('admin')->id();
        $packageData = $request->only(['name_ar', 'name_en', 'price_monthly', 'price_annual', 'status']);

        DB::beginTransaction();
        try {
            $package = Package::create($packageData);

            if ($request->has('features')) {
                $package->features()->sync($request->features);
            }

            DB::table('subscription_employee_log')->insert([
                'employee_id' => $adminId,
                'package_id' => $package->id,
                'action_type' => 'created',
                'description' => __('New package created by employee.'),
                'payload' => json_encode($package->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('packages.index')->with('error', __('Unable to create package.'));
        }

        $customers = User::where('user-type', 'passenger')
            ->whereNotNull('email')
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($customers as $customer) {
            try {
                Mail::to($customer->email)->send(new NewPackageNotificationMail($package, $customer));
                PlatformEmailLog::create([
                    'assigned_from_employee_id' => $adminId,
                    'driver_id' => $customer->id,
                    'driver_email' => $customer->email,
                    'email_type' => 'new_package_notification',
                    'status' => 'success'
                ]);

                $sentCount++;
            } catch (\Throwable $e) {
                PlatformEmailLog::create([
                    'assigned_from_employee_id' => $adminId,
                    'driver_id' => $customer->id,
                    'driver_email' => $customer->email,
                    'email_type' => 'new_package_notification',
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
                $failedCount++;
            }
        }

        $message = __('Package created successfully.');
        if ($failedCount > 0) {
            $message .= ' ' . __(':count emails failed to send.', ['count' => $failedCount]);
        }

        return redirect()->route('packages.index')->with('success', $message);
    }

    public function edit(Package $package)
    {
        return view('dashboard.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_annual' => 'required|numeric|min:0',
            'status' => 'required|in:0,1,2,3',
            'features' => 'nullable|array',
            'features.*' => 'exists:features,id'
        ]);

        $adminId = auth()->guard('admin')->id();
        $oldPackageData = $package->only(['name_ar', 'name_en', 'price_monthly', 'price_annual', 'status']);

        DB::beginTransaction();
        try {
            $package->update($request->only(['name_ar', 'name_en', 'price_monthly', 'price_annual', 'status']));

            if ($request->has('features')) {
                $package->features()->sync($request->features);
            } else {
                $package->features()->detach();
            }

            DB::table('subscription_employee_log')->insert([
                'employee_id' => $adminId,
                'package_id' => $package->id,
                'action_type' => 'updated',
                'description' => __('Package updated by employee.'),
                'payload' => json_encode([
                    'old' => $oldPackageData,
                    'new' => $package->only(['name_ar', 'name_en', 'price_monthly', 'price_annual', 'status']),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('packages.index')->with('error', __('Unable to update package.'));
        }

        $customers = User::where('user-type', 'passenger')
            ->whereNotNull('email')
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($customers as $customer) {
            try {
                Mail::to($customer->email)->send(new NewPackageNotificationMail($package, $customer));

                PlatformEmailLog::create([
                    'assigned_from_employee_id' => $adminId,
                    'driver_id' => $customer->id,
                    'driver_email' => $customer->email,
                    'email_type' => 'package_update_notification',
                    'status' => 'sent',
                    'error_message' => null,
                ]);

                $sentCount++;
            } catch (\Throwable $e) {
                PlatformEmailLog::create([
                    'assigned_from_employee_id' => $adminId,
                    'driver_id' => $customer->id,
                    'driver_email' => $customer->email,
                    'email_type' => 'package_update_notification',
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                $failedCount++;
            }
        }

        $message = __('Package updated successfully.');
        if ($failedCount > 0) {
            $message .= ' ' . __(':count emails failed to send.', ['count' => $failedCount]);
        }

        return redirect()->route('packages.index')->with('success', $message);
    }

    public function destroy(Package $package)
    {
        try {
            $package->delete();
            return redirect()->route('packages.index')->with('success', __('Package deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()->route('packages.index')->with('error', __('Unable to delete package.'));
        }
    }
}
