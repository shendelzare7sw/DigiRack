<x-app-layout>
    <x-slot name="title">{{ $product->name }}</x-slot>

    {{-- Breadcrumb --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <nav class="flex items-center text-sm text-gray-500 font-medium flex-wrap gap-y-1" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-brand-navy transition-colors">Home</a>
            <x-icon name="chevron-right" class="w-4 h-4 mx-2 text-gray-300" />
            <a href="{{ route('products.index') }}" class="hover:text-brand-navy transition-colors">Produk</a>
            @if($product->category)
                <x-icon name="chevron-right" class="w-4 h-4 mx-2 text-gray-300" />
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-brand-navy transition-colors">{{ $product->category->name }}</a>
            @endif
            <x-icon name="chevron-right" class="w-4 h-4 mx-2 text-gray-300" />
            <span class="text-brand-navy font-semibold truncate max-w-[200px]">{{ $product->name }}</span>
        </nav>
    </div>

    {{-- Product Hero Section --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
        x-data="{
            activeImage: 0,
            touchStartX: 0,
            images: {{ Js::from($product->images->count() > 0 ? $product->images->pluck('url')->toArray() : [$product->primary_image_url]) }},
            quantity: 1,
            maxStock: {{ $product->stock }},
            isWishlisted: {{ $isWishlisted ? 'true' : 'false' }},
            addingToCart: false,
            wishlistLoading: false,

            incrementQty() { if (this.quantity < this.maxStock) this.quantity++ },
            decrementQty() { if (this.quantity > 1) this.quantity-- },

            async addToCart() {
                @guest
                    window.location.href = '{{ route('login') }}';
                    return;
                @endguest

                this.addingToCart = true;
                try {
                    const res = await fetch('{{ route('buyer.cart.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ product_id: {{ $product->id }}, quantity: this.quantity })
                    });
                    const data = await res.json();
                    if (res.ok) {
                        // Update cart badge in navbar
                        const badge = document.getElementById('navCartBadge');
                        if (badge) { badge.textContent = data.cartCount; badge.classList.remove('hidden'); }
                        this.showToast(data.message, 'success');
                    } else {
                        this.showToast(data.message || 'Gagal menambahkan ke keranjang.', 'error');
                    }
                } catch (e) {
                    this.showToast('Terjadi kesalahan jaringan.', 'error');
                } finally {
                    this.addingToCart = false;
                }
            },

            async toggleWishlist() {
                @guest
                    window.location.href = '{{ route('login') }}';
                    return;
                @endguest

                this.wishlistLoading = true;
                try {
                    const res = await fetch('{{ route('buyer.wishlist.toggle') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ product_id: {{ $product->id }} })
                    });
                    const data = await res.json();
                    if (res.ok) {
                        this.isWishlisted = data.status === 'added';
                        this.showToast(data.message, 'success');
                    }
                } catch(e) {
                    this.showToast('Gagal memperbarui wishlist.', 'error');
                } finally {
                    this.wishlistLoading = false;
                }
            },

            showToast(message, type) {
                // Dispatch custom event for toast component
                window.dispatchEvent(new CustomEvent('show-toast', { detail: { message, type } }));
            },
        }">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Left: Image Gallery --}}
            <div class="w-full lg:w-[480px] xl:w-[540px] shrink-0">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    {{-- Main Image --}}
                    <div class="relative aspect-square bg-gray-50 overflow-hidden cursor-zoom-in"
                        @click="$dispatch('open-lightbox', { index: activeImage })"
                        @touchstart.passive="touchStartX = $event.touches[0].clientX"
                        @touchend="
                            const diff = touchStartX - $event.changedTouches[0].clientX;
                            if (Math.abs(diff) > 50) {
                                if (diff > 0 && activeImage < images.length - 1) activeImage++;
                                else if (diff < 0 && activeImage > 0) activeImage--;
                            }
                        ">
                        <template x-for="(img, idx) in images" :key="idx">
                            <img :src="img" :alt="'{{ $product->name }} - gambar ' + (idx+1)"
                                x-show="activeImage === idx"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="w-full h-full object-contain p-4"
                            >
                        </template>

                        @if($product->flashSale)
                            <div class="absolute top-4 left-4 bg-red-500 text-white text-sm font-bold px-3 py-1.5 rounded-full shadow-lg">
                                -{{ $product->flashSale->discount_percent }}%
                            </div>
                        @endif

                        {{-- Nav arrows on main image --}}
                        <button x-show="images.length > 1 && activeImage > 0" @click.stop="activeImage--"
                            class="absolute left-2 top-1/2 -translate-y-1/2 w-9 h-9 bg-white/80 backdrop-blur rounded-full shadow flex items-center justify-center text-gray-600 hover:bg-white transition-all">
                            <x-icon name="chevron-left" class="w-5 h-5" />
                        </button>
                        <button x-show="images.length > 1 && activeImage < images.length - 1" @click.stop="activeImage++"
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 bg-white/80 backdrop-blur rounded-full shadow flex items-center justify-center text-gray-600 hover:bg-white transition-all">
                            <x-icon name="chevron-right" class="w-5 h-5" />
                        </button>

                        {{-- Image counter --}}
                        <div x-show="images.length > 1" class="absolute bottom-3 right-3 bg-black/50 text-white text-xs font-medium px-2.5 py-1 rounded-full backdrop-blur-sm">
                            <span x-text="activeImage + 1"></span>/<span x-text="images.length"></span>
                        </div>
                    </div>

                    {{-- Thumbnail Row --}}
                    <div class="flex gap-2 p-3 overflow-x-auto hide-scroll bg-gray-50/50 border-t border-gray-100" x-show="images.length > 1">
                        <template x-for="(img, idx) in images" :key="'thumb-'+idx">
                            <button @click="activeImage = idx"
                                class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden border-2 shrink-0 transition-all"
                                :class="activeImage === idx ? 'border-brand-navy shadow-sm' : 'border-transparent opacity-60 hover:opacity-100'">
                                <img :src="img" alt="" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Right: Product Info --}}
            <div class="flex-1 min-w-0">
                {{-- Category Badge --}}
                @if($product->category)
                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                        class="inline-flex items-center gap-1.5 bg-brand-navylight text-brand-navy text-xs font-semibold px-3 py-1.5 rounded-full mb-3 hover:bg-brand-navy hover:text-white transition-colors">
                        {{ $product->category->name }}
                    </a>
                @endif

                {{-- Product Name --}}
                <h1 class="font-display font-bold text-xl sm:text-2xl lg:text-3xl text-gray-900 leading-tight mb-3">
                    {{ $product->name }}
                </h1>

                {{-- Rating & Stats --}}
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 mb-4 text-sm">
                    <div class="flex items-center gap-1.5">
                        <x-star-rating :value="$product->avg_rating" size="w-4 h-4" />
                        <span class="font-semibold text-gray-700">{{ number_format($product->avg_rating, 1) }}</span>
                        <span class="text-gray-400">({{ $product->reviews->count() }} ulasan)</span>
                    </div>
                    <span class="text-gray-300">|</span>
                    <span class="text-gray-500">
                        <span class="font-semibold text-gray-700">{{ $product->sold_count }}</span> terjual
                    </span>
                </div>

                {{-- Price --}}
                <div class="bg-gradient-to-r from-brand-bluelight to-blue-50/50 rounded-2xl p-5 mb-5">
                    @if($product->flashSale)
                        <p class="text-sm text-gray-400 line-through mb-1">{{ $product->formatted_price }}</p>
                        <p class="font-display font-bold text-3xl sm:text-4xl text-brand-blue">
                            {{ $product->flashSale->formatted_sale_price }}
                        </p>
                        <div class="mt-2 inline-flex items-center gap-1.5 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                            <x-icon name="fire" class="w-3.5 h-3.5" />
                            Flash Sale -{{ $product->flashSale->discount_percent }}%
                        </div>
                    @else
                        <p class="font-display font-bold text-3xl sm:text-4xl text-brand-blue">
                            {{ $product->formatted_price }}
                        </p>
                    @endif
                </div>

                {{-- Badges: Condition & Stock --}}
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full {{ $product->condition === 'new' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' }}">
                        <x-icon name="{{ $product->condition === 'new' ? 'check-badge' : 'arrow-path' }}" class="w-3.5 h-3.5" />
                        {{ $product->condition === 'new' ? 'Baru' : 'Bekas' }}
                    </span>

                    @if($product->stock > 10)
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-green-50 text-green-700 border border-green-200">
                            <x-icon name="check-circle" class="w-3.5 h-3.5" />
                            Stok Tersedia ({{ $product->stock }})
                        </span>
                    @elseif($product->stock > 0)
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-brand-bluelight text-brand-blue border border-brand-blue/20">
                            <x-icon name="exclamation-triangle" class="w-3.5 h-3.5" />
                            Stok Terbatas ({{ $product->stock }})
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-red-50 text-red-600 border border-red-200">
                            <x-icon name="x-circle" class="w-3.5 h-3.5" />
                            Stok Habis
                        </span>
                    @endif
                </div>

                {{-- Quantity Selector & CTA --}}
                @if($product->isInStock() && (!Auth::check() || Auth::user()->role !== 'admin'))
                    <div class="space-y-4">
                        {{-- Quantity --}}
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-gray-700">Jumlah:</span>
                            <div class="inline-flex items-center bg-gray-100 rounded-xl overflow-hidden border border-gray-200">
                                <button @click="decrementQty()" :disabled="quantity <= 1"
                                    class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-200 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                    <x-icon name="minus" class="w-4 h-4" />
                                </button>
                                <input type="number" x-model.number="quantity" min="1" :max="maxStock"
                                    class="w-16 h-10 text-center border-0 bg-white text-sm font-semibold focus:ring-0"
                                    @input="quantity = Math.max(1, Math.min(quantity, maxStock))"
                                >
                                <button @click="incrementQty()" :disabled="quantity >= maxStock"
                                    class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-200 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                    <x-icon name="plus" class="w-4 h-4" />
                                </button>
                            </div>
                            <span class="text-xs text-gray-400">Maks. <span x-text="maxStock"></span> pcs</span>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3">
                            {{-- Form Beli Langsung --}}
                            <form action="{{ route('buyer.checkout.init') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" :value="quantity">
                                <button type="submit" 
                                    class="w-full bg-brand-navy hover:bg-brand-navydark text-white font-bold py-3.5 px-6 rounded-xl shadow-sm flex items-center justify-center gap-2 transition-all text-sm sm:text-base active:scale-[0.98]">
                                    <x-icon name="credit-card" class="w-5 h-5" />
                                    Beli Langsung
                                </button>
                            </form>

                            <button @click="addToCart()"
                                :disabled="addingToCart"
                                class="flex-1 bg-brand-blue hover:bg-blue-600 disabled:opacity-60 text-white font-bold py-3.5 px-6 rounded-xl shadow-sm flex items-center justify-center gap-2 transition-all text-sm sm:text-base active:scale-[0.98]">
                                <x-icon name="shopping-cart" class="w-5 h-5" />
                                <span x-text="addingToCart ? 'Menambahkan...' : '+ Keranjang'"></span>
                            </button>
                            <button @click="toggleWishlist()"
                                :disabled="wishlistLoading"
                                class="sm:w-auto px-5 py-3.5 rounded-xl font-bold text-sm border-2 flex items-center justify-center gap-2 transition-all active:scale-[0.98]"
                                :class="isWishlisted ? 'bg-red-50 border-red-300 text-red-500 hover:bg-red-100' : 'border-gray-200 text-gray-600 hover:border-brand-navy hover:text-brand-navy'">
                                <svg class="w-5 h-5 transition-transform" :class="isWishlisted ? 'scale-110' : ''" viewBox="0 0 24 24" :fill="isWishlisted ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                                <span x-text="isWishlisted ? 'Wishlisted' : 'Wishlist'" class="hidden sm:inline"></span>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-200">
                        <x-icon name="x-circle" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                        <p class="text-gray-500 font-medium">Stok produk ini sedang habis</p>
                        <p class="text-xs text-gray-400 mt-1">Tambahkan ke wishlist untuk notifikasi ketersediaan.</p>
                        <button @click="toggleWishlist()" 
                            class="mt-4 inline-flex items-center gap-2 bg-brand-navy hover:bg-brand-navydark text-white font-bold text-sm px-5 py-2.5 rounded-xl transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" :fill="isWishlisted ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                            <span x-text="isWishlisted ? 'Sudah di Wishlist' : 'Tambah ke Wishlist'"></span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Store Info Card --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <img src="{{ $product->store->logo_url }}" alt="{{ $product->store->name }}"
                class="w-14 h-14 rounded-full border-2 border-gray-100 object-cover shrink-0">
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-gray-900 truncate">{{ $product->store->name }}</h3>
                <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                    @if($product->store->is_verified)
                        <span class="inline-flex items-center gap-1 text-green-600 font-semibold">
                            <x-icon name="check-badge" class="w-4 h-4" />
                            Terverifikasi
                        </span>
                    @endif
                    <span class="flex items-center gap-1">
                        <x-icon name="star" class="w-3.5 h-3.5 text-yellow-400" />
                        {{ number_format($product->store->avg_rating, 1) }}
                    </span>
                    <span class="flex items-center gap-1">
                        <x-icon name="cube" class="w-3.5 h-3.5 text-gray-400" />
                        {{ $storeProductCount }} produk
                    </span>
                </div>
            </div>
            <a href="{{ route('store.show', $product->store->slug) }}" class="shrink-0 border-2 border-brand-navy text-brand-navy hover:bg-brand-navy hover:text-white font-bold text-sm px-5 py-2.5 rounded-xl transition-colors">
                Kunjungi Toko
            </a>
        </div>
    </section>

    {{-- Tabs: Deskripsi / Spesifikasi / Ulasan --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10"
        x-data="{ activeTab: 'deskripsi' }">

        {{-- Tab Headers --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex gap-1 -mb-px overflow-x-auto hide-scroll">
                @foreach(['deskripsi' => 'Deskripsi', 'spesifikasi' => 'Spesifikasi', 'ulasan' => 'Ulasan (' . $product->reviews->count() . ')'] as $key => $label)
                    <button @click="activeTab = '{{ $key }}'"
                        class="whitespace-nowrap pb-3 px-5 text-sm font-semibold transition-colors border-b-2 shrink-0"
                        :class="activeTab === '{{ $key }}'
                            ? 'text-brand-navy border-brand-navy'
                            : 'text-gray-400 border-transparent hover:text-gray-600 hover:border-gray-300'">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Tab: Deskripsi --}}
        <div x-show="activeTab === 'deskripsi'" x-transition class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>

        {{-- Tab: Spesifikasi --}}
        <div x-show="activeTab === 'spesifikasi'" x-cloak x-transition class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
            @if(count($specs) > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($specs as $spec)
                        <div class="flex py-3.5 px-1 gap-4">
                            <span class="text-sm font-medium text-gray-500 w-40 shrink-0">{{ $spec['label'] ?? '-' }}</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $spec['value'] ?? '-' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <x-icon name="clipboard-document-list" class="w-10 h-10 mx-auto mb-3 text-gray-300" />
                    <p>Spesifikasi belum tersedia untuk produk ini.</p>
                </div>
            @endif
        </div>

        {{-- Tab: Ulasan --}}
        <div x-show="activeTab === 'ulasan'" x-cloak x-transition class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
            @if($product->reviews->count() > 0)
                {{-- Rating Summary --}}
                <div class="flex flex-col sm:flex-row gap-8 mb-8 pb-8 border-b border-gray-100">
                    <div class="text-center sm:text-left shrink-0">
                        <div class="text-5xl font-display font-bold text-gray-900">{{ number_format($product->avg_rating, 1) }}</div>
                        <div class="mt-2">
                            <x-star-rating :value="$product->avg_rating" size="w-5 h-5" />
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ $product->reviews->count() }} ulasan</p>
                    </div>

                    {{-- Rating Distribution Bars --}}
                    <div class="flex-1 space-y-2">
                        @foreach($ratingDist as $star => $data)
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium text-gray-500 w-4 text-right">{{ $star }}</span>
                                <x-icon name="star" class="w-3.5 h-3.5 text-yellow-400" />
                                <div class="flex-1 bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="h-full bg-yellow-400 rounded-full transition-all duration-500" style="width: {{ $data['percent'] }}%"></div>
                                </div>
                                <span class="text-xs text-gray-400 w-8">{{ $data['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Review list --}}
                <div class="space-y-6">
                    @foreach($product->reviews as $review)
                        <div class="flex gap-4">
                            <img src="{{ $review->buyer->avatar_url }}" alt="{{ $review->buyer->name }}"
                                class="w-10 h-10 rounded-full border border-gray-100 shrink-0 object-cover">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-sm text-gray-800">{{ $review->buyer->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <x-star-rating :value="$review->rating" size="w-3.5 h-3.5" />
                                @if($review->comment)
                                    <p class="mt-2 text-sm text-gray-700 leading-relaxed">{{ $review->comment }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <x-icon name="chat-bubble-bottom-center-text" class="w-10 h-10 mx-auto mb-3 text-gray-300" />
                    <p class="font-medium">Belum ada ulasan</p>
                    <p class="text-xs mt-1">Jadilah yang pertama memberikan ulasan untuk produk ini.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Related Products --}}
    @if($relatedProducts->count() > 0)
    <section class="bg-gray-50 py-10 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="font-display font-bold text-xl sm:text-2xl text-gray-900 mb-6 flex items-center gap-2">
                <x-icon name="squares-2x2" class="w-6 h-6 text-brand-blue" />
                Produk Serupa
            </h2>

            <div x-data="{
                scrollLeft() { $refs.related.scrollBy({ left: -300, behavior: 'smooth' }) },
                scrollRight() { $refs.related.scrollBy({ left: 300, behavior: 'smooth' }) }
            }" class="relative group">
                <button @click="scrollLeft" class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 bg-white w-10 h-10 rounded-full shadow-lg text-gray-700 hover:text-brand-blue hidden md:flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 hover:scale-110">
                    <x-icon name="chevron-left" class="w-5 h-5" />
                </button>
                <button @click="scrollRight" class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 bg-white w-10 h-10 rounded-full shadow-lg text-gray-700 hover:text-brand-blue hidden md:flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 hover:scale-110">
                    <x-icon name="chevron-right" class="w-5 h-5" />
                </button>

                <div x-ref="related" class="flex overflow-x-auto pb-4 -mx-4 px-4 sm:mx-0 sm:px-1 gap-4 sm:gap-5 snap-x snap-mandatory hide-scroll scroll-smooth">
                    @foreach($relatedProducts as $rp)
                        <div class="w-[220px] sm:w-[240px] md:w-[260px] shrink-0 snap-start">
                            <x-product-card :product="$rp" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Lightbox Modal --}}
    <div x-data="{
            lbOpen: false,
            lbIndex: 0,
            lbImages: {{ Js::from($product->images->count() > 0 ? $product->images->pluck('url')->toArray() : [$product->primary_image_url]) }},
            lbTouchX: 0,
            next() { if (this.lbIndex < this.lbImages.length - 1) this.lbIndex++; },
            prev() { if (this.lbIndex > 0) this.lbIndex--; },
        }"
        @open-lightbox.window="lbIndex = $event.detail.index; lbOpen = true"
        @keydown.escape.window="lbOpen = false"
        @keydown.right.window="if(lbOpen) next()"
        @keydown.left.window="if(lbOpen) prev()">

        <div x-show="lbOpen" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[999] bg-black/90 flex items-center justify-center"
            @touchstart.passive="lbTouchX = $event.touches[0].clientX"
            @touchend="const d = lbTouchX - $event.changedTouches[0].clientX; if(Math.abs(d) > 50) { d > 0 ? next() : prev(); }">

            {{-- Close Button — top right, very prominent --}}
            <button @click="lbOpen = false" class="absolute top-4 right-4 z-50 w-12 h-12 bg-white hover:bg-gray-100 rounded-full shadow-xl flex items-center justify-center text-gray-800 transition-all active:scale-90 touch-manipulation border border-gray-200">
                <x-icon name="x-mark" class="w-7 h-7" />
            </button>

            {{-- Prev --}}
            <button x-show="lbIndex > 0" @click.stop="prev()"
                class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 z-10 w-11 h-11 bg-white/90 hover:bg-white backdrop-blur rounded-full shadow-lg flex items-center justify-center text-gray-700 transition-colors touch-manipulation">
                <x-icon name="chevron-left" class="w-6 h-6" />
            </button>

            {{-- Next --}}
            <button x-show="lbIndex < lbImages.length - 1" @click.stop="next()"
                class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 z-10 w-11 h-11 bg-white/90 hover:bg-white backdrop-blur rounded-full shadow-lg flex items-center justify-center text-gray-700 transition-colors touch-manipulation">
                <x-icon name="chevron-right" class="w-6 h-6" />
            </button>

            {{-- Image --}}
            <img :src="lbImages[lbIndex]" alt="" class="max-w-[90vw] max-h-[85vh] object-contain select-none rounded-lg shadow-2xl" @click.stop>

            {{-- Counter --}}
            <div x-show="lbImages.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-white/90 backdrop-blur text-gray-800 text-sm font-bold px-4 py-1.5 rounded-full shadow-lg">
                <span x-text="lbIndex + 1"></span> / <span x-text="lbImages.length"></span>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @endpush
</x-app-layout>
