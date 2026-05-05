<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CloudflareTurnstile
{
    public function verify(?string $token, ?string $ip = null): bool
    {
        if (! config('services.turnstile.secret')) {
            return app()->environment('local', 'testing');
        }

        if (! $token) {
            return false;
        }

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret'),
            'response' => $token,
            'remoteip' => $ip,
        ]);

        return (bool) $response->json('success');
    }
}
