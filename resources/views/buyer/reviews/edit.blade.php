<x-app-layout>
    <x-slot name="title">{{ $review ? 'Edit Ulasan' : 'Tulis Ulasan' }}</x-slot>

    @php
        $reviewMedia = collect($review->media ?? [])->values();
        $initialRating = (int) old('rating', $review->rating ?? 5);
    @endphp

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('buyer.orders.show', $order->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div class="min-w-0">
                <h1 class="font-display font-bold text-2xl text-gray-900">{{ $review ? 'Edit Ulasan' : 'Tulis Ulasan' }}</h1>
                <p class="mt-1 text-sm text-gray-500">Pesanan #{{ $order->invoice_number }}</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-bold mb-1">Ada data yang perlu diperbaiki.</p>
                <ul class="list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('buyer.reviews.store', $orderItem->id) }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6 space-y-6" x-data="reviewEditor({{ $initialRating }}, {{ $maxMediaItems }}, {{ $reviewMedia->count() }})">
                    @csrf

                    <template x-for="path in removedMedia" :key="path">
                        <input type="hidden" name="remove_media[]" :value="path">
                    </template>
                    <input type="hidden" name="rating" x-model="rating">

                    <div>
                        <p class="text-sm font-bold text-gray-900 mb-3">Rating Produk</p>
                        <div class="grid grid-cols-5 gap-2">
                            @for($rating = 1; $rating <= 5; $rating++)
                                <button type="button" @click="rating = {{ $rating }}" class="min-h-12 rounded-xl border px-2 py-2 text-sm font-bold transition-colors touch-manipulation" :class="rating === {{ $rating }} ? 'border-yellow-400 bg-yellow-100 text-yellow-800' : 'border-gray-200 bg-white text-gray-600 hover:border-yellow-300'">
                                    <span class="flex items-center justify-center gap-1">
                                        <x-icon name="star" class="w-4 h-4 text-yellow-500" />
                                        {{ $rating }}
                                    </span>
                                </button>
                            @endfor
                        </div>
                        <x-input-error :messages="$errors->get('rating')" class="mt-2" />
                    </div>

                    <div>
                        <label for="comment" class="block text-sm font-bold text-gray-900">Komentar</label>
                        <textarea id="comment" name="comment" rows="5" maxlength="1000" class="mt-2 w-full rounded-xl border-gray-200 text-sm focus:border-yellow-400 focus:ring-yellow-400" placeholder="Ceritakan kualitas produk, kemasan, atau pengalaman penggunaan.">{{ old('comment', $review->comment ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                    </div>

                    @if($reviewMedia->isNotEmpty())
                        <div>
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <p class="text-sm font-bold text-gray-900">Media Saat Ini</p>
                                <p class="text-xs text-gray-400"><span x-text="currentTotal()"></span>/{{ $maxMediaItems }}</p>
                            </div>
                            <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                                @foreach($reviewMedia as $media)
                                    @php
                                        $mediaPath = $media['path'] ?? '';
                                        $mediaUrl = $mediaPath ? asset('storage/' . $mediaPath) : '';
                                        $mediaType = $media['type'] ?? 'image';
                                    @endphp
                                    @if($mediaPath)
                                        <button type="button" @click="toggleExisting('{{ $mediaPath }}')" class="relative aspect-square overflow-hidden rounded-xl border bg-white transition-all" :class="isRemoved('{{ $mediaPath }}') ? 'border-red-300 opacity-50' : 'border-gray-100 hover:border-yellow-300'">
                                            @if($mediaType === 'video')
                                                <video src="{{ $mediaUrl }}" class="w-full h-full object-cover" muted playsinline preload="metadata"></video>
                                                <span class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">Video</span>
                                            @else
                                                <img src="{{ $mediaUrl }}" alt="Media ulasan {{ $loop->iteration }}" class="w-full h-full object-cover">
                                            @endif
                                            <span class="absolute right-1 top-1 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/95 text-gray-700 shadow" :class="isRemoved('{{ $mediaPath }}') ? 'text-red-600' : ''">
                                                <x-icon name="x-mark" class="w-4 h-4" />
                                            </span>
                                            <span x-show="isRemoved('{{ $mediaPath }}')" x-cloak class="absolute inset-x-1 bottom-1 rounded bg-red-600 px-1 py-0.5 text-[10px] font-bold text-white">Dihapus</span>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <label for="review_media" class="block text-sm font-bold text-gray-900">Tambah Foto & Video</label>
                            <p class="text-xs text-gray-400"><span x-text="currentTotal()"></span>/{{ $maxMediaItems }}</p>
                        </div>
                        <input id="review_media" x-ref="mediaInput" type="file" name="review_media[]" multiple accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime" @change="addMedia($event)" class="block w-full text-sm text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-navylight file:px-3 file:py-2 file:text-sm file:font-bold file:text-brand-navy hover:file:bg-brand-navy hover:file:text-white">
                        <p class="mt-1 text-[11px] text-gray-500">Maksimal {{ $maxMediaItems }} media. Gambar 8 MB per file, video 20 MB per file.</p>
                        <p x-show="fileError" x-cloak class="mt-1 text-[11px] font-semibold text-red-600" x-text="fileError"></p>
                        <x-input-error :messages="$errors->get('review_media')" class="mt-2" />
                        @foreach($errors->get('review_media.*') as $messages)
                            <x-input-error :messages="$messages" class="mt-2" />
                        @endforeach

                        <div x-show="previews.length" x-cloak class="mt-4">
                            <p class="mb-2 text-xs font-semibold text-gray-600">Preview media baru</p>
                            <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                                <template x-for="(preview, index) in previews" :key="preview.url">
                                    <div class="relative aspect-square overflow-hidden rounded-xl border border-gray-100 bg-white">
                                        <template x-if="preview.type === 'image'">
                                            <img :src="preview.url" :alt="preview.name" class="h-full w-full object-cover">
                                        </template>
                                        <template x-if="preview.type === 'video'">
                                            <video :src="preview.url" class="h-full w-full object-cover" muted playsinline preload="metadata"></video>
                                        </template>
                                        <button type="button" @click="removeMedia(index)" class="absolute right-1 top-1 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/95 text-gray-700 shadow hover:text-red-600" aria-label="Hapus media">
                                            <x-icon name="x-mark" class="w-4 h-4" />
                                        </button>
                                        <span x-show="preview.type === 'video'" class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">Video</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-2">
                        <a href="{{ route('buyer.orders.show', $order->id) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-600 hover:border-gray-300 hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-yellow-500 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-yellow-600 transition-colors">
                            <x-icon name="paper-airplane" class="w-4 h-4" />
                            {{ $review ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-24">
                    <img src="{{ $orderItem->product->primary_image_url }}" alt="{{ $orderItem->product->name }}" class="w-full aspect-square rounded-xl border border-gray-100 object-cover bg-gray-50">
                    <h2 class="mt-4 font-bold text-gray-900 leading-snug">{{ $orderItem->product->name }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ number_format($orderItem->quantity) }} x Rp {{ number_format($orderItem->price_snapshot, 0, ',', '.') }}</p>
                    <p class="mt-3 text-xs text-gray-400">{{ $order->store->name }}</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.reviewEditor = function(initialRating, maxMedia, existingCount) {
            return {
                rating: initialRating,
                maxMedia: maxMedia,
                existingCount: existingCount,
                removedMedia: [],
                files: [],
                previews: [],
                fileError: '',
                imageMaxBytes: 8 * 1024 * 1024,
                videoMaxBytes: 20 * 1024 * 1024,
                allowedImageTypes: ['image/jpeg', 'image/png', 'image/webp'],
                allowedVideoTypes: ['video/mp4', 'video/webm', 'video/quicktime'],

                currentTotal() {
                    return this.existingCount - this.removedMedia.length + this.files.length;
                },

                remainingSlots() {
                    return this.maxMedia - this.currentTotal();
                },

                addMedia(event) {
                    this.fileError = '';
                    const selected = Array.from(event.target.files || []);

                    for (const file of selected) {
                        const isImage = this.allowedImageTypes.includes(file.type);
                        const isVideo = this.allowedVideoTypes.includes(file.type);

                        if (this.remainingSlots() <= 0) {
                            this.fileError = 'Media ulasan maksimal ' + this.maxMedia + ' file.';
                            break;
                        }

                        if (!isImage && !isVideo) {
                            this.fileError = 'Media harus berupa JPG, PNG, WebP, MP4, WebM, atau MOV.';
                            continue;
                        }

                        if (isImage && file.size > this.imageMaxBytes) {
                            this.fileError = 'Ada gambar yang lebih dari 8 MB. File itu tidak dimasukkan.';
                            continue;
                        }

                        if (isVideo && file.size > this.videoMaxBytes) {
                            this.fileError = 'Ada video yang lebih dari 20 MB. File itu tidak dimasukkan.';
                            continue;
                        }

                        this.files.push(file);
                        this.previews.push({
                            name: file.name,
                            type: isVideo ? 'video' : 'image',
                            url: URL.createObjectURL(file),
                        });
                    }

                    this.syncInput();
                },

                removeMedia(index) {
                    const preview = this.previews[index];
                    if (preview) {
                        URL.revokeObjectURL(preview.url);
                    }
                    this.previews.splice(index, 1);
                    this.files.splice(index, 1);
                    this.fileError = '';
                    this.syncInput();
                },

                syncInput() {
                    if (!this.$refs.mediaInput) return;

                    const transfer = new DataTransfer();
                    this.files.forEach((file) => transfer.items.add(file));
                    this.$refs.mediaInput.files = transfer.files;
                },

                toggleExisting(path) {
                    if (this.removedMedia.includes(path)) {
                        if (this.currentTotal() >= this.maxMedia) {
                            this.fileError = 'Media ulasan maksimal ' + this.maxMedia + ' file.';
                            return;
                        }

                        this.removedMedia = this.removedMedia.filter((item) => item !== path);
                        this.fileError = '';
                        return;
                    }

                    this.removedMedia.push(path);
                    this.fileError = '';
                },

                isRemoved(path) {
                    return this.removedMedia.includes(path);
                },
            };
        };
    </script>
    @endpush
</x-app-layout>
