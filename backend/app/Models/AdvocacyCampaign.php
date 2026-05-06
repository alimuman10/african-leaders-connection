<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdvocacyCampaign extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'title', 'country', 'summary', 'description', 'impact_metrics', 'status', 'featured'];

    protected $casts = ['impact_metrics' => 'array', 'featured' => 'boolean'];
}
