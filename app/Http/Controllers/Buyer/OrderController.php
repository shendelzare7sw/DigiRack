<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product.store')
            ->where('buyer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('buyer.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['items.product.store', 'store'])->where('buyer_id', Auth::id())->findOrFail($id);
        return view('buyer.orders.show', compact('order'));
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

            // Hanya subtotal nilai produk yang masuk ke wallet
            // Total price minus shipping cost
            $productSubtotal = $order->total_price - $order->shipping_cost;

            $courier = $order->shipping_address['courier'] ?? '';
            $shippingToSeller = 0;
            if (str_starts_with(strtolower($courier), 'toko_')) {
                $shippingToSeller = $order->shipping_cost;
            }

            // Potongan platform per-item
            $totalQty = $order->items->sum('quantity');
            $feePerItem = \App\Models\SystemSetting::val('platform_fee_per_item', 0);
            $totalPlatformFee = $totalQty * $feePerItem;
            
            $netToSeller = $productSubtotal + $shippingToSeller - $totalPlatformFee;
            if ($netToSeller < 0) {
                 $netToSeller = 0;
            }

            if ($netToSeller > 0) {
                // Cari atau buat wallet untuk toko
                $wallet = Wallet::firstOrCreate(
                    ['store_id' => $order->store_id],
                    ['balance' => 0]
                );

                // Tambah balance
                $wallet->balance += $netToSeller;
                $wallet->save();

                // Catat mutasi
                $desc = 'Penerimaan dana pesanan ' . $order->invoice_number;
                if ($shippingToSeller > 0) {
                    $desc .= ' (+Ongkir Internal: Rp' . number_format($shippingToSeller, 0, ',', '.') . ')';
                }
                if ($totalPlatformFee > 0) {
                    $desc .= ' (Dipotong Fee Platform: Rp' . number_format($totalPlatformFee, 0, ',', '.') . ')';
                }

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'credit',
                    'amount' => $netToSeller,
                    'reference' => 'ORDER-' . $order->id,
                    'description' => $desc,
                ]);
            }

            DB::commit();

            return back()->with('success', 'Pesanan telah selesai! Dana telah diteruskan ke penjual. Jangan lupa berikan ulasan Anda!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal merubah status pesanan: ' . $e->getMessage());
        }
    }
}
