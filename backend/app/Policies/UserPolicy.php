<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function updateStatus(User $user, User $target): bool
    {
        return $user->hasRole('Super Admin') && ! $target->hasRole('Super Admin');
    }

    public function delete(User $user, User $target): bool
    {
        return $user->hasRole('Super Admin') && ! $target->hasRole('Super Admin');
    }
}
