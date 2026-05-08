<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Store;

class ProductModerationController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['store', 'category', 'primaryImage']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by store
        if ($request->filled('store')) {
            $query->where('store_id', $request->store);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        $categories = Category::orderBy('name')->get();
        $stores = Store::orderBy('name')->get();

        // Stats
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $inactiveProducts = Product::where('status', 'inactive')->count();
        $outOfStock = Product::where('stock', 0)->count();

        return view('admin.products.index', compact(
            'products', 'categories', 'stores',
            'totalProducts', 'activeProducts', 'inactiveProducts', 'outOfStock'
        ));
    }

    public function show($id)
    {
        $product = Product::with(['store', 'category', 'images', 'reviews.buyer'])->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();

        $label = $product->status === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Produk \"{$product->name}\" berhasil {$label}.");
    }
}
