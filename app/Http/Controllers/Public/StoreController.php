<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Product;
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
        $store = Store::withCount('products')->where('slug', $slug)->firstOrFail();

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

        return view('public.seller.storefront', compact('store', 'products', 'storeProductCount', 'sort'));
    }
}
