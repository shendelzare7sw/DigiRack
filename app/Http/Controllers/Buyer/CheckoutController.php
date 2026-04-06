<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\City;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Province;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    /**
     * Beli Langsung (Add to cart & go to checkout with only this item selected)
     */
    public function init(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cartItem = Cart::where('user_id', Auth::id())->where('product_id', $product->id)->first();
        if ($cartItem) {
            $cartItem->update(['quantity' => $request->quantity]); // Setel ulang sesuai yang diminta dari Beli Langsung
        } else {
            $cartItem = Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        // Lanjut ke index checkout seolah form dikirim
        $request->merge(['selected_items' => [$cartItem->id]]);
        return $this->index($request);
    }

    /**
     * Tampilkan halaman checkout berdasarkan item yang dipilih dari keranjang.
     */
    public function index(Request $request)
    {
        $selectedItems = $request->input('selected_items', []);

        if (empty($selectedItems)) {
            return redirect()->route('buyer.cart.index')->with('error', 'Pilih minimal satu produk untuk di-checkout.');
        }

        $cartItems = Cart::with(['product.store', 'product.category'])
            ->where('user_id', Auth::id())
            ->whereIn('id', $selectedItems)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart.index')->with('error', 'Item tidak ditemukan.');
        }

        // Hitung total
        $totalPrice = 0;
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $totalPrice += ($item->product->price * $item->quantity);
            $totalWeight += ($item->product->weight_gram * $item->quantity);
        }

        // Ambil data untuk dropdown alamat
        $provinces = Province::orderBy('name')->get();

        return view('buyer.checkout.index', compact('cartItems', 'totalPrice', 'totalWeight', 'provinces', 'selectedItems'));
    }

    /**
     * Proses pembuatan Order dari form Checkout, lalu get SNAP Token Midtrans.
     */
    public function process(Request $request)
    {
        $request->validate([
            'selected_items' => 'required|string',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string',
            'courier' => 'required|string',
        ]);

        $selectedItemIds = json_decode($request->selected_items, true);

        if (!is_array($selectedItemIds) || empty($selectedItemIds)) {
            return redirect()->route('buyer.cart.index')->with('error', 'Data item tidak valid.');
        }

        $cartItems = Cart::with(['product'])->where('user_id', Auth::id())->whereIn('id', $selectedItemIds)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart.index')->with('error', 'Item keranjang tidak ditemukan.');
        }

        // Ambil info kota & provinsi untuk disimpan statis di order (agar tidak berubah jika master kota dihapus)
        $city = City::with('province')->find($request->city_id);
        $fullAddress = $request->address . ', ' . $city->type . ' ' . $city->name . ', ' . $city->province->name . ', ' . $request->postal_code;

        try {
            DB::beginTransaction();

            $totalPrice = 0;
            foreach ($cartItems as $item) {
                // Verifikasi stok akhir (race condition protection)
                if ($item->product->stock < $item->quantity) {
                    throw new \Exception('Stok untuk produk ' . $item->product->name . ' tidak mencukupi.');
                }
                $totalPrice += ($item->product->price * $item->quantity);
            }

            // Simulasi ongkir flat untuk dummy data
            $shippingCost = 25000;
            $grandTotal = $totalPrice + $shippingCost;

            // Buat master order
            $order = Order::create([
                'invoice_number' => 'INV/' . date('Ymd') . '/' . strtoupper(uniqid()),
                'buyer_id' => Auth::id(),
                'status' => 'pending', // unpaid
                'total_price' => $grandTotal,
                'shipping_cost' => $shippingCost,
                'payment_method' => 'midtrans',
                'payment_status' => 'unpaid',
                'shipping_address' => json_encode([
                    'name' => $request->recipient_name,
                    'phone' => $request->phone,
                    'full_address' => $fullAddress,
                    'courier' => $request->courier,
                ])
            ]);

            // Buat order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'subtotal' => $item->product->price * $item->quantity,
                ]);

                // Hapus item dari keranjang
                Cart::destroy($item->id);
            }

            // Setup Midtrans
            $serverKey = SystemSetting::val('midtrans_server_key', env('MIDTRANS_SERVER_KEY'));
            $isProduction = SystemSetting::val('midtrans_is_production', env('MIDTRANS_IS_PRODUCTION', false)) === 'true';

            Config::$serverKey = $serverKey;
            Config::$isProduction = $isProduction;
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $order->invoice_number,
                    'gross_amount' => $grandTotal,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'phone' => Auth::user()->phone ?? $request->phone,
                ],
            ];

            // Dapatkan Snap Token Midtrans
            $snapToken = Snap::getSnapToken($params);
            $order->update(['payment_token' => $snapToken]);

            DB::commit();

            return view('buyer.checkout.payment', compact('order', 'snapToken'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('buyer.cart.index')->with('error', $e->getMessage());
        }
    }
}
