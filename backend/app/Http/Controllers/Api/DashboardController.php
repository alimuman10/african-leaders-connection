<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Service;
use App\Models\Story;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'metrics' => [
                'users' => User::count(),
                'contact_messages' => ContactMessage::count(),
                'stories' => Story::count(),
                'projects' => Project::count(),
                'services' => Service::count(),
            ],
            'recent_activities' => ActivityLog::latest()->limit(12)->get(),
            'quick_actions' => [
                ['label' => 'Create Story', 'href' => '/admin/stories/create'],
                ['label' => 'Add Project', 'href' => '/admin/projects/create'],
                ['label' => 'Manage Services', 'href' => '/admin/services'],
                ['label' => 'Review Messages', 'href' => '/admin/contact'],
            ],
        ]);
    }
}
