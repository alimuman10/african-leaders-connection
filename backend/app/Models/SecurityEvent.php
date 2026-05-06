<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    protected $fillable = ['user_id', 'event', 'severity', 'subject_type', 'subject_id', 'metadata', 'ip_address', 'user_agent'];

    protected $casts = ['metadata' => 'array'];
}
