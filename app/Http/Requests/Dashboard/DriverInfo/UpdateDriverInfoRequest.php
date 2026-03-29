<?php

namespace App\Http\Requests\Dashboard\DriverInfo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverInfoRequest extends FormRequest
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
            'approval' => 'required|boolean',

            // // User fields
            // 'user-first-name' => 'required|string|max:255',
            // 'user-last-name' => 'required|string|max:255',
            // 'email' => 'required|email|unique:users,email,' . $this->route('edit_info_request'),
            // 'phone-no' => 'required|string|max:20',
            // 'gender' => 'required|in:male,female',
            // 'image' => 'nullable|image|max:2048',
            // 'university-id' => 'nullable|exists:universities,id',
            // 'stage-id' => 'nullable|exists:stages,id',

            // // Driver Info fields
            // 'car-brand' => 'required|string|max:255',
            // 'car-model' => 'required|string|max:255',
            // 'car-number' => 'required|integer',
            // 'car-letters' => 'required|string|max:10',
            // 'car-color' => 'required|string|max:255',
            // 'driver-license-link' => 'nullable|url',
            // 'allow-disabilities' => 'required|boolean',

            // // Cars
            // 'driver-type-id' => 'required|exists:driver_type,id',
            // 'car_form_img' => 'nullable|image|max:2048',
            // 'licnese_img'   => 'nullable|image|max:2048',
            // 'car_front_img' => 'nullable|image|max:2048',
            // 'car_back_img'  => 'nullable|image|max:2048',
            // 'car_rside_img' => 'nullable|image|max:2048',
            // 'car_lside_img' => 'nullable|image|max:2048',
            // 'car_insideFront_img' => 'nullable|image|max:2048',
            // 'car_insideBack_img' => 'nullable|image|max:2048',
        ];
    }
}
