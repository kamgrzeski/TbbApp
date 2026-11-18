<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class BeerReportUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'csv_file' => [
                'required',
                File::types(['csv', 'txt'])->max(5 * 1024),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'csv_file.required' => 'Wybierz plik CSV z raportem.',
            'csv_file.max' => 'Plik może mieć maksymalnie 5 MB.',
        ];
    }
}
