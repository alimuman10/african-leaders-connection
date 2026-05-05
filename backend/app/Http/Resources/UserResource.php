<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'profession' => $this->profession,
            'organization' => $this->organization,
            'leadership_interest' => $this->leadership_interest,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at,
            'last_login_at' => $this->last_login_at,
            'roles' => method_exists($this->resource, 'getRoleNames') ? $this->getRoleNames() : [],
            'redirect_path' => $this->redirectPath(),
            'profile' => $this->whenLoaded('profile'),
            'created_at' => $this->created_at,
        ];
    }

    private function redirectPath(): string
    {
        $roles = method_exists($this->resource, 'getRoleNames') ? $this->getRoleNames() : collect();

        return match (true) {
            $roles->contains('Super Admin'), $roles->contains('Admin') => '/admin/dashboard',
            $roles->contains('Content Manager') => '/admin/content',
            $roles->contains('Community Manager') => '/admin/community',
            default => '/member/dashboard',
        };
    }
}
