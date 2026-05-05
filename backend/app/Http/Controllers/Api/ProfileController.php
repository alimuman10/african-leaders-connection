<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        return new UserResource(request()->user()->load('profile'));
    }

    public function update(ProfileRequest $request)
    {
        $user = $request->user();
        $user->update($request->safe()->only([
            'name',
            'phone',
            'country',
            'profession',
            'organization',
            'leadership_interest',
        ]));
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->safe()->except(['name'])
        );

        return new UserResource($user->fresh()->load('profile'));
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        ]);

        $request->user()->update(['password' => Hash::make($validated['password'])]);
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Password updated. Please log in again.']);
    }

    public function deactivate(Request $request)
    {
        $user = $request->user();
        $user->update(['status' => 'deactivated']);
        $user->tokens()->delete();
        Auth::guard('web')->logout();
        $request->session()?->invalidate();
        $request->session()?->regenerateToken();

        return response()->json(['message' => 'Account deactivated.']);
    }
}
