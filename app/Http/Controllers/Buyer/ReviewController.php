<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Review;
use App\Notifications\ReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    private const IMAGE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const VIDEO_MIME_TYPES = ['video/mp4', 'video/webm', 'video/quicktime'];
    private const IMAGE_MAX_BYTES = 8 * 1024 * 1024;
    private const VIDEO_MAX_BYTES = 20 * 1024 * 1024;
    private const MEDIA_MAX_ITEMS = 5;

    public function edit(OrderItem $orderItem)
    {
        $orderItem->load(['order.store', 'product.images', 'product.store']);
        $order = $orderItem->order;

        $guard = $this->guardReviewableOrder($orderItem);
        if ($guard) {
            return $guard;
        }

        $review = Review::where([
            'buyer_id' => Auth::id(),
            'order_id' => $order->id,
            'product_id' => $orderItem->product_id,
        ])->first();

        return view('buyer.reviews.edit', [
            'orderItem' => $orderItem,
            'order' => $order,
            'review' => $review,
            'maxMediaItems' => self::MEDIA_MAX_ITEMS,
        ]);
    }

    public function store(Request $request, OrderItem $orderItem)
    {
        $orderItem->load(['order', 'product.store.user']);
        $order = $orderItem->order;

        $guard = $this->guardReviewableOrder($orderItem);
        if ($guard) {
            return $guard;
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'review_media' => ['nullable', 'array'],
            'review_media.*' => ['file'],
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => ['string'],
        ]);

        $review = Review::where([
            'buyer_id' => Auth::id(),
            'order_id' => $order->id,
            'product_id' => $orderItem->product_id,
        ])->first();

        $media = collect($review?->media ?? [])->values();
        $pathsToDelete = collect();

        if ($request->filled('remove_media')) {
            $removePaths = collect($validated['remove_media'])
                ->filter()
                ->unique()
                ->values();

            $media = $media->reject(function ($item) use ($removePaths, $pathsToDelete) {
                $path = $item['path'] ?? null;

                if ($path && $removePaths->contains($path)) {
                    $pathsToDelete->push($path);
                    return true;
                }

                return false;
            })->values();
        }

        $files = $this->validatedMediaFiles($request);

        if ($media->count() + count($files) > self::MEDIA_MAX_ITEMS) {
            throw ValidationException::withMessages([
                'review_media' => 'Media ulasan maksimal ' . self::MEDIA_MAX_ITEMS . ' file termasuk foto dan video.',
            ]);
        }

        foreach ($files as $file) {
            $mime = $file->getMimeType();
            $type = in_array($mime, self::IMAGE_MIME_TYPES, true) ? 'image' : 'video';
            $media->push([
                'path' => $file->store('review-media/' . Auth::id(), 'public'),
                'type' => $type,
                'mime' => $mime,
                'name' => $file->getClientOriginalName(),
            ]);
        }

        $wasUpdated = $review !== null;

        $savedReview = Review::updateOrCreate(
            [
                'buyer_id' => Auth::id(),
                'order_id' => $order->id,
                'product_id' => $orderItem->product_id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'media' => $media->isEmpty() ? null : $media->values()->all(),
            ]
        );

        $pathsToDelete->each(fn ($path) => Storage::disk('public')->delete($path));

        $orderItem->product->update([
            'avg_rating' => round((float) $orderItem->product->reviews()->avg('rating'), 1),
        ]);

        $this->notifySellerAboutReview($savedReview, $wasUpdated);

        return redirect()
            ->route('buyer.orders.show', $order->id)
            ->with('success', 'Terima kasih, ulasan Anda berhasil disimpan.');
    }

    private function guardReviewableOrder(OrderItem $orderItem)
    {
        $order = $orderItem->order;

        if (!$order || (int) $order->buyer_id !== (int) Auth::id()) {
            abort(404);
        }

        if ($order->status !== 'completed') {
            return redirect()
                ->route('buyer.orders.show', $order->id)
                ->with('error', 'Ulasan hanya bisa diberikan setelah transaksi selesai.');
        }

        if (!$orderItem->product) {
            return redirect()
                ->route('buyer.orders.show', $order->id)
                ->with('error', 'Produk tidak tersedia untuk diulas.');
        }

        return null;
    }

    private function validatedMediaFiles(Request $request): array
    {
        $files = $request->file('review_media', []);
        $errors = [];

        foreach ($files as $index => $file) {
            if (!$file->isValid()) {
                $errors['review_media.' . $index] = 'Media ulasan gagal diupload. Coba pilih file lain.';
                continue;
            }

            $mime = $file->getMimeType();
            $size = $file->getSize();
            $isImage = in_array($mime, self::IMAGE_MIME_TYPES, true);
            $isVideo = in_array($mime, self::VIDEO_MIME_TYPES, true);

            if (!$isImage && !$isVideo) {
                $errors['review_media.' . $index] = 'Media ulasan harus berupa gambar JPG/PNG/WebP atau video MP4/WebM/MOV.';
                continue;
            }

            if ($isImage && $size > self::IMAGE_MAX_BYTES) {
                $errors['review_media.' . $index] = 'Ukuran gambar ulasan maksimal 8 MB per file.';
            }

            if ($isVideo && $size > self::VIDEO_MAX_BYTES) {
                $errors['review_media.' . $index] = 'Ukuran video ulasan maksimal 20 MB per file.';
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        return $files;
    }

    private function notifySellerAboutReview(Review $review, bool $wasUpdated): void
    {
        try {
            $review->loadMissing(['buyer', 'product.store.user']);

            $seller = $review->product?->store?->user;
            if (!$seller || (int) $seller->id === (int) $review->buyer_id) {
                return;
            }

            $productName = Str::limit($review->product?->name ?? 'produk', 60);
            $buyerName = Str::limit($review->buyer?->name ?? 'Pembeli', 40);
            $type = $wasUpdated ? 'review_updated' : 'review_created';
            $title = $wasUpdated ? 'Ulasan produk diperbarui' : 'Ulasan produk baru';
            $verb = $wasUpdated ? 'memperbarui ulasan' : 'memberi ulasan';
            $message = "{$buyerName} {$verb} {$review->rating} bintang untuk {$productName}.";

            if ($review->comment) {
                $message .= ' "' . Str::limit($review->comment, 80) . '"';
            }

            $seller->notify(new ReviewNotification(
                $type,
                $title,
                $message,
                route('products.reviews.index', $review->product->slug),
            ));
        } catch (\Throwable $e) {
            Log::warning('Review notification failed: ' . $e->getMessage(), [
                'review_id' => $review->id,
                'product_id' => $review->product_id,
            ]);
        }
    }
}
