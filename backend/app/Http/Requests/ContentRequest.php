<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200'],
            'summary' => ['nullable', 'string', 'max:600'],
            'excerpt' => ['nullable', 'string', 'max:600'],
            'body' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:160'],
            'type' => ['nullable', 'string', 'max:80'],
            'status' => ['nullable', Rule::in(['draft', 'review', 'published', 'planned', 'active', 'completed', 'paused'])],
            'featured' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
            'country' => ['nullable', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:180'],
            'impact_metrics' => ['nullable', 'array'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
