<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = ['name', 'country', 'sector', 'website', 'logo_path', 'active'];

    protected $casts = ['active' => 'boolean'];
}
