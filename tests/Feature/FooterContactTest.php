<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_footer_displays_contact_details_from_system_settings(): void
    {
        SystemSetting::create(['key' => 'platform_phone', 'value' => '0812 3456 7890']);
        SystemSetting::create(['key' => 'platform_email', 'value' => 'halo@example.test']);
        SystemSetting::create(['key' => 'platform_address', 'value' => 'Jl. Digital Hook No. 10, Tangerang']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('0812 3456 7890')
            ->assertSee('tel:081234567890', false)
            ->assertSee('halo@example.test')
            ->assertSee('Jl. Digital Hook No. 10, Tangerang');
    }

    public function test_admin_can_update_public_contact_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.settings.store'), [
            'platform_phone' => '0812 9876 5432',
            'platform_email' => 'admin@example.test',
            'platform_address' => "Ruko Digital Hook\nKota Tangerang",
            'delivery_fee_kota_tangerang' => 0,
            'delivery_fee_tangerang_selatan' => 10_000,
            'delivery_fee_kabupaten_tangerang' => 20_000,
        ])->assertSessionHas('success');

        $this->assertSame('0812 9876 5432', SystemSetting::val('platform_phone'));
        $this->assertSame('admin@example.test', SystemSetting::val('platform_email'));
        $this->assertSame("Ruko Digital Hook\nKota Tangerang", SystemSetting::val('platform_address'));
    }

    public function test_footer_does_not_invent_contact_details_when_settings_are_empty(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('tel:', false)
            ->assertDontSee('mailto:', false);
    }
}
