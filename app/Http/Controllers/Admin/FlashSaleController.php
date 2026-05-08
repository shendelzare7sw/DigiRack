<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlashSale;
use App\Models\Product;

class FlashSaleController extends Controller
{
    public function index(Request $request)
    {
        $query = FlashSale::with(['product.store', 'product.primaryImage']);

        // Filter by status
        if ($request->filled('filter')) {
            $now = now();
            match ($request->filter) {
                'ongoing' => $query->where('is_active', true)
                                   ->where('start_time', '<=', $now)
                                   ->where('end_time', '>=', $now),
                'upcoming' => $query->where('start_time', '>', $now),
                'expired' => $query->where('end_time', '<', $now),
                'inactive' => $query->where('is_active', false),
                default => null,
            };
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $flashSales = $query->latest()->paginate(15)->withQueryString();

        // Stats
        $now = now();
        $totalFlashSales = FlashSale::count();
        $ongoingCount = FlashSale::where('is_active', true)->where('start_time', '<=', $now)->where('end_time', '>=', $now)->count();
        $upcomingCount = FlashSale::where('start_time', '>', $now)->count();
        $expiredCount = FlashSale::where('end_time', '<', $now)->count();

        // Products for create form (exclude products already in active flash sale)
        $availableProducts = Product::where('status', 'active')
            ->whereDoesntHave('flashSale')
            ->orderBy('name')
            ->get(['id', 'name', 'price']);

        return view('admin.flash-sales.index', compact(
            'flashSales', 'totalFlashSales', 'ongoingCount', 'upcomingCount', 'expiredCount',
            'availableProducts'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'discount_percent' => 'required|integer|min:1|max:90',
            'stock_flash' => 'required|integer|min:1',
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        $product = Product::findOrFail($request->product_id);
        $salePrice = (int) round($product->price * (100 - $request->discount_percent) / 100);

        FlashSale::create([
            'product_id' => $request->product_id,
            'discount_percent' => $request->discount_percent,
            'original_price' => $product->price,
            'sale_price' => $salePrice,
            'stock_flash' => $request->stock_flash,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => true,
        ]);

        return back()->with('success', "Flash Sale untuk \"{$product->name}\" berhasil dibuat.");
    }

    public function update(Request $request, $id)
    {
        $flashSale = FlashSale::findOrFail($id);

        $request->validate([
            'discount_percent' => 'required|integer|min:1|max:90',
            'stock_flash' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $salePrice = (int) round($flashSale->original_price * (100 - $request->discount_percent) / 100);

        $flashSale->update([
            'discount_percent' => $request->discount_percent,
            'sale_price' => $salePrice,
            'stock_flash' => $request->stock_flash,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return back()->with('success', "Flash Sale berhasil diperbarui.");
    }

    public function toggleActive($id)
    {
        $flashSale = FlashSale::findOrFail($id);
        $flashSale->is_active = !$flashSale->is_active;
        $flashSale->save();

        $label = $flashSale->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Flash Sale berhasil {$label}.");
    }

    public function destroy($id)
    {
        $flashSale = FlashSale::findOrFail($id);
        $flashSale->delete();

        return back()->with('success', 'Flash Sale berhasil dihapus.');
    }
}
