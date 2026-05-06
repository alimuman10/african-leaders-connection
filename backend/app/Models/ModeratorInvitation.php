<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModeratorInvitation extends Model
{
    protected $fillable = ['user_id', 'invited_by', 'email', 'token', 'status', 'accepted_at', 'revoked_at', 'expires_at'];

    protected $casts = ['accepted_at' => 'datetime', 'revoked_at' => 'datetime', 'expires_at' => 'datetime'];
}
