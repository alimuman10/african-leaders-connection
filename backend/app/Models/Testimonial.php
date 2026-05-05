<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'role', 'organization', 'quote', 'featured', 'active'];

    protected $casts = ['featured' => 'boolean', 'active' => 'boolean'];
}
