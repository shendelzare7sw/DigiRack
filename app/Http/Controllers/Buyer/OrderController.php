<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderNotification;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request, MidtransService $midtrans)
    {
        // Webhook can be delayed or blocked. Keep the buyer's recent unpaid
        // orders fresh by confirming status server-to-server before rendering.
        Order::where('buyer_id', Auth::id())
            ->where('status', 'pending_payment')
            ->where('payment_status', 'unpaid')
            ->whereNotNull('payment_reference')
            ->orderByDesc('created_at')
            ->limit($request->has('payment') ? 5 : 3)
            ->pluck('payment_reference')
            ->unique()
            ->each(fn ($ref) => $midtrans->syncByReference($ref));

        $orders = Order::with('items.product.store')
            ->where('buyer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('buyer.orders.index', compact('orders'));
    }

    public function show($id, MidtransService $midtrans)
    {
        $order = Order::with(['items.product.store', 'store', 'reviews', 'storeReview'])->where('buyer_id', Auth::id())->findOrFail($id);

        // Fallback for a delayed/undelivered Midtrans webhook: confirm payment
        // status server-to-server when the buyer opens an unpaid order.
        if ($order->payment_status === 'unpaid' && $order->payment_reference) {
            if ($midtrans->syncByReference($order->payment_reference)) {
                $order->refresh();
            }
        }

        return view('buyer.orders.show', compact('order'));
    }

    public function invoice($id)
    {
        $order = Order::with(['items.product', 'store', 'buyer'])
            ->where('buyer_id', Auth::id())
            ->findOrFail($id);

        $itemsSubtotal = $order->items->sum(fn ($item) => $item->price_snapshot * $item->quantity);
        $buyerFees = collect($order->applied_buyer_fees ?? []);
        $grandTotal = $order->total_price + $buyerFees->sum('amount');

        return view('buyer.orders.invoice', compact('order', 'itemsSubtotal', 'buyerFees', 'grandTotal'));
    }

    public function confirm(Request $request, $id)
    {
        $order = Order::where('buyer_id', Auth::id())->findOrFail($id);

        if ($order->status !== 'shipped') {
            return back()->with('error', 'Pesanan belum dikirim, tidak dapat diselesaikan.');
        }

        try {
            DB::beginTransaction();

            // Ubah status jadi selesai
            $order->status = 'completed';
            $order->save();

            DB::commit();

            // Informasikan pemilik toko bahwa pesanan sudah ditutup.
            try {
                $sellerUser = $order->store->user ?? null;
                if ($sellerUser) {
                    $sellerUser->notify(new OrderNotification(
                        'order_completed',
                        '💰 Dana Pesanan Dicairkan!',
                        'Pembeli telah mengonfirmasi penerimaan pesanan ' . $order->invoice_number . '.',
                        route('admin.orders.show', $order->id),
                        '🎉'
                    ));
                }
            } catch (\Exception $e) {}

            return back()->with('success', 'Pesanan telah selesai. Jangan lupa berikan ulasan Anda!');

        } catch (\Exception $e) {
            \Log::error('Order confirm error: ' . $e->getMessage(), ['order_id' => $id]);
            return back()->with('error', 'Terjadi kesalahan saat menyelesaikan pesanan. Silakan coba lagi.');
        }
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $order = Order::with(['store.user'])
            ->where('buyer_id', Auth::id())
            ->findOrFail($id);

        if (in_array($order->status, ['shipped', 'completed', 'cancelled', 'cancellation_requested'], true)) {
            return back()->with('error', 'Pesanan ini sudah tidak dapat diajukan untuk dibatalkan.');
        }

        $reason = $request->filled('cancellation_reason')
            ? $request->cancellation_reason
            : 'Pembeli mengajukan pembatalan dari halaman pesanan.';

        try {
            DB::beginTransaction();

            if ($order->status === 'pending_payment') {
                $order->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => $reason,
                    'cancellation_response' => 'Pesanan dibatalkan sebelum pembayaran dilakukan.',
                    'cancellation_requested_at' => now(),
                    'cancellation_resolved_at' => now(),
                ]);

                DB::commit();
                return back()->with('success', 'Pesanan berhasil dibatalkan.');
            }

            if ($order->status !== 'processing') {
                DB::rollBack();
                return back()->with('error', 'Pesanan ini belum dapat diajukan untuk dibatalkan.');
            }

            $order->update([
                'status' => 'cancellation_requested',
                'cancellation_reason' => $reason,
                'cancellation_response' => null,
                'cancellation_requested_at' => now(),
                'cancellation_resolved_at' => null,
            ]);

            DB::commit();

            try {
                $sellerUser = $order->store->user ?? null;
                if ($sellerUser) {
                    $sellerUser->notify(new OrderNotification(
                        'order_cancellation_requested',
                        'Permintaan Pembatalan Pesanan',
                        'Pembeli meminta pembatalan pesanan ' . $order->invoice_number . '. Silakan tentukan apakah pesanan dibatalkan atau tetap diproses.',
                        route('admin.orders.show', $order->id),
                        '!'
                    ));
                }
            } catch (\Exception $e) {
                Log::warning('Order cancellation request notification failed: ' . $e->getMessage(), ['order_id' => $order->id]);
            }

            return back()->with('success', 'Permintaan pembatalan dikirim ke Digital Hook dan akan ditinjau oleh admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation request error: ' . $e->getMessage(), ['order_id' => $id]);
            return back()->with('error', 'Terjadi kesalahan saat mengajukan pembatalan. Silakan coba lagi.');
        }
    }
}
