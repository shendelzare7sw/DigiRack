<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
