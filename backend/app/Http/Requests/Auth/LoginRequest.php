<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Services\SecurityAuditLogger;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        $user = User::where('email', $this->string('email'))->first();
        $audit = app(SecurityAuditLogger::class);

        if ($user?->locked_until && $user->locked_until->isFuture()) {
            RateLimiter::hit($this->throttleKey());
            $audit->failedLogin((string) $this->email, $this, $user, 'account_locked');

            throw ValidationException::withMessages([
                'email' => __('This account is temporarily locked. Please try again later or reset your password.'),
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            if ($user) {
                $this->recordFailedAttempt($user);
            }
            $audit->failedLogin((string) $this->email, $this, $user);

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        /** @var \App\Models\User $authenticated */
        $authenticated = Auth::user();

        if (in_array($authenticated->status, ['suspended', 'banned', 'deactivated'], true)) {
            Auth::guard('web')->logout();
            RateLimiter::hit($this->throttleKey());
            $audit->failedLogin($authenticated->email, $this, $authenticated, 'inactive_account');

            throw ValidationException::withMessages([
                'email' => __('This account is not active.'),
            ]);
        }

        if (config('auth_security.require_verified_email') && ! $authenticated->hasVerifiedEmail() && ! $authenticated->hasRole('Super Admin')) {
            Auth::guard('web')->logout();
            $audit->failedLogin($authenticated->email, $this, $authenticated, 'email_unverified');

            throw ValidationException::withMessages([
                'email' => __('Please verify your email address before signing in.'),
            ]);
        }

        $authenticated->forceFill([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ])->save();

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('Too many login attempts. Please try again in :seconds seconds.', [
                'seconds' => $seconds,
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    private function recordFailedAttempt(User $user): void
    {
        $attempts = $user->failed_login_attempts + 1;

        $user->forceFill([
            'failed_login_attempts' => $attempts,
            'locked_until' => $attempts >= config('auth_security.max_failed_login_attempts') ? now()->addMinutes(config('auth_security.lockout_minutes')) : $user->locked_until,
        ])->save();
    }
}
