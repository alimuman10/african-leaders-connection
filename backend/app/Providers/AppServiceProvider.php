<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::define('use moderator tools', fn (User $user) => $user->hasAnyRole(['Moderator', 'Super Admin']));
        Gate::define('access super admin dashboard', fn (User $user) => $user->hasRole('Super Admin'));
        Gate::define('protect super admin', fn (User $user, User $target) => $user->hasRole('Super Admin') && ! $target->hasRole('Super Admin'));
    }
}
