<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
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

        $provinces = Province::orderBy('name')->get();
        $buyerFees = \App\Models\BuyerTransactionFee::where('is_active', true)->get();
        $totalBuyerFees = $buyerFees->sum('amount');

        return view('buyer.checkout.index', compact('storesData', 'totalPrice', 'provinces', 'selectedItems', 'buyerFees', 'totalBuyerFees'));
    }

    /**
     * Proses pembuatan Order dari form Checkout, lalu get SNAP Token Midtrans.
     */
    public function process(Request $request, RajaOngkirService $rajaOngkir)
    {
        $request->validate([
            'selected_items' => 'required|string',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string',
            'couriers' => 'required|array', // key: store_id, value: courier string (e.g. "jne")
        ]);

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
            // Verifikasi stok dan harga valid
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('buyer.cart.index')->with('error', 'Stok untuk produk ' . $item->product->name . ' tidak mencukupi.');
            }
            if ($item->product->price <= 0) {
                return redirect()->route('buyer.cart.index')->with('error', 'Terdapat produk dengan harga tidak valid (Rp 0). Harap lepas centang produk ini.');
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

        $city = City::with('province')->find($request->city_id);
        $fullAddress = $request->address . ', ' . $city->type . ' ' . $city->name . ', ' . $city->province->name . ', ' . $request->postal_code;

        $paymentReference = 'PAY/' . date('Ymd') . '/' . strtoupper(uniqid());
        $grandTotalGross = 0;
        $createdOrders = [];

        try {
            DB::beginTransaction();

            $index = 1;
            foreach ($storesData as $storeId => $data) {
                // Determine courier
                $courierCode = $request->couriers[$storeId] ?? 'jne';
                
                // Calculate actual shipping cost
                $shippingCost = 0;

                if (str_starts_with($courierCode, 'toko_')) {
                    $courierId = explode('_', $courierCode)[1];
                    $storeCourier = \App\Models\StoreCourier::find($courierId);
                    if ($storeCourier) {
                        $shippingCost = (int) $storeCourier->price;
                        $courierCode = 'toko_' . $storeCourier->name;
                    }
                } else {
                    $originCityId = $data['store']->city_id ?? 153; // default jakarta selatan if not set
                    $ongkirResponse = $rajaOngkir->getCost($originCityId, $city->id, $data['totalWeight'], $courierCode);
                    
                    if (isset($ongkirResponse['rajaongkir']['results'][0]['costs'][0]['cost'][0]['value'])) {
                        $shippingCost = $ongkirResponse['rajaongkir']['results'][0]['costs'][0]['cost'][0]['value'];
                    } else {
                        // Fallback
                        $shippingCost = 25000;
                    }
                }

                $totalOrderPrice = $data['subtotal'] + $shippingCost;
                $grandTotalGross += $totalOrderPrice;

                if ($totalOrderPrice <= 0) {
                    throw new \Exception("Pesanan ditolak karena total akhir Rp 0. Tidak ada nilai transaksi yang valid.");
                }

                // Check active buyer fees only on the first order to avoid duplicate charging
                $appliedFeesJson = null;
                if ($index === 1) {
                    $buyerFees = \App\Models\BuyerTransactionFee::where('is_active', true)->get();
                    if ($buyerFees->isNotEmpty()) {
                        $feeRecords = [];
                        foreach ($buyerFees as $fee) {
                            $grandTotalGross += $fee->amount;
                            $feeRecords[] = [
                                'name' => $fee->name,
                                'amount' => $fee->amount
                            ];
                        }
                        $appliedFeesJson = json_encode($feeRecords);
                    }
                }

                // Create Order
                $order = Order::create([
                    'invoice_number' => 'INV/' . date('Ymd') . '/' . strtoupper(uniqid()) . '-' . $index,
                    'buyer_id' => Auth::id(),
                    'store_id' => $storeId,
                    'status' => 'pending_payment',
                    'total_price' => $totalOrderPrice,
                    'shipping_cost' => $shippingCost,
                    'payment_method' => 'midtrans',
                    'payment_status' => 'unpaid',
                    'payment_reference' => $paymentReference,
                    'shipping_address' => [
                        'name' => $request->recipient_name,
                        'phone' => $request->phone,
                        'full_address' => $fullAddress,
                        'courier' => strtoupper($courierCode),
                        'destination_city_id' => $city->id
                    ],
                    'applied_buyer_fees' => $appliedFeesJson ? json_decode($appliedFeesJson, true) : null
                ]);

                // Create Order Items
                foreach ($data['items'] as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product->id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'subtotal' => $item->product->price * $item->quantity,
                    ]);

                    Cart::destroy($item->id);
                }

                $createdOrders[] = $order;
                $index++;
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
                    'order_id' => $paymentReference, // Use single token for all grouped orders
                    'gross_amount' => $grandTotalGross,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'phone' => Auth::user()->phone ?? $request->phone,
                ],
            ];

            // Get Snap Token
            $snapToken = Snap::getSnapToken($params);
            
            // Save token to all created orders
            foreach ($createdOrders as $ord) {
                $ord->update(['payment_token' => $snapToken]);
            }

            DB::commit();

            // Pass the first order just to render the view
            $order = $createdOrders[0];
            return view('buyer.checkout.payment', compact('order', 'snapToken', 'grandTotalGross', 'paymentReference'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('buyer.cart.index')->with('error', $e->getMessage());
        }
    }
}
