<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

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

        $package = Package::create($request->only(['name_ar', 'name_en', 'price_monthly', 'price_annual', 'status']));

        // Sync features if provided
        if ($request->has('features')) {
            $package->features()->sync($request->features);
        }

        return redirect()->route('packages.index')->with('success', __('Package created successfully.'));
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
