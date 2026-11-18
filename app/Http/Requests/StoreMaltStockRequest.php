<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaltStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'kg' => ['required', 'numeric', 'min:0'],
            'extract' => ['nullable', 'numeric'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
        ];
    }
}
