<?php

namespace App\Policies;

use App\Models\User;

class ModerationPolicy
{
    public function useTools(User $user): bool
    {
        return $user->hasAnyRole(['Moderator', 'Super Admin']);
    }

    public function override(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }
}
