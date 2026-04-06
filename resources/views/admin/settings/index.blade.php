<x-app-layout>
    <x-slot name="title">Pengaturan Sistem</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="mb-6 flex justify-between items-center">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Setting Nav --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sticky top-28">
                    <ul class="space-y-2">
                        <li>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-brand-navylight/30 text-brand-navy font-semibold text-sm transition-colors">
                                <x-icon name="credit-card" class="w-5 h-5 text-brand-navy" />
                                Payment Gateway (Midtrans)
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 text-gray-600 font-medium text-sm transition-colors opacity-50 cursor-not-allowed">
                                <x-icon name="truck" class="w-5 h-5" />
                                Logistik & Ongkir (Akan Datang)
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 text-gray-600 font-medium text-sm transition-colors opacity-50 cursor-not-allowed">
                                <x-icon name="globe-alt" class="w-5 h-5" />
                                Profil Platform (Akan Datang)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Setting Form --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
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

                            <div class="pt-4 border-t border-gray-100 flex justify-end">
                                <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white font-bold py-2.5 px-6 rounded-xl text-sm transition-colors">
                                    Simpan Pengaturan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
