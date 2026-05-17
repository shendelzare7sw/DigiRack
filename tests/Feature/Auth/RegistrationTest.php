<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\RegistrationOtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('register.otp.notice', absolute: false));

        $code = null;
        Notification::assertSentOnDemand(RegistrationOtpNotification::class, function ($notification, array $channels, AnonymousNotifiable $notifiable) use (&$code) {
            $code = str($notification->toMail($notifiable)->subject)->afterLast(': ')->toString();

            return $notifiable->routes['mail'] === 'test@example.com';
        });

        $response = $this->post(route('register.otp.verify'), [
            'code' => $code,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertTrue(User::where('email', 'test@example.com')->whereNotNull('email_verified_at')->exists());
    }
}
