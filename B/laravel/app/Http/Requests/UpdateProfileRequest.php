<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'username' => [
                'sometimes', 'string', 'max:50',
                'regex:/^\S+$/',
                Rule::unique('users', 'username')->ignore($this->user()->id),
            ],
            'bio' => ['sometimes', 'nullable', 'string', 'max:500'],
            'profileImage' => [
                'sometimes', 'nullable', 'image',
                'mimes:jpeg,png,webp',
                'max:'.config('auth_token.max_image_size_kb'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'The username must not contain spaces.',
        ];
    }
}
