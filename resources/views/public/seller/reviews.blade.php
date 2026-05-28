<x-app-layout>
    <x-slot name="title">Ulasan Toko {{ $store->name }}</x-slot>

    @php
        $activeRating = request('rating');
    @endphp

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('store.show', $store->slug) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div class="min-w-0">
                <h1 class="font-display font-bold text-2xl text-gray-900">Ulasan Toko</h1>
                <p class="mt-1 text-sm text-gray-500 line-clamp-1">{{ $store->name }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 mb-5">
            <div class="flex items-center gap-4">
                <img src="{{ $store->logo_url }}" alt="{{ $store->name }}" class="h-16 w-16 rounded-full border border-gray-100 object-cover bg-white shrink-0">
                <div class="min-w-0 flex-1">
                    <h2 class="font-bold text-gray-900 line-clamp-1">{{ $store->name }}</h2>
                    <p class="mt-1 text-xs text-gray-500">{{ $store->is_verified ? 'Terverifikasi' : 'Belum terverifikasi' }} · {{ $store->products_count }} produk</p>
                    <a href="{{ route('store.show', $store->slug) }}" class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-brand-navy hover:text-brand-blue">
                        Lihat toko
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-bold text-gray-500">Performa Toko</p>
                <div class="mt-2 flex items-end gap-2">
                    <span class="font-display text-3xl font-bold text-gray-900">{{ number_format($store->avg_rating, 1) }}</span>
                    <x-star-rating :value="$store->avg_rating" size="w-4 h-4" />
                </div>
                <p class="mt-1 text-xs text-gray-400">{{ $store->reviews_count }} ulasan toko</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-bold text-gray-500">Ringkasan</p>
                <p class="mt-2 font-display text-3xl font-bold text-gray-900">{{ $storeReviews->total() }}</p>
                <p class="mt-1 text-xs text-gray-400">ulasan sesuai filter</p>
            </div>
        </div>

        <div class="mb-5 overflow-x-auto hide-scroll">
            <div class="flex min-w-max items-center gap-2">
                <a href="{{ route('store.reviews.index', array_merge(['slug' => $store->slug], request()->except(['page', 'rating']))) }}" class="rounded-full px-4 py-2 text-xs font-bold transition-colors {{ !$activeRating ? 'bg-brand-navy text-white' : 'bg-white text-gray-700 border border-gray-100 hover:border-brand-navy hover:text-brand-navy' }}">
                    Semua
                </a>
                @for($rating = 5; $rating >= 1; $rating--)
                    <a href="{{ route('store.reviews.index', array_merge(['slug' => $store->slug], request()->except(['page', 'rating']), ['rating' => $rating])) }}" class="inline-flex items-center gap-1 rounded-full px-4 py-2 text-xs font-bold transition-colors {{ (string) $activeRating === (string) $rating ? 'bg-brand-navy text-white' : 'bg-white text-gray-700 border border-gray-100 hover:border-brand-navy hover:text-brand-navy' }}">
                        <x-icon name="star" class="w-3.5 h-3.5 text-yellow-400" />
                        {{ $rating }}
                        <span class="text-[10px] opacity-70">{{ $ratingCounts[$rating] ?? 0 }}</span>
                    </a>
                @endfor
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden min-h-[280px] sm:min-h-[340px]">
            @if($storeReviews->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($storeReviews as $storeReview)
                        <article class="p-4 sm:p-5">
                            <div class="flex gap-3">
                                <img src="{{ $storeReview->buyer->avatar_url }}" alt="{{ $storeReview->buyer->name }}" class="h-10 w-10 rounded-full border border-gray-100 object-cover shrink-0">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-bold text-sm text-gray-900 truncate">{{ $storeReview->buyer->name }}</p>
                                            <p class="mt-0.5 text-[11px] text-gray-400">{{ $storeReview->created_at->diffForHumans() }}</p>
                                        </div>
                                        <x-star-rating :value="$storeReview->rating" size="w-4 h-4" />
                                    </div>

                                    @if($storeReview->comment)
                                        <p class="mt-3 text-sm leading-relaxed text-gray-700">{{ $storeReview->comment }}</p>
                                    @endif

                                    @if($storeReview->seller_reply)
                                        <div class="mt-4 rounded-xl border border-brand-navylight bg-brand-navylight/20 p-3">
                                            <div class="flex items-center gap-2">
                                                <x-icon name="building-storefront" class="w-4 h-4 text-brand-navy" />
                                                <p class="text-xs font-bold text-brand-navy">Balasan Toko</p>
                                                @if($storeReview->seller_replied_at)
                                                    <span class="text-[10px] text-gray-400">{{ $storeReview->seller_replied_at->diffForHumans() }}</span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-sm leading-relaxed text-gray-700">{{ $storeReview->seller_reply }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($storeReviews->hasPages())
                    <div class="border-t border-gray-100 bg-gray-50 px-4 py-3">
                        {{ $storeReviews->links() }}
                    </div>
                @endif
            @else
                <div class="flex min-h-[260px] flex-col items-center justify-center px-6 py-16 text-center text-gray-400">
                    <x-icon name="chat-bubble-bottom-center-text" class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                    <p class="font-bold text-gray-600">Belum ada ulasan yang cocok</p>
                    <p class="mt-1 text-xs">Coba ubah filter rating ulasan.</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <style>
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @endpush
</x-app-layout>
