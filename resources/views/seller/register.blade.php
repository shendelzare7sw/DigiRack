<x-app-layout>
    <x-slot name="title">Buka Toko Baru</x-slot>

    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-brand-navy to-brand-blue p-8 text-center text-white">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <x-icon name="building-storefront" class="w-8 h-8" />
                    </div>
                    <h1 class="text-2xl font-bold font-display">Buka Toko di DigiRack</h1>
                    <p class="text-white/80 text-sm mt-2">Mulai berjualan produk IT & jaringan Anda kepada ribuan pembeli.</p>
                </div>

                {{-- Form --}}
                <div class="p-8">
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('seller.register.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-5">
                            <x-input-label for="store_name" value="Nama Toko" class="text-gray-700 font-semibold" />
                            <x-text-input id="store_name" class="block mt-1 w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl" type="text" name="store_name" :value="old('store_name')" required placeholder="Contoh: NetGear Indonesia" />
                            <x-input-error :messages="$errors->get('store_name')" class="mt-2" />
                        </div>

                        <div class="mb-5">
                            <x-input-label for="store_description" value="Deskripsi Singkat Toko (opsional)" class="text-gray-700 font-semibold" />
                            <textarea id="store_description" name="store_description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm" placeholder="Jelaskan jenis produk yang akan Anda jual...">{{ old('store_description') }}</textarea>
                            <x-input-error :messages="$errors->get('store_description')" class="mt-2" />
                        </div>

                        <div class="mb-5">
                            <x-input-label for="identity_document" value="Upload KTP / ID Card" class="text-gray-700 font-semibold" />
                            <input id="identity_document" type="file" name="identity_document" required accept=".jpg,.jpeg,.png,.webp,.pdf"
                                class="block mt-1 w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-navylight file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-brand-navy hover:file:bg-brand-navy hover:file:text-white transition-colors border border-gray-300 rounded-xl bg-white">
                            <p class="mt-1 text-[11px] text-gray-500">Format JPG, PNG, WEBP, atau PDF. Maksimal 2MB.</p>
                            <x-input-error :messages="$errors->get('identity_document')" class="mt-2" />
                        </div>

                        {{-- Info card --}}
                        <div class="p-4 bg-orange-50 border border-orange-100 rounded-xl mb-6">
                            <div class="flex gap-3">
                                <x-icon name="exclamation-triangle" class="w-5 h-5 text-orange-500 shrink-0 mt-0.5" />
                                <div class="text-xs text-orange-800 space-y-1">
                                    <p class="font-bold">Informasi Penting:</p>
                                    <ul class="list-disc pl-4 space-y-0.5">
                                        <li>Toko Anda akan <strong>menunggu verifikasi Admin</strong> berdasarkan data identitas.</li>
                                        <li>Anda bisa masuk dashboard seller, tetapi menu penjualan terkunci sampai disetujui.</li>
                                        <li>Akun Anda tetap berstatus pembeli sampai toko lolos verifikasi.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-brand-navy hover:bg-brand-navydark text-white font-bold py-3 rounded-xl shadow-sm transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <x-icon name="rocket-launch" class="w-5 h-5" />
                            Daftarkan Toko Saya
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-brand-navy font-semibold transition-colors">
                            &larr; Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
