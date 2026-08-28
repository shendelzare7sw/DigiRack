<?php

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTurnstile implements ValidationRule
{
    public function __construct(
        private readonly ?string $remoteIp,
        private readonly string $action = 'login',
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! app(TurnstileService::class)->verify((string) $value, $this->remoteIp, $this->action)) {
            $fail('Verifikasi keamanan gagal atau kedaluwarsa. Silakan coba kembali.');
        }
    }
}
