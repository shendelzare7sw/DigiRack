<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileService
{
    public function enabled(): bool
    {
        return (bool) config('services.turnstile.enabled')
            && filled(config('services.turnstile.site_key'))
            && filled(config('services.turnstile.secret_key'));
    }

    public function verify(string $token, ?string $remoteIp = null, string $expectedAction = 'login'): bool
    {
        if (! $this->enabled() || blank($token) || strlen($token) > 2048) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->timeout(6)
                ->post(config('services.turnstile.verify_url'), array_filter([
                    'secret' => config('services.turnstile.secret_key'),
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ]));

            if (! $response->successful()) {
                return false;
            }

            $result = $response->json();
            if (! ($result['success'] ?? false)) {
                return false;
            }

            if (($result['action'] ?? null) !== $expectedAction) {
                return false;
            }

            $allowedHostnames = config('services.turnstile.allowed_hostnames', []);
            if ($allowedHostnames && ! in_array($result['hostname'] ?? '', $allowedHostnames, true)) {
                return false;
            }

            return true;
        } catch (\Throwable $exception) {
            Log::warning('Cloudflare Turnstile verification failed.', [
                'exception' => $exception::class,
            ]);

            return false;
        }
    }
}
