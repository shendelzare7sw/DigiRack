<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    private const IMAGE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const VIDEO_MIME_TYPES = ['video/mp4', 'video/webm', 'video/quicktime'];
    private const IMAGE_MAX_BYTES = 8 * 1024 * 1024;
    private const VIDEO_MAX_BYTES = 20 * 1024 * 1024;

    public function store(Request $request, OrderItem $orderItem)
    {
        $orderItem->load(['order', 'product']);
        $order = $orderItem->order;

        if (!$order || (int) $order->buyer_id !== (int) Auth::id()) {
            abort(404);
        }

        if ($order->status !== 'completed') {
            return back()->with('error', 'Ulasan hanya bisa diberikan setelah transaksi selesai.');
        }

        if (!$orderItem->product) {
            return back()->with('error', 'Produk tidak tersedia untuk diulas.');
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

        if ($request->filled('remove_media')) {
            $removePaths = collect($validated['remove_media'])
                ->filter()
                ->unique()
                ->values();

            $media = $media->reject(function ($item) use ($removePaths) {
                $path = $item['path'] ?? null;

                if ($path && $removePaths->contains($path)) {
                    Storage::disk('public')->delete($path);
                    return true;
                }

                return false;
            })->values();
        }

        foreach ($this->validatedMediaFiles($request) as $file) {
            $mime = $file->getMimeType();
            $type = in_array($mime, self::IMAGE_MIME_TYPES, true) ? 'image' : 'video';
            $media->push([
                'path' => $file->store('review-media/' . Auth::id(), 'public'),
                'type' => $type,
                'mime' => $mime,
                'name' => $file->getClientOriginalName(),
            ]);
        }

        Review::updateOrCreate(
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

        $orderItem->product->update([
            'avg_rating' => round((float) $orderItem->product->reviews()->avg('rating'), 1),
        ]);

        return back()->with('success', 'Terima kasih, ulasan Anda berhasil disimpan.');
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
}
