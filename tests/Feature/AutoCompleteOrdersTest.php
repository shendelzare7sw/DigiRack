<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
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
        $wallet = Wallet::where('store_id', $order->store_id)->first();

        $this->assertSame('completed', $order->status);
        $this->assertNotNull($wallet);
        $this->assertSame(315000, (int) $wallet->balance);
    }

    public function test_recent_delivered_order_is_not_auto_completed_yet(): void
    {
        Notification::fake();

        $order = $this->createShippedOrder(deliveredAt: now()->subHours(23));

        Artisan::call('orders:auto-complete');

        $order->refresh();

        $this->assertSame('shipped', $order->status);
        $this->assertNull(Wallet::where('store_id', $order->store_id)->first());
    }

    public function test_old_shipped_order_without_delivery_confirmation_is_not_auto_completed(): void
    {
        Notification::fake();

        $order = $this->createShippedOrder(shippedAt: now()->subDays(3), deliveredAt: null);

        Artisan::call('orders:auto-complete');

        $order->refresh();

        $this->assertSame('shipped', $order->status);
        $this->assertNull(Wallet::where('store_id', $order->store_id)->first());
    }

    private function createShippedOrder($shippedAt = null, $deliveredAt = null): Order
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);

        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Auto Complete Store',
            'slug' => 'auto-complete-store-' . uniqid(),
            'is_active' => true,
            'is_verified' => true,
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
