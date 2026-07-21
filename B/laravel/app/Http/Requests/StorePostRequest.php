<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:2200'],
            'image' => [
                'required', 'image',
                'mimes:jpeg,png,webp',
                'max:'.config('auth_token.max_image_size_kb'),
            ],
            'hashtags' => ['sometimes', 'array', 'max:30'],
            'hashtags.*' => ['string', 'max:50'],
        ];
    }
}
