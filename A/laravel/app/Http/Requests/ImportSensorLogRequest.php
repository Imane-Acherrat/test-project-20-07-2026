<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportSensorLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 10 MB = 10240 KB, as required by the CSV import validation rule.
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please choose a CSV file to upload.',
            'file.mimes' => 'Only CSV files are accepted.',
            'file.max' => 'The uploaded file may not be larger than 10 MB.',
        ];
    }
}
