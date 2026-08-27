<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_and_buyer_roles_are_available(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($buyer->isBuyer());
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($buyer->isSeller());
        $this->assertFalse(Route::has('switch.role'));
        $this->assertFalse(Route::has('seller.dashboard'));
    }

    public function test_dashboard_redirects_each_role_to_its_panel(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($buyer)->get(route('dashboard'))->assertRedirect(route('buyer.dashboard'));
        $this->actingAs($admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    }
}
