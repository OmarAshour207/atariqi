<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PartnerAchievement;
use Illuminate\Http\Request;

class PartnerAchievementController extends Controller
{
    public function index(Request $request)
    {
        $partnerAchievements = PartnerAchievement::when($request->input('type'), function ($query, $type) {
            return $query->where('type', $type);
        })->paginate(20);

        return view('dashboard.partner_achievements.index', compact('partnerAchievements'));
    }

        public function create()
    {
        return view('dashboard.partner_achievements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|image',
            'type' => 'nullable|string|max:255'
        ]);

        // Handle image upload if needed
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('partners', 'public');
            $validated['icon'] = '/storage/' . $path;
        }

        PartnerAchievement::create($validated);

        return redirect()->route('partner-achievements.index', ['type' => $validated['type'] ?? null])
            ->with('success', 'Section updated successfully.');
    }

    public function edit(Request $request, $id)
    {
        $partnerAchievement = PartnerAchievement::findOrFail($id);
        return view('dashboard.partner_achievements.edit', compact('partnerAchievement'));
    }

    public function update(Request $request, $id)
    {
        $partnerAchievement = PartnerAchievement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|image',
            'type' => 'nullable|string|max:255'
        ]);

        // Handle image upload if needed
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('partners', 'public');
            $validated['icon'] = '/storage/' . $path;
        }

        $partnerAchievement->update($validated);
        return redirect()->route('partner-achievements.index', ['type' => $validated['type'] ?? null])
            ->with('success', 'Stat updated successfully.');
    }

    public function destroy($id)
    {
        $stat = PartnerAchievement::findOrFail($id);
        $stat->delete();

        return redirect()->route('partner-achievements.index')
            ->with('success', 'Stat deleted successfully.');
    }
}
