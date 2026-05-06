<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\LoginHistory;
use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SecurityAuditLogger
{
    public function login(User $user, Request $request): void
    {
        LoginHistory::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'status' => 'success',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'logged_in_at' => now(),
        ]);

        $this->event('auth.login', $user, 'info', null, [], $request);
    }

    public function failedLogin(string $email, Request $request, ?User $user = null, string $reason = 'invalid_credentials'): void
    {
        LoginHistory::create([
            'user_id' => $user?->id,
            'email' => $email,
            'status' => 'failed',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        $this->event('auth.failed_login', $user, 'warning', null, ['email' => $email, 'reason' => $reason], $request);
    }

    public function event(string $event, ?User $actor = null, string $severity = 'info', ?Model $subject = null, array $metadata = [], ?Request $request = null): void
    {
        $request ??= request();

        SecurityEvent::create([
            'user_id' => $actor?->id,
            'event' => $event,
            'severity' => $severity,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'metadata' => $metadata,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        ActivityLog::create([
            'user_id' => $actor?->id,
            'action' => $event,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'properties' => $metadata,
            'ip_address' => $request->ip(),
        ]);
    }
}
