<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Halaman Keranjang Belanja
     * GET /buyer/cart
     */
    public function index()
    {
        $cartItems = Cart::with(['product.store', 'product.primaryImage', 'product.flashSale', 'product.category'])
            ->where('user_id', Auth::id())
            ->get();

        // Kelompokkan per toko
        $grouped = $cartItems->groupBy(function ($item) {
            return $item->product->store_id;
        });

        // Total keseluruhan
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
        $totalItems = $cartItems->sum('quantity');

        return view('buyer.cart.index', compact('grouped', 'totalPrice', 'totalItems', 'cartItems'));
    }

    /**
     * Tambah Produk ke Keranjang
     * POST /buyer/cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::with('store')->findOrFail($request->product_id);

        if ($product->isOwnedBy($request->user())) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Produk milik toko sendiri tidak bisa dimasukkan ke keranjang.'], 403);
            }

            return back()->with('error', 'Produk milik toko sendiri tidak bisa dimasukkan ke keranjang.');
        }

        // Cek stok
        if ($product->stock < $request->quantity) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock], 422);
            }
            return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock);
        }

        // Cek apakah sudah ada di cart
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Total di keranjang melebihi stok. Stok tersedia: ' . $product->stock], 422);
                }
                return back()->with('error', 'Total di keranjang melebihi stok.');
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke keranjang!',
                'cartCount' => $cartCount,
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    /**
     * Update Quantity
     * PATCH /buyer/cart/{id}
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);
        $product = $cartItem->product;

        if ($product->isOwnedBy($request->user())) {
            $cartItem->delete();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Produk milik toko sendiri dihapus dari keranjang.'], 403);
            }

            return back()->with('error', 'Produk milik toko sendiri tidak bisa ada di keranjang.');
        }

        if ($request->quantity > $product->stock) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock], 422);
            }
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
        $subtotal = $product->price * $request->quantity;
        $totalPrice = Cart::where('user_id', Auth::id())->get()->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Kuantitas berhasil diperbarui.',
                'cartCount' => $cartCount,
                'subtotal' => $subtotal,
                'formattedSubtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                'totalPrice' => $totalPrice,
                'formattedTotal' => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
            ]);
        }

        return back()->with('success', 'Kuantitas berhasil diperbarui.');
    }

    /**
     * Hapus Item dari Keranjang
     * DELETE /buyer/cart/{id}
     */
    public function destroy(Request $request, $id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->delete();

        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
        $totalPrice = Cart::where('user_id', Auth::id())->get()->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Produk dihapus dari keranjang.',
                'cartCount' => $cartCount,
                'totalPrice' => $totalPrice,
                'formattedTotal' => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
            ]);
        }

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
