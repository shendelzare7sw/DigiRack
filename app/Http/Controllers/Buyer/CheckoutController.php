<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\City;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Province;
use App\Models\SystemSetting;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    /**
     * Beli Langsung
     */
    public function init(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);
        
        if ($product->price <= 0) {
            return back()->with('error', 'Produk ini tidak dapat dibeli karena harganya tidak valid (Mengandung unsur kerugian/Rp 0).');
        }
        
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cartItem = Cart::where('user_id', Auth::id())->where('product_id', $product->id)->first();
        if ($cartItem) {
            $cartItem->update(['quantity' => $request->quantity]);
        } else {
            $cartItem = Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        $request->merge(['selected_items' => [$cartItem->id]]);
        return $this->index($request);
    }

    /**
     * Tampilkan halaman checkout.
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

        // Get user saved addresses (with city_id for ongkir)
        $addresses = Auth::user()->addresses()->orderByDesc('is_primary')->get();

        // If no addresses, redirect to profile to create one
        if ($addresses->isEmpty()) {
            return redirect()->route('profile.edit')
                ->with('error', 'Anda belum memiliki alamat pengiriman. Silakan tambahkan alamat terlebih dahulu.')
                ->withFragment('address-section');
        }

        $storesData = [];
        $totalPrice = 0;

        foreach ($cartItems as $item) {
            $storeId = $item->product->store_id;
            if (!isset($storesData[$storeId])) {
                $customCouriers = \App\Models\StoreCourier::where('store_id', $storeId)->where('is_active', true)->get();
                $storesData[$storeId] = [
                    'store' => $item->product->store,
                    'items' => [],
                    'totalWeight' => 0,
                    'subtotal' => 0,
                    'custom_couriers' => $customCouriers
                ];
            }
            $storesData[$storeId]['items'][] = $item;
            $storesData[$storeId]['totalWeight'] += ($item->product->weight_gram * $item->quantity);
            $storesData[$storeId]['subtotal'] += ($item->product->price * $item->quantity);
            $totalPrice += ($item->product->price * $item->quantity);
        }

        $buyerFees = \App\Models\BuyerTransactionFee::where('is_active', true)->get();
        $totalBuyerFees = $buyerFees->sum('amount');

        // Check if Midtrans is configured
        $midtransReady = !empty(SystemSetting::val('midtrans_server_key', env('MIDTRANS_SERVER_KEY')));

        return view('buyer.checkout.index', compact(
            'storesData', 'totalPrice', 'selectedItems', 'buyerFees', 'totalBuyerFees',
            'addresses', 'midtransReady'
        ));
    }

    /**
     * Proses pembuatan Order dari form Checkout, lalu get SNAP Token Midtrans atau manual transfer.
     */
    public function process(Request $request, RajaOngkirService $rajaOngkir)
    {
        $request->validate([
            'selected_items' => 'required|string',
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:midtrans',
            'couriers' => 'required|array',
        ]);

        // Verify address belongs to user
        $address = Address::where('user_id', Auth::id())->findOrFail($request->address_id);

        $selectedItemIds = json_decode($request->selected_items, true);

        if (!is_array($selectedItemIds) || empty($selectedItemIds)) {
            return redirect()->route('buyer.cart.index')->with('error', 'Data item tidak valid.');
        }

        $cartItems = Cart::with(['product.store'])->where('user_id', Auth::id())->whereIn('id', $selectedItemIds)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart.index')->with('error', 'Item keranjang tidak ditemukan.');
        }

        // Group by Store
        $storesData = [];
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('buyer.cart.index')->with('error', 'Stok untuk produk ' . $item->product->name . ' tidak mencukupi.');
            }
            if ($item->product->price <= 0) {
                return redirect()->route('buyer.cart.index')->with('error', 'Terdapat produk dengan harga tidak valid (Rp 0).');
            }
            
            $storeId = $item->product->store_id;
            if (!isset($storesData[$storeId])) {
                $storesData[$storeId] = [
                    'store' => $item->product->store,
                    'items' => [],
                    'totalWeight' => 0,
                    'subtotal' => 0,
                ];
            }
            $storesData[$storeId]['items'][] = $item;
            $storesData[$storeId]['totalWeight'] += ($item->product->weight_gram * $item->quantity);
            $storesData[$storeId]['subtotal'] += ($item->product->price * $item->quantity);
        }

        // Build full address string
        $fullAddress = $address->full_address . ', ' . $address->city . ', ' . $address->province . ', ' . $address->postal_code;

        $paymentReference = 'PAY/' . date('Ymd') . '/' . strtoupper(uniqid());
        $grandTotalGross = 0;
        $createdOrders = [];
        $paymentMethod = $request->payment_method;

        try {
            DB::beginTransaction();

            $index = 1;
            foreach ($storesData as $storeId => $data) {
                $courierCode = $request->couriers[$storeId] ?? 'jne';
                $shippingCost = 0;

                if (str_starts_with($courierCode, 'toko_')) {
                    $courierId = explode('_', $courierCode)[1];
                    $storeCourier = \App\Models\StoreCourier::find($courierId);
                    if ($storeCourier) {
                        $shippingCost = (int) $storeCourier->price;
                        $courierCode = 'toko_' . $storeCourier->name;
                    }
                } else {
                    $originCityId = $data['store']->city_id ?? 153;
                    $destCityId = $address->city_id ?? 153;
                    $ongkirResponse = $rajaOngkir->getCost($originCityId, $destCityId, $data['totalWeight'], $courierCode);
                    
                    if (isset($ongkirResponse['rajaongkir']['results'][0]['costs'][0]['cost'][0]['value'])) {
                        $shippingCost = $ongkirResponse['rajaongkir']['results'][0]['costs'][0]['cost'][0]['value'];
                    } else {
                        $shippingCost = 25000; // Fallback
                    }
                }

                $totalOrderPrice = $data['subtotal'] + $shippingCost;
                $grandTotalGross += $totalOrderPrice;

                if ($totalOrderPrice <= 0) {
                    throw new \Exception("Pesanan ditolak karena total akhir Rp 0.");
                }

                $appliedFeesJson = null;
                if ($index === 1) {
                    $buyerFees = \App\Models\BuyerTransactionFee::where('is_active', true)->get();
                    if ($buyerFees->isNotEmpty()) {
                        $feeRecords = [];
                        foreach ($buyerFees as $fee) {
                            $grandTotalGross += $fee->amount;
                            $feeRecords[] = ['name' => $fee->name, 'amount' => $fee->amount];
                        }
                        $appliedFeesJson = json_encode($feeRecords);
                    }
                }

                $order = Order::create([
                    'invoice_number' => 'INV/' . date('Ymd') . '/' . strtoupper(uniqid()) . '-' . $index,
                    'buyer_id' => Auth::id(),
                    'store_id' => $storeId,
                    'status' => 'pending_payment',
                    'total_price' => $totalOrderPrice,
                    'shipping_cost' => $shippingCost,
                    'payment_method' => $paymentMethod === 'midtrans' ? 'transfer' : 'transfer',
                    'payment_status' => 'unpaid',
                    'payment_reference' => $paymentReference,
                    'shipping_address' => [
                        'name' => $address->recipient_name,
                        'phone' => $address->phone,
                        'full_address' => $fullAddress,
                        'courier' => strtoupper($courierCode),
                        'destination_city_id' => $address->city_id
                    ],
                    'applied_buyer_fees' => $appliedFeesJson ? json_decode($appliedFeesJson, true) : null
                ]);

                foreach ($data['items'] as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product->id,
                        'product_name_snapshot' => $item->product->name,
                        'price_snapshot' => $item->product->price,
                        'quantity' => $item->quantity,
                    ]);

                    Cart::destroy($item->id);
                }

                $createdOrders[] = $order;
                $index++;
            }

            // --- Payment Method Handling ---
            if ($paymentMethod === 'midtrans') {
                $serverKey = SystemSetting::val('midtrans_server_key', env('MIDTRANS_SERVER_KEY'));
                
                if (empty($serverKey)) {
                    // Midtrans not configured - fallback to manual
                    DB::commit();
                    $order = $createdOrders[0];
                    return view('buyer.checkout.manual-transfer', compact('order', 'grandTotalGross', 'paymentReference'));
                }

                $isProduction = SystemSetting::val('midtrans_is_production', env('MIDTRANS_IS_PRODUCTION', false)) === 'true';
                Config::$serverKey = $serverKey;
                Config::$isProduction = $isProduction;
                Config::$isSanitized = true;
                Config::$is3ds = true;

                $params = [
                    'transaction_details' => [
                        'order_id' => $paymentReference,
                        'gross_amount' => $grandTotalGross,
                    ],
                    'customer_details' => [
                        'first_name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                        'phone' => Auth::user()->phone ?? $address->phone,
                    ],
                    'callbacks' => [
                        'finish' => route('buyer.orders.index') . '?payment=success',
                    ],
                ];

                $snapToken = Snap::getSnapToken($params);
                
                foreach ($createdOrders as $ord) {
                    $ord->update(['payment_token' => $snapToken]);
                }

                DB::commit();
                $order = $createdOrders[0];
                return view('buyer.checkout.payment', compact('order', 'snapToken', 'grandTotalGross', 'paymentReference'));

            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('buyer.cart.index')->with('error', 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi atau hubungi admin.');
        }
    }

    /**
     * Upload bukti transfer manual
     */
    public function uploadProof(Request $request, $orderId)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $order = Order::where('buyer_id', Auth::id())->findOrFail($orderId);

        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Pembayaran sudah diverifikasi sebelumnya.');
        }

        // Store the file
        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        // Delete old proof if exists
        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $order->update(['payment_proof' => $path]);

        // Also update all orders with same payment_reference
        if ($order->payment_reference) {
            Order::where('payment_reference', $order->payment_reference)
                ->where('id', '!=', $order->id)
                ->update(['payment_proof' => $path]);
        }

        return redirect()->route('buyer.orders.show', $order->id)
            ->with('success', 'Bukti transfer berhasil diunggah. Menunggu verifikasi dari penjual/admin.');
    }
}
