<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_renders_gallery_lightbox_controls(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Jaringan Nusantara',
            'slug' => 'jaringan-nusantara',
            'is_active' => true,
            'is_verified' => true,
            'verification_status' => 'approved',
        ]);
        $category = Category::create([
            'name' => 'Server & Hardware',
            'slug' => 'server-hardware',
            'is_active' => true,
        ]);
        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Switch Ruijie',
            'slug' => 'switch-ruijie',
            'description' => 'Switch untuk jaringan kantor.',
            'price' => 4500000,
            'stock' => 6,
            'condition' => 'new',
            'status' => 'active',
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/switch-1.jpg',
            'is_primary' => true,
            'sort_order' => 0,
        ]);
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/switch-2.jpg',
            'sort_order' => 1,
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertSee('Tutup gambar');
        $response->assertSee('openLightbox');
        $response->assertSee('lightboxNext');
        $response->assertSee('handleGallerySwipe');
    }
}
