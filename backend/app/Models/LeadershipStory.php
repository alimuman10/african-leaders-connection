<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadershipStory extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'title', 'country', 'summary', 'body', 'status', 'review_notes', 'submitted_at'];

    protected $casts = ['submitted_at' => 'datetime'];
}
