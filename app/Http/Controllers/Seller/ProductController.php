<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * List semua produk milik seller
     */
    public function index(Request $request)
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'Anda belum memiliki toko. Silakan buat toko terlebih dahulu.');
        }

        $query = Product::with(['category', 'primaryImage'])
            ->where('store_id', $store->id);

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // Stats
        $stats = [
            'total' => Product::where('store_id', $store->id)->count(),
            'active' => Product::where('store_id', $store->id)->where('status', 'active')->count(),
            'inactive' => Product::where('store_id', $store->id)->where('status', '!=', 'active')->count(),
            'lowStock' => Product::where('store_id', $store->id)->where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'outOfStock' => Product::where('store_id', $store->id)->where('stock', 0)->count(),
        ];

        return view('seller.products.index', compact('products', 'categories', 'stats', 'store'));
    }

    /**
     * Form tambah produk
     */
    public function create()
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'Anda belum memiliki toko.');
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('seller.products.create', compact('categories'));
    }

    /**
     * Simpan produk baru
     */
    public function store(Request $request)
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('seller.dashboard')->with('error', 'Anda belum memiliki toko.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:20',
            'price' => 'required|integer|min:1000',
            'stock' => 'required|integer|min:0',
            'weight_gram' => 'required|integer|min:1',
            'condition' => 'required|in:new,used',
            'specs' => 'nullable|array',
            'specs.*.label' => 'nullable|string|max:100',
            'specs.*.value' => 'nullable|string|max:255',
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Generate slug
        $slug = Str::slug($validated['name']);
        $existingSlug = Product::where('slug', 'like', $slug . '%')->count();
        if ($existingSlug > 0) {
            $slug .= '-' . ($existingSlug + 1);
        }

        // Filter empty specs
        $specs = [];
        if (!empty($validated['specs'])) {
            foreach ($validated['specs'] as $spec) {
                if (!empty($spec['label']) && !empty($spec['value'])) {
                    $specs[] = ['label' => $spec['label'], 'value' => $spec['value']];
                }
            }
        }

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'weight_gram' => $validated['weight_gram'],
            'condition' => $validated['condition'],
            'specs' => $specs,
            'status' => 'active',
        ]);

        // Upload images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products/' . $store->id, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk "' . $product->name . '" berhasil ditambahkan!');
    }

    /**
     * Form edit produk
     */
    public function edit($id)
    {
        $store = Auth::user()->store;
        $product = Product::with('images')->where('store_id', $store->id)->findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('seller.products.edit', compact('product', 'categories'));
    }

    /**
     * Update produk
     */
    public function update(Request $request, $id)
    {
        $store = Auth::user()->store;
        $product = Product::where('store_id', $store->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:20',
            'price' => 'required|integer|min:1000',
            'stock' => 'required|integer|min:0',
            'weight_gram' => 'required|integer|min:1',
            'condition' => 'required|in:new,used',
            'status' => 'required|in:active,inactive,draft',
            'specs' => 'nullable|array',
            'specs.*.label' => 'nullable|string|max:100',
            'specs.*.value' => 'nullable|string|max:255',
            'new_images' => 'nullable|array|max:5',
            'new_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id',
        ]);

        // Filter specs
        $specs = [];
        if (!empty($validated['specs'])) {
            foreach ($validated['specs'] as $spec) {
                if (!empty($spec['label']) && !empty($spec['value'])) {
                    $specs[] = ['label' => $spec['label'], 'value' => $spec['value']];
                }
            }
        }

        // Update slug if name changed
        $slug = $product->slug;
        if ($product->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $existingSlug = Product::where('slug', 'like', $slug . '%')->where('id', '!=', $product->id)->count();
            if ($existingSlug > 0) {
                $slug .= '-' . ($existingSlug + 1);
            }
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'weight_gram' => $validated['weight_gram'],
            'condition' => $validated['condition'],
            'status' => $validated['status'],
            'specs' => $specs,
        ]);

        // Delete images
        if (!empty($validated['delete_images'])) {
            $imagesToDelete = ProductImage::whereIn('id', $validated['delete_images'])
                ->where('product_id', $product->id)
                ->get();

            foreach ($imagesToDelete as $img) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }
        }

        // Upload new images
        if ($request->hasFile('new_images')) {
            $maxOrder = $product->images()->max('sort_order') ?? -1;
            $hasPrimary = $product->images()->where('is_primary', true)->exists();

            foreach ($request->file('new_images') as $index => $image) {
                $path = $image->store('products/' . $store->id, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => !$hasPrimary && $index === 0,
                    'sort_order' => $maxOrder + $index + 1,
                ]);
            }
        }

        // Ensure there's a primary image
        if (!$product->images()->where('is_primary', true)->exists()) {
            $firstImage = $product->images()->orderBy('sort_order')->first();
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk "' . $product->name . '" berhasil diperbarui!');
    }

    /**
     * Hapus produk
     */
    public function destroy($id)
    {
        $store = Auth::user()->store;
        $product = Product::where('store_id', $store->id)->findOrFail($id);

        // Delete images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk "' . $productName . '" berhasil dihapus.');
    }
}
