<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Halaman Katalog Produk (publik, tanpa login)
     * GET /products
     */
    public function index(Request $request)
    {
        $query = Product::with(['store', 'category', 'primaryImage', 'flashSale'])
            ->where('status', 'active');

        // 1. Search query
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 2. Filter by category slug
        if ($request->filled('category')) {
            $categorySlugs = is_array($request->category) ? $request->category : [$request->category];
            $query->whereHas('category', function ($q) use ($categorySlugs) {
                $q->whereIn('slug', $categorySlugs);
            });
        }

        // 3. Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) $request->max_price);
        }

        // 4. Filter by minimum rating
        if ($request->filled('rating')) {
            $query->where('avg_rating', '>=', (float) $request->rating);
        }

        // 5. Filter by condition
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // 6. Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('sold_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('avg_rating', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(20)->withQueryString();

        // Ambil semua kategori aktif untuk sidebar filter
        $categories = Category::where('is_active', true)
            ->withCount(['products' => function ($q) {
                $q->where('status', 'active');
            }])
            ->orderBy('sort_order')
            ->get();

        // Wishlist IDs untuk user yang login
        $wishlistIds = [];
        if (Auth::check()) {
            $wishlistIds = Wishlist::where('user_id', Auth::id())
                ->pluck('product_id')
                ->toArray();
        }

        return view('products.index', compact('products', 'categories', 'wishlistIds'));
    }

    /**
     * Halaman Detail Produk (publik, tanpa login)
     * GET /products/{slug}
     */
    public function show(string $slug)
    {
        $product = Product::with([
            'store',
            'category',
            'images',
            'flashSale',
            'reviews' => function ($q) {
                $q->with('buyer')->latest()->take(10);
            },
        ])
            ->withCount('reviews')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // Rating distribution
        $reviewCount = $product->reviews_count;
        $ratingDist = [];
        if ($reviewCount > 0) {
            for ($i = 5; $i >= 1; $i--) {
                $count = Review::where('product_id', $product->id)->where('rating', $i)->count();
                $ratingDist[$i] = [
                    'count' => $count,
                    'percent' => round(($count / $reviewCount) * 100),
                ];
            }
        }

        // Specs dari JSON
        $specs = is_array($product->specs) ? $product->specs : [];

        // Produk serupa (kategori yang sama, exclude current)
        $relatedProducts = Product::with(['store', 'category', 'primaryImage', 'flashSale'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->inRandomOrder()
            ->take(10)
            ->get();

        // Wishlist check
        $wishlistIds = [];
        $isWishlisted = false;
        $isOwnProduct = false;
        if (Auth::check()) {
            $isOwnProduct = $product->isOwnedBy(Auth::user());
            $wishlistIds = Wishlist::where('user_id', Auth::id())
                ->pluck('product_id')
                ->toArray();
            $isWishlisted = in_array($product->id, $wishlistIds);
        }

        return view('products.show', compact(
            'product', 'ratingDist', 'specs',
            'relatedProducts', 'isWishlisted', 'isOwnProduct', 'reviewCount', 'wishlistIds'
        ));
    }

    public function reviews(Request $request, string $slug)
    {
        $product = Product::with(['store', 'category', 'primaryImage'])
            ->withCount('reviews')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $reviewQuery = Review::with('buyer')
            ->where('product_id', $product->id);

        if ($request->filled('rating') && in_array((int) $request->rating, [1, 2, 3, 4, 5], true)) {
            $reviewQuery->where('rating', (int) $request->rating);
        }

        if ($request->boolean('media')) {
            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 12;
            $mediaReviews = $reviewQuery
                ->latest()
                ->get()
                ->filter(fn (Review $review) => $this->reviewHasMedia($review))
                ->values();

            $reviews = new LengthAwarePaginator(
                $mediaReviews->forPage($page, $perPage)->values(),
                $mediaReviews->count(),
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        } else {
            $reviews = $reviewQuery->latest()->paginate(12)->withQueryString();
        }

        $ratingCounts = Review::where('product_id', $product->id)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        $reviewsWithMediaCount = Review::where('product_id', $product->id)
            ->get()
            ->filter(fn (Review $review) => $this->reviewHasMedia($review))
            ->count();

        return view('products.reviews', compact(
            'product',
            'reviews',
            'ratingCounts',
            'reviewsWithMediaCount'
        ));
    }

    private function reviewHasMedia(Review $review): bool
    {
        return collect($review->media ?? [])
            ->contains(fn ($media) => filled($media['path'] ?? null));
    }
}
