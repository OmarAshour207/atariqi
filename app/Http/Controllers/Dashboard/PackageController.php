<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\NewPackageNotificationMail;
use App\Models\Package;
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

        if ($request->filled('min_price')) {
            $query->where('price_monthly', '>=', $request->min_price)
                  ->orWhere('price_annual', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_monthly', '<=', $request->max_price)
                  ->orWhere('price_annual', '<=', $request->max_price);
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

        $emailLogId = DB::table('platform_email_log')->insertGetId([
            'email_type' => 'new_package_notification',
            'package_id' => $package->id,
            'subject' => __('New Package Available'),
            'total_recipients' => $customers->count(),
            'sent_count' => 0,
            'failed_count' => 0,
            'failed_recipients' => null,
            'details' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sentCount = 0;
        $failedCount = 0;
        $failedRecipients = [];

        foreach ($customers as $customer) {
            try {
                Mail::to($customer->email)->send(new NewPackageNotificationMail($package, $customer));
                $sentCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                $failedRecipients[] = $customer->email;
            }
        }

        DB::table('platform_email_log')->where('id', $emailLogId)->update([
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'failed_recipients' => $failedRecipients ? json_encode($failedRecipients) : null,
            'details' => $failedCount > 0
                ? __('Some emails failed to send to customers.')
                : __('All notification emails were sent successfully.'),
            'updated_at' => now(),
        ]);

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

        $package->update($request->only(['name_ar', 'name_en', 'price_monthly', 'price_annual', 'status']));

        // Sync features if provided
        if ($request->has('features')) {
            $package->features()->sync($request->features);
        } else {
            $package->features()->detach();
        }

        return redirect()->route('packages.index')->with('success', __('Package updated successfully.'));
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
