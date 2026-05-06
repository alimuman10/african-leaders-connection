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
            'account_status' => $this->status,
            'email_verified_at' => $this->email_verified_at,
            'last_login_at' => $this->last_login_at,
            'roles' => method_exists($this->resource, 'getRoleNames') ? $this->getRoleNames() : [],
            'permissions' => method_exists($this->resource, 'getAllPermissions') ? $this->getAllPermissions()->pluck('name')->values() : [],
            'is_moderator' => method_exists($this->resource, 'hasRole') ? $this->hasRole('Moderator') : false,
            'is_super_admin' => method_exists($this->resource, 'hasRole') ? $this->hasRole('Super Admin') : false,
            'redirect_path' => $this->redirectPath(),
            'profile' => $this->whenLoaded('profile'),
            'created_at' => $this->created_at,
        ];
    }

    private function redirectPath(): string
    {
        $roles = method_exists($this->resource, 'getRoleNames') ? $this->getRoleNames() : collect();

        return match (true) {
            $roles->contains('Super Admin') => '/admin/dashboard',
            default => '/member/dashboard',
        };
    }
}
