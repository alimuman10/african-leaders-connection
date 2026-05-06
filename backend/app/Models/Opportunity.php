<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'category', 'country', 'eligibility', 'summary', 'external_url', 'deadline_at', 'status', 'featured'];

    protected $casts = ['deadline_at' => 'datetime', 'featured' => 'boolean'];
}
