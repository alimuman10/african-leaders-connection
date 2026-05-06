<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModerationAction extends Model
{
    protected $fillable = ['moderator_id', 'report_id', 'actionable_type', 'actionable_id', 'action', 'notes'];
}
