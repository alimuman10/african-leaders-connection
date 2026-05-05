<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadershipResource;

class ResourceLibraryController extends Controller
{
    public function index()
    {
        return LeadershipResource::where('active', true)->latest()->paginate(12);
    }
}
