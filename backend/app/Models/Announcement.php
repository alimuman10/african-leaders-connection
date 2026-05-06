<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = ['created_by', 'title', 'body', 'audience', 'country', 'category', 'scheduled_at', 'archived_at'];

    protected $casts = ['scheduled_at' => 'datetime', 'archived_at' => 'datetime'];
}
