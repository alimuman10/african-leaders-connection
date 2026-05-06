<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'country' => $validated['country'] ?? null,
            'organization' => $validated['organization'] ?? null,
            'password' => $validated['password'],
            'status' => 'pending',
        ]);

        Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);
        $user->assignRole('Member');

        Profile::create([
            'user_id' => $user->id,
            'phone' => $validated['phone'] ?? null,
            'country' => $validated['country'] ?? null,
            'organization' => $validated['organization'] ?? null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Welcome to African Leaders Connection. Your account is ready.');
    }
}
