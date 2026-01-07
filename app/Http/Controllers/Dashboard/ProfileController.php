<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Profile\UpdateProfileRequest;
use App\Http\Requests\Dashboard\ChangePasswordRequest;
use App\Traits\FileTrait;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use FileTrait;

    public function edit()
    {
        return view('dashboard.profile.edit');
    }

    public function update(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();

        if($request->hasFile('image')) {
            if ($user->image) {
                $this->removeImage($user->image);
            }
            $data['image'] = $this->uploadImage($request->file('image'), 'uploads/user/images/');
        }

        $user->update($data);
        session()->flash('success', __('Data updated successfully!'));
        return redirect()->back();
    }

    public function showChangePasswordForm()
    {
        return view('dashboard.profile.change_password');
    }

}
