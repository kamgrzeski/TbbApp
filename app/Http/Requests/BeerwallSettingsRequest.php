<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeerwallSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        return collect($days)
            ->mapWithKeys(fn (string $day) => ["{$day}_close_time" => ['nullable', 'date_format:H:i']])
            ->all();
    }
}
