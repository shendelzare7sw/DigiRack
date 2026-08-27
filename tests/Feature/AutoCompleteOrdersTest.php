<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AutoCompleteOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivered_order_is_auto_completed_after_configured_hours(): void
    {
        Notification::fake();

        $order = $this->createShippedOrder(deliveredAt: now()->subHours(25));

        Artisan::call('orders:auto-complete');

        $order->refresh();
        $this->assertSame('completed', $order->status);
    }

    public function test_recent_delivered_order_is_not_auto_completed_yet(): void
    {
        Notification::fake();

        $order = $this->createShippedOrder(deliveredAt: now()->subHours(23));

        Artisan::call('orders:auto-complete');

        $order->refresh();

        $this->assertSame('shipped', $order->status);
    }

    public function test_old_shipped_order_without_delivery_confirmation_is_not_auto_completed(): void
    {
        Notification::fake();

        $order = $this->createShippedOrder(shippedAt: now()->subDays(3), deliveredAt: null);

        Artisan::call('orders:auto-complete');

        $order->refresh();

        $this->assertSame('shipped', $order->status);
    }

    public function test_seller_must_upload_delivery_proof_when_marking_order_delivered(): void
    {
        Notification::fake();
        Storage::fake('public');

        $order = $this->createShippedOrder(deliveredAt: null);
        $seller = $order->store->user;

        $response = $this->actingAs($seller)->post(route('admin.orders.delivered', $order->id), [
            'delivery_confirmation_note' => 'Diterima oleh Ani.',
            'delivery_proofs' => [
                UploadedFile::fake()->image('proof-a.jpg')->size(1024),
                UploadedFile::fake()->image('proof-b.png')->size(1024),
            ],
        ]);

        $response->assertSessionHas('success');

        $order->refresh();

        $this->assertNotNull($order->delivered_at);
        $this->assertSame('Diterima oleh Ani.', $order->delivery_confirmation_note);
        $this->assertNotNull($order->delivery_proof_path);
        $this->assertCount(2, $order->delivery_proof_paths);
        $this->assertSame($order->delivery_proof_paths[0], $order->delivery_proof_path);
        foreach ($order->delivery_proof_paths as $proofPath) {
            Storage::disk('public')->assertExists($proofPath);
        }
    }

    private function createShippedOrder($shippedAt = null, $deliveredAt = null): Order
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'admin']);

        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Auto Complete Store',
            'slug' => 'auto-complete-store-' . uniqid(),
            'is_active' => true,
            'is_verified' => true,
            'verification_status' => 'approved',
        ]);

        $category = Category::create([
            'name' => 'Rack Server',
            'slug' => 'rack-server-' . uniqid(),
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Intel Xeon',
            'slug' => 'intel-xeon-' . uniqid(),
            'price' => 300000,
            'stock' => 10,
            'sold_count' => 2,
            'status' => 'active',
        ]);

        $order = Order::create([
            'invoice_number' => 'INV/' . date('Ymd') . '/' . strtoupper(uniqid()),
            'buyer_id' => $buyer->id,
            'store_id' => $store->id,
            'status' => 'shipped',
            'total_price' => 315000,
            'shipping_cost' => 15000,
            'payment_method' => 'transfer',
            'payment_status' => 'paid',
            'payment_reference' => 'PAY-' . date('Ymd') . '-' . strtoupper(uniqid()),
            'shipping_tracking_number' => 'KURIR-TOKO',
            'shipped_at' => $shippedAt ?? now()->subDays(2),
            'delivered_at' => $deliveredAt,
            'delivery_confirmation_note' => $deliveredAt ? 'Paket diterima oleh penerima.' : null,
            'shipping_address' => [
                'name' => 'Buyer',
                'phone' => '081234567890',
                'full_address' => 'Jl. Testing',
                'courier' => 'toko_internal',
            ],
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'price_snapshot' => $product->price,
            'quantity' => 1,
        ]);

        return $order;
    }
}
