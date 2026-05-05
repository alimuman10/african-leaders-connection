<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message', 'status',
        'ip_address', 'user_agent', 'replied_at', 'archived_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'archived_at' => 'datetime',
    ];
}
