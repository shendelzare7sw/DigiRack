<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SystemSetting;
use App\Notifications\OrderNotification;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Transaction;

class MidtransService
{
    /**
     * Apply Midtrans config from SystemSetting (fallback to env).
     */
    public function boot(): bool
    {
        $serverKey = SystemSetting::val('midtrans_server_key', env('MIDTRANS_SERVER_KEY'));

        if (empty($serverKey)) {
            return false;
        }

        Config::$serverKey = $serverKey;
        Config::$isProduction = SystemSetting::val('midtrans_is_production', env('MIDTRANS_IS_PRODUCTION', false)) === 'true';
        Config::$isSanitized = true;
        Config::$is3ds = true;

        return true;
    }

    /**
     * Actively query Midtrans for the latest transaction status and sync all
     * orders sharing the given payment_reference. Used as a fallback when the
     * async webhook is delayed or never delivered.
     */
    public function syncByReference(string $paymentReference): bool
    {
        if (!$this->boot()) {
            return false;
        }

        try {
            $status = Transaction::status($paymentReference);
        } catch (\Throwable $e) {
            // 404 = transaction not yet created at Midtrans, or unreachable.
            Log::info('Midtrans status check skipped: ' . $e->getMessage(), ['ref' => $paymentReference]);
            return false;
        }

        $transactionStatus = $status->transaction_status ?? null;
        $fraudStatus = $status->fraud_status ?? null;

        if (!$transactionStatus) {
            return false;
        }

        $orders = Order::with('items.product', 'store.user', 'buyer')
            ->where('payment_reference', $paymentReference)
            ->get();

        $changed = false;
        foreach ($orders as $order) {
            if ($this->applyTransactionStatus($order, $transactionStatus, $fraudStatus)) {
                $changed = true;
            }
        }

        return $changed;
    }

    /**
     * Map a Midtrans transaction status onto an order and run the side effects
     * (stock decrement, notifications) exactly once on the unpaid -> paid edge.
     * Returns true if the order was modified.
     */
    public function applyTransactionStatus(Order $order, string $transactionStatus, ?string $fraudStatus): bool
    {
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                $order->payment_status = 'pending';
            } elseif ($fraudStatus === 'accept') {
                $order->payment_status = 'paid';
            }
        } elseif ($transactionStatus === 'settlement') {
            $order->payment_status = 'paid';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'], true)) {
            $order->payment_status = 'failed';
            $order->status = 'cancelled';
        } elseif ($transactionStatus === 'pending') {
            $order->payment_status = 'pending';
        }

        $becamePaid = $order->isDirty('payment_status') && $order->payment_status === 'paid';

        if ($becamePaid) {
            $order->status = 'processing';

            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity);
                    $item->product->increment('sold_count', $item->quantity);
                }
            }

            $this->notifyPaid($order);
        }

        if (!$order->isDirty()) {
            return false;
        }

        $order->save();

        return true;
    }

    private function notifyPaid(Order $order): void
    {
        try {
            $sellerUser = $order->store->user ?? null;
            if ($sellerUser) {
                $sellerUser->notify(new OrderNotification(
                    'new_order',
                    '🎉 Pesanan Baru Masuk!',
                    'Pesanan ' . $order->invoice_number . ' senilai Rp ' . number_format($order->total_price, 0, ',', '.') . ' telah dibayar. Segera proses dan kirimkan!',
                    route('seller.orders.show', $order->id),
                    '🛒'
                ));
            }

            $buyer = $order->buyer ?? null;
            if ($buyer) {
                $buyer->notify(new OrderNotification(
                    'payment_success',
                    '✅ Pembayaran Berhasil!',
                    'Pembayaran untuk pesanan ' . $order->invoice_number . ' telah dikonfirmasi. Penjual akan segera memproses pesanan Anda.',
                    route('buyer.orders.show', $order->id),
                    '💳'
                ));
            }
        } catch (\Throwable $e) {
            // Notification failure must not block payment processing.
            Log::warning('Order paid notification failed: ' . $e->getMessage(), ['order_id' => $order->id]);
        }
    }
}
