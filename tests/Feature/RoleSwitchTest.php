<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_account_can_switch_between_buyer_and_seller_modes(): void
    {
        $user = User::factory()->create([
            'role' => 'seller',
            'email_verified_at' => now(),
        ]);

        Store::create([
            'user_id' => $user->id,
            'name' => 'Toko Role Switch',
            'slug' => 'toko-role-switch',
            'is_active' => true,
            'is_verified' => true,
            'verification_status' => 'approved',
        ]);

        $buyerResponse = $this
            ->actingAs($user)
            ->get(route('switch.role', 'buyer'));

        $buyerResponse
            ->assertRedirect(route('home'))
            ->assertSessionHas('active_role', 'buyer');

        $sellerResponse = $this
            ->actingAs($user)
            ->get(route('switch.role', 'seller'));

        $sellerResponse
            ->assertRedirect(route('seller.dashboard'))
            ->assertSessionHas('active_role', 'seller');
    }

    public function test_buyer_routes_automatically_return_seller_to_buyer_mode(): void
    {
        $user = User::factory()->create([
            'role' => 'seller',
            'email_verified_at' => now(),
        ]);

        Store::create([
            'user_id' => $user->id,
            'name' => 'Toko Auto Buyer',
            'slug' => 'toko-auto-buyer',
            'is_active' => true,
            'is_verified' => true,
            'verification_status' => 'approved',
        ]);

        $response = $this
            ->actingAs($user)
            ->withSession(['active_role' => 'seller'])
            ->get(route('buyer.cart.index'));

        $response
            ->assertOk()
            ->assertSessionHas('active_role', 'buyer');
    }
}
