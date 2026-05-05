<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
            'cf_turnstile_response' => ['nullable', 'string', 'max:2048'],
            'device_name' => ['nullable', 'string', 'max:80'],
        ];
    }
}
