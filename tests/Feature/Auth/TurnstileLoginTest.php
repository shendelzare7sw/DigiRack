<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TurnstileLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.turnstile', [
            'enabled' => true,
            'site_key' => '1x00000000000000000000AA',
            'secret_key' => '1x0000000000000000000000000000000AA',
            'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            'allowed_hostnames' => ['digihook.test'],
        ]);
    }

    public function test_login_page_renders_turnstile_widget_when_enabled(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('cf-turnstile', false)
            ->assertSee('1x00000000000000000000AA', false)
            ->assertSee('https://challenges.cloudflare.com/turnstile/v0/api.js', false);
    }

    public function test_user_can_login_after_successful_turnstile_verification(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'login',
                'hostname' => 'digihook.test',
            ]),
        ]);

        $user = User::factory()->create();

        $response = $this->post(route('login'), [
            'identifier' => $user->email,
            'password' => 'password',
            'cf-turnstile-response' => 'valid-test-token',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('home'));
        Http::assertSent(fn ($request) => $request['secret'] === '1x0000000000000000000000000000000AA'
            && $request['response'] === 'valid-test-token');
    }

    public function test_login_is_rejected_when_turnstile_verification_fails(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ]),
        ]);

        $user = User::factory()->create();

        $this->post(route('login'), [
            'identifier' => $user->email,
            'password' => 'password',
            'cf-turnstile-response' => 'invalid-test-token',
        ])->assertSessionHasErrors('cf-turnstile-response');

        $this->assertGuest();
    }

    public function test_login_requires_turnstile_token_when_enabled(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), [
            'identifier' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('cf-turnstile-response');

        $this->assertGuest();
        Http::assertNothingSent();
    }
}
