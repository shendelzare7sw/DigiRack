<x-app-layout>
    <x-slot name="title">Pengaturan Sistem</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900 flex items-center gap-2">
                    Pengaturan Sistem
                    <x-icon name="cog-6-tooth" class="w-7 h-7 sm:w-8 sm:h-8 text-gray-400" />
                </h1>
                <p class="text-sm text-gray-500 mt-1">Konfigurasi dinamis platform DigiRack Enterprise.</p>
            </div>
        </div>

        @if(session('success'))
            <x-toast type="success" message="{{ session('success') }}" />
        @endif

        <div x-data="{ activeTab: 'payment' }" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Setting Nav --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sticky top-28">
                    <ul class="space-y-2">
                        <li>
                            <button @click="activeTab = 'payment'" type="button" 
                                x-bind:class="activeTab === 'payment' ? 'bg-brand-navylight/30 text-brand-navy font-semibold' : 'text-gray-600 font-medium hover:bg-gray-50'"
                                class="w-full flex items-center justify-start gap-3 px-4 py-3 rounded-xl text-sm transition-colors text-left focus:outline-none">
                                <x-icon name="credit-card" class="w-5 h-5" x-bind:class="activeTab === 'payment' ? 'text-brand-navy' : 'text-gray-400'" />
                                Payment Gateway (Midtrans)
                            </button>
                        </li>
                        <li>
                            <button @click="activeTab = 'logistics'" type="button" 
                                x-bind:class="activeTab === 'logistics' ? 'bg-brand-navylight/30 text-brand-navy font-semibold' : 'text-gray-600 font-medium hover:bg-gray-50'"
                                class="w-full flex items-center justify-start gap-3 px-4 py-3 rounded-xl text-sm transition-colors text-left focus:outline-none">
                                <x-icon name="truck" class="w-5 h-5" x-bind:class="activeTab === 'logistics' ? 'text-brand-navy' : 'text-gray-400'" />
                                Logistik & Ongkir
                            </button>
                        </li>
                        <li>
                            <button @click="activeTab = 'profile'" type="button" 
                                x-bind:class="activeTab === 'profile' ? 'bg-brand-navylight/30 text-brand-navy font-semibold' : 'text-gray-600 font-medium hover:bg-gray-50'"
                                class="w-full flex items-center justify-start gap-3 px-4 py-3 rounded-xl text-sm transition-colors text-left focus:outline-none">
                                <x-icon name="globe-alt" class="w-5 h-5" x-bind:class="activeTab === 'profile' ? 'text-brand-navy' : 'text-gray-400'" />
                                Profil Platform
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Setting Form --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Payment Settings (Midtrans) --}}
                <div x-show="activeTab === 'payment'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                    <div class="mb-6 border-b border-gray-100 pb-4">
                        <h2 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                            <x-icon name="credit-card" class="w-6 h-6 text-brand-navy" />
                            Kredensial Midtrans
                        </h2>
                        <p class="text-xs text-gray-500 mt-2">Dapatkan keys ini di dashboard Midtrans (Settings > Access Keys). Keys ini akan digunakan untuk semua transaksi di platform DigiRack (Rekening Bersama / Escrow).</p>
                    </div>

                    <form action="{{ route('admin.settings.store') }}" method="POST">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Environment <span class="text-red-500">*</span></label>
                                <select name="midtrans_is_production" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20 bg-white">
                                    <option value="false" {{ ($settings['midtrans_is_production'] ?? '') == 'false' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                    <option value="true" {{ ($settings['midtrans_is_production'] ?? '') == 'true' ? 'selected' : '' }}>Production (Live)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Server Key <span class="text-red-500">*</span></label>
                                <input type="text" name="midtrans_server_key" value="{{ $settings['midtrans_server_key'] ?? '' }}" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20"
                                    placeholder="Contoh: SB-Mid-server-xxxxxxxx">
                                <p class="text-[10px] text-gray-400 mt-1">Rahasia. Jangan berikan Server Key kepada siapa pun.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Client Key <span class="text-red-500">*</span></label>
                                <input type="text" name="midtrans_client_key" value="{{ $settings['midtrans_client_key'] ?? '' }}" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20"
                                    placeholder="Contoh: SB-Mid-client-xxxxxxxx">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Merchant ID</label>
                                <input type="text" name="midtrans_merchant_id" value="{{ $settings['midtrans_merchant_id'] ?? '' }}"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20"
                                    placeholder="Contoh: G123456789">
                                <p class="text-[10px] text-gray-400 mt-1">Merchant ID Anda dari dashboard Midtrans (Settings > General). Wajib untuk production.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">IRIS API Key (Untuk Payout) <span class="text-[10px] text-gray-400 font-normal">— Opsional</span></label>
                                <input type="text" name="midtrans_iris_api_key" value="{{ $settings['midtrans_iris_api_key'] ?? '' }}"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20"
                                    placeholder="Kosongkan jika belum punya">
                                <p class="text-[10px] text-gray-400 mt-1">Jika dikosongkan, payout akan diproses secara manual oleh Admin (transfer manual ke rekening seller).</p>
                            </div>

                            <hr class="my-6 border-gray-100">
                            
                            <h2 class="font-bold text-lg text-gray-900 mt-6 mb-2">Potongan & Fee Platform</h2>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Potongan Fee Per Produk (Rp)</label>
                                <input type="number" name="platform_fee_per_item" value="{{ $settings['platform_fee_per_item'] ?? '0' }}" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20"
                                    placeholder="0">
                                <p class="text-[10px] text-gray-400 mt-1">Dipotong setiap kali barang terjual (dikalikan kuantitas), dibebankan ke hasil jualan Seller.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fee Pencairan / Withdrawal (%)</label>
                                <input type="number" step="0.1" name="withdrawal_fee_percentage" value="{{ $settings['withdrawal_fee_percentage'] ?? '2' }}" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20"
                                    placeholder="2">
                                <p class="text-[10px] text-gray-400 mt-1">Persentase potongan saat seller melakukan Tarik Dana ke rekening bank.</p>
                            </div>

                            <div class="pt-4 border-t border-gray-100 flex justify-end">
                                <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white font-bold py-2.5 px-6 rounded-xl text-sm transition-colors">
                                    Simpan Pengaturan Pembayaran
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Logistics Settings --}}
                <div x-show="activeTab === 'logistics'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                    <div class="mb-6 border-b border-gray-100 pb-4">
                        <h2 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                            <x-icon name="truck" class="w-6 h-6 text-brand-navy" />
                            Logistik & Ongkos Kirim
                        </h2>
                        <p class="text-xs text-gray-500 mt-2">Konfigurasi API pihak ketiga (contoh: RajaOngkir) untuk mengambil tarif kurir pengiriman secara otomatis.</p>
                    </div>

                    <form action="{{ route('admin.settings.store') }}" method="POST">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">RajaOngkir API Key</label>
                                <input type="text" name="rajaongkir_api_key" value="{{ $settings['rajaongkir_api_key'] ?? '' }}"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20"
                                    placeholder="xxxxxxxxxxxx">
                                <p class="text-[10px] text-gray-400 mt-1">Dapatkan API Key ini dari akun RajaOngkir PRO Anda.</p>
                            </div>

                            <div class="pt-4 border-t border-gray-100 flex justify-end">
                                <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white font-bold py-2.5 px-6 rounded-xl text-sm transition-colors">
                                    Simpan Pengaturan Logistik
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Platform Profile Settings --}}
                <div x-show="activeTab === 'profile'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                    <div class="mb-6 border-b border-gray-100 pb-4">
                        <h2 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                            <x-icon name="globe-alt" class="w-6 h-6 text-brand-navy" />
                            Profil Platform
                        </h2>
                        <p class="text-xs text-gray-500 mt-2">Informasi dasar platform Anda yang akan ditampilkan di halaman kontak dan struk invoice.</p>
                    </div>

                    <form action="{{ route('admin.settings.store') }}" method="POST">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Platform Lengkap</label>
                                <input type="text" name="platform_name" value="{{ $settings['platform_name'] ?? 'DigiRack Enterprise' }}"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Support</label>
                                    <input type="email" name="platform_email" value="{{ $settings['platform_email'] ?? 'support@digirack.com' }}"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nomor CS (WhatsApp)</label>
                                    <input type="text" name="platform_phone" value="{{ $settings['platform_phone'] ?? '081234567890' }}"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20"
                                        placeholder="08xxxxxxxx">
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100 flex justify-end">
                                <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white font-bold py-2.5 px-6 rounded-xl text-sm transition-colors">
                                    Simpan Profil
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
