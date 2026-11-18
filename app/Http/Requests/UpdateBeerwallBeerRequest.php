<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBeerwallBeerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'beer_name' => ['required', 'string', 'max:255'],
            'beer_style' => ['nullable', 'string', 'max:255'],
            'beer_description' => ['nullable', 'string'],
            'beer_blg' => ['nullable', 'string'],
            'beer_alc' => ['nullable', 'string'],
            'beer_price_small' => ['required', 'numeric'],
            'beer_price_medium' => ['required', 'numeric'],
            'beer_price_large' => ['required', 'numeric'],
            'position' => ['required', 'numeric'],
        ];
    }
}
