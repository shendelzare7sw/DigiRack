<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\SystemSetting;
use App\Notifications\OrderNotification;
use App\Notifications\ReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Get the single Digital Hook business record.
     */
    protected function getStore()
    {
        return Auth::user()->store;
    }

    public function index(Request $request)
    {
        $store = $this->getStore();
        if (! $store) {
            return redirect()->route('dashboard')->with('error', 'Profil bisnis Digital Hook belum tersedia.');
        }

        $query = Order::with(['buyer', 'items.product'])->where('store_id', $store->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('seller.orders.index', compact('orders', 'store'));
    }

    public function show($id)
    {
        $store = $this->getStore();
        $order = Order::with(['buyer', 'items.product', 'reviews.buyer', 'reviews.product'])->where('store_id', $store->id)->findOrFail($id);

        return view('seller.orders.show', compact('order', 'store'));
    }

    public function replyReview(Request $request, $id, $reviewId)
    {
        $store = $this->getStore();
        $order = Order::with('buyer')->where('store_id', $store->id)->findOrFail($id);

        $review = Review::with(['buyer', 'product'])
            ->where('order_id', $order->id)
            ->where('id', $reviewId)
            ->whereHas('product', fn ($query) => $query->where('store_id', $store->id))
            ->firstOrFail();

        $validated = $request->validate([
            'seller_reply' => ['required', 'string', 'max:1000'],
        ]);

        $review->update([
            'seller_reply' => $validated['seller_reply'],
            'seller_replied_at' => now(),
        ]);

        try {
            if ($review->buyer) {
                $productName = Str::limit($review->product?->name ?? 'produk', 60);

                $review->buyer->notify(new ReviewNotification(
                    'review_replied',
                    'Digital Hook membalas ulasan Anda',
                    'Penjual membalas ulasan Anda untuk '.$productName.'.',
                    route('buyer.orders.show', $order->id),
                ));
            }
        } catch (\Throwable $e) {
            Log::warning('Product review reply notification failed: '.$e->getMessage(), [
                'review_id' => $review->id,
                'order_id' => $order->id,
            ]);
        }

        return back()->with('success', 'Balasan ulasan produk berhasil disimpan.');
    }

    public function report(Request $request)
    {
        $store = $this->getStore();
        if (! $store) {
            return redirect()->route('dashboard')->with('error', 'Profil bisnis Digital Hook belum tersedia.');
        }

        $query = Order::with(['buyer', 'items'])->where('store_id', $store->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'count' => $orders->count(),
            'gross' => $orders->sum('total_price'),
            'paidGross' => $orders->whereIn('status', ['processing', 'shipped', 'completed'])->sum('total_price'),
            'completed' => $orders->where('status', 'completed')->count(),
            'inProgress' => $orders->whereIn('status', ['processing', 'shipped'])->count(),
            'cancelled' => $orders->whereIn('status', ['cancelled', 'cancellation_requested'])->count(),
        ];

        return view('seller.orders.report', compact('orders', 'store', 'summary'));
    }

    public function updateStatus(Request $request, $id)
    {
        $store = $this->getStore();
        $order = Order::where('store_id', $store->id)->findOrFail($id);

        $request->validate([
            'status' => 'required|in:processing,shipped,cancelled',
            'shipping_tracking_number' => 'nullable|string|max:100',
        ]);

        // State validation logic
        if ($order->status == 'completed' || $order->status == 'cancelled' || $order->status == 'cancellation_requested') {
            return back()->with('error', 'Pesanan yang sudah selesai atau dibatalkan tidak dapat diubah statusnya.');
        }

        // Handle payment verification (manual transfer)
        if ($request->status == 'processing' && $request->has('verify_payment')) {
            if ($order->status != 'pending_payment') {
                return back()->with('error', 'Hanya pesanan yang menunggu pembayaran yang bisa diverifikasi.');
            }
            $order->payment_status = 'paid';
            // Also mark all orders with same payment_reference as paid
            if ($order->payment_reference) {
                Order::where('payment_reference', $order->payment_reference)
                    ->where('id', '!=', $order->id)
                    ->update(['payment_status' => 'paid', 'status' => 'processing']);
            }
        }

        if ($request->status == 'shipped') {
            if ($order->status != 'processing') {
                return back()->with('error', 'Hanya pesanan yang diproses (sudah dibayar) yang bisa dikirim.');
            }
            if (empty($request->shipping_tracking_number)) {
                return back()->with('error', 'Nomor resi pengiriman wajib diisi saat mengubah status menjadi Dikirim.');
            }
            $order->shipping_tracking_number = $request->shipping_tracking_number;
            $order->shipped_at = $order->shipped_at ?? now();
        }

        if ($request->status == 'cancelled') {
            // Ideally: trigger refund if paid. For now, simple cancel mechanism.
        }

        $order->status = $request->status;
        $order->save();

        // Notify buyer when order is shipped
        if ($request->status === 'shipped') {
            try {
                $buyer = $order->buyer ?? null;
                if ($buyer) {
                    $resi = $order->shipping_tracking_number ?? '-';
                    $buyer->notify(new OrderNotification(
                        'order_shipped',
                        '🚚 Pesanan Sedang Dikirim!',
                        'Pesanan '.$order->invoice_number.' sudah dikirim dengan nomor resi: '.$resi.'. Pantau pengiriman Anda.',
                        route('buyer.orders.show', $order->id),
                        '📦'
                    ));
                }
            } catch (\Exception $e) {
            }
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui menjadi '.$order->status_label);
    }

    public function markDelivered(Request $request, $id)
    {
        $store = $this->getStore();
        $order = Order::with('buyer')
            ->where('store_id', $store->id)
            ->findOrFail($id);

        $request->validate([
            'delivery_confirmation_note' => 'nullable|string|max:500',
            'delivery_proofs' => 'required|array|min:1|max:6',
            'delivery_proofs.*' => 'image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        if ($order->status !== 'shipped') {
            return back()->with('error', 'Hanya pesanan yang sedang dikirim yang bisa ditandai sampai.');
        }

        if ($order->delivered_at) {
            return back()->with('success', 'Paket sudah tercatat sampai sebelumnya.');
        }

        $order->delivered_at = now();
        $order->delivery_confirmation_note = $request->filled('delivery_confirmation_note')
            ? $request->delivery_confirmation_note
            : 'Digital Hook menandai paket sudah sampai berdasarkan konfirmasi pengiriman.';
        $proofPaths = collect($request->file('delivery_proofs', []))
            ->map(fn ($file) => $file->store('delivery-proofs', 'public'))
            ->values()
            ->all();

        $order->delivery_proof_paths = $proofPaths;
        $order->delivery_proof_path = $proofPaths[0] ?? null;
        $order->save();

        try {
            $buyer = $order->buyer ?? null;
            if ($buyer) {
                $hours = (int) SystemSetting::val('auto_complete_hours', 24);
                $deadline = $hours > 0
                    ? ' Jika tidak dikonfirmasi dalam '.$hours.' jam, pesanan akan otomatis selesai.'
                    : '';

                $buyer->notify(new OrderNotification(
                    'order_delivered',
                    'Paket Tercatat Sampai',
                    'Pesanan '.$order->invoice_number.' tercatat sudah sampai di alamat tujuan.'.$deadline,
                    route('buyer.orders.show', $order->id),
                    'check'
                ));
            }
        } catch (\Exception $e) {
            Log::warning('Order delivered notification failed: '.$e->getMessage(), ['order_id' => $order->id]);
        }

        return back()->with('success', 'Paket ditandai sudah sampai. Timer auto-selesai pembeli dimulai sekarang.');
    }

    public function resolveCancellation(Request $request, $id)
    {
        $store = $this->getStore();
        $order = Order::with(['items.product', 'buyer'])
            ->where('store_id', $store->id)
            ->findOrFail($id);

        $request->validate([
            'decision' => 'required|in:approve,reject',
            'cancellation_response' => 'nullable|string|max:500',
        ]);

        if ($order->status !== 'cancellation_requested') {
            return back()->with('error', 'Pesanan ini tidak memiliki permintaan pembatalan yang menunggu persetujuan.');
        }

        $response = $request->filled('cancellation_response')
            ? $request->cancellation_response
            : ($request->decision === 'approve'
                ? 'Permintaan pembatalan disetujui penjual.'
                : 'Permintaan pembatalan ditolak penjual dan pesanan akan tetap diproses.');

        try {
            DB::beginTransaction();

            if ($request->decision === 'approve') {
                $order->status = 'cancelled';
                $order->cancellation_response = $response;
                $order->cancellation_resolved_at = now();
                $order->save();

                if ($order->payment_status === 'paid') {
                    foreach ($order->items as $item) {
                        if ($item->product) {
                            $item->product->increment('stock', $item->quantity);
                            $item->product->decrement('sold_count', min($item->product->sold_count, $item->quantity));
                        }
                    }
                }
            } else {
                $order->status = 'processing';
                $order->cancellation_response = $response;
                $order->cancellation_resolved_at = now();
                $order->save();
            }

            DB::commit();

            try {
                $buyer = $order->buyer ?? null;
                if ($buyer) {
                    $approved = $request->decision === 'approve';
                    $buyer->notify(new OrderNotification(
                        $approved ? 'order_cancelled' : 'order_cancellation_rejected',
                        $approved ? 'Pesanan Dibatalkan' : 'Permintaan Pembatalan Ditolak',
                        $approved
                            ? 'Permintaan pembatalan pesanan '.$order->invoice_number.' disetujui penjual.'
                            : 'Penjual menolak pembatalan pesanan '.$order->invoice_number.' dan akan melanjutkan proses pengiriman.',
                        route('buyer.orders.show', $order->id),
                        $approved ? 'x' : '!'
                    ));
                }
            } catch (\Exception $e) {
                Log::warning('Order cancellation resolution notification failed: '.$e->getMessage(), ['order_id' => $order->id]);
            }

            $message = $request->decision === 'approve'
                ? 'Permintaan pembatalan disetujui. Pesanan sudah dibatalkan.'
                : 'Permintaan pembatalan ditolak. Pesanan kembali ke status diproses.';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation resolution error: '.$e->getMessage(), ['order_id' => $id]);

            return back()->with('error', 'Terjadi kesalahan saat memproses permintaan pembatalan. Silakan coba lagi.');
        }
    }
}
