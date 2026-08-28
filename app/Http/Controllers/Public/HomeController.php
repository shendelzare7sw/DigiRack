<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use App\Models\SystemSetting;
use App\Models\Wishlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $wishlistIds = Auth::check()
            ? Wishlist::where('user_id', Auth::id())->pluck('product_id')->all()
            : [];

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $flashSales = FlashSale::query()
            ->with(['product.store', 'product.category', 'product.primaryImage', 'product.flashSale'])
            ->where('is_active', true)
            ->where('end_time', '>=', now())
            ->get();

        $usedPartsSettings = SystemSetting::query()
            ->whereIn('key', [
                'used_parts_section_enabled',
                'used_parts_section_title',
                'used_parts_section_description',
                'used_parts_section_cta_label',
                'used_parts_section_product_ids',
            ])
            ->pluck('value', 'key');

        $usedPartsSection = [
            'enabled' => $usedPartsSettings->get('used_parts_section_enabled', 'true') === 'true',
            'title' => $usedPartsSettings->get('used_parts_section_title') ?: 'Mencari pretelan untuk perbaikan perangkat?',
            'description' => $usedPartsSettings->get('used_parts_section_description') ?: 'Temukan komponen laptop dan PC second untuk penggantian, perbaikan, atau proyek rakitan.',
            'cta_label' => $usedPartsSettings->get('used_parts_section_cta_label') ?: 'Cari lebih banyak',
        ];

        $selectedUsedPartIds = $this->decodeProductIds(
            $usedPartsSettings->get('used_parts_section_product_ids')
        );
        $usedParts = $usedPartsSection['enabled']
            ? $this->usedParts($selectedUsedPartIds)
            : collect();

        $products = Product::query()
            ->with(['store', 'category', 'primaryImage', 'flashSale'])
            ->where('status', 'active')
            ->whereDoesntHave('flashSale')
            ->when($usedParts->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $usedParts->pluck('id')))
            ->inRandomOrder()
            ->take(10)
            ->get();

        return view('welcome', compact(
            'wishlistIds',
            'categories',
            'flashSales',
            'usedPartsSection',
            'usedParts',
            'products',
        ));
    }

    /**
     * @param  array<int, int>  $selectedIds
     * @return Collection<int, Product>
     */
    private function usedParts(array $selectedIds): Collection
    {
        $query = Product::query()
            ->with(['store', 'category', 'primaryImage', 'flashSale'])
            ->where('status', 'active')
            ->where('condition', 'used')
            ->where('stock', '>', 0);

        if ($selectedIds === []) {
            return $query->latest()->take(10)->get();
        }

        $positions = array_flip($selectedIds);

        return $query
            ->whereIn('id', $selectedIds)
            ->get()
            ->sortBy(fn (Product $product) => $positions[$product->id] ?? PHP_INT_MAX)
            ->values();
    }

    /**
     * @return array<int, int>
     */
    private function decodeProductIds(?string $value): array
    {
        $ids = json_decode($value ?: '[]', true);

        if (! is_array($ids)) {
            return [];
        }

        return collect($ids)
            ->filter(fn ($id) => filter_var($id, FILTER_VALIDATE_INT) !== false)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->take(10)
            ->values()
            ->all();
    }
}
