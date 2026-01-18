<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\HomepageStat;
use Illuminate\Http\Request;

class HomepageStatController extends Controller
{
    public function index(Request $request)
    {
        $stats = HomepageStat::paginate(20);

        return view('dashboard.homepage_stats.index', compact('stats'));
    }

        public function create()
    {
        return view('dashboard.homepage_stats.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'nullable|string|max:255',
            'label' => 'nullable|string|max:255',
            'icon' => 'nullable|image',
        ]);

        // Handle image upload if needed
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('homepage-stats', 'public');
            $validated['icon'] = '/storage/' . $path;
        }

        HomepageStat::create($validated);

        return redirect()->route('homepage-stats.index')
            ->with('success', 'Section updated successfully.');
    }

    public function edit(Request $request, $id)
    {
        $stat = HomepageStat::findOrFail($id);
        return view('dashboard.homepage_stats.edit', compact('stat'));
    }

    public function update(Request $request, $id)
    {
        $stat = HomepageStat::findOrFail($id);

        $validated = $request->validate([
            'number' => 'nullable|string|max:255',
            'label' => 'nullable|string|max:255',
            'icon' => 'nullable|image',
        ]);

        // Handle image upload if needed
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('homepage-stats', 'public');
            $validated['icon'] = '/storage/' . $path;
        }

        $stat->update($validated);
        return redirect()->route('homepage-stats.index')
            ->with('success', 'Section updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $stat = HomepageStat::findOrFail($id);
        $stat->delete();

        return redirect()->route('homepage-stats.index')
            ->with('success', 'Stat deleted successfully.');
    }
}
