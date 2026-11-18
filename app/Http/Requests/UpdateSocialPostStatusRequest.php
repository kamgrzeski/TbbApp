<?php

namespace App\Http\Requests;

use App\Models\SocialPost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSocialPostStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_keys(SocialPost::STATUSES))],
        ];
    }
}
