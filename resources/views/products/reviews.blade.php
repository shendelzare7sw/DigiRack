<x-app-layout>
    <x-slot name="title">Ulasan {{ $product->name }}</x-slot>

    @php
        $activeRating = request('rating');
        $mediaActive = request()->boolean('media');
        $baseQuery = request()->except('page');
    @endphp

    <div
        class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8"
        x-data="{
            mediaOpen: false,
            mediaUrl: '',
            mediaType: 'image',
            mediaAlt: '',
            openMedia(url, type, alt) {
                this.mediaUrl = url;
                this.mediaType = type;
                this.mediaAlt = alt;
                this.mediaOpen = true;
            },
            closeMedia() {
                this.mediaOpen = false;
                this.mediaUrl = '';
                this.mediaType = 'image';
                this.mediaAlt = '';
            },
        }"
        x-effect="document.body.classList.toggle('overflow-hidden', mediaOpen)"
        @keydown.escape.window="if (mediaOpen) closeMedia()">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('products.show', $product->slug) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div class="min-w-0">
                <h1 class="font-display font-bold text-2xl text-gray-900">Ulasan</h1>
                <p class="mt-1 text-sm text-gray-500 line-clamp-1">{{ $product->name }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 mb-5">
            <div class="flex items-center gap-4">
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-xl border border-gray-100 object-cover shrink-0">
                <div class="min-w-0 flex-1">
                    <h2 class="font-bold text-gray-900 line-clamp-1">{{ $product->name }}</h2>
                    <p class="mt-1 text-xs text-gray-500">{{ $product->store->name }}</p>
                    <a href="{{ route('products.show', $product->slug) }}" class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-brand-navy hover:text-brand-blue">
                        Lihat produk
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-bold text-gray-500">Kualitas Produk</p>
                <div class="mt-2 flex items-end gap-2">
                    <span class="font-display text-3xl font-bold text-gray-900">{{ number_format($product->avg_rating, 1) }}</span>
                    <x-star-rating :value="$product->avg_rating" size="w-4 h-4" />
                </div>
                <p class="mt-1 text-xs text-gray-400">{{ $product->reviews_count }} ulasan</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-bold text-gray-500">Foto & Video</p>
                <p class="mt-2 font-display text-3xl font-bold text-gray-900">{{ $reviewsWithMediaCount }}</p>
                <p class="mt-1 text-xs text-gray-400">ulasan bermedia</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-bold text-gray-500">Toko</p>
                <p class="mt-2 font-bold text-gray-900 line-clamp-1">{{ $product->store->name }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ $product->store->is_verified ? 'Terverifikasi' : 'Belum terverifikasi' }}</p>
            </div>
        </div>

        <div class="mb-5 overflow-x-auto hide-scroll">
            <div class="flex min-w-max items-center gap-2">
                <a href="{{ route('products.reviews.index', array_merge(['slug' => $product->slug], request()->except(['page', 'rating', 'media']))) }}" class="rounded-full px-4 py-2 text-xs font-bold transition-colors {{ !$activeRating && !$mediaActive ? 'bg-brand-navy text-white' : 'bg-white text-gray-700 border border-gray-100 hover:border-brand-navy hover:text-brand-navy' }}">
                    Semua
                </a>
                <a href="{{ route('products.reviews.index', array_merge(['slug' => $product->slug], request()->except(['page', 'media']), ['media' => 1])) }}" class="whitespace-nowrap rounded-full px-4 py-2 text-xs font-bold transition-colors {{ $mediaActive ? 'bg-brand-navy text-white' : 'bg-white text-gray-700 border border-gray-100 hover:border-brand-navy hover:text-brand-navy' }}">
                    Foto & Video
                </a>
                @for($rating = 5; $rating >= 1; $rating--)
                    <a href="{{ route('products.reviews.index', array_merge(['slug' => $product->slug], request()->except(['page', 'rating']), ['rating' => $rating])) }}" class="inline-flex items-center gap-1 rounded-full px-4 py-2 text-xs font-bold transition-colors {{ (string) $activeRating === (string) $rating ? 'bg-brand-navy text-white' : 'bg-white text-gray-700 border border-gray-100 hover:border-brand-navy hover:text-brand-navy' }}">
                        <x-icon name="star" class="w-3.5 h-3.5 text-yellow-400" />
                        {{ $rating }}
                        <span class="text-[10px] opacity-70">{{ $ratingCounts[$rating] ?? 0 }}</span>
                    </a>
                @endfor
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden min-h-[280px] sm:min-h-[340px]">
            @if($reviews->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($reviews as $review)
                        @php $reviewMedia = collect($review->media ?? []); @endphp
                        <article class="p-4 sm:p-5">
                            <div class="flex gap-3">
                                <img src="{{ $review->buyer->avatar_url }}" alt="{{ $review->buyer->name }}" class="h-10 w-10 rounded-full border border-gray-100 object-cover shrink-0">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-bold text-sm text-gray-900 truncate">{{ $review->buyer->name }}</p>
                                            <p class="mt-0.5 text-[11px] text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                                        </div>
                                        <x-icon name="ellipsis-vertical" class="w-5 h-5 text-gray-300 shrink-0" />
                                    </div>

                                    <div class="mt-2">
                                        <x-star-rating :value="$review->rating" size="w-4 h-4" />
                                    </div>

                                    @if($review->comment)
                                        <p class="mt-2 text-sm leading-relaxed text-gray-700">{{ $review->comment }}</p>
                                    @endif

                                    @if($reviewMedia->isNotEmpty())
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach($reviewMedia as $media)
                                                @php
                                                    $mediaPath = $media['path'] ?? '';
                                                    $mediaUrl = $mediaPath ? asset('storage/' . $mediaPath) : '';
                                                    $mediaType = $media['type'] ?? 'image';
                                                @endphp
                                                @if($mediaPath)
                                                    <button type="button" @click="openMedia({{ Js::from($mediaUrl) }}, {{ Js::from($mediaType) }}, {{ Js::from('Media ulasan ' . $loop->iteration) }})" class="relative h-24 w-24 overflow-hidden rounded-xl border border-gray-100 bg-gray-50 cursor-zoom-in focus:outline-none focus:ring-2 focus:ring-brand-navy focus:ring-offset-2">
                                                        @if($mediaType === 'video')
                                                            <video src="{{ $mediaUrl }}" class="h-full w-full object-cover" muted playsinline preload="metadata"></video>
                                                            <span class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">Video</span>
                                                        @else
                                                            <img src="{{ $mediaUrl }}" alt="Media ulasan {{ $loop->iteration }}" class="h-full w-full object-cover">
                                                        @endif
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($review->seller_reply)
                                        <div class="mt-4 rounded-xl border border-brand-navylight bg-brand-navylight/20 p-3">
                                            <div class="flex items-center gap-2">
                                                <x-icon name="building-storefront" class="w-4 h-4 text-brand-navy" />
                                                <p class="text-xs font-bold text-brand-navy">Balasan Seller</p>
                                                @if($review->seller_replied_at)
                                                    <span class="text-[10px] text-gray-400">{{ $review->seller_replied_at->diffForHumans() }}</span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-sm leading-relaxed text-gray-700">{{ $review->seller_reply }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($reviews->hasPages())
                    <div class="border-t border-gray-100 bg-gray-50 px-4 py-3">
                        {{ $reviews->links() }}
                    </div>
                @endif
            @else
                <div class="flex min-h-[260px] flex-col items-center justify-center px-6 py-16 text-center text-gray-400">
                    <x-icon name="chat-bubble-bottom-center-text" class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                    <p class="font-bold text-gray-600">Belum ada ulasan yang cocok</p>
                    <p class="mt-1 text-xs">Coba ubah filter ulasan.</p>
                </div>
            @endif
        </div>

        <div x-show="mediaOpen" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 p-3 sm:p-6"
            role="dialog"
            aria-modal="true"
            aria-label="Preview media ulasan"
            @click.self="closeMedia()">
            <button type="button" @click="closeMedia()" class="absolute top-3 right-3 sm:top-5 sm:right-5 z-20 rounded-full bg-white px-4 py-2 text-sm font-bold text-gray-900 shadow-lg hover:bg-gray-100 active:scale-95 transition-all" aria-label="Tutup media">
                Tutup media
            </button>
            <template x-if="mediaType === 'video'">
                <video :src="mediaUrl" :aria-label="mediaAlt" controls autoplay playsinline class="max-h-[82vh] max-w-[94vw] rounded-lg bg-black shadow-2xl" @click.stop></video>
            </template>
            <template x-if="mediaType !== 'video'">
                <img :src="mediaUrl" :alt="mediaAlt" class="max-h-[82vh] max-w-[94vw] rounded-lg object-contain select-none shadow-2xl" @click.stop>
            </template>
        </div>
    </div>

    @push('scripts')
    <style>
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @endpush
</x-app-layout>
