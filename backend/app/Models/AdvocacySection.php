<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvocacySection extends Model
{
    protected $fillable = [
        'title', 'slug', 'summary', 'content', 'image_path', 'sort_order', 'active',
    ];

    protected $casts = ['active' => 'boolean'];
}
