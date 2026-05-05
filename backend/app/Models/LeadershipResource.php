<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadershipResource extends Model
{
    protected $fillable = ['title', 'type', 'summary', 'file_path', 'external_url', 'member_only', 'active'];

    protected $casts = ['member_only' => 'boolean', 'active' => 'boolean'];
}
