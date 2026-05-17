<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SystemSetting;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    /**
     * Handle webhook notification from Midtrans
     */
    public function midtransCallback(Request $request, MidtransService $midtrans)
    {
        $payload = $request->all();
        \Log::info('Midtrans Webhook Payload: ', $payload);

        // Verifikasi Signature Key Midtrans agar mencegah Request Palsu
        $serverKey = SystemSetting::val('midtrans_server_key', env('MIDTRANS_SERVER_KEY'));

        // Handle Midtrans Test Notification (URL Test dari Dashboard)
        if (!isset($payload['order_id']) || !isset($payload['status_code']) || !isset($payload['gross_amount']) || !isset($payload['signature_key'])) {
            return response()->json(['message' => 'Test notification received or invalid payload'], 200);
        }

        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        $signatureKey = $payload['signature_key'];

        $mySignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($mySignature !== $signatureKey) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Cari Order berdasarkan payment_reference atau invoice_number
        $orders = Order::with('items.product', 'store.user', 'buyer')
            ->where('payment_reference', $orderId)
            ->orWhere('invoice_number', $orderId)
            ->get();

        if ($orders->isEmpty()) {
            // Midtrans Test Notification mengirim order_id dummy yang tidak ada di DB.
            // Wajib return 200 OK agar tombol "Tes" di dashboard Midtrans sukses.
            return response()->json(['message' => 'Order not found (or Test Notification)'], 200);
        }

        $transactionStatus = $payload['transaction_status'];
        $fraudStatus = $payload['fraud_status'] ?? null;

        foreach ($orders as $order) {
            $midtrans->applyTransactionStatus($order, $transactionStatus, $fraudStatus);
        }

        return response()->json(['message' => 'Notification processed successfully']);
    }
}
