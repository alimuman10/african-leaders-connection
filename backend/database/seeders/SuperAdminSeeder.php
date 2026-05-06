<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('auth_security.super_admin_email');
        $password = config('auth_security.super_admin_password');

        if (User::role('Super Admin')->exists()) {
            return;
        }

        if (blank($password)) {
            throw ValidationException::withMessages([
                'SUPER_ADMIN_PASSWORD' => 'Set SUPER_ADMIN_PASSWORD in .env before bootstrapping the Super Admin.',
            ]);
        }

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $user = User::withTrashed()->where('email', $email)->first();

        if ($user) {
            $user->restore();
            $user->forceFill([
                'name' => $user->name ?: config('auth_security.super_admin_name'),
                'password' => Hash::make($password),
                'email_verified_at' => $user->email_verified_at ?: now(),
                'status' => 'active',
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ])->save();
        } else {
            $user = User::create([
                'name' => config('auth_security.super_admin_name'),
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'status' => 'active',
            ]);
        }

        $user->syncRoles([$role->name]);

        if (Schema::hasTable('user_profiles')) {
            $user->profile()->firstOrCreate(['user_id' => $user->id]);
        }
    }
}
