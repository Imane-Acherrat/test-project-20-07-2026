<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['sometimes', 'string', 'max:2200'],
            'image' => [
                'sometimes', 'image',
                'mimes:jpeg,png,webp',
                'max:'.config('auth_token.max_image_size_kb'),
            ],
            'hashtags' => ['sometimes', 'array', 'max:30'],
            'hashtags.*' => ['string', 'max:50'],
        ];
    }
}
