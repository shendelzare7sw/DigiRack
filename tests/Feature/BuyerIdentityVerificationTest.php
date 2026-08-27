<?php

namespace Tests\Feature;

use App\Models\IdentityVerification;
use App\Models\User;
use App\Notifications\IdentityVerificationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BuyerIdentityVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_submit_ktp_for_admin_review(): void
    {
        Notification::fake();
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin']);
        $buyer = User::factory()->create(['role' => 'buyer']);

        $response = $this->actingAs($buyer)->post(route('profile.identity.update'), [
            'legal_name' => 'Budi Tangerang',
            'nik' => '3671010101010001',
            'identity_document' => UploadedFile::fake()->image('ktp.jpg'),
            'consent' => '1',
        ]);

        $response->assertSessionHas('success');
        $verification = IdentityVerification::whereBelongsTo($buyer)->firstOrFail();
        $this->assertSame(IdentityVerification::STATUS_PENDING, $verification->status);
        $this->assertSame('3671010101010001', $verification->nik);
        Storage::disk('local')->assertExists($verification->document_path);
        Notification::assertSentTo($admin, IdentityVerificationNotification::class);
    }

    public function test_admin_can_approve_buyer_identity(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $buyer = User::factory()->create(['role' => 'buyer']);
        $verification = IdentityVerification::create([
            'user_id' => $buyer->id,
            'legal_name' => 'Budi Tangerang',
            'nik' => '3671010101010001',
            'nik_hash' => hash_hmac('sha256', '3671010101010001', (string) config('app.key')),
            'document_path' => 'identity-documents/ktp.jpg',
            'document_mime' => 'image/jpeg',
            'status' => IdentityVerification::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.identity.approve', $buyer))
            ->assertSessionHas('success');

        $this->assertSame(IdentityVerification::STATUS_VERIFIED, $verification->fresh()->status);
        $this->assertTrue($buyer->fresh()->isIdentityVerified());
        Notification::assertSentTo($buyer, IdentityVerificationNotification::class);
    }

    public function test_unverified_buyer_cannot_open_checkout(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);

        $this->actingAs($buyer)
            ->post(route('buyer.checkout.index'), ['selected_products' => []])
            ->assertRedirect(route('profile.identity.edit'));
    }
}
