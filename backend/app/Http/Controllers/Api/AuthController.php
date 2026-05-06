<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\CloudflareTurnstile;
use App\Services\SecurityAuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, CloudflareTurnstile $turnstile, SecurityAuditLogger $audit)
    {
        abort_unless($turnstile->verify($request->cf_turnstile_response, $request->ip()), 422, 'Human verification failed.');

        $user = User::create([
            ...$request->safe()->only([
                'name',
                'email',
                'phone',
                'country',
                'profession',
                'organization',
                'leadership_interest',
                'password',
            ]),
            'status' => 'pending verification',
        ]);
        Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);
        $user->assignRole('Member');
        $user->profile()->create($request->safe()->only([
            'phone',
            'country',
            'profession',
            'organization',
            'leadership_interest',
        ]));
        $user->sendEmailVerificationNotification();
        $audit->event('auth.registered', $user, 'info', $user, ['role' => 'Member'], $request);

        return response()->json([
            'user' => new UserResource($user->load('profile')),
            'token' => $user->createToken('member-api')->plainTextToken,
        ], 201);
    }

    public function login(LoginRequest $request, CloudflareTurnstile $turnstile, SecurityAuditLogger $audit)
    {
        abort_unless($turnstile->verify($request->cf_turnstile_response, $request->ip()), 422, 'Human verification failed.');

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            if ($user) {
                $this->recordFailedAttempt($user);
            }

            $audit->failedLogin((string) $request->email, $request, $user);

            throw ValidationException::withMessages(['email' => 'The provided credentials are incorrect.']);
        }

        if ($user->locked_until && $user->locked_until->isFuture()) {
            $audit->failedLogin($user->email, $request, $user, 'account_locked');
            throw ValidationException::withMessages(['email' => 'This account is temporarily locked. Please try again later or reset your password.']);
        }

        if (in_array($user->status, ['suspended', 'banned', 'deactivated'], true)) {
            $audit->failedLogin($user->email, $request, $user, 'inactive_account');
            throw ValidationException::withMessages(['email' => 'This account is not active.']);
        }

        if (config('auth_security.require_verified_email') && ! $user->hasVerifiedEmail() && ! $user->hasRole('Super Admin')) {
            $audit->failedLogin($user->email, $request, $user, 'email_unverified');
            throw ValidationException::withMessages(['email' => 'Please verify your email address before signing in.']);
        }

        Auth::login($user, (bool) $request->boolean('remember'));
        $user->forceFill([
            'failed_login_attempts' => 0,
            'last_login_at' => now(),
            'locked_until' => null,
        ])->save();
        $audit->login($user, $request);

        return response()->json([
            'user' => new UserResource($user->load('profile')),
            'token' => $user->createToken($request->device_name ?: 'api-token')->plainTextToken,
        ]);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user()->load('profile'));
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : throw ValidationException::withMessages(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(10)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        ]);

        $status = Password::reset($validated, function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ])->save();
            $user->tokens()->delete();
            app(SecurityAuditLogger::class)->event('auth.password_reset', $user, 'warning', $user, ['tokens_revoked' => true]);
        });

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : throw ValidationException::withMessages(['email' => __($status)]);
    }

    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent.']);
    }

    public function logout(Request $request, SecurityAuditLogger $audit)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()?->delete();
        Auth::guard('web')->logout();
        $audit->event('auth.logout', $user, 'info', $user, [], $request);

        return response()->noContent();
    }

    private function recordFailedAttempt(User $user): void
    {
        $attempts = $user->failed_login_attempts + 1;
        $maxAttempts = config('auth_security.max_failed_login_attempts');

        $user->forceFill([
            'failed_login_attempts' => $attempts,
            'locked_until' => $attempts >= $maxAttempts ? now()->addMinutes(config('auth_security.lockout_minutes')) : $user->locked_until,
        ])->save();
    }
}
