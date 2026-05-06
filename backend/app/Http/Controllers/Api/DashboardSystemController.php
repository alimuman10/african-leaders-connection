<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdvocacyCampaign;
use App\Models\Announcement;
use App\Models\Comment;
use App\Models\CommunityProject;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\HomepageSection;
use App\Models\LeadershipStory;
use App\Models\ModerationAction;
use App\Models\ModeratorInvitation;
use App\Models\Opportunity;
use App\Models\PlatformSetting;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardSystemController extends Controller
{
    private array $resources = [
        'posts' => Post::class,
        'events' => Event::class,
        'opportunities' => Opportunity::class,
        'campaigns' => AdvocacyCampaign::class,
        'announcements' => Announcement::class,
        'settings' => PlatformSetting::class,
        'homepage' => HomepageSection::class,
    ];

    public function memberDashboard(Request $request)
    {
        $user = $request->user()->load('profile');

        return response()->json([
            'welcome' => "Welcome, {$user->name}",
            'profile_completion' => $user->profile?->completion_percentage ?? $this->profileCompletion($user),
            'leadership_impact_score' => 72,
            'recent_activities' => ActivityLog::where('user_id', $user->id)->latest()->limit(6)->get(),
            'upcoming_events' => Event::where('status', 'scheduled')->orderBy('starts_at')->limit(5)->get(),
            'suggested_opportunities' => Opportunity::where('status', 'active')->latest()->limit(5)->get(),
            'latest_announcements' => Announcement::latest()->limit(5)->get(),
            'quick_actions' => ['Complete profile', 'Submit story', 'Find opportunities', 'Join campaign'],
            'moderator' => [
                'enabled' => $user->hasRole('Moderator'),
                'status' => $user->hasRole('Moderator') ? 'accepted' : null,
            ],
        ]);
    }

    public function memberProfile(Request $request)
    {
        return response()->json($request->user()->load('profile'));
    }

    public function updateMemberProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:160'],
            'professional_title' => ['nullable', 'string', 'max:160'],
            'leadership_category' => ['nullable', 'string', 'max:160'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'skills' => ['nullable', 'array'],
            'interests' => ['nullable', 'array'],
            'social_links' => ['nullable', 'array'],
            'portfolio_link' => ['nullable', 'url', 'max:255'],
            'causes_supported' => ['nullable', 'array'],
        ]);

        $user = $request->user();
        $user->update(collect($validated)->only(['name', 'phone', 'country', 'organization'])->all());
        $profile = $user->profile()->updateOrCreate(['user_id' => $user->id], [
            ...collect($validated)->except(['name'])->all(),
            'completion_percentage' => $this->profileCompletion($user->fresh()->load('profile'), $validated),
        ]);

        return response()->json($user->fresh()->load('profile'));
    }

    public function memberNotifications(Request $request)
    {
        return response()->json($request->user()->notifications()->latest()->limit(30)->get());
    }

    public function memberEvents()
    {
        return response()->json(Event::where('status', 'scheduled')->orderBy('starts_at')->paginate(12));
    }

    public function registerForEvent(Request $request, Event $event)
    {
        $registration = EventRegistration::firstOrCreate(
            ['event_id' => $event->id, 'user_id' => $request->user()->id],
            ['registered_at' => now(), 'status' => 'registered']
        );

        return response()->json($registration, 201);
    }

    public function memberOpportunities()
    {
        return response()->json(Opportunity::where('status', 'active')->latest()->paginate(12));
    }

    public function memberSubmissions(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:story,project,campaign'],
            'title' => ['required', 'string', 'max:180'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string', 'max:10000'],
            'country' => ['nullable', 'string', 'max:120'],
        ]);

        $model = match ($validated['type']) {
            'project' => CommunityProject::class,
            'campaign' => AdvocacyCampaign::class,
            default => LeadershipStory::class,
        };

        $basePayload = [
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'summary' => $validated['summary'] ?? null,
            'country' => $validated['country'] ?? null,
            'status' => 'pending',
        ];

        $submission = match ($validated['type']) {
            'project' => $model::create([
                ...$basePayload,
                'description' => $validated['body'] ?? ($validated['summary'] ?? null),
                'submitted_at' => now(),
            ]),
            'campaign' => $model::create([
                ...$basePayload,
                'description' => $validated['body'] ?? ($validated['summary'] ?? null),
            ]),
            default => $model::create([
                ...$basePayload,
                'body' => $validated['body'] ?? ($validated['summary'] ?? ''),
                'submitted_at' => now(),
            ]),
        };

        return response()->json($submission, 201);
    }

    public function moderatorReports()
    {
        return response()->json(Report::whereIn('status', ['open', 'assigned'])->latest()->paginate(20));
    }

    public function moderatorReportAction(Request $request, Report $report)
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $action = ModerationAction::create([
            'moderator_id' => $request->user()->id,
            'report_id' => $report->id,
            'action' => $validated['action'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $report->update(['status' => 'reviewed']);

        return response()->json($action, 201);
    }

    public function moderatorActions(Request $request)
    {
        return response()->json(ModerationAction::where('moderator_id', $request->user()->id)->latest()->paginate(20));
    }

    public function adminDashboard()
    {
        return response()->json([
            'metrics' => [
                'total_members' => User::role('Member')->count(),
                'active_members' => User::where('status', 'active')->count(),
                'suspended_members' => User::where('status', 'suspended')->count(),
                'total_moderators' => User::role('Moderator')->count(),
                'pending_submissions' => LeadershipStory::where('status', 'pending')->count() + CommunityProject::where('status', 'pending')->count(),
                'reported_content' => Report::where('status', 'open')->count(),
                'upcoming_events' => Event::where('status', 'scheduled')->count(),
            ],
            'growth' => User::selectRaw('DATE(created_at) as date, COUNT(*) as total')->groupBy('date')->latest('date')->limit(14)->get(),
            'countries' => User::select('country', DB::raw('COUNT(*) as total'))->whereNotNull('country')->groupBy('country')->orderByDesc('total')->limit(12)->get(),
            'activity' => ActivityLog::latest()->limit(10)->get(),
        ]);
    }

    public function adminMembers(Request $request)
    {
        $query = User::query()->with('profile', 'roles');

        foreach (['country', 'status'] as $filter) {
            $query->when($request->filled($filter), fn ($q) => $q->where($filter, $request->{$filter}));
        }

        $query->when($request->filled('search'), fn ($q) => $q->where(function ($inner) use ($request) {
            $inner->where('name', 'like', "%{$request->search}%")->orWhere('email', 'like', "%{$request->search}%");
        }));

        return response()->json($query->latest()->paginate(20));
    }

    public function updateMemberStatus(Request $request, User $user)
    {
        abort_if($user->hasRole('Super Admin'), 403, 'The main Super Admin cannot be suspended, banned, or demoted.');

        $validated = $request->validate(['status' => ['required', 'in:active,suspended,banned,pending verification']]);
        $user->update(['status' => $validated['status']]);

        return response()->json($user->fresh('roles'));
    }

    public function inviteModerator(Request $request)
    {
        $validated = $request->validate(['user_id' => ['required', 'exists:users,id']]);
        $user = User::findOrFail($validated['user_id']);

        $invitation = ModeratorInvitation::create([
            'user_id' => $user->id,
            'invited_by' => $request->user()->id,
            'email' => $user->email,
            'token' => Str::random(48),
            'status' => 'invited',
            'expires_at' => now()->addDays(14),
        ]);

        return response()->json($invitation, 201);
    }

    public function revokeModerator(User $user)
    {
        abort_if($user->hasRole('Super Admin'), 403);
        $user->removeRole('Moderator');
        ModeratorInvitation::where('user_id', $user->id)->whereIn('status', ['invited', 'accepted'])->update(['status' => 'revoked', 'revoked_at' => now()]);

        return response()->json(['message' => 'Moderator privileges revoked.']);
    }

    public function adminReports()
    {
        return response()->json(Report::latest()->paginate(20));
    }

    public function adminAnalytics()
    {
        return response()->json([
            'member_growth' => User::selectRaw('DATE(created_at) as date, COUNT(*) total')->groupBy('date')->latest('date')->limit(30)->get(),
            'content_engagement' => ['posts' => Post::count(), 'comments' => Comment::count()],
            'event_attendance' => EventRegistration::count(),
            'campaign_engagement' => DB::table('campaign_supporters')->count(),
            'top_contributors' => User::withCount(['stories'])->orderByDesc('stories_count')->limit(10)->get(),
        ]);
    }

    public function resourceIndex(string $resource)
    {
        $model = $this->resolveResource($resource);

        return response()->json($model::latest()->paginate(20));
    }

    public function resourceStore(Request $request, string $resource)
    {
        $model = $this->resolveResource($resource);
        $payload = $this->resourcePayload($request, $resource);

        if (isset($payload['title']) && in_array('slug', (new $model())->getFillable(), true)) {
            $payload['slug'] = $payload['slug'] ?? Str::slug($payload['title']).'-'.Str::lower(Str::random(6));
        }

        if (in_array('user_id', (new $model())->getFillable(), true)) {
            $payload['user_id'] = $request->user()->id;
        }

        if (in_array('created_by', (new $model())->getFillable(), true)) {
            $payload['created_by'] = $request->user()->id;
        }

        return response()->json($model::create($payload), 201);
    }

    public function resourceUpdate(Request $request, string $resource, int $id)
    {
        $model = $this->resolveResource($resource);
        $item = $model::findOrFail($id);
        $item->update($this->resourcePayload($request, $resource));

        return response()->json($item);
    }

    public function resourceDestroy(string $resource, int $id)
    {
        $model = $this->resolveResource($resource);
        $model::findOrFail($id)->delete();

        return response()->noContent();
    }

    private function resolveResource(string $resource): string
    {
        abort_unless(isset($this->resources[$resource]), 404);

        return $this->resources[$resource];
    }

    private function resourcePayload(Request $request, string $resource): array
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string'],
            'summary' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:180'],
            'eligibility' => ['nullable', 'string', 'max:255'],
            'external_url' => ['nullable', 'url', 'max:255'],
            'category' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'audience' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'string', 'max:80'],
            'featured' => ['nullable', 'boolean'],
            'is_online' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'deadline_at' => ['nullable', 'date'],
            'scheduled_at' => ['nullable', 'date'],
            'key' => ['sometimes', 'string', 'max:160'],
            'value' => ['nullable'],
            'group' => ['nullable', 'string', 'max:120'],
            'content' => ['nullable'],
            'active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $model = $this->resolveResource($resource);
        $fillable = (new $model())->getFillable();

        return collect($validated)
            ->filter(fn ($value) => $value !== '')
            ->only($fillable)
            ->all();
    }

    private function profileCompletion(User $user, array $incoming = []): int
    {
        $profile = collect($user->profile?->toArray() ?? [])->merge($incoming);
        $fields = ['phone', 'country', 'city', 'organization', 'professional_title', 'leadership_category', 'bio', 'skills', 'interests', 'social_links', 'portfolio_link', 'causes_supported'];
        $filled = collect($fields)->filter(fn ($field) => filled($profile->get($field)))->count();

        return (int) round(($filled / count($fields)) * 100);
    }
}
