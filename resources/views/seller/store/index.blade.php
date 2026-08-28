<x-app-layout>
    <x-slot name="title">Profil Bisnis Digital Hook</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Profil Bisnis</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola identitas Digital Hook sebagai satu-satunya bisnis di sistem.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 text-green-700 border border-green-200 p-4 rounded-xl mb-6 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 text-green-500" />
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.business-profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 border-b pb-3 mb-6">Identitas Digital Hook</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1" x-data="{ logoPreview: '{{ $store->logo_url }}' }">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Logo Bisnis</label>
                        <div class="relative w-32 h-32 rounded-full overflow-hidden border-4 border-gray-50 flex items-center justify-center bg-gray-100 group">
                            <img :src="logoPreview" alt="Logo Digital Hook" class="w-full h-full object-cover">
                            <label class="absolute inset-x-0 bottom-0 bg-black/50 text-white text-xs text-center py-2 cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity">
                                Ubah
                                <input type="file" name="logo" class="hidden" accept="image/*" @change="logoPreview = URL.createObjectURL($event.target.files[0])">
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Maksimal 2MB (JPG/PNG/WEBP).</p>
                        @error('logo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bisnis *</label>
                            <input type="text" name="name" value="{{ old('name', $store->name) }}" required class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl">
                            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Bisnis</label>
                            <textarea name="description" rows="5" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl" placeholder="Jelaskan layanan, jam operasional, atau informasi Digital Hook...">{{ old('description', $store->description) }}</textarea>
                            @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-brand-blue hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-transform active:scale-95">
                    Simpan Profil Bisnis
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
