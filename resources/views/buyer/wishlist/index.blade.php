<x-app-layout>
    <x-slot name="title">Wishlist Saya</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-1" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900 flex items-center gap-3">
                    <svg class="w-7 h-7 text-red-500" viewBox="0 0 24 24" fill="currentColor"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" /></svg>
                    Wishlist Saya
                </h1>
                <p class="text-sm text-gray-500 mt-1">{{ $wishlists->total() }} produk di wishlist Anda</p>
            </div>
        </div>

        @if($wishlists->isEmpty())
            {{-- Empty Wishlist --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 sm:p-16 text-center">
                <div class="w-28 h-28 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-14 h-14 text-red-300" viewBox="0 0 24 24" fill="currentColor"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" /></svg>
                </div>
                <h2 class="font-display font-bold text-xl text-gray-700 mb-2">Wishlist Masih Kosong</h2>
                <p class="text-gray-500 text-sm mb-8 max-w-md mx-auto">Simpan produk favorit Anda di sini agar mudah ditemukan nanti.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-brand-blue hover:bg-blue-600 text-white font-bold px-8 py-3.5 rounded-xl shadow-sm transition-colors">
                    <x-icon name="magnifying-glass" class="w-5 h-5" />
                    Jelajahi Produk
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-5">
                @foreach($wishlists as $wishlist)
                    <div x-data="{ removing: false }" :class="removing ? 'opacity-40 pointer-events-none' : ''">
                        <div class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-brand-navy/20 transition-all duration-300 relative">
                            {{-- Remove from Wishlist Button --}}
                            <button @click="removing = true; fetch('{{ route('buyer.wishlist.toggle') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }, body: JSON.stringify({ product_id: {{ $wishlist->product_id }} }) }).then(() => { $el.closest('[x-data]').remove() })"
                                class="absolute top-2 right-2 z-10 w-8 h-8 bg-white/90 backdrop-blur rounded-full flex items-center justify-center shadow-sm text-red-500 hover:bg-red-500 hover:text-white transition-all opacity-0 group-hover:opacity-100">
                                <x-icon name="x-mark" class="w-4 h-4" />
                            </button>

                            {{-- Image --}}
                            <a href="{{ route('products.show', $wishlist->product->slug) }}" class="block relative overflow-hidden aspect-square bg-gray-50">
                                <img src="{{ $wishlist->product->primary_image_url }}" alt="{{ $wishlist->product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            </a>

                            {{-- Content --}}
                            <div class="p-3 sm:p-4">
                                <p class="text-xs text-gray-400 font-medium mb-1">{{ $wishlist->product->category->name ?? '' }}</p>
                                <a href="{{ route('products.show', $wishlist->product->slug) }}" class="block">
                                    <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-brand-navy transition-colors">
                                        {{ $wishlist->product->name }}
                                    </h3>
                                </a>
                                <div class="mt-2">
                                    <p class="text-base font-bold text-brand-blue">{{ $wishlist->product->formatted_price }}</p>
                                </div>
                                <div class="mt-2 flex items-center gap-1">
                                    <x-star-rating :value="$wishlist->product->avg_rating" size="w-3.5 h-3.5" />
                                    <span class="text-xs text-gray-400">{{ number_format($wishlist->product->avg_rating, 1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $wishlists->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
