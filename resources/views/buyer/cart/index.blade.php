<x-app-layout>
    <x-slot name="title">Keranjang Belanja</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 pb-28 lg:pb-8"
        x-data="{
            selectedItems: [],
            selectAll: false,
            
            get selectedTotalPrice() {
                let total = 0;
                this.selectedItems.forEach(id => {
                    const el = document.getElementById('item-price-' + id);
                    if (el) total += parseInt(el.value) * parseInt(document.getElementById('item-qty-' + id).value);
                });
                return total;
            },

            get selectedTotalCount() {
                let count = 0;
                this.selectedItems.forEach(id => {
                    const el = document.getElementById('item-qty-' + id);
                    if (el) count += parseInt(el.value);
                });
                return count;
            },

            toggleAll() {
                if (this.selectAll) {
                    this.selectedItems = Array.from(document.querySelectorAll('.item-checkbox')).map(cb => cb.value);
                } else {
                    this.selectedItems = [];
                }
            },

            checkAllState() {
                const totalCheckboxes = document.querySelectorAll('.item-checkbox').length;
                this.selectAll = totalCheckboxes > 0 && this.selectedItems.length === totalCheckboxes;
            },

            async updateQty(cartId, qty, productPrice) {
                try {
                    const res = await fetch(`/buyer/cart/${cartId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ quantity: qty })
                    });
                    const data = await res.json();
                    if (res.ok) {
                        // Update nav badge
                        const badge = document.getElementById('navCartBadge');
                        if (badge) badge.textContent = data.cartCount;
                        
                        // Update DOM internal inputs
                        document.getElementById('item-qty-' + cartId).value = qty;
                        
                        // Update visual subtotal
                        const sub = document.getElementById('subtotal-' + cartId);
                        if (sub) sub.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(qty * productPrice);
                    } else {
                        alert(data.message || 'Gagal memperbarui.');
                    }
                } catch(e) {
                    alert('Terjadi kesalahan jaringan.');
                }
            },

            async removeItem(cartId) {
                try {
                    const res = await fetch(`/buyer/cart/${cartId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        }
                    });
                    const data = await res.json();
                    if (res.ok) {
                        document.getElementById('cart-item-' + cartId)?.remove();
                        this.selectedItems = this.selectedItems.filter(id => id !== cartId.toString());
                        
                        const badge = document.getElementById('navCartBadge');
                        if (badge) badge.textContent = data.cartCount;
                        if (data.cartCount === 0) {
                            window.location.reload();
                        }
                        // Remove empty store groups
                        document.querySelectorAll('.store-group').forEach(g => {
                            if (g.querySelectorAll('.cart-item').length === 0) g.remove();
                        });
                        this.checkAllState();
                    }
                } catch(e) {
                    alert('Terjadi kesalahan.');
                }
            },
        }">

        {{-- Page Header --}}
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-1" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900 flex items-center gap-3">
                    <x-icon name="shopping-cart" class="w-7 h-7 text-brand-blue" />
                    Keranjang Belanja
                </h1>
            </div>
        </div>

        @if($cartItems->isEmpty())
            {{-- Empty Cart --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 sm:p-16 text-center">
                <div class="w-28 h-28 bg-brand-bluelight rounded-full flex items-center justify-center mx-auto mb-6">
                    <x-icon name="shopping-cart" class="w-14 h-14 text-brand-blue/50" />
                </div>
                <h2 class="font-display font-bold text-xl text-gray-700 mb-2">Keranjang Masih Kosong</h2>
                <p class="text-gray-500 text-sm mb-8 max-w-md mx-auto">Ayo mulai belanja dan temukan produk infrastruktur IT terbaik untuk kebutuhan Anda.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-brand-blue hover:bg-blue-600 text-white font-bold px-8 py-3.5 rounded-xl shadow-sm transition-colors">
                    <x-icon name="magnifying-glass" class="w-5 h-5" />
                    Jelajahi Produk
                </a>
            </div>
        @else
            <form action="{{ route('buyer.checkout.index') }}" method="POST" class="flex flex-col lg:flex-row gap-8">
                @csrf
                {{-- Cart Items (grouped by store) --}}
                <div class="flex-1 min-w-0 space-y-5">
                    {{-- Master Checkbox --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll()"
                            class="w-5 h-5 text-brand-blue border-gray-300 rounded focus:ring-brand-blue/50 cursor-pointer">
                        <label class="font-semibold text-gray-700 cursor-pointer" @click="selectAll = !selectAll; toggleAll()">Pilih Semua Barang</label>
                    </div>

                    @foreach($grouped as $storeId => $items)
                        @php $store = $items->first()->product->store; @endphp
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden store-group">
                            {{-- Store Header --}}
                            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-100 bg-gray-50/80">
                                <img src="{{ $store->logo_url }}" alt="{{ $store->name }}" class="w-8 h-8 rounded-full border border-gray-200 object-cover">
                                <h3 class="font-semibold text-sm text-gray-800 truncate">{{ $store->name }}</h3>
                                @if($store->is_verified)
                                    <x-icon name="check-badge" class="w-4 h-4 text-green-500 shrink-0" />
                                @endif
                            </div>

                            {{-- Items --}}
                            <div class="divide-y divide-gray-50">
                                @foreach($items as $cartItem)
                                    <div id="cart-item-{{ $cartItem->id }}" class="cart-item flex gap-4 p-5"
                                        x-data="{ qty: {{ $cartItem->quantity }}, price: {{ $cartItem->product->price }}, stock: {{ $cartItem->product->stock }} }">
                                        
                                        {{-- Checkbox --}}
                                        <div class="pt-2 sm:pt-8">
                                            <input type="checkbox" name="selected_items[]" value="{{ $cartItem->id }}" x-model="selectedItems" @change="checkAllState()"
                                                class="item-checkbox w-5 h-5 text-brand-blue border-gray-300 rounded focus:ring-brand-blue/50 cursor-pointer">
                                        </div>

                                        {{-- Hidden Data --}}
                                        <input type="hidden" id="item-qty-{{ $cartItem->id }}" value="{{ $cartItem->quantity }}">
                                        <input type="hidden" id="item-price-{{ $cartItem->id }}" value="{{ $cartItem->product->price }}">

                                        {{-- Product Image --}}
                                        <a href="{{ route('products.show', $cartItem->product->slug) }}" class="shrink-0">
                                            <img src="{{ $cartItem->product->primary_image_url }}" alt="{{ $cartItem->product->name }}"
                                                class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl object-cover border border-gray-100">
                                        </a>

                                        {{-- Product Info --}}
                                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                                            <div>
                                                <a href="{{ route('products.show', $cartItem->product->slug) }}" class="font-semibold text-sm text-gray-900 hover:text-brand-navy transition-colors line-clamp-2">
                                                    {{ $cartItem->product->name }}
                                                </a>
                                                <p class="text-xs text-gray-400 mt-1">{{ $cartItem->product->category->name ?? '' }}</p>
                                            </div>

                                            <div class="flex flex-wrap items-center justify-between gap-3 mt-3">
                                                {{-- Quantity Controls --}}
                                                <div class="inline-flex items-center bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                                    <button type="button" @click="qty = Math.max(1, qty - 1); updateQty({{ $cartItem->id }}, qty, price)"
                                                        :disabled="qty <= 1"
                                                        class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-200 disabled:opacity-30 transition-colors">
                                                        <x-icon name="minus" class="w-3.5 h-3.5" />
                                                    </button>
                                                    <input type="number" x-model.number="qty" min="1" :max="stock"
                                                        @change="qty = Math.max(1, Math.min(qty, stock)); updateQty({{ $cartItem->id }}, qty, price)"
                                                        class="w-12 h-8 text-center border-0 bg-white text-sm font-medium focus:ring-0">
                                                    <button type="button" @click="qty = Math.min(qty + 1, stock); updateQty({{ $cartItem->id }}, qty, price)"
                                                        :disabled="qty >= stock"
                                                        class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-200 disabled:opacity-30 transition-colors">
                                                        <x-icon name="plus" class="w-3.5 h-3.5" />
                                                    </button>
                                                </div>

                                                {{-- Price & Delete --}}
                                                <div class="flex items-center gap-4">
                                                    <span id="subtotal-{{ $cartItem->id }}" class="font-bold text-brand-blue text-sm sm:text-base">
                                                        Rp {{ number_format($cartItem->product->price * $cartItem->quantity, 0, ',', '.') }}
                                                    </span>
                                                    <button type="button" @click="removeItem({{ $cartItem->id }})"
                                                        class="text-gray-300 hover:text-red-500 transition-colors p-1">
                                                        <x-icon name="trash" class="w-5 h-5" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order Summary Sidebar --}}
                <div class="fixed bottom-0 left-0 right-0 z-40 lg:static lg:w-[340px] shrink-0">
                    <div class="bg-white lg:rounded-2xl border-t lg:border border-gray-200 lg:border-gray-100 shadow-[0_-8px_30px_rgb(0,0,0,0.08)] lg:shadow-sm p-4 sm:p-6 lg:sticky lg:top-28">
                        <h3 class="hidden lg:block font-bold text-lg text-gray-900 mb-5">Ringkasan Belanja</h3>

                        <div class="hidden lg:block space-y-3 text-sm border-b border-gray-100 pb-5 mb-5">
                            <div class="flex justify-between text-gray-600">
                                <span>Total Item Terpilih</span>
                                <span class="font-semibold" x-text="selectedTotalCount + ' pcs'"></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal Produk</span>
                                <span class="font-semibold" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedTotalPrice)"></span>
                            </div>
                        </div>

                        <div class="flex flex-row lg:flex-col justify-between lg:justify-start items-center lg:items-stretch lg:mb-6 gap-3 lg:gap-5">
                            <div class="flex-1 lg:flex-none flex flex-col lg:flex-row lg:justify-between lg:items-center">
                                <span class="hidden lg:block font-bold text-gray-900">Total Tagihan</span>
                                <span class="block lg:hidden text-[11px] text-gray-500 font-semibold mb-0.5 uppercase tracking-wide">Total Tagihan</span>
                                <span class="font-display font-bold text-lg sm:text-xl text-brand-blue leading-none lg:text-right" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedTotalPrice)"></span>
                            </div>

                            <button type="submit" :disabled="selectedItems.length === 0" 
                                class="w-auto lg:w-full text-white font-bold py-3 lg:py-3.5 px-6 lg:px-0 rounded-xl text-sm flex items-center justify-center gap-2 transition-all shadow-sm"
                                :class="selectedItems.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-brand-navy hover:bg-brand-navydark'">
                                Checkout <span class="hidden lg:inline">Sekarang</span> <span class="lg:hidden" x-show="selectedTotalCount > 0" x-text="'(' + selectedTotalCount + ')'"></span>
                            </button>
                        </div>

                        @if(session('error'))
                            <p class="text-xs text-red-500 mt-3 text-center border-t border-gray-100 pt-3">{{ session('error') }}</p>
                        @endif

                        <a href="{{ route('products.index') }}" class="hidden lg:block w-full text-center text-sm text-brand-navy hover:text-brand-navydark font-semibold mt-4 py-2 transition-colors">
                            &larr; Lanjut Belanja
                        </a>
                    </div>
                </div>
            </form>
        @endif
    </div>
</x-app-layout>
