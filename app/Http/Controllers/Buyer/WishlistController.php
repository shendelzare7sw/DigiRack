<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Halaman Wishlist
     * GET /buyer/wishlist
     */
    public function index()
    {
        $wishlists = Wishlist::with(['product.store', 'product.category', 'product.primaryImage', 'product.flashSale'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('buyer.wishlist.index', compact('wishlists'));
    }

    /**
     * Toggle Wishlist (tambah/hapus)
     * POST /buyer/wishlist/toggle
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::with('store')->findOrFail($request->product_id);

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($product->isOwnedBy($request->user())) {
            if ($existing) {
                $existing->delete();
            }

            $wishlistCount = Wishlist::where('user_id', Auth::id())->count();

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'blocked',
                    'message' => 'Produk milik toko sendiri tidak bisa dimasukkan ke wishlist.',
                    'wishlistCount' => $wishlistCount,
                ], 403);
            }

            return back()->with('error', 'Produk milik toko sendiri tidak bisa dimasukkan ke wishlist.');
        }

        if ($existing) {
            $existing->delete();
            $status = 'removed';
            $message = 'Produk dihapus dari wishlist.';
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
            ]);
            $status = 'added';
            $message = 'Produk ditambahkan ke wishlist!';
        }

        $wishlistCount = Wishlist::where('user_id', Auth::id())->count();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'wishlistCount' => $wishlistCount,
            ]);
        }

        return back()->with('success', $message);
    }
}
