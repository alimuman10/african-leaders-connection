<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    protected $fillable = ['key', 'title', 'subtitle', 'content', 'active', 'sort_order'];

    protected $casts = ['content' => 'array', 'active' => 'boolean'];
}
