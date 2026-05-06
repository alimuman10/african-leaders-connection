<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\CloudflareTurnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, CloudflareTurnstile $turnstile)
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
            'status' => 'pending',
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

        return response()->json([
            'user' => new UserResource($user->load('profile')),
            'token' => $user->createToken('member-api')->plainTextToken,
        ], 201);
    }

    public function login(LoginRequest $request, CloudflareTurnstile $turnstile)
    {
        abort_unless($turnstile->verify($request->cf_turnstile_response, $request->ip()), 422, 'Human verification failed.');

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => 'The provided credentials are incorrect.']);
        }

        if (in_array($user->status, ['suspended', 'banned', 'deactivated'], true)) {
            throw ValidationException::withMessages(['email' => 'This account is not active.']);
        }

        Auth::login($user, (bool) $request->boolean('remember'));
        $user->forceFill(['last_login_at' => now()])->save();

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
            $user->forceFill(['password' => Hash::make($password)])->save();
            $user->tokens()->delete();
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

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        Auth::guard('web')->logout();

        return response()->noContent();
    }
}
