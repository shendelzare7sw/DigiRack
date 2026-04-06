<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    /**
     * Handle webhook notification from Midtrans
     */
    public function midtransCallback(Request $request)
    {
        $payload = $request->all();

        // Verifikasi Signature Key Midtrans agar mencegah Request Palsu
        $serverKey = SystemSetting::val('midtrans_server_key', env('MIDTRANS_SERVER_KEY'));
        
        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        $signatureKey = $payload['signature_key'];

        $mySignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($mySignature !== $signatureKey) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Cari Order berdasarkan invoice_number yang diset sebagai order_id di Midtrans
        $order = Order::with('items.product')->where('invoice_number', $orderId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $transactionStatus = $payload['transaction_status'];
        $fraudStatus = $payload['fraud_status'] ?? '';

        // Tentukan Status Order kita berdasarkan callback Midtrans
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $order->payment_status = 'pending';
            } else if ($fraudStatus == 'accept') {
                $order->payment_status = 'paid';
            }
        } else if ($transactionStatus == 'settlement') {
            $order->payment_status = 'paid';
        } else if ($transactionStatus == 'cancel' ||
          $transactionStatus == 'deny' ||
          $transactionStatus == 'expire') {
            $order->payment_status = 'failed';
            $order->status = 'cancelled';
        } else if ($transactionStatus == 'pending') {
            $order->payment_status = 'pending';
        }

        if ($order->isDirty('payment_status') && $order->payment_status === 'paid') {
            // Ketika sukses bayar, otomatis potong stok produk sebagai validasi fix
            $order->status = 'processing'; // Naikkan status pesanan jadi diproses / perlu dikirim seller
            
            foreach($order->items as $item) {
                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity);
                    $item->product->increment('sold', $item->quantity);
                }
            }
        }

        $order->save();

        return response()->json(['message' => 'Notification processed successfully']);
    }
}
