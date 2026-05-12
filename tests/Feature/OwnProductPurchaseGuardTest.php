<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnProductPurchaseGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_sees_management_actions_on_own_product_detail(): void
    {
        [$seller, $product] = $this->createOwnProduct();

        $response = $this
            ->actingAs($seller)
            ->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertSee('Ini produk toko Anda');
        $response->assertSee('Kelola Produk');
        $response->assertDontSee('Beli Langsung');
        $response->assertDontSee('+ Keranjang');
        $response->assertDontSee('Tambah ke Wishlist');
    }

    public function test_seller_cannot_add_own_product_to_cart(): void
    {
        [$seller, $product] = $this->createOwnProduct();

        $response = $this
            ->actingAs($seller)
            ->postJson(route('buyer.cart.store'), [
                'product_id' => $product->id,
                'quantity' => 1,
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('carts', [
            'user_id' => $seller->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_seller_cannot_wishlist_own_product(): void
    {
        [$seller, $product] = $this->createOwnProduct();

        $response = $this
            ->actingAs($seller)
            ->postJson(route('buyer.wishlist.toggle'), [
                'product_id' => $product->id,
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $seller->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_seller_cannot_checkout_own_product_directly_or_from_cart(): void
    {
        [$seller, $product] = $this->createOwnProduct();

        $directResponse = $this
            ->actingAs($seller)
            ->from(route('products.show', $product->slug))
            ->post(route('buyer.checkout.init'), [
                'product_id' => $product->id,
                'quantity' => 1,
            ]);

        $directResponse
            ->assertRedirect(route('products.show', $product->slug))
            ->assertSessionHas('error', 'Produk milik toko sendiri tidak bisa dibeli atau di-checkout.');

        $cart = Cart::create([
            'user_id' => $seller->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $cartResponse = $this
            ->actingAs($seller)
            ->post(route('buyer.checkout.index'), [
                'selected_items' => [$cart->id],
            ]);

        $cartResponse
            ->assertRedirect(route('buyer.cart.index'))
            ->assertSessionHas('error', 'Keranjang berisi produk milik toko sendiri. Hapus produk tersebut sebelum checkout.');
    }

    public function test_product_listing_marks_own_product_instead_of_purchase_actions(): void
    {
        [$seller, $product] = $this->createOwnProduct();

        $response = $this
            ->actingAs($seller)
            ->get(route('products.index'));

        $response->assertOk();
        $response->assertSee($product->name);
        $response->assertSee('Produk Saya');
        $response->assertSee('Kelola Produk');
        $response->assertDontSee('+ Keranjang');
    }

    private function createOwnProduct(): array
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'email_verified_at' => now(),
        ]);

        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Toko Seller',
            'slug' => 'toko-seller',
            'is_active' => true,
            'is_verified' => true,
            'verification_status' => 'approved',
        ]);

        $category = Category::create([
            'name' => 'Switch',
            'slug' => 'switch',
            'is_active' => true,
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Switch Milik Sendiri',
            'slug' => 'switch-milik-sendiri',
            'description' => 'Produk milik seller.',
            'price' => 1500000,
            'stock' => 5,
            'weight_gram' => 1000,
            'condition' => 'new',
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/self-switch.jpg',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        return [$seller, $product];
    }
}
