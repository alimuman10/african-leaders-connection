<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\SecurityEvent;
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

        if (blank($password)) {
            throw ValidationException::withMessages([
                'SUPER_ADMIN_PASSWORD' => 'Set SUPER_ADMIN_PASSWORD in .env before bootstrapping the Super Admin.',
            ]);
        }

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $user = User::withTrashed()->where('email', $email)->first();
        $created = false;

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
            if (User::role('Super Admin')->exists()) {
                return;
            }

            $user = User::create([
                'name' => config('auth_security.super_admin_name'),
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'status' => 'active',
            ]);
            $created = true;
        }

        $user->syncRoles([$role->name]);

        if (Schema::hasTable('user_profiles')) {
            $user->profile()->firstOrCreate(['user_id' => $user->id]);
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $created ? 'super_admin.bootstrapped' : 'super_admin.bootstrap_verified',
            'subject_type' => $user->getMorphClass(),
            'subject_id' => $user->id,
            'properties' => ['source' => 'artisan auth:bootstrap-super-admin'],
            'ip_address' => 'console',
        ]);

        if (Schema::hasTable('security_events')) {
            SecurityEvent::create([
                'user_id' => $user->id,
                'event' => $created ? 'super_admin.bootstrapped' : 'super_admin.bootstrap_verified',
                'severity' => 'warning',
                'subject_type' => $user->getMorphClass(),
                'subject_id' => $user->id,
                'metadata' => ['source' => 'artisan auth:bootstrap-super-admin'],
                'ip_address' => 'console',
                'user_agent' => 'artisan',
            ]);
        }
    }
}
