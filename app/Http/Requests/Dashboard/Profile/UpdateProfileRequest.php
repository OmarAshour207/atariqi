<?php

namespace App\Http\Requests\Dashboard\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $this->user()->id . ',id',
            'phone' => 'nullable|unique:users,phone,' . $this->user()->id . ',id',
            'password' => 'nullable|string|min:8|confirmed',

            'image' => 'nullable|file|mimes:pdf,jpg,png,jpeg,webp',
        ];
    }
}
