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

        if($request->has('old_password') && $request->has('new_password') && $request->has('confirm_new_password')) {
            if(Hash::check($request->old_password, $admin->password)) {
                $admin->update(['password' => Hash::make($request->new_password)]);
                session()->flash('success', __('Password updated successfully!'));
                return redirect()->back();
            } else {
                session()->flash('error', __('Old password is incorrect!'));
                return redirect()->back();
            }
        }
    }

}
