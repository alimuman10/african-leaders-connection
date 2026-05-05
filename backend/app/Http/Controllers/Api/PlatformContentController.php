<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Http\Requests\ContentRequest;
use App\Http\Resources\ContentResource;
use App\Models\AdvocacySection;
use App\Models\LeadershipContent;
use App\Models\Project;
use App\Models\Service;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlatformContentController extends Controller
{
    use LogsActivity;

    public function publicStories(Request $request)
    {
        return ContentResource::collection(
            Story::published()
                ->when($request->search, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
                ->latest('published_at')
                ->paginate($request->integer('per_page', 12))
        );
    }

    public function publicProjects(Request $request)
    {
        return ContentResource::collection(Project::query()->latest()->paginate($request->integer('per_page', 12)));
    }

    public function publicServices(Request $request)
    {
        return ContentResource::collection(
            Service::where('active', true)->orderBy('sort_order')->orderBy('title')->paginate($request->integer('per_page', 20))
        );
    }

    public function publicAdvocacy()
    {
        return ContentResource::collection(AdvocacySection::where('active', true)->orderBy('sort_order')->get());
    }

    public function publicLeadership(Request $request)
    {
        return ContentResource::collection(
            LeadershipContent::where('status', 'published')->latest('published_at')->paginate($request->integer('per_page', 12))
        );
    }

    public function submitStory(ContentRequest $request)
    {
        $story = Story::create([
            ...$request->safe()->only(['title', 'excerpt', 'body', 'image_path', 'country', 'region']),
            'slug' => $this->uniqueSlug(Story::class, $request->title),
            'author_id' => $request->user()->id,
            'status' => 'review',
        ]);

        return new ContentResource($story);
    }

    public function index(Request $request)
    {
        return ContentResource::collection($this->modelForRoute($request)->latest()->paginate($request->integer('per_page', 15)));
    }

    public function store(ContentRequest $request)
    {
        $model = $this->modelForRoute($request);
        $payload = $this->payloadFor($request, $model);

        $content = $model->create($payload);
        $this->logActivity('content.created', $content, ['model' => $model]);

        return new ContentResource($content);
    }

    public function show(Request $request, int $id)
    {
        return new ContentResource($this->modelForRoute($request)->findOrFail($id));
    }

    public function update(ContentRequest $request, int $id)
    {
        $model = $this->modelForRoute($request);
        $content = $model->findOrFail($id);
        $content->update($this->payloadFor($request, $model, $content->slug ?? null));
        $this->logActivity('content.updated', $content, ['model' => $model]);

        return new ContentResource($content->fresh());
    }

    public function destroy(Request $request, int $id)
    {
        $content = $this->modelForRoute($request)->findOrFail($id);
        $this->logActivity('content.deleted', $content, ['title' => $content->title ?? null]);
        $content->delete();

        return response()->noContent();
    }

    private function modelForRoute(Request $request): string
    {
        return match (true) {
            str_contains($request->path(), 'projects') => Project::class,
            str_contains($request->path(), 'services') => Service::class,
            str_contains($request->path(), 'advocacy') => AdvocacySection::class,
            str_contains($request->path(), 'leadership') => LeadershipContent::class,
            default => Story::class,
        };
    }

    private function payloadFor(ContentRequest $request, string $model, ?string $currentSlug = null): array
    {
        $data = $request->safe()->except(['slug']);
        if (in_array($model, [Story::class, Project::class, Service::class, AdvocacySection::class, LeadershipContent::class], true)) {
            $data['slug'] = $request->slug ?: ($currentSlug ?: $this->uniqueSlug($model, $request->title));
        }
        if (in_array($model, [Story::class, LeadershipContent::class], true) && auth()->check()) {
            $data['author_id'] = $data['author_id'] ?? auth()->id();
        }

        return $data;
    }

    private function uniqueSlug(string $model, string $title): string
    {
        $slug = Str::slug($title);
        $base = $slug;
        $counter = 2;
        while ($model::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
