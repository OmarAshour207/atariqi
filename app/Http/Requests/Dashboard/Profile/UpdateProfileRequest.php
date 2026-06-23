<?php

namespace App\Http\Requests\Dashboard\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->guard('admin')->check();
    }

    public function rules()
    {
        return [
            'old_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'same:confirm_new_password'],
            'confirm_new_password' => ['required', 'string', 'min:8'],
        ];
    }

    public function attributes()
    {
        return [
            'old_password' => __('Old Password'),
            'new_password' => __('New Password'),
            'confirm_new_password' => __('Confirm New Password'),
        ];
    }
}
