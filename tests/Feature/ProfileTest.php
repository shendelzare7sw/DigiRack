<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ConfirmPhoneChange;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_verified_email_status_is_displayed_on_profile(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
        $response->assertSee('Terverifikasi');
    }

    public function test_profile_information_can_be_updated(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo($user, CustomVerifyEmail::class);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_phone_change_requires_verified_email(): void
    {
        $user = User::factory()->unverified()->create([
            'phone' => '081111111111',
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '082222222222',
            ]);

        $response
            ->assertSessionHasErrors('phone')
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('081111111111', $user->phone);
        $this->assertNull($user->pending_phone);
    }

    public function test_phone_change_is_pending_until_confirmed_from_email(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'phone' => '081111111111',
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '082222222222',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile')
            ->assertSessionHas('status', 'phone-change-verification-sent');

        $user->refresh();

        $this->assertSame('081111111111', $user->phone);
        $this->assertSame('082222222222', $user->pending_phone);
        $this->assertNotNull($user->pending_phone_token);
        $this->assertNotNull($user->pending_phone_expires_at);

        $sentNotification = null;

        Notification::assertSentOnDemand(ConfirmPhoneChange::class, function ($notification) use (&$sentNotification) {
            $sentNotification = $notification;

            return true;
        });

        $tokenProperty = new \ReflectionProperty($sentNotification, 'token');
        $tokenProperty->setAccessible(true);
        $token = $tokenProperty->getValue($sentNotification);

        $confirmationUrl = URL::temporarySignedRoute(
            'profile.phone.confirm',
            now()->addMinutes(60),
            [
                'user' => $user->id,
                'token' => $token,
            ],
        );

        $confirmResponse = $this
            ->actingAs($user)
            ->get($confirmationUrl);

        $confirmResponse
            ->assertRedirect('/profile')
            ->assertSessionHas('status', 'phone-updated');

        $user->refresh();

        $this->assertSame('082222222222', $user->phone);
        $this->assertNull($user->pending_phone);
        $this->assertNull($user->pending_phone_token);
        $this->assertNull($user->pending_phone_expires_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
