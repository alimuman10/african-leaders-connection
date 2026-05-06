<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $password = Password::min(10)->letters()->mixedCase()->numbers()->symbols();

        if (! app()->environment('testing')) {
            $password->uncompromised();
        }

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email', $this->reservedSuperAdminEmailRule()],
            'phone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:160'],
            'password' => ['required', 'confirmed', $password],
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
