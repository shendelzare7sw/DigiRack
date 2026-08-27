<x-app-layout>
    <x-slot name="title">Pengaturan Sistem</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white text-gray-500 transition-all shadow-sm" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900">Pengaturan Digital Hook</h1>
                <p class="text-sm text-gray-500 mt-1">Pembayaran, penyelesaian pesanan, dan kontak toko.</p>
            </div>
        </div>

        @if(session('success'))
            <x-toast type="success" message="{{ session('success') }}" />
        @endif

        <form action="{{ route('admin.settings.store') }}" method="POST" class="space-y-6">
            @csrf
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <h2 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                    <x-icon name="credit-card" class="w-6 h-6 text-brand-navy" /> Pembayaran Midtrans
                </h2>
                <p class="text-xs text-gray-500 mt-2 mb-6">Pembayaran pembeli diterima langsung oleh akun merchant Digital Hook.</p>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Environment</label>
                        <select name="midtrans_is_production" required class="w-full border-gray-200 rounded-xl text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                            <option value="false" @selected(($settings['midtrans_is_production'] ?? 'false') === 'false')>Sandbox (Testing)</option>
                            <option value="true" @selected(($settings['midtrans_is_production'] ?? '') === 'true')>Production (Live)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Server Key</label>
                        <input type="password" name="midtrans_server_key" value="{{ $settings['midtrans_server_key'] ?? '' }}" autocomplete="new-password" class="w-full border-gray-200 rounded-xl text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Client Key</label>
                            <input type="text" name="midtrans_client_key" value="{{ $settings['midtrans_client_key'] ?? '' }}" class="w-full border-gray-200 rounded-xl text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Merchant ID</label>
                            <input type="text" name="midtrans_merchant_id" value="{{ $settings['midtrans_merchant_id'] ?? '' }}" class="w-full border-gray-200 rounded-xl text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pesanan selesai otomatis setelah (jam)</label>
                        <input type="number" name="auto_complete_hours" value="{{ $settings['auto_complete_hours'] ?? '24' }}" min="0" max="168" class="w-full border-gray-200 rounded-xl text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                        <p class="text-xs text-gray-400 mt-1">Dihitung sejak kurir toko menandai pesanan sudah sampai. Isi 0 untuk menonaktifkan.</p>
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <h2 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                    <x-icon name="globe-alt" class="w-6 h-6 text-brand-navy" /> Profil Toko
                </h2>
                <div class="space-y-5 mt-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama toko</label>
                        <input type="text" name="platform_name" value="{{ $settings['platform_name'] ?? 'Digital Hook' }}" class="w-full border-gray-200 rounded-xl text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email dukungan</label>
                            <input type="email" name="platform_email" value="{{ $settings['platform_email'] ?? 'support@digihook.com' }}" class="w-full border-gray-200 rounded-xl text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">WhatsApp</label>
                            <input type="text" name="platform_phone" value="{{ $settings['platform_phone'] ?? '' }}" placeholder="08xxxxxxxxxx" class="w-full border-gray-200 rounded-xl text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                        </div>
                    </div>
                </div>
            </section>

            <div class="flex justify-end">
                <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white font-bold py-2.5 px-6 rounded-xl text-sm transition-colors">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</x-app-layout>
