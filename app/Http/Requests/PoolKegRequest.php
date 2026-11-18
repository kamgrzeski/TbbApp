<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PoolKegRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipe_id' => [
                'required',
                Rule::exists('recipes', 'id')
                    ->whereNotNull('finished_at')
                    ->where('is_archived', false),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
