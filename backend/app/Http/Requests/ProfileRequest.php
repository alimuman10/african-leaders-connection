<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'profession' => ['nullable', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:160'],
            'leadership_interest' => ['nullable', 'string', 'max:180'],
            'leadership_category' => ['nullable', 'string', 'max:160'],
            'professional_title' => ['nullable', 'string', 'max:160'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'skills' => ['nullable', 'array'],
            'interests' => ['nullable', 'array'],
            'social_links' => ['nullable', 'array'],
            'portfolio_link' => ['nullable', 'url', 'max:255'],
            'causes_supported' => ['nullable', 'array'],
        ];
    }
}
