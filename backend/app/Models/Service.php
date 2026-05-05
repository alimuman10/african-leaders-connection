<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'category', 'summary', 'description',
        'image_path', 'sort_order', 'active',
    ];

    protected $casts = ['active' => 'boolean'];
}
