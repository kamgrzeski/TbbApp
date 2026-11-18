<?php

namespace App\Http\Requests;

use App\Services\KegStockService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IssueKegRequest extends FormRequest
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
                Rule::exists('recipes', 'id'),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'tap_number' => ['required', 'integer', 'between:1,' . KegStockService::TAPS_COUNT],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
