<x-app-layout>
    <x-slot name="title">Katalog Produk IT Enterprise</x-slot>

    {{-- Breadcrumb --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <nav class="flex items-center text-sm text-gray-500 font-medium" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-brand-navy transition-colors">Home</a>
            <x-icon name="chevron-right" class="w-4 h-4 mx-2 text-gray-300" />
            <span class="text-brand-navy font-semibold">Produk</span>
            @if(request('category') && !is_array(request('category')))
                <x-icon name="chevron-right" class="w-4 h-4 mx-2 text-gray-300" />
                <span class="text-brand-navy font-semibold capitalize">{{ str_replace('-', ' ', request('category')) }}</span>
            @endif
        </nav>
    </div>

    {{-- Page Header & Active Filters --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900">
                    @if(request('q'))
                        Hasil Pencarian: <span class="text-brand-blue">"{{ request('q') }}"</span>
                    @else
                        Katalog Produk
                    @endif
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Menampilkan {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
                </p>
            </div>

            {{-- Sort Dropdown --}}
            <div x-data="{ sortOpen: false }" class="relative" @click.away="sortOpen = false">
                <button @click="sortOpen = !sortOpen" type="button"
                    class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 hover:border-brand-navy/30 transition-colors shadow-sm">
                    <x-icon name="arrows-up-down" class="w-4 h-4 text-gray-400" />
                    <span>
                        @switch(request('sort', 'newest'))
                            @case('price_asc') Harga Terendah @break
                            @case('price_desc') Harga Tertinggi @break
                            @case('popular') Terpopuler @break
                            @case('rating') Rating Tertinggi @break
                            @default Terbaru
                        @endswitch
                    </span>
                    <x-icon name="chevron-down" class="w-4 h-4 text-gray-400" />
                </button>

                <div x-show="sortOpen" x-transition x-cloak
                    class="absolute right-0 mt-2 w-52 bg-white rounded-xl border border-gray-100 shadow-lg z-30 overflow-hidden py-1">
                    @php
                        $sortOptions = [
                            'newest' => 'Terbaru',
                            'price_asc' => 'Harga Terendah',
                            'price_desc' => 'Harga Tertinggi',
                            'popular' => 'Terpopuler',
                            'rating' => 'Rating Tertinggi',
                        ];
                    @endphp
                    @foreach($sortOptions as $val => $label)
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $val]) }}"
                            class="block px-4 py-2.5 text-sm transition-colors {{ request('sort', 'newest') === $val ? 'bg-brand-navylight text-brand-navy font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Active Filter Tags --}}
        @php
            $hasFilters = request('category') || request('min_price') || request('max_price') || request('rating') || request('condition');
        @endphp
        @if($hasFilters)
            <div class="flex flex-wrap items-center gap-2 mt-4">
                <span class="text-xs text-gray-500 font-medium mr-1">Filter aktif:</span>

                @if(request('category'))
                    @foreach((array) request('category') as $catSlug)
                        <a href="{{ request()->fullUrlWithQuery(['category' => array_values(array_diff((array) request('category'), [$catSlug]))]) }}"
                            class="inline-flex items-center gap-1.5 bg-brand-bluelight text-brand-blue text-xs font-semibold px-3 py-1.5 rounded-full hover:bg-brand-blue hover:text-white transition-colors group">
                            {{ ucfirst(str_replace('-', ' ', $catSlug)) }}
                            <x-icon name="x-mark" class="w-3 h-3 opacity-60 group-hover:opacity-100" />
                        </a>
                    @endforeach
                @endif

                @if(request('min_price'))
                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-600 text-xs font-semibold px-3 py-1.5 rounded-full">
                        Min: Rp {{ number_format(request('min_price'), 0, ',', '.') }}
                    </span>
                @endif

                @if(request('max_price'))
                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-600 text-xs font-semibold px-3 py-1.5 rounded-full">
                        Max: Rp {{ number_format(request('max_price'), 0, ',', '.') }}
                    </span>
                @endif

                @if(request('rating'))
                    <span class="inline-flex items-center gap-1 bg-yellow-50 text-yellow-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                        <x-icon name="star" class="w-3 h-3 text-yellow-400" />
                        {{ request('rating') }}+
                    </span>
                @endif

                @if(request('condition'))
                    <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                        {{ request('condition') === 'new' ? 'Baru' : 'Bekas' }}
                    </span>
                @endif

                <a href="{{ route('products.index', request('q') ? ['q' => request('q')] : []) }}" class="text-xs text-red-500 hover:text-red-700 font-medium ml-2 transition-colors">
                    Hapus semua filter
                </a>
            </div>
        @endif
    </div>

    {{-- Main Content: Sidebar + Grid --}}
    <div x-data="{ filterOpen: false }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex gap-8">

            {{-- Mobile Filter Toggle --}}
            <button @click="filterOpen = true"
                class="lg:hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-brand-navy text-white font-bold text-sm px-6 py-3 rounded-full shadow-lg flex items-center gap-2 hover:bg-brand-navydark transition-colors">
                <x-icon name="funnel" class="w-5 h-5" />
                Filter Produk
            </button>

            {{-- Mobile Filter Overlay --}}
            <div x-show="filterOpen" x-cloak class="fixed inset-0 z-50 lg:hidden">
                <div @click="filterOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
                <div x-show="filterOpen" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                    class="relative w-[320px] max-w-[85vw] h-full bg-white overflow-y-auto shadow-2xl">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h2 class="font-bold text-lg text-gray-900">Filter Produk</h2>
                        <button @click="filterOpen = false" class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                            <x-icon name="x-mark" class="w-6 h-6" />
                        </button>
                    </div>
                    <div class="p-5">
                        @include('products.partials.filter-sidebar', ['categories' => $categories, 'class' => ''])
                    </div>
                </div>
            </div>

            {{-- Desktop Sidebar --}}
            <aside class="hidden lg:block w-[280px] shrink-0">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-28">
                    <h2 class="font-bold text-base text-gray-900 mb-5 flex items-center gap-2">
                        <x-icon name="funnel" class="w-5 h-5 text-brand-navy" />
                        Filter Produk
                    </h2>
                    @include('products.partials.filter-sidebar', ['categories' => $categories, 'class' => ''])
                </div>
            </aside>

            {{-- Product Grid --}}
            <div class="flex-1 min-w-0">
                @if($products->isEmpty())
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <x-icon name="magnifying-glass" class="w-10 h-10 text-gray-300" />
                        </div>
                        <h3 class="font-display font-bold text-xl text-gray-700 mb-2">Produk Tidak Ditemukan</h3>
                        <p class="text-gray-500 text-sm mb-6 max-w-md mx-auto">Coba ubah kata kunci pencarian atau sesuaikan filter Anda untuk menemukan produk yang tepat.</p>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-brand-navy hover:bg-brand-navydark text-white font-bold px-6 py-3 rounded-xl transition-colors text-sm">
                            <x-icon name="arrow-path" class="w-4 h-4" />
                            Reset Filter
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5">
                        @foreach($products as $product)
                            <x-product-card :product="$product" :wishlisted="in_array($product->id, $wishlistIds)" />
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Sync sort dropdown with filter form hidden input
        document.querySelectorAll('[data-sort-value]').forEach(el => {
            el.addEventListener('click', function(e) {
                document.getElementById('filterSortInput').value = this.dataset.sortValue;
            });
        });
    </script>
    @endpush
</x-app-layout>
