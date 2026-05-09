<x-app-layout>
    <x-slot name="title">Checkout Pesanan</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8" x-data="checkoutPage({
            storesCount: {{ count($storesData) }},
            stores: {{ json_encode(collect($storesData)->map(fn($data, $id) => ['id' => $id, 'weight' => $data['totalWeight'], 'subtotal' => $data['subtotal'], 'origin_city_id' => $data['store']['city_id'] ?? 153])->values()) }},
            productSubtotal: {{ $totalPrice }},
            totalBuyerFees: {{ $totalBuyerFees }},
            midtransReady: {{ $midtransReady ? 'true' : 'false' }},
            addresses: {{ json_encode($addresses->map(fn($a) => [
                'id' => $a->id,
                'label' => $a->label,
                'recipient_name' => $a->recipient_name,
                'phone' => $a->phone,
                'full_address' => $a->full_address,
                'city' => $a->city,
                'province' => $a->province,
                'postal_code' => $a->postal_code,
                'city_id' => $a->city_id,
                'is_primary' => $a->is_primary,
            ])) }}
        })">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('buyer.cart.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900">Checkout Pesanan</h1>
                <p class="text-sm text-gray-500 mt-1">Pilih alamat pengiriman dan metode pembayaran.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('buyer.checkout.process') }}" class="flex flex-col lg:flex-row gap-8">
            @csrf
            <input type="hidden" name="selected_items" value="{{ json_encode($selectedItems) }}">
            <input type="hidden" name="address_id" :value="selectedAddressId">

            <div class="flex-1 min-w-0 space-y-6">

                {{-- Pilih Alamat Pengiriman --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-lg text-gray-900 mb-5 flex items-center gap-2">
                        <x-icon name="map-pin" class="w-6 h-6 text-brand-blue" />
                        Alamat Pengiriman
                    </h2>

                    @if($errors->any())
                        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <template x-for="addr in addresses" :key="addr.id">
                            <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all"
                                :class="selectedAddressId == addr.id ? 'border-brand-navy bg-brand-navylight/10 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="address_radio" :value="addr.id" x-model="selectedAddressId" @change="onAddressChange()" class="mt-1 text-brand-navy focus:ring-brand-navy">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-bold text-sm text-gray-900" x-text="addr.label"></span>
                                        <template x-if="addr.is_primary">
                                            <span class="text-[10px] font-bold bg-brand-blue text-white px-2 py-0.5 rounded-full">UTAMA</span>
                                        </template>
                                        <template x-if="!addr.city_id">
                                            <span class="text-[10px] font-bold bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full border border-yellow-300">Perlu diperbarui</span>
                                        </template>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700" x-text="addr.recipient_name + ' (' + addr.phone + ')'"></p>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2" x-text="addr.full_address"></p>
                                    <p class="text-xs text-gray-400 mt-1" x-text="addr.city + ', ' + addr.province + ' ' + addr.postal_code"></p>
                                </div>
                            </label>
                        </template>
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        <a href="{{ route('profile.edit') }}#address-section" class="text-sm font-semibold text-brand-blue hover:text-blue-700 transition-colors flex items-center gap-1">
                            <x-icon name="plus" class="w-4 h-4" />
                            Tambah / Ubah Alamat
                        </a>
                    </div>

                    {{-- Warning if selected address has no city_id --}}
                    <div x-show="selectedAddressId && !selectedAddress?.city_id" class="mt-4 bg-yellow-50 border border-yellow-200 text-yellow-700 p-3 rounded-xl text-sm flex items-start gap-2">
                        <x-icon name="exclamation-triangle" class="w-5 h-5 shrink-0 mt-0.5" />
                        <div>
                            Alamat ini belum memiliki data kota yang valid untuk menghitung ongkir. 
                            <a href="{{ route('profile.edit') }}#address-section" class="font-bold underline">Edit alamat ini</a> dan pilih provinsi/kota dari dropdown.
                        </div>
                    </div>
                </div>

                {{-- Daftar Produk Per Toko --}}
                @foreach($storesData as $storeId => $data)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-lg text-gray-900 mb-5 border-b border-gray-100 pb-4 flex justify-between items-center">
                        <span class="flex items-center gap-2">
                            <x-icon name="building-storefront" class="w-5 h-5 text-brand-navy" />
                            {{ $data['store']->name }}
                        </span>
                        <span class="text-sm font-medium text-gray-500 max-w-[200px] text-right truncate">Asal: {{ $data['store']->city->name ?? 'Kota Pengirim' }}</span>
                    </h2>

                    <div class="space-y-4 mb-6">
                        @foreach($data['items'] as $item)
                        <div class="flex gap-4">
                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-16 h-16 rounded-lg object-cover border border-gray-100 shrink-0">
                            <div class="flex-1 min-w-0 flex flex-col justify-between py-1">
                                <div>
                                    <p class="font-semibold text-sm text-gray-900 truncate">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->product->weight_gram }} gr / item</p>
                                </div>
                                <div class="flex justify-between items-center text-sm font-semibold mt-2">
                                    <span class="text-gray-500">Qty: {{ $item->quantity }}</span>
                                    <span class="text-gray-900">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- Pilihan Kurir Per Toko --}}
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <p class="text-sm font-semibold text-gray-700 mb-3 block">Metode Pengiriman</p>
                        
                        <div x-show="!selectedAddress?.city_id" class="text-sm text-yellow-600 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                            Pilih alamat dengan data kota valid terlebih dahulu untuk menghitung ongkir.
                        </div>

                        <div x-show="selectedAddress?.city_id" class="space-y-3">
                            <select name="couriers[{{ $storeId }}]" x-model="selectedCouriers[{{ $storeId }}]" @change="calculateShipping({{ $storeId }})" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:border-brand-navy focus:ring-brand-navy/20 bg-white">
                                <option value="">Pilih Kurir</option>
                                @if(isset($data['custom_couriers']) && $data['custom_couriers']->count() > 0)
                                    <optgroup label="Kurir Internal Toko">
                                        @foreach($data['custom_couriers'] as $customC)
                                            <option value="toko_{{ $customC->id }}">{{ $customC->name }} - Rp {{ number_format($customC->price, 0, ',', '.') }}{{ $customC->estimation ? ' ('.$customC->estimation.')' : '' }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                <optgroup label="Ekspedisi Reguler">
                                    <option value="jne">JNE</option>
                                    <option value="pos">POS Indonesia</option>
                                    <option value="tiki">TIKI</option>
                                </optgroup>
                            </select>

                            <div x-show="isCalculating[{{ $storeId }}]" class="text-xs text-gray-500 flex items-center gap-2">
                                <svg class="animate-spin h-3 w-3 text-brand-blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Menghitung tarif...
                            </div>
                            
                            <div x-show="ongkirError[{{ $storeId }}]" x-text="ongkirError[{{ $storeId }}]" class="text-xs text-red-500 font-medium"></div>

                            <div x-show="shippingCosts[{{ $storeId }}] > 0" class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Biaya Kirim (<span x-text="stores.find(s => s.id == {{ $storeId }}).weight"></span>g)</span>
                                <span class="font-bold text-gray-900" x-text="'Rp ' + formatRupiah(shippingCosts[{{ $storeId }}])"></span>
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach

            </div>

            {{-- Ringkasan Pembayaran --}}
            <div class="lg:w-[360px] shrink-0">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-28">
                    <h3 class="font-bold text-lg text-gray-900 mb-5">Ringkasan Pembayaran</h3>

                    <div class="space-y-3 text-sm border-b border-gray-100 pb-5 mb-5">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal Produk</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Total Ongkos Kirim <br><template x-if="isAnyCalculating"><span class="text-[10px] text-brand-blue mt-1 truncate">Sedang menghitung...</span></template></span>
                            <span class="font-semibold text-gray-900" x-text="totalShipping > 0 ? 'Rp ' + formatRupiah(totalShipping) : '-'"></span>
                        </div>
                        @if($totalBuyerFees > 0)
                        <div class="pt-3 border-t border-gray-50">
                            <p class="font-semibold text-gray-900 mb-1 text-xs">Biaya Tambahan</p>
                            @foreach($buyerFees as $fee)
                            <div class="flex justify-between text-gray-500 text-xs mb-1">
                                <span>{{ $fee->name }}</span>
                                <span>Rp {{ number_format($fee->amount, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-center mb-1">
                        <span class="font-bold text-gray-900">Total Tagihan</span>
                        <span class="font-display font-bold text-xl text-brand-blue" x-text="grandTotal > 0 ? 'Rp ' + formatRupiah(grandTotal) : 'Rp {{ number_format($totalPrice, 0, ',', '.') }}'"></span>
                    </div>
                    <p class="text-xs text-gray-400 mb-5 text-right">Termasuk PPN jika ada</p>

                    {{-- Payment Method --}}
                    <div class="border-t border-gray-100 pt-5 mb-5">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Metode Pembayaran</p>
                        @if($midtransReady)
                            <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-brand-navy bg-brand-navylight/10">
                                <x-icon name="shield-check" class="w-6 h-6 text-green-500 shrink-0" />
                                <div>
                                    <span class="text-sm font-semibold text-gray-900">Midtrans Payment Gateway</span>
                                    <p class="text-xs text-gray-500">Virtual Account, E-Wallet (GoPay, OVO, DANA), Kartu Kredit, QRIS</p>
                                </div>
                            </div>
                            <input type="hidden" name="payment_method" value="midtrans">
                        @else
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700 flex items-start gap-2">
                                <x-icon name="exclamation-triangle" class="w-5 h-5 shrink-0 mt-0.5" />
                                <div>
                                    <p class="font-bold">Payment Gateway belum dikonfigurasi</p>
                                    <p class="text-xs mt-1">Hubungi admin untuk mengaktifkan Midtrans agar checkout dapat dilakukan.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <button type="submit" :disabled="!canSubmit" class="w-full bg-brand-navy hover:bg-brand-navydark disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3.5 rounded-xl text-sm flex items-center justify-center gap-2 transition-all shadow-sm">
                        <x-icon name="shield-check" class="w-5 h-5" />
                        Lanjut ke Pembayaran
                    </button>

                    <div class="mt-5 flex items-center justify-center gap-2 text-xs text-gray-400">
                        <x-icon name="lock-closed" class="w-4 h-4" />
                        Pembayaran dijamin aman via Midtrans
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function checkoutPage(config) {
            return {
                stores: config.stores,
                addresses: config.addresses,
                productSubtotal: config.productSubtotal,
                totalBuyerFees: config.totalBuyerFees,
                customCouriersData: {!! json_encode(collect($storesData)->mapWithKeys(function($data, $id) {
                    return [$id => $data['custom_couriers']->map(fn($c) => ['id' => 'toko_'.$c->id, 'price' => $c->price])];
                })) !!},
                
                selectedAddressId: config.addresses.find(a => a.is_primary)?.id || config.addresses[0]?.id || '',
                
                selectedCouriers: {},
                shippingCosts: {},
                isCalculating: {},
                ongkirError: {},

                get selectedAddress() {
                    return this.addresses.find(a => a.id == this.selectedAddressId) || null;
                },

                init() {
                    this.stores.forEach(store => {
                        this.selectedCouriers[store.id] = "";
                        this.shippingCosts[store.id] = 0;
                        this.isCalculating[store.id] = false;
                        this.ongkirError[store.id] = null;
                    });
                },

                onAddressChange() {
                    // Reset all shipping when address changes
                    this.stores.forEach(store => {
                        this.selectedCouriers[store.id] = "";
                        this.shippingCosts[store.id] = 0;
                        this.ongkirError[store.id] = null;
                    });
                },

                async calculateShipping(storeId) {
                    const courier = this.selectedCouriers[storeId];
                    const addr = this.selectedAddress;
                    if (!courier || !addr?.city_id) return;

                    const store = this.stores.find(s => s.id == storeId);
                    
                    this.isCalculating[storeId] = true;
                    this.ongkirError[storeId] = null;
                    this.shippingCosts[storeId] = 0;

                    if (courier.startsWith('toko_')) {
                        let customCouriers = this.customCouriersData[storeId] || [];
                        let customItem = customCouriers.find(c => c.id === courier);
                        if(customItem) {
                            this.shippingCosts[storeId] = parseFloat(customItem.price);
                        } else {
                            this.ongkirError[storeId] = "Tarif kurir toko tidak valid.";
                        }
                        this.isCalculating[storeId] = false;
                        return;
                    }

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch('/api/ongkir/calculate', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                origin: store.origin_city_id,
                                destination: addr.city_id,
                                weight: store.weight,
                                courier: courier
                            })
                        });

                        const data = await res.json();
                        if (data.success && data.data.length > 0) {
                            this.shippingCosts[storeId] = data.data[0].cost[0].value;
                        } else {
                            this.ongkirError[storeId] = data.message || "Layanan tidak didukung untuk rute ini.";
                            this.selectedCouriers[storeId] = "";
                        }
                    } catch (e) {
                        this.ongkirError[storeId] = "Gagal menghubungi server ongkir.";
                    } finally {
                        this.isCalculating[storeId] = false;
                    }
                },

                get isAnyCalculating() {
                    return Object.values(this.isCalculating).some(status => status === true);
                },

                get totalShipping() {
                    return Object.values(this.shippingCosts).reduce((a, b) => a + (b || 0), 0);
                },

                get grandTotal() {
                    return this.productSubtotal + this.totalShipping + this.totalBuyerFees;
                },

                get canSubmit() {
                    if (!this.selectedAddressId) return false;
                    
                    for (const store of this.stores) {
                        if (!this.selectedCouriers[store.id] || this.shippingCosts[store.id] === 0) {
                            return false;
                        }
                    }
                    return {{ $midtransReady ? 'true' : 'false' }};
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
