<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistStateSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_product_card_reflects_wishlist_added_from_product_detail(): void
    {
        [$buyer, $product] = $this->createPublicProduct('intel-xeon-sync');

        $this->actingAs($buyer)
            ->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('isWishlisted: false', false);

        $this->actingAs($buyer)
            ->postJson(route('buyer.wishlist.toggle'), [
                'product_id' => $product->id,
            ])
            ->assertOk()
            ->assertJsonPath('status', 'added');

        $this->actingAs($buyer)
            ->get(route('store.show', $product->store->slug))
            ->assertOk()
            ->assertSee('data-product-id="' . $product->id . '"', false)
            ->assertSee('data-wishlisted="1"', false)
            ->assertSee('aria-label="Hapus dari wishlist"', false);

        $this->actingAs($buyer)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('data-product-id="' . $product->id . '"', false)
            ->assertSee('data-wishlisted="1"', false);
    }

    public function test_related_product_card_reflects_wishlist_state_on_product_detail(): void
    {
        [$buyer, $product] = $this->createPublicProduct('network-tool-sync');

        $related = Product::create([
            'store_id' => $product->store_id,
            'category_id' => $product->category_id,
            'name' => 'Related Switch',
            'slug' => 'related-switch-sync',
            'description' => 'Produk serupa untuk test wishlist.',
            'price' => 450000,
            'stock' => 7,
            'weight_gram' => 700,
            'condition' => 'new',
            'status' => 'active',
        ]);

        Wishlist::create([
            'user_id' => $buyer->id,
            'product_id' => $related->id,
        ]);

        $this->actingAs($buyer)
            ->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('data-product-id="' . $related->id . '"', false)
            ->assertSee('data-wishlisted="1"', false);
    }

    private function createPublicProduct(string $slug): array
    {
        $buyer = User::factory()->create([
            'role' => 'buyer',
            'email_verified_at' => now(),
        ]);

        $seller = User::factory()->create([
            'role' => 'seller',
            'email_verified_at' => now(),
        ]);

        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Wishlist Sync Store',
            'slug' => 'wishlist-sync-store-' . uniqid(),
            'is_active' => true,
            'is_verified' => true,
            'verification_status' => 'approved',
        ]);

        $category = Category::create([
            'name' => 'Wishlist Sync Category',
            'slug' => 'wishlist-sync-category-' . uniqid(),
            'is_active' => true,
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Intel Xeon Sync',
            'slug' => $slug . '-' . uniqid(),
            'description' => 'Produk untuk test sinkron wishlist.',
            'price' => 300000,
            'stock' => 14,
            'weight_gram' => 500,
            'condition' => 'new',
            'status' => 'active',
        ]);

        return [$buyer, $product];
    }
}
