<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Profile\UpdateProfileRequest;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('dashboard.profile.edit');
    }

    public function update(UpdateProfileRequest $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!Hash::check($request->old_password, $admin->password)) {
            return redirect()->back()->with('error', __('Old password is incorrect!'));
        }

        $admin->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', __('Password updated successfully!'));
    }
}
