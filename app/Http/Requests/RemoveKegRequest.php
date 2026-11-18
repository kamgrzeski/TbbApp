<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemoveKegRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipe_id' => ['required', 'exists:recipes,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
