<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\StoreReview;
use App\Notifications\ReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreReviewController extends Controller
{
    public function reply(Request $request, StoreReview $storeReview)
    {
        $store = Auth::user()->store;

        if (!$store || (int) $storeReview->store_id !== (int) $store->id) {
            abort(404);
        }

        $validated = $request->validate([
            'seller_reply' => ['required', 'string', 'max:1000'],
        ]);

        $storeReview->update([
            'seller_reply' => $validated['seller_reply'],
            'seller_replied_at' => now(),
        ]);

        $this->notifyBuyerAboutReply($storeReview);

        return back()->with('success', 'Balasan ulasan toko berhasil disimpan.');
    }

    private function notifyBuyerAboutReply(StoreReview $storeReview): void
    {
        try {
            $storeReview->loadMissing(['buyer', 'store', 'order']);

            if (!$storeReview->buyer) {
                return;
            }

            $storeName = Str::limit($storeReview->store?->name ?? 'Toko', 60);

            $storeReview->buyer->notify(new ReviewNotification(
                'store_review_replied',
                'Toko membalas ulasan Anda',
                $storeName . ' membalas ulasan performa toko yang Anda tulis.',
                route('buyer.orders.show', $storeReview->order_id),
            ));
        } catch (\Throwable $e) {
            Log::warning('Store review reply notification failed: ' . $e->getMessage(), [
                'store_review_id' => $storeReview->id,
            ]);
        }
    }
}
