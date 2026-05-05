<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $users = User::query()
            ->with('profile')
            ->when($request->search, fn ($query, $search) => $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($request->role, fn ($query, $role) => $query->role($role))
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->country, fn ($query, $country) => $query->where('country', $country))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return new UserResource($user->load('profile'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['nullable', Password::min(10)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
            'roles' => ['nullable', 'array'],
            'phone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:120'],
            'profession' => ['nullable', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:160'],
            'leadership_interest' => ['nullable', 'string', 'max:180'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'country' => $validated['country'] ?? null,
            'profession' => $validated['profession'] ?? null,
            'organization' => $validated['organization'] ?? null,
            'leadership_interest' => $validated['leadership_interest'] ?? null,
            'password' => Hash::make($validated['password'] ?? str()->password(16)),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $user->assignRole($validated['roles'] ?? ['Member']);
        $user->profile()->create(collect($validated)->only([
            'phone',
            'country',
            'profession',
            'organization',
            'leadership_interest',
        ])->all());
        $this->logActivity('user.created', $user);

        return new UserResource($user->load('profile'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:120'],
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user)],
            'status' => ['sometimes', Rule::in(['active', 'pending', 'suspended', 'deactivated'])],
            'roles' => ['sometimes', 'array'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:40'],
            'country' => ['sometimes', 'nullable', 'string', 'max:120'],
            'profession' => ['sometimes', 'nullable', 'string', 'max:120'],
            'organization' => ['sometimes', 'nullable', 'string', 'max:160'],
            'leadership_interest' => ['sometimes', 'nullable', 'string', 'max:180'],
        ]);

        $user->update(collect($validated)->except('roles')->all());
        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }
        $this->logActivity('user.updated', $user);

        return new UserResource($user->fresh()->load('profile'));
    }

    public function suspend(User $user)
    {
        $this->guardSuperAdminMutation($user);
        $user->update(['status' => 'suspended']);
        $user->tokens()->delete();
        $this->logActivity('user.suspended', $user);

        return new UserResource($user->fresh()->load('profile'));
    }

    public function reactivate(User $user)
    {
        $user->update(['status' => 'active']);
        $this->logActivity('user.reactivated', $user);

        return new UserResource($user->fresh()->load('profile'));
    }

    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate(['role' => ['required', 'string', 'exists:roles,name']]);
        $user->assignRole($validated['role']);
        $this->logActivity('user.role_assigned', $user, ['role' => $validated['role']]);

        return new UserResource($user->fresh()->load('profile'));
    }

    public function removeRole(Request $request, User $user)
    {
        $validated = $request->validate(['role' => ['required', 'string', 'exists:roles,name']]);
        if ($user->hasRole('Super Admin') && $validated['role'] === 'Super Admin') {
            throw ValidationException::withMessages(['role' => 'Super Admin role cannot be removed through the API.']);
        }

        $user->removeRole($validated['role']);
        $this->logActivity('user.role_removed', $user, ['role' => $validated['role']]);

        return new UserResource($user->fresh()->load('profile'));
    }

    public function destroy(User $user)
    {
        $this->guardSuperAdminMutation($user);
        $this->logActivity('user.deleted', $user, ['email' => $user->email]);
        $user->delete();

        return response()->noContent();
    }

    private function guardSuperAdminMutation(User $user): void
    {
        abort_if($user->hasRole('Super Admin'), 403, 'Super Admin accounts cannot be modified through this action.');
    }
}
