<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'profile_picture' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'preferred_location' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
