<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ContactSettingsService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(private ContactSettingsService $contactSettings)
    {
    }

    public function index()
    {
        return view('dashboard.settings.edit', [
            'settings' => $this->contactSettings->mergedSettings(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'phonenumber' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'linkedin' => 'nullable|string|max:500',
            'instagram' => 'nullable|string|max:500',
            'twitter' => 'nullable|string|max:500',
            'google_play' => 'nullable|string|max:500',
            'app_store' => 'nullable|string|max:500',
        ]);

        setting($data)->save();
        $this->contactSettings->sync($data);

        session()->flash('success', __('Saved successfully'));

        return redirect()->back();
    }

    public function changeLocale($locale)
    {
        $allowed = ['en', 'ar'];

        if (in_array($locale, $allowed, true)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        return redirect()->back();
    }
}
