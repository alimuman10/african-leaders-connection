<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug ?? null,
            'summary' => $this->summary ?? $this->excerpt ?? null,
            'body' => $this->body ?? $this->content ?? $this->description ?? null,
            'category' => $this->category ?? null,
            'type' => $this->type ?? null,
            'status' => $this->status ?? null,
            'featured' => $this->featured ?? null,
            'active' => $this->active ?? null,
            'image_path' => $this->image_path ?? null,
            'country' => $this->country ?? null,
            'region' => $this->region ?? null,
            'location' => $this->location ?? null,
            'impact_metrics' => $this->impact_metrics ?? null,
            'published_at' => $this->published_at ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
