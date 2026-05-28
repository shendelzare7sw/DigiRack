<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Store;
use App\Models\StoreReview;
use App\Models\User;
use App\Notifications\ReviewNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
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
            ->assertSee('Belum Diulas')
            ->assertSee(route('buyer.reviews.edit', $item), false)
            ->assertDontSee('Kirim Ulasan');

        $this->actingAs($buyer)
            ->get(route('buyer.reviews.edit', $item))
            ->assertOk()
            ->assertSee('Tulis Ulasan')
            ->assertSee('Kirim Ulasan')
            ->assertSee('Maksimal 5 media');

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

    public function test_seller_receives_notification_when_buyer_creates_review(): void
    {
        Notification::fake();
        [$buyer, $order, $item, $product] = $this->createOrderWithItem('completed');
        $seller = $product->store->user;

        $this->actingAs($buyer)->post(route('buyer.reviews.store', $item), [
            'rating' => 5,
            'comment' => 'Produk bagus dan sesuai deskripsi.',
        ])->assertRedirect(route('buyer.orders.show', $order->id));

        Notification::assertSentTo($seller, ReviewNotification::class, function ($notification) use ($order, $seller) {
            $data = $notification->toArray($seller);

            return $data['type'] === 'review_created'
                && $data['title'] === 'Ulasan produk baru'
                && $data['action_url'] === route('seller.orders.show', $order->id)
                && str_contains($data['message'], '5 bintang');
        });
    }

    public function test_seller_receives_notification_when_buyer_updates_review(): void
    {
        Notification::fake();
        [$buyer, $order, $item, $product] = $this->createOrderWithItem('completed');
        $seller = $product->store->user;

        Review::create([
            'buyer_id' => $buyer->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 4,
            'comment' => 'Ulasan awal.',
        ]);

        $this->actingAs($buyer)->post(route('buyer.reviews.store', $item), [
            'rating' => 5,
            'comment' => 'Ulasan sudah diedit.',
        ])->assertRedirect(route('buyer.orders.show', $order->id));

        Notification::assertSentTo($seller, ReviewNotification::class, function ($notification) use ($order, $seller) {
            $data = $notification->toArray($seller);

            return $data['type'] === 'review_updated'
                && $data['title'] === 'Ulasan produk diperbarui'
                && $data['action_url'] === route('seller.orders.show', $order->id)
                && str_contains($data['message'], 'memperbarui ulasan');
        });
    }

    public function test_seller_can_reply_to_product_review_from_order_detail(): void
    {
        Notification::fake();
        [$buyer, $order, , $product] = $this->createOrderWithItem('completed');
        $seller = $product->store->user;

        $review = Review::create([
            'buyer_id' => $buyer->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Produk sangat membantu.',
        ]);

        $this->actingAs($seller)
            ->get(route('seller.orders.show', $order->id))
            ->assertOk()
            ->assertSee('Ulasan Pembeli')
            ->assertSee('Balas Ulasan Pembeli');

        $this->actingAs($seller)
            ->post(route('seller.orders.reviews.reply', [$order->id, $review->id]), [
                'seller_reply' => 'Terima kasih, semoga produknya membantu.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'seller_reply' => 'Terima kasih, semoga produknya membantu.',
        ]);

        Notification::assertSentTo($buyer, ReviewNotification::class, function ($notification) use ($order, $buyer) {
            $data = $notification->toArray($buyer);

            return $data['type'] === 'review_replied'
                && $data['action_url'] === route('buyer.orders.show', $order->id);
        });

        $this->get(route('products.reviews.index', $product->slug))
            ->assertOk()
            ->assertSee('Balasan Seller')
            ->assertSee('Terima kasih, semoga produknya membantu.');
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

    public function test_review_media_is_limited_to_five_files(): void
    {
        Storage::fake('public');
        [$buyer, , $item] = $this->createOrderWithItem('completed');

        $response = $this->actingAs($buyer)->post(route('buyer.reviews.store', $item), [
            'rating' => 5,
            'comment' => 'Terlalu banyak media.',
            'review_media' => [
                UploadedFile::fake()->image('review-1.jpg')->size(1024),
                UploadedFile::fake()->image('review-2.jpg')->size(1024),
                UploadedFile::fake()->image('review-3.jpg')->size(1024),
                UploadedFile::fake()->image('review-4.jpg')->size(1024),
                UploadedFile::fake()->image('review-5.jpg')->size(1024),
                UploadedFile::fake()->image('review-6.jpg')->size(1024),
            ],
        ]);

        $response->assertSessionHasErrors('review_media');
        $this->assertSame(0, Review::count());
    }

    public function test_buyer_can_edit_existing_review(): void
    {
        [$buyer, $order, $item, $product] = $this->createOrderWithItem('completed');

        Review::create([
            'buyer_id' => $buyer->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 4,
            'comment' => 'Ulasan awal.',
        ]);

        $this->actingAs($buyer)
            ->get(route('buyer.reviews.edit', $item))
            ->assertOk()
            ->assertSee('Edit Ulasan')
            ->assertSee('Perbarui Ulasan');

        $response = $this->actingAs($buyer)->post(route('buyer.reviews.store', $item), [
            'rating' => 5,
            'comment' => 'Ulasan sudah diedit.',
        ]);

        $response->assertRedirect(route('buyer.orders.show', $order->id));

        $this->assertDatabaseHas('reviews', [
            'buyer_id' => $buyer->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Ulasan sudah diedit.',
        ]);
        $this->assertSame(1, Review::count());
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

    public function test_buyer_can_review_store_after_completed_order(): void
    {
        Notification::fake();
        [$buyer, $order, , $product] = $this->createOrderWithItem('completed');
        $seller = $product->store->user;

        $this->actingAs($buyer)
            ->get(route('buyer.store-reviews.edit', $order->id))
            ->assertOk()
            ->assertSee('Tulis Ulasan Toko');

        $this->actingAs($buyer)
            ->post(route('buyer.store-reviews.store', $order->id), [
                'rating' => 5,
                'comment' => 'Pelayanan toko cepat dan ramah.',
            ])
            ->assertRedirect(route('buyer.orders.show', $order->id));

        $this->assertDatabaseHas('store_reviews', [
            'buyer_id' => $buyer->id,
            'store_id' => $product->store_id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Pelayanan toko cepat dan ramah.',
        ]);
        $this->assertSame('5.0', $product->store->fresh()->avg_rating);

        Notification::assertSentTo($seller, ReviewNotification::class, function ($notification) use ($seller) {
            $data = $notification->toArray($seller);

            return $data['type'] === 'store_review_created'
                && $data['title'] === 'Ulasan toko baru'
                && str_contains($data['message'], '5 bintang');
        });

        $this->get(route('store.show', $product->store->slug))
            ->assertOk()
            ->assertSee('Ulasan Performa Toko')
            ->assertSee('Pelayanan toko cepat dan ramah.');
    }

    public function test_seller_can_reply_to_store_review_from_dashboard(): void
    {
        Notification::fake();
        [$buyer, $order, , $product] = $this->createOrderWithItem('completed');
        $seller = $product->store->user;

        $storeReview = StoreReview::create([
            'buyer_id' => $buyer->id,
            'store_id' => $product->store_id,
            'order_id' => $order->id,
            'rating' => 4,
            'comment' => 'Toko responsif.',
        ]);

        $this->actingAs($seller)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('Ulasan Performa Toko')
            ->assertSee('Toko responsif.');

        $this->actingAs($seller)
            ->post(route('seller.store-reviews.reply', $storeReview->id), [
                'seller_reply' => 'Terima kasih sudah berbelanja di toko kami.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('store_reviews', [
            'id' => $storeReview->id,
            'seller_reply' => 'Terima kasih sudah berbelanja di toko kami.',
        ]);

        Notification::assertSentTo($buyer, ReviewNotification::class, function ($notification) use ($order, $buyer) {
            $data = $notification->toArray($buyer);

            return $data['type'] === 'store_review_replied'
                && $data['action_url'] === route('buyer.orders.show', $order->id);
        });

        $this->get(route('store.show', $product->store->slug))
            ->assertOk()
            ->assertSee('Balasan Toko')
            ->assertSee('Terima kasih sudah berbelanja di toko kami.');
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
