<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email', $this->reservedSuperAdminEmailRule()],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
            'country' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'profession' => ['nullable', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:160'],
            'leadership_interest' => ['nullable', 'string', 'max:180'],
            'cf_turnstile_response' => ['nullable', 'string', 'max:2048'],
        ];
    }

    private function reservedSuperAdminEmailRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (strtolower((string) $value) === strtolower((string) config('auth_security.super_admin_email'))) {
                $fail('This email address is reserved for platform bootstrap.');
            }
        };
    }
}
