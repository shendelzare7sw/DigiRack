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
                'district' => $a->district,
                'province' => $a->province,
                'postal_code' => $a->postal_code,
                'shipping_fee' => app(\App\Services\DeliveryAreaService::class)->shippingFee($a),
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
                                        <span class="text-[10px] font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded-full border border-green-200">TERJANGKAU</span>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700" x-text="addr.recipient_name + ' (' + addr.phone + ')'"></p>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2" x-text="addr.full_address"></p>
                                    <p class="text-xs text-gray-400 mt-1" x-text="'Kec. ' + addr.district + ', ' + addr.city + ', ' + addr.province + ' ' + addr.postal_code"></p>
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

                </div>

                {{-- Daftar Produk --}}
                @foreach($storesData as $storeId => $data)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-lg text-gray-900 mb-5 border-b border-gray-100 pb-4 flex items-center gap-2">
                        <x-icon name="shopping-bag" class="w-5 h-5 text-brand-navy" />
                        Produk Pesanan
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
                    
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <input type="hidden" name="couriers[{{ $storeId }}]" value="digital_hook_sameday">
                        <div class="flex items-start gap-3"><x-icon name="truck" class="w-6 h-6 text-brand-blue shrink-0" /><div class="flex-1"><p class="text-sm font-bold text-gray-900">{{ config('digitalhook.courier_name') }}</p><p class="text-xs text-gray-600 mt-1">Diantar langsung pada hari yang sama untuk pesanan sebelum pukul {{ config('digitalhook.order_cutoff') }}.</p><div class="mt-2 flex justify-between text-sm"><span>Tarif wilayah</span><strong x-text="selectedAddress ? 'Rp ' + formatRupiah(selectedAddress.shipping_fee) : '-' "></strong></div></div></div>
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
                            <div class="grid grid-cols-2 gap-2 rounded-xl border-2 border-brand-navy bg-brand-navylight/10 p-3">
                                <div class="flex items-center gap-2 text-xs font-semibold text-gray-800"><x-icon name="building-library" class="h-5 w-5 text-blue-700" /> Transfer Bank</div>
                                <div class="flex items-center gap-2 text-xs font-semibold text-gray-800"><x-icon name="qr-code" class="h-5 w-5 text-red-600" /> QRIS</div>
                                <div class="flex items-center gap-2 text-xs font-semibold text-gray-800"><x-icon name="device-phone-mobile" class="h-5 w-5 text-cyan-600" /> E-Wallet</div>
                                <div class="flex items-center gap-2 text-xs font-semibold text-gray-800"><x-icon name="credit-card" class="h-5 w-5 text-amber-600" /> Kartu</div>
                            </div>
                            <input type="hidden" name="payment_method" value="midtrans">
                        @else
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700 flex items-start gap-2">
                                <x-icon name="exclamation-triangle" class="w-5 h-5 shrink-0 mt-0.5" />
                                <div>
                                    <p class="font-bold">Pembayaran online belum tersedia</p>
                                    <p class="text-xs mt-1">Hubungi admin Digital Hook agar checkout dapat dilakukan.</p>
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
                        Pembayaran online diproses melalui koneksi terenkripsi
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
                        this.selectedCouriers[store.id] = "digital_hook_sameday";
                        this.shippingCosts[store.id] = this.selectedAddress?.shipping_fee || 0;
                        this.isCalculating[store.id] = false;
                        this.ongkirError[store.id] = null;
                    });
                },

                onAddressChange() {
                    this.stores.forEach(store => {
                        this.selectedCouriers[store.id] = "digital_hook_sameday";
                        this.shippingCosts[store.id] = this.selectedAddress?.shipping_fee || 0;
                        this.ongkirError[store.id] = null;
                    });
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
