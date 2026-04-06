<x-app-layout>
    <x-slot name="title">Checkout Pesanan</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="mb-6">
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900">Checkout Pesanan</h1>
            <p class="text-sm text-gray-500 mt-1">Lengkapi data pengiriman untuk memproses transaksi Anda.</p>
        </div>

        <form method="POST" action="{{ route('buyer.checkout.process') }}" class="flex flex-col lg:flex-row gap-8">
            @csrf
            {{-- Simpan data referensi keranjang yang dipilih --}}
            <input type="hidden" name="selected_items" value="{{ json_encode($selectedItems) }}">

            <div class="flex-1 min-w-0 space-y-6">
                {{-- Form Pengiriman --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-lg text-gray-900 mb-5 flex items-center gap-2">
                        <x-icon name="map-pin" class="w-6 h-6 text-brand-orange" />
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

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Penerima <span class="text-red-500">*</span></label>
                                <input type="text" name="recipient_name" value="{{ old('recipient_name', Auth::user()->name) }}" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nomor Telepon <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone) }}" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="locationDropdown()">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Provinsi <span class="text-red-500">*</span></label>
                                <select name="province_id" x-model="selectedProvince" @change="fetchCities()" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20 bg-white">
                                    <option value="">Pilih Provinsi</option>
                                    <template x-for="prov in provinces" :key="prov.id">
                                        <option :value="prov.id" x-text="prov.name" :selected="prov.id == {{ old('province_id', 'null') }}"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kota / Kabupaten <span class="text-red-500">*</span></label>
                                <select name="city_id" x-model="selectedCity" :disabled="!selectedProvince || isLoading" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20 bg-white disabled:bg-gray-100">
                                    <option value="">Pilih Kota/Kabupaten</option>
                                    <template x-for="city in cities" :key="city.id">
                                        <option :value="city.id" x-text="city.type + ' ' + city.name" :selected="city.id == {{ old('city_id', 'null') }}"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-gray-100 pt-4 mt-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                                <textarea name="address" rows="2" required placeholder="Nama jalan, gedung, patokan, dll..."
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">{{ old('address') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kode Pos <span class="text-red-500">*</span></label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pilihan Kurir Dummy --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-lg text-gray-900 mb-5 flex items-center gap-2">
                        <x-icon name="truck" class="w-6 h-6 text-brand-navy" />
                        Pilih Kurir Pengiriman
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-center gap-4 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 focus-within:ring-2 focus-within:ring-brand-orange focus-within:border-brand-orange transition-all">
                            <input type="radio" name="courier" value="JNE Reguler" required class="w-5 h-5 text-brand-orange focus:ring-brand-orange border-gray-300" checked>
                            <div class="flex-1">
                                <p class="font-bold text-sm text-gray-900">JNE Reguler</p>
                                <p class="text-xs text-gray-500 mt-0.5">Estimasi 2-3 Hari</p>
                            </div>
                            <span class="font-semibold text-sm text-brand-orange">Rp 25.000</span>
                        </label>
                        <label class="relative flex items-center gap-4 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 focus-within:ring-2 focus-within:ring-brand-orange focus-within:border-brand-orange transition-all">
                            <input type="radio" name="courier" value="J&T Express" required class="w-5 h-5 text-brand-orange focus:ring-brand-orange border-gray-300">
                            <div class="flex-1">
                                <p class="font-bold text-sm text-gray-900">J&T Express</p>
                                <p class="text-xs text-gray-500 mt-0.5">Estimasi 1-2 Hari</p>
                            </div>
                            <span class="font-semibold text-sm text-brand-orange">Rp 25.000</span>
                        </label>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-3 text-center">Catatan: Simulasi ini meratakan ongkir (Flat Rate) sebesar Rp 25.000 untuk tujuan pengetesan.</p>
                </div>

                {{-- Daftar Produk --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 line-clamp-none">
                    <h2 class="font-bold text-lg text-gray-900 mb-5 border-b border-gray-100 pb-4">Pesanan Anda</h2>
                    <div class="space-y-4">
                        @foreach($cartItems as $item)
                        <div class="flex gap-4">
                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-16 h-16 rounded-lg object-cover border border-gray-100 shrink-0">
                            <div class="flex-1 min-w-0 flex flex-col justify-between py-1">
                                <div>
                                    <p class="font-semibold text-sm text-gray-900 truncate">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-500">Toko: <span class="font-medium text-brand-navy">{{ $item->product->store->name }}</span></p>
                                </div>
                                <div class="flex justify-between items-center text-sm font-semibold mt-2">
                                    <span class="text-gray-500">Qty: {{ $item->quantity }}</span>
                                    <span class="text-gray-900">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- Ringkasan Checkout Sidebar --}}
            <div class="lg:w-[360px] shrink-0">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-28">
                    <h3 class="font-bold text-lg text-gray-900 mb-5">Ringkasan Pembayaran</h3>

                    <div class="space-y-3 text-sm border-b border-gray-100 pb-5 mb-5">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal Produk</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Estimasi Ongkos Kirim <br><span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded text-gray-500 mt-1 inline-block">Flat Rate</span></span>
                            <span class="font-semibold text-gray-900">Rp 25.000</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-1">
                        <span class="font-bold text-gray-900">Total Tagihan</span>
                        <span class="font-display font-bold text-xl text-brand-orange">Rp {{ number_format($totalPrice + 25000, 0, ',', '.') }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mb-6 text-right">Termasuk PPN jika ada</p>

                    <button type="submit" class="w-full bg-brand-navy hover:bg-brand-navydark text-white font-bold py-3.5 rounded-xl text-sm flex items-center justify-center gap-2 transition-all shadow-sm">
                        <x-icon name="shield-check" class="w-5 h-5" />
                        Pilih Metode Pembayaran
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
        function locationDropdown() {
            return {
                provinces: [],
                cities: [],
                selectedProvince: "{{ old('province_id', '') }}",
                selectedCity: "{{ old('city_id', '') }}",
                isLoading: false,

                init() {
                    this.fetchProvinces();
                    if (this.selectedProvince) {
                        this.fetchCities();
                    }
                },

                async fetchProvinces() {
                    try {
                        const res = await fetch('/api/locations/provinces');
                        this.provinces = await res.json();
                    } catch (e) {
                        console.error("Gagal load provinsi");
                    }
                },

                async fetchCities() {
                    if (!this.selectedProvince) {
                        this.cities = [];
                        return;
                    }
                    this.isLoading = true;
                    try {
                        const res = await fetch(`/api/locations/cities/${this.selectedProvince}`);
                        this.cities = await res.json();
                        
                        // Reset city jika tidak ada di lists
                        if (!this.cities.some(c => c.id == this.selectedCity)) {
                            this.selectedCity = "";
                        }
                    } catch (e) {
                        console.error("Gagal load kota");
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
