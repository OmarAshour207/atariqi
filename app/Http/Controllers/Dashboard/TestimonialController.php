<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $testimonials = Testimonial::paginate(20);

        return view('dashboard.testimonials.index', compact('testimonials'));
    }

    public function create(Request $request)
    {
        return view('dashboard.testimonials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable',
        ]);

        // Handle image upload if needed
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('testimonails', 'public');
            $validated['icon'] = '/storage/' . $path;
        }

        Testimonial::create($validated);

        return redirect()->route('testimonials.index')
            ->with('success', 'Section updated successfully.');
    }

    public function edit(Request $request, $id)
    {
        $testimonial = Testimonial::firstOrFail();
        return view('dashboard.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, $id)
    {
        $section = Testimonial::findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'icon' => 'nullable',
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

    public function destroy(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        return redirect()->route('testimonials.index')
            ->with('success', 'Testimonial deleted successfully.');
    }
}
