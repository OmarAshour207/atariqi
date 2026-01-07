<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('dashboard.settings.edit');
    }

    public function store(Request $request)
    {
        setting($request->except('_token'))->save();
        session()->flash('success', __('Saved successfully'));
        return redirect()->back();
    }

    public function changeLocale($locale)
    {
        app()->setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }
}
