<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StoreReview;
use App\Notifications\ReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreReviewController extends Controller
{
    public function edit(Order $order)
    {
        $guard = $this->guardReviewableOrder($order);
        if ($guard) {
            return $guard;
        }

        $review = StoreReview::where([
            'buyer_id' => Auth::id(),
            'order_id' => $order->id,
            'store_id' => $order->store_id,
        ])->first();

        return view('buyer.store-reviews.edit', compact('order', 'review'));
    }

    public function store(Request $request, Order $order)
    {
        $guard = $this->guardReviewableOrder($order);
        if ($guard) {
            return $guard;
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $review = StoreReview::where([
            'buyer_id' => Auth::id(),
            'order_id' => $order->id,
            'store_id' => $order->store_id,
        ])->first();

        $wasUpdated = $review !== null;

        $savedReview = StoreReview::updateOrCreate(
            [
                'buyer_id' => Auth::id(),
                'order_id' => $order->id,
                'store_id' => $order->store_id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        $this->updateStoreRating($order);
        $this->notifySellerAboutStoreReview($savedReview, $wasUpdated);

        return redirect()
            ->route('buyer.orders.show', $order->id)
            ->with('success', 'Terima kasih, ulasan toko berhasil disimpan.');
    }

    private function guardReviewableOrder(Order $order)
    {
        $order->loadMissing(['store.user']);

        if ((int) $order->buyer_id !== (int) Auth::id()) {
            abort(404);
        }

        if ($order->status !== 'completed') {
            return redirect()
                ->route('buyer.orders.show', $order->id)
                ->with('error', 'Ulasan toko hanya bisa diberikan setelah transaksi selesai.');
        }

        if (!$order->store) {
            return redirect()
                ->route('buyer.orders.show', $order->id)
                ->with('error', 'Toko tidak tersedia untuk diulas.');
        }

        return null;
    }

    private function updateStoreRating(Order $order): void
    {
        $order->store->update([
            'avg_rating' => round((float) $order->store->reviews()->avg('rating'), 1),
        ]);
    }

    private function notifySellerAboutStoreReview(StoreReview $review, bool $wasUpdated): void
    {
        try {
            $review->loadMissing(['buyer', 'store.user']);

            $seller = $review->store?->user;
            if (!$seller || (int) $seller->id === (int) $review->buyer_id) {
                return;
            }

            $buyerName = Str::limit($review->buyer?->name ?? 'Pembeli', 40);
            $type = $wasUpdated ? 'store_review_updated' : 'store_review_created';
            $title = $wasUpdated ? 'Ulasan toko diperbarui' : 'Ulasan toko baru';
            $verb = $wasUpdated ? 'memperbarui ulasan toko' : 'memberi ulasan toko';
            $message = "{$buyerName} {$verb} {$review->rating} bintang untuk toko Anda.";

            if ($review->comment) {
                $message .= ' "' . Str::limit($review->comment, 80) . '"';
            }

            $seller->notify(new ReviewNotification(
                $type,
                $title,
                $message,
                route('seller.dashboard'),
            ));
        } catch (\Throwable $e) {
            Log::warning('Store review notification failed: ' . $e->getMessage(), [
                'store_review_id' => $review->id,
                'store_id' => $review->store_id,
            ]);
        }
    }
}
