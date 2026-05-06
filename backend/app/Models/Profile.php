<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'user_profiles';

    protected $fillable = [
        'user_id',
        'phone',
        'country',
        'city',
        'profession',
        'organization',
        'leadership_interest',
        'leadership_category',
        'professional_title',
        'profile_photo_path',
        'bio',
        'skills',
        'interests',
        'social_links',
        'portfolio_link',
        'causes_supported',
        'completion_percentage',
    ];

    protected $casts = [
        'skills' => 'array',
        'interests' => 'array',
        'social_links' => 'array',
        'causes_supported' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
