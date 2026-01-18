<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\Request;

class HomepageSectionController extends Controller
{
    public function index(Request $request)
    {
        $sections = HomepageSection::when($request->has('section'), function ($query) use ($request) {
            $query->where('section_key', $request->input('section'));
        })->get();

        return view('dashboard.homepage_sections.index', compact('sections'));
    }

    public function edit(Request $request, $id)
    {
        $section = HomepageSection::where('section_key', $id)->firstOrFail();
        return view('dashboard.homepage_sections.edit', compact('section'));
    }

    public function update(Request $request, $id)
    {
        $section = HomepageSection::findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'icon' => 'nullable|image',
        ]);

        // Handle image upload if needed
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('homepage-sections', 'public');
            $validated['icon'] = '/storage/' . $path;
        }

        $section->update($validated);

        return redirect()->back()
            ->with('success', 'Section updated successfully.');
    }
}
