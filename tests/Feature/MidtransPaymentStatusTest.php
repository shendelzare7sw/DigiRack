<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Notifications\OrderNotification;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MidtransPaymentStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_midtrans_status_keeps_order_unpaid_without_invalid_payment_status(): void
    {
        [$order] = $this->createPendingOrder();

        $changed = app(MidtransService::class)->applyTransactionStatus($order, 'pending', null);

        $order->refresh();

        $this->assertFalse($changed);
        $this->assertSame('pending_payment', $order->status);
        $this->assertSame('unpaid', $order->payment_status);
    }

    public function test_settlement_marks_order_paid_and_decrements_stock_once(): void
    {
        Notification::fake();
        [$order, $product, $buyer, $seller] = $this->createPendingOrder();

        $service = app(MidtransService::class);

        $this->assertTrue($service->applyTransactionStatus($order, 'settlement', 'accept'));
        $this->assertFalse($service->applyTransactionStatus($order->fresh(), 'settlement', 'accept'));

        $order->refresh();
        $product->refresh();

        $this->assertSame('processing', $order->status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame(8, $product->stock);
        $this->assertSame(2, $product->sold_count);

        Notification::assertSentTo($buyer, OrderNotification::class);
        Notification::assertSentTo($seller, OrderNotification::class);
    }

    public function test_failed_midtrans_status_cancels_unpaid_order_without_invalid_payment_status(): void
    {
        [$order] = $this->createPendingOrder();

        $changed = app(MidtransService::class)->applyTransactionStatus($order, 'expire', null);

        $order->refresh();

        $this->assertTrue($changed);
        $this->assertSame('cancelled', $order->status);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame('Pembayaran kedaluwarsa di Midtrans.', $order->cancellation_response);
        $this->assertNotNull($order->cancellation_resolved_at);
    }

    private function createPendingOrder(): array
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'admin']);

        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Server Store',
            'slug' => 'server-store-'.uniqid(),
        ]);

        $category = Category::create([
            'name' => 'Rack Server',
            'slug' => 'rack-server-'.uniqid(),
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Intel Xeon',
            'slug' => 'intel-xeon-'.uniqid(),
            'price' => 315000,
            'stock' => 10,
            'sold_count' => 0,
            'status' => 'active',
        ]);

        $order = Order::create([
            'invoice_number' => 'INV/'.date('Ymd').'/'.strtoupper(uniqid()),
            'buyer_id' => $buyer->id,
            'store_id' => $store->id,
            'status' => 'pending_payment',
            'total_price' => 315000,
            'shipping_cost' => 0,
            'payment_method' => 'transfer',
            'payment_status' => 'unpaid',
            'payment_reference' => 'PAY-'.date('Ymd').'-'.strtoupper(uniqid()),
            'shipping_address' => [
                'name' => 'Buyer',
                'phone' => '081234567890',
                'full_address' => 'Jl. Testing',
                'courier' => 'TOKO_TEST',
            ],
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'price_snapshot' => $product->price,
            'quantity' => 2,
        ]);

        return [$order, $product, $buyer, $seller];
    }
}
