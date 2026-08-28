<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Rules\ValidTurnstile;
use App\Services\TurnstileService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];

        if (app(TurnstileService::class)->enabled()) {
            $rules['cf-turnstile-response'] = ['required', 'string', 'max:2048', new ValidTurnstile($this->ip())];
        }

        return $rules;
    }

    /**
     * Get custom validation messages for the login form.
     */
    public function messages(): array
    {
        return [
            'cf-turnstile-response.required' => 'Selesaikan verifikasi keamanan terlebih dahulu.',
            'cf-turnstile-response.max' => 'Respons verifikasi keamanan tidak valid. Silakan muat ulang halaman.',
        ];
    }

    /**
     * Attempt to authenticate using email, username, or phone.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = $this->input('identifier');
        $password = $this->input('password');
        $remember = $this->boolean('remember');

        // Determine which field the identifier maps to
        $fieldType = $this->detectIdentifierType($identifier);

        // Find user first
        $user = User::where($fieldType, $identifier)->first();

        if (! $user || ! Auth::attempt([$fieldType => $identifier, 'password' => $password], $remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Detect whether the identifier is an email, phone, or username.
     */
    protected function detectIdentifierType(string $identifier): string
    {
        // If it contains @, it's an email
        if (str_contains($identifier, '@')) {
            return 'email';
        }

        // If it starts with 0 or +62 and is mostly digits, it's a phone number
        $cleaned = preg_replace('/[\s\-\(\)]/', '', $identifier);
        if (preg_match('/^(\+62|62|08)\d{8,}$/', $cleaned)) {
            return 'phone';
        }

        // Otherwise treat as username
        return 'username';
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identifier' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('identifier')).'|'.$this->ip());
    }
}
