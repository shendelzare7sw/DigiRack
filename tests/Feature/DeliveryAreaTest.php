<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Address;
use App\Services\DeliveryAreaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryAreaTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_service_accepts_configured_tangerang_districts_only(): void
    {
        $service = app(DeliveryAreaService::class);

        $this->assertTrue($service->isCovered('Banten', 'Kota Tangerang', 'Karawaci'));
        $this->assertTrue($service->isCovered('Banten', 'Kota Tangerang Selatan', 'Pamulang'));
        $this->assertTrue($service->isCovered('Banten', 'Kabupaten Tangerang', 'Kelapa Dua'));
        $this->assertFalse($service->isCovered('Banten', 'Kabupaten Tangerang', 'Kronjo'));
        $address = new Address([
            'province' => 'Banten',
            'city' => 'Kota Tangerang',
            'district' => 'Karawaci',
        ]);
        $this->assertSame(20_000, $service->shippingFee($address));
    }

    public function test_buyer_cannot_save_address_outside_delivery_coverage(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);

        $response = $this->actingAs($buyer)->post(route('profile.addresses.store'), [
            'label' => 'Rumah',
            'recipient_name' => 'Budi',
            'phone' => '081234567890',
            'full_address' => 'Jalan Raya nomor 1',
            'province' => 'Banten',
            'city' => 'Kabupaten Tangerang',
            'district' => 'Kronjo',
            'postal_code' => '15550',
        ]);

        $response->assertSessionHasErrors('district');
        $this->assertDatabaseCount('addresses', 0);
    }

    public function test_buyer_can_save_covered_address(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);

        $this->actingAs($buyer)->post(route('profile.addresses.store'), [
            'label' => 'Rumah',
            'recipient_name' => 'Budi',
            'phone' => '081234567890',
            'full_address' => 'Jalan Raya nomor 1',
            'province' => 'Banten',
            'city' => 'Kota Tangerang Selatan',
            'district' => 'Pamulang',
            'postal_code' => '15417',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('addresses', [
            'user_id' => $buyer->id,
            'city' => 'Kota Tangerang Selatan',
            'district' => 'Pamulang',
            'is_primary' => true,
        ]);
    }
}
