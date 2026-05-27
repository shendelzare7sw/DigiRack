<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BuyerReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_review_completed_order_item_from_order_detail(): void
    {
        [$buyer, $order, $item, $product] = $this->createOrderWithItem('completed');

        $this->actingAs($buyer)
            ->get(route('buyer.orders.show', $order->id))
            ->assertOk()
            ->assertSee('Beri Ulasan Produk')
            ->assertSee('Kirim Ulasan');

        $response = $this->actingAs($buyer)->post(route('buyer.reviews.store', $item), [
            'rating' => 5,
            'comment' => 'Produk bagus dan sesuai deskripsi.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'buyer_id' => $buyer->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Produk bagus dan sesuai deskripsi.',
        ]);

        $this->assertSame('5.0', $product->fresh()->avg_rating);
    }

    public function test_buyer_cannot_review_order_before_it_is_completed(): void
    {
        [$buyer, , $item] = $this->createOrderWithItem('shipped');

        $response = $this->actingAs($buyer)->post(route('buyer.reviews.store', $item), [
            'rating' => 4,
            'comment' => 'Belum selesai.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(0, Review::count());
    }

    public function test_buyer_can_attach_images_and_videos_to_review(): void
    {
        Storage::fake('public');
        [$buyer, , $item] = $this->createOrderWithItem('completed');

        $response = $this->actingAs($buyer)->post(route('buyer.reviews.store', $item), [
            'rating' => 5,
            'comment' => 'Ulasan dengan media.',
            'review_media' => [
                UploadedFile::fake()->image('review-router.jpg')->size(1024),
                UploadedFile::fake()->create('review-demo.mp4', 2048, 'video/mp4'),
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $review = Review::firstOrFail();

        $this->assertCount(2, $review->media);
        $this->assertSame('image', $review->media[0]['type']);
        $this->assertSame('video', $review->media[1]['type']);

        foreach ($review->media as $media) {
            Storage::disk('public')->assertExists($media['path']);
        }
    }

    public function test_guest_can_view_all_product_reviews_page(): void
    {
        [$buyer, $order, , $product] = $this->createOrderWithItem('completed');

        Review::create([
            'buyer_id' => $buyer->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Produk tampil bagus di halaman semua ulasan.',
            'media' => [
                [
                    'path' => 'review-media/sample.jpg',
                    'type' => 'image',
                    'mime' => 'image/jpeg',
                    'name' => 'sample.jpg',
                ],
            ],
        ]);
        $product->update(['avg_rating' => 5]);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('Lihat Semua Ulasan');

        $this->get(route('products.reviews.index', $product->slug))
            ->assertOk()
            ->assertSee('Ulasan')
            ->assertSee('Produk tampil bagus di halaman semua ulasan.')
            ->assertSee('Foto & Video', false);
    }

    private function createOrderWithItem(string $status): array
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
            'name' => 'Review Store',
            'slug' => 'review-store-' . uniqid(),
            'is_active' => true,
            'is_verified' => true,
            'verification_status' => 'approved',
        ]);

        $category = Category::create([
            'name' => 'Review Category',
            'slug' => 'review-category-' . uniqid(),
            'is_active' => true,
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Produk Review',
            'slug' => 'produk-review-' . uniqid(),
            'description' => 'Produk untuk test ulasan.',
            'price' => 300000,
            'stock' => 5,
            'weight_gram' => 1000,
            'condition' => 'new',
            'status' => 'active',
        ]);

        $order = Order::create([
            'invoice_number' => 'INV-REVIEW-' . strtoupper(uniqid()),
            'buyer_id' => $buyer->id,
            'store_id' => $store->id,
            'status' => $status,
            'total_price' => 315000,
            'shipping_cost' => 15000,
            'payment_method' => 'transfer',
            'payment_status' => 'paid',
            'shipping_address' => [
                'name' => $buyer->name,
                'phone' => '081234567890',
                'full_address' => 'Jl. Review',
                'courier' => 'toko_internal',
            ],
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'price_snapshot' => $product->price,
            'quantity' => 1,
        ]);

        return [$buyer, $order, $item, $product];
    }
}
