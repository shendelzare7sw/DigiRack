<x-app-layout>
    <x-slot name="title">Pengaturan Profil Toko</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('seller.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold font-display text-gray-900">Profil Toko</h1>
                        <p class="text-gray-500 text-sm mt-1">Kelola identitas etalase publik dan rekening bank Anda.</p>
                    </div>
                    <a href="{{ route('store.show', $store->slug) }}" target="_blank" class="bg-white border border-gray-200 text-brand-navy hover:bg-gray-50 font-bold px-4 py-2 rounded-xl text-sm flex items-center gap-2 transition-colors shrink-0">
                        <x-icon name="arrow-top-right-on-square" class="w-4 h-4" />
                        Lihat Etalase Publik
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 text-green-700 border border-green-200 p-4 rounded-xl mb-6 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 text-green-500" />
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('seller.store.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Informasi Dasar --}}
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 border-b pb-3 mb-6">Informasi Dasar</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1" x-data="{ logoPreview: '{{ $store->logo_url }}' }">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Logo Toko</label>
                        <div class="relative w-32 h-32 rounded-full overflow-hidden border-4 border-gray-50 flex items-center justify-center bg-gray-100 group">
                            <img :src="logoPreview" class="w-full h-full object-cover">
                            <label class="absolute inset-x-0 bottom-0 bg-black/50 text-white text-xs text-center py-2 cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity">
                                Ubah
                                <input type="file" name="logo" class="hidden" accept="image/*" @change="logoPreview = URL.createObjectURL($event.target.files[0])">
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Maksimal 2MB (JPG/PNG).</p>
                        @error('logo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Toko *</label>
                            <input type="text" name="name" value="{{ old('name', $store->name) }}" required class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl">
                            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi & Bio Toko</label>
                            <textarea name="description" rows="4" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl" placeholder="Ceritakan tentang toko Anda, jam operasional, atau kebijakan pengiriman...">{{ old('description', $store->description) }}</textarea>
                            @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Banner Etalase --}}
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 border-b pb-3 mb-6">Banner Etalase Publik</h2>
                
                <div class="mb-4" x-data="{ bannerPreview: '{{ $store->banner_url ?? '' }}' }">
                    <p class="text-sm text-gray-600 mb-4">Unggah gambar landscape resolusi tinggi (contoh: 1200x400) yang akan mempercantik halaman publik toko Anda.</p>
                    
                    @if($store->banner)
                        <div class="h-40 w-full rounded-xl overflow-hidden mb-4 border relative group">
                            <img :src="bannerPreview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <label class="bg-white text-gray-900 font-bold px-4 py-2 rounded-lg cursor-pointer hover:bg-gray-100">
                                    Ganti Banner
                                    <input type="file" name="banner" class="hidden" accept="image/*" @change="bannerPreview = URL.createObjectURL($event.target.files[0])">
                                </label>
                            </div>
                        </div>
                    @else
                        <div class="h-40 border-2 border-dashed border-gray-300 rounded-xl flex flex-col items-center justify-center text-gray-500 bg-gray-50 hover:bg-gray-100 transition-colors relative overflow-hidden group">
                            <template x-if="bannerPreview">
                                <img :src="bannerPreview" class="absolute inset-0 w-full h-full object-cover z-10">
                            </template>
                            <label class="cursor-pointer text-center relative z-20" x-bind:class="{'opacity-0 hover:opacity-100 absolute inset-0 bg-black/50 flex flex-col items-center justify-center text-white': bannerPreview}">
                                <x-icon name="photo" class="w-8 h-8 mx-auto mb-2 text-gray-400" x-bind:class="{'text-white': bannerPreview}" />
                                <span class="font-bold text-brand-navy" x-bind:class="{'text-white': bannerPreview}">Pilih Gambar Banner</span>
                                <p class="text-xs mt-1" x-bind:class="{'text-white': bannerPreview}">Maks 4MB (JPG/PNG/WEBP)</p>
                                <input type="file" name="banner" class="hidden" accept="image/*" @change="bannerPreview = URL.createObjectURL($event.target.files[0])">
                            </label>
                        </div>
                    @endif
                    @error('banner') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Rekening Pencairan --}}
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 border-b pb-3 mb-6 flex items-center gap-2">
                    <x-icon name="building-library" class="w-5 h-5 text-green-600" />
                    Rekening Pencairan Dana
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $store->bank_name) }}" placeholder="Mis: BCA, Mandiri" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Rekening</label>
                        <input type="text" name="bank_account_no" value="{{ old('bank_account_no', $store->bank_account_no) }}" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Atas Nama</label>
                        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $store->bank_account_name) }}" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl">
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-3"><x-icon name="information-circle" class="w-3.5 h-3.5 inline" /> Rekening ini akan digunakan saat Anda mengajukan pencairan (Withdrawal) Saldo Wallet.</p>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-brand-blue hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-transform active:scale-95">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
