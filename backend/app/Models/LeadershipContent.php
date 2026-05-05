<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadershipContent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'author_id', 'type', 'title', 'slug', 'summary', 'content',
        'image_path', 'status', 'published_at',
    ];

    protected $casts = ['published_at' => 'datetime'];
}
