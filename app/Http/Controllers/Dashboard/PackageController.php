<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::orderBy('created_at', 'desc')->paginate(20);

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
        ]);

        Package::create($request->only(['name_ar', 'name_en', 'price_monthly', 'price_annual', 'status']));

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
        ]);

        $package->update($request->only(['name_ar', 'name_en', 'price_monthly', 'price_annual', 'status']));

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
