<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'summary', 'description', 'location', 'is_online', 'speakers', 'resources', 'status', 'starts_at', 'ends_at'];

    protected $casts = ['is_online' => 'boolean', 'speakers' => 'array', 'resources' => 'array', 'starts_at' => 'datetime', 'ends_at' => 'datetime'];
}
