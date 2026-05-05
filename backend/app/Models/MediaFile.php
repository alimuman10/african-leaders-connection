<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    protected $fillable = [
        'user_id', 'mediable_type', 'mediable_id', 'disk', 'path',
        'original_name', 'mime_type', 'size', 'collection',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mediable()
    {
        return $this->morphTo();
    }
}
