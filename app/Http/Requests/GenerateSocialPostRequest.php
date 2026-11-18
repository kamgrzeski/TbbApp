<?php

namespace App\Http\Requests;

use App\Models\SocialPost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateSocialPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'topic_type' => ['required', Rule::in(array_keys(SocialPost::TOPICS))],
            'recipe_id' => ['nullable', 'integer', 'exists:recipes,id'],
            'custom_topic' => ['nullable', 'string', 'max:255', 'required_if:topic_type,' . SocialPost::TOPIC_CUSTOM],
            'custom_guidelines' => ['nullable', 'string', 'max:1000'],
            'platform' => ['required', Rule::in(array_keys(SocialPost::PLATFORMS))],
            'event_at' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
