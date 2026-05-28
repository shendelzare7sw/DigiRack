<x-app-layout>
    <x-slot name="title">{{ $store->name }} - DigiRack</x-slot>

    @php
        $previousUrl = url()->previous();
        $currentUrl = url()->current();
        $backUrl = $previousUrl && $previousUrl !== $currentUrl ? $previousUrl : route('products.index');
    @endphp

    {{-- Store Header / Banner --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="relative rounded-2xl overflow-hidden bg-gray-100 h-48 sm:h-64 md:h-80 shadow-inner">
            {{-- Back Button (floating over banner) --}}
            <a href="{{ $backUrl }}" data-store-back-button class="absolute top-4 left-4 z-30 inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/80 backdrop-blur-sm border border-white/40 hover:bg-white text-gray-700 hover:text-brand-navy transition-all shadow-lg pointer-events-auto touch-manipulation" title="Kembali" aria-label="Kembali">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            @if($store->banner)
                <img src="{{ $store->banner_url }}" alt="Banner {{ $store->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-r from-brand-navy to-brand-navydark flex items-center justify-center">
                    <x-icon name="building-storefront" class="w-24 h-24 text-white/20" />
                </div>
            @endif
        </div>

        {{-- Store Info Card (Overlapping Banner) --}}
        <div class="relative -mt-16 sm:-mt-20 mx-4 sm:mx-8">
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-lg border border-gray-100 flex flex-col sm:flex-row items-center sm:items-start gap-6">
                {{-- Logo --}}
                <div class="shrink-0 relative group">
                    <img src="{{ $store->logo_url }}" alt="{{ $store->name }}" class="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-white shadow-md object-cover bg-white">
                    @if($store->is_verified)
                        <div class="absolute bottom-1 right-1 bg-white rounded-full p-1 shadow">
                            <x-icon name="check-badge" class="w-6 h-6 text-green-500" />
                        </div>
                    @endif
                </div>

                {{-- Details --}}
                <div class="flex-1 text-center sm:text-left min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold font-display text-gray-900 truncate mb-2">{{ $store->name }}</h1>
                    
                    <div class="flex flex-wrap justify-center sm:justify-start items-center gap-4 text-sm text-gray-600 mb-4">
                        <span class="flex items-center gap-1.5">
                            <x-icon name="map-pin" class="w-4 h-4 text-brand-orange" />
                            {{ $store->city->name ?? 'Indonesia' }}
                        </span>
                        <span class="text-gray-300">|</span>
                        <span class="flex items-center gap-1.5">
                            <x-icon name="star" class="w-4 h-4 text-yellow-500" />
                            <span class="font-bold text-gray-900">{{ number_format($store->avg_rating, 1) }}</span>
                            <span class="text-gray-400">({{ $store->reviews_count }} ulasan toko)</span>
                        </span>
                        <span class="text-gray-300">|</span>
                        <span class="flex items-center gap-1.5">
                            <x-icon name="cube" class="w-4 h-4 text-brand-navy" />
                            <span class="font-bold text-gray-900">{{ $storeProductCount }}</span> Produk
                        </span>
                    </div>

                    @if($store->description)
                        <p class="text-sm text-gray-600 line-clamp-3 md:line-clamp-none max-w-3xl leading-relaxed">
                            {{ $store->description }}
                        </p>
                    @endif
                </div>

                {{-- Action (If we have chat or follow features later) --}}
                <!--
                <div class="shrink-0 flex gap-3 w-full sm:w-auto">
                    <button class="flex-1 sm:flex-none border-2 border-brand-navy text-brand-navy hover:bg-brand-navy hover:text-white font-bold px-6 py-2.5 rounded-xl transition-colors">Chat Penjual</button>
                </div>
                -->
            </div>
        </div>
    </section>

    {{-- Store Reviews --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        @php
            $storeReviewsUrl = url('/toko/' . $store->slug . '/reviews');
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Ulasan Performa Toko</h2>
                    <p class="mt-1 text-sm text-gray-500">Penilaian pembeli terhadap pelayanan toko.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-2 rounded-xl bg-yellow-50 px-4 py-2 text-yellow-800">
                        <x-icon name="star" class="w-5 h-5 text-yellow-500" />
                        <span class="font-display text-2xl font-bold">{{ number_format($store->avg_rating, 1) }}</span>
                        <span class="text-xs font-semibold">{{ $store->reviews_count }} ulasan</span>
                    </div>
                    @if($store->reviews_count > 0)
                        <a href="{{ $storeReviewsUrl }}" class="inline-flex items-center justify-center gap-1 rounded-xl border border-brand-navylight px-4 py-2 text-xs font-bold text-brand-navy hover:border-brand-navy hover:bg-brand-navylight/40 transition-colors">
                            Lihat Semua Ulasan
                            <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        </a>
                    @endif
                </div>
            </div>

            @if($storeReviews->isNotEmpty())
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    @foreach($storeReviews as $storeReview)
                        <article class="rounded-2xl border border-gray-100 bg-gray-50/50 p-4">
                            <div class="flex gap-3">
                                <img src="{{ $storeReview->buyer->avatar_url }}" alt="{{ $storeReview->buyer->name }}" class="h-10 w-10 rounded-full border border-gray-100 object-cover shrink-0">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-bold text-sm text-gray-900 truncate">{{ $storeReview->buyer->name }}</p>
                                            <p class="mt-0.5 text-[11px] text-gray-400">{{ $storeReview->created_at->diffForHumans() }}</p>
                                        </div>
                                        <x-star-rating :value="$storeReview->rating" size="w-3.5 h-3.5" />
                                    </div>
                                    @if($storeReview->comment)
                                        <p class="mt-2 text-sm leading-relaxed text-gray-700">{{ $storeReview->comment }}</p>
                                    @endif
                                    @if($storeReview->seller_reply)
                                        <div class="mt-3 rounded-xl border border-brand-navylight bg-white p-3">
                                            <p class="text-xs font-bold text-brand-navy">Balasan Toko</p>
                                            <p class="mt-1 text-sm leading-relaxed text-gray-700">{{ $storeReview->seller_reply }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 py-10 text-center text-gray-400">
                    <x-icon name="chat-bubble-bottom-center-text" class="w-10 h-10 mx-auto mb-3 text-gray-300" />
                    <p class="font-bold text-gray-600">Belum ada ulasan toko</p>
                    <p class="mt-1 text-xs">Ulasan toko akan tampil setelah transaksi selesai dan pembeli memberi penilaian.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Filter & Sorting Header --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 mb-6">
        <div class="flex border-b border-gray-200 justify-between items-end pb-4">
            <h2 class="text-xl font-bold text-gray-900">Etalase Produk</h2>
            
            <form action="{{ route('store.show', $store->slug) }}" method="GET" class="flex items-center">
                <select name="sort" onchange="this.form.submit()" class="text-sm border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-lg bg-gray-50">
                    <option value="latest" {{ $sort == 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="popular" {{ $sort == 'popular' ? 'selected' : '' }}>Terlaris</option>
                    <option value="price_asc" {{ $sort == 'price_asc' ? 'selected' : '' }}>Termurah</option>
                    <option value="price_desc" {{ $sort == 'price_desc' ? 'selected' : '' }}>Termahal</option>
                </select>
            </form>
        </div>
    </section>

    {{-- Product Grid --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        @if($products->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
                @foreach($products as $product)
                    <x-product-card :product="$product" :wishlisted="in_array($product->id, $wishlistIds ?? [])" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-20 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <x-icon name="cube-transparent" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                <h3 class="text-lg font-bold text-gray-900">Belum Ada Produk</h3>
                <p class="text-gray-500 mt-1">Toko ini sepertinya sedang merapikan etalasenya.</p>
            </div>
        @endif
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var backButton = document.querySelector('[data-store-back-button]');
                if (!backButton) return;

                var fallbackUrl = backButton.getAttribute('href');
                var navigated = false;

                function goBack(event) {
                    if (event) event.preventDefault();
                    if (navigated) return;
                    navigated = true;

                    var hasSameOriginReferrer = document.referrer && (function() {
                        try {
                            return new URL(document.referrer).origin === window.location.origin;
                        } catch (error) {
                            return false;
                        }
                    })();

                    if (window.history.length > 1 && hasSameOriginReferrer) {
                        window.history.back();
                        return;
                    }

                    window.location.assign(fallbackUrl);
                }

                backButton.addEventListener('click', goBack);
                backButton.addEventListener('touchend', goBack, { passive: false });
            });
        </script>
    @endpush
</x-app-layout>
