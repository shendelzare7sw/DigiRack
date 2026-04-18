<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Get the authenticated user's store.
     */
    protected function getStore()
    {
        return Auth::user()->store;
    }

    public function index(Request $request)
    {
        $store = $this->getStore();
        if (!$store) {
            return redirect()->route('dashboard')->with('error', 'Anda harus membuka toko terlebih dahulu.');
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
        $order = Order::with(['buyer', 'items.product'])->where('store_id', $store->id)->findOrFail($id);

        return view('seller.orders.show', compact('order', 'store'));
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
        if ($order->status == 'completed' || $order->status == 'cancelled') {
            return back()->with('error', 'Pesanan yang sudah selesai atau dibatalkan tidak dapat diubah statusnya.');
        }

        if ($request->status == 'shipped') {
            if ($order->status != 'processing') {
                return back()->with('error', 'Hanya pesanan yang diproses (sudah dibayar) yang bisa dikirim.');
            }
            if (empty($request->shipping_tracking_number)) {
                return back()->with('error', 'Nomor resi pengiriman wajib diisi saat mengubah status menjadi Dikirim.');
            }
            $order->shipping_tracking_number = $request->shipping_tracking_number;
        }

        if ($request->status == 'cancelled') {
            // Ideally: trigger refund if paid. For now, simple cancel mechanism.
        }

        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Status pesanan berhasil diperbarui menjadi ' . $order->status_label);
    }
}
