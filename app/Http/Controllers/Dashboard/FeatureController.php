<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\FeatureUpdatedNotificationMail;
use App\Models\Feature;
use App\Models\PlatformEmailLog;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FeatureController extends Controller
{
    public function index(Request $request)
    {
        $query = Feature::with('service');

        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('name_en', 'like', '%' . $request->name . '%')
                    ->orWhere('name_ar', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'desc');

        $features = $query->orderBy($sortBy, $sortDirection)
            ->paginate(20)
            ->appends($request->query());

        $services = Service::orderBy('service')->get();

        return view('dashboard.features.index', compact('features', 'services'));
    }

    public function create()
    {
        $services = Service::orderBy('service')->get();
        return view('dashboard.features.create', compact('services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'service_id' => 'nullable|exists:services,id',
        ]);

        $feature = Feature::create($data);

        DB::table('subscription_employee_log')->insert([
            'employee_id' => auth()->guard('admin')->id(),
            'package_id' => null,
            'action_type' => 'feature_created',
            'description' => __('Feature created by employee.'),
            'payload' => json_encode(['new' => $feature->toArray()]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('features.index')->with('success', __('Feature created successfully.'));
    }

    public function show(Feature $feature)
    {
        return view('dashboard.features.show', compact('feature'));
    }

    public function edit(Feature $feature)
    {
        $services = Service::orderBy('service')->get();
        return view('dashboard.features.edit', compact('feature', 'services'));
    }

    public function update(Request $request, Feature $feature)
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'service_id' => 'nullable|exists:services,id',
        ]);

        $oldFeatureData = $feature->only(['name_ar', 'name_en', 'description_ar', 'description_en', 'service_id']);
        $feature->update($data);

        DB::table('subscription_employee_log')->insert([
            'employee_id' => auth()->guard('admin')->id(),
            'package_id' => null,
            'action_type' => 'feature_updated',
            'description' => __('Feature updated by employee.'),
            'payload' => json_encode([
                'old' => $oldFeatureData,
                'new' => $feature->only(['name_ar', 'name_en', 'description_ar', 'description_en', 'service_id']),
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $customers = User::where('user-type', 'passenger')
            ->whereNotNull('email')
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($customers as $customer) {
            try {
                Mail::to($customer->email)->send(new FeatureUpdatedNotificationMail($feature, $customer));
                PlatformEmailLog::create([
                    'assigned_from_employee_id' => auth()->guard('admin')->id(),
                    'driver_id' => $customer->id,
                    'driver_email' => $customer->email,
                    'email_type' => 'feature_updated_notification',
                    'status' => 'success'
                ]);

                $sentCount++;
            } catch (\Throwable $e) {
                PlatformEmailLog::create([
                    'assigned_from_employee_id' => auth()->guard('admin')->id(),
                    'driver_id' => $customer->id,
                    'driver_email' => $customer->email,
                    'email_type' => 'feature_updated_notification',
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
                $failedCount++;
            }
        }

        $message = __('Feature updated successfully.');
        if ($failedCount > 0) {
            $message .= ' ' . __(':count emails failed to send.', ['count' => $failedCount]);
        }

        return redirect()->route('features.index')->with('success', __('Feature updated successfully.'));
    }

    public function destroy(Feature $feature)
    {
        try {
            $feature->delete();
            return redirect()->route('features.index')->with('success', __('Feature deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()->route('features.index')->with('error', __('Unable to delete feature.'));
        }
    }
}
