<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use App\Notifications\StoreStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminStoreVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_registration_form_has_identity_document_preview(): void
    {
        $user = User::factory()->create([
            'role' => 'buyer',
            'email_verified_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('seller.register.form'));

        $response->assertOk();
        $response->assertSee('Preview dokumen identitas');
        $response->assertSee('updatePreview');
    }

    public function test_admin_can_open_store_identity_review_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'buyer', 'phone' => '081234567890']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'NetOffers',
            'slug' => 'netoffers',
            'description' => 'Supplier kabel LAN',
            'identity_document_path' => 'store-identities/ktp.jpg',
            'identity_submitted_at' => now(),
            'is_active' => false,
            'is_verified' => false,
            'verification_status' => 'pending',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.stores.show', $store));

        $response->assertOk();
        $response->assertSee('Review Identitas Toko');
        $response->assertSee('NetOffers');
        $response->assertSee('Loloskan Toko');
        $response->assertSee('Tolak Pengajuan');
    }

    public function test_pending_store_cannot_be_activated_before_verification(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'buyer']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Pending Store',
            'slug' => 'pending-store',
            'is_active' => false,
            'is_verified' => false,
            'verification_status' => 'pending',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.stores.toggle', $store));

        $response->assertRedirect();
        $this->assertFalse($store->fresh()->is_active);
    }

    public function test_store_registration_notifies_admin_and_applicant(): void
    {
        Notification::fake();
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $buyer = User::factory()->create([
            'role' => 'buyer',
            'email_verified_at' => now(),
        ]);

        $response = $this
            ->actingAs($buyer)
            ->post(route('seller.register.store'), [
                'store_name' => 'Net Officer',
                'store_description' => 'Supplier switch dan kabel',
                'identity_document' => UploadedFile::fake()->image('ktp.jpg'),
            ]);

        $response->assertRedirect(route('seller.dashboard'));

        $store = Store::where('name', 'Net Officer')->firstOrFail();

        Notification::assertSentTo($admin, StoreStatusNotification::class, function ($notification) use ($store) {
            $data = $notification->toArray($store->user);

            return $data['type'] === 'store_registration_submitted'
                && $data['action_url'] === route('admin.stores.show', $store->id);
        });

        Notification::assertSentTo($buyer, StoreStatusNotification::class, function ($notification) {
            return $notification->toArray(new User)['type'] === 'store_registration_received';
        });
    }

    public function test_store_approval_and_rejection_notify_applicant(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $buyer = User::factory()->create(['role' => 'buyer']);
        $approvedStore = Store::create([
            'user_id' => $buyer->id,
            'name' => 'Approved Store',
            'slug' => 'approved-store',
            'is_active' => false,
            'is_verified' => false,
            'verification_status' => 'pending',
        ]);

        $this
            ->actingAs($admin)
            ->post(route('admin.stores.verify', $approvedStore));

        Notification::assertSentTo($buyer, StoreStatusNotification::class, function ($notification) {
            return $notification->toArray(new User)['type'] === 'store_verified';
        });

        $secondBuyer = User::factory()->create(['role' => 'buyer']);
        $rejectedStore = Store::create([
            'user_id' => $secondBuyer->id,
            'name' => 'Rejected Store',
            'slug' => 'rejected-store',
            'is_active' => false,
            'is_verified' => false,
            'verification_status' => 'pending',
        ]);

        $this
            ->actingAs($admin)
            ->post(route('admin.stores.reject', $rejectedStore), [
                'verification_notes' => 'Foto identitas tidak jelas.',
            ]);

        Notification::assertSentTo($secondBuyer, StoreStatusNotification::class, function ($notification) {
            return $notification->toArray(new User)['type'] === 'store_rejected';
        });
    }

    public function test_admin_can_preview_pending_storefront_but_public_cannot(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $buyer = User::factory()->create(['role' => 'buyer']);
        $store = Store::create([
            'user_id' => $buyer->id,
            'name' => 'Pending Preview',
            'slug' => 'pending-preview',
            'is_active' => false,
            'is_verified' => false,
            'verification_status' => 'pending',
        ]);

        $this->get(route('store.show', $store->slug))->assertNotFound();

        $this
            ->actingAs($admin)
            ->get(route('store.show', $store->slug))
            ->assertOk()
            ->assertSee('Pending Preview');
    }
}
