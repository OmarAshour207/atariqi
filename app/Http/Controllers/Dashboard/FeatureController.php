<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Service;
use Illuminate\Http\Request;

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

        Feature::create($data);

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

        $feature->update($data);

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
