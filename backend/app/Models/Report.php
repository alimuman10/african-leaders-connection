<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = ['reporter_id', 'assigned_to', 'reportable_type', 'reportable_id', 'reason', 'details', 'status'];
}
