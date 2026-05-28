<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    /**
     * Tampilkan halaman etalase publik toko.
     * @param string $slug
     */
    public function show($slug, Request $request)
    {
        $store = Store::withCount([
            'products' => fn ($query) => $query->where('status', 'active'),
            'reviews',
        ])->where('slug', $slug)->firstOrFail();

        // Public visitors cannot open pending/banned stores. Admins may preview a store from the review page.
        if (!$store->is_active && ! Auth::user()?->isAdmin()) {
            abort(404, 'Toko ini sedang tidak aktif.');
        }

        // Query Products
        $query = Product::with(['category', 'primaryImage', 'flashSale'])
            ->where('store_id', $store->id)
            ->where('status', 'active');

        // Sorting
        $sort = $request->query('sort', 'latest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'popular') {
            $query->orderBy('sold_count', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(24)->appends($request->query());

        // Ambil store products count from db
        $storeProductCount = Product::where('store_id', $store->id)->where('status', 'active')->count();
        $storeReviews = $store->reviews()
            ->with('buyer')
            ->latest()
            ->take(2)
            ->get();

        $wishlistIds = [];
        if (Auth::check()) {
            $wishlistIds = Wishlist::where('user_id', Auth::id())
                ->pluck('product_id')
                ->toArray();
        }

        return view('public.seller.storefront', compact('store', 'products', 'storeProductCount', 'storeReviews', 'sort', 'wishlistIds'));
    }

    public function reviews($slug, Request $request)
    {
        $store = Store::withCount(['products', 'reviews'])->where('slug', $slug)->firstOrFail();

        if (!$store->is_active && ! Auth::user()?->isAdmin()) {
            abort(404, 'Toko ini sedang tidak aktif.');
        }

        $ratingCounts = $store->reviews()
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->all();

        $reviewQuery = $store->reviews()
            ->with(['buyer', 'order'])
            ->latest();

        if ($request->filled('rating') && in_array((int) $request->rating, [1, 2, 3, 4, 5], true)) {
            $reviewQuery->where('rating', (int) $request->rating);
        }

        $storeReviews = $reviewQuery->paginate(12)->withQueryString();

        return view('public.seller.reviews', compact('store', 'storeReviews', 'ratingCounts'));
    }
}
