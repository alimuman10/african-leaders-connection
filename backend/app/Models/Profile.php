<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'country',
        'profession',
        'organization',
        'leadership_interest',
        'profile_photo_path',
        'bio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
