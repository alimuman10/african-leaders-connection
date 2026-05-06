<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityProject extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'title', 'country', 'category', 'summary', 'description', 'status', 'submitted_at'];

    protected $casts = ['submitted_at' => 'datetime'];
}
