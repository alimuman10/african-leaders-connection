<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'summary', 'description', 'status', 'image_path',
        'impact_metrics', 'location', 'region', 'country', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'impact_metrics' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];
}
