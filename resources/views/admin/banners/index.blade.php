<x-app-layout>
    <x-slot name="title">Kelola Banner</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Kelola Banner</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola banner promosi yang tampil di halaman utama platform.</p>
            </div>
        </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-2 shadow-sm">
                <x-icon name="check-circle" class="w-5 h-5" /> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-2 shadow-sm">
                <x-icon name="x-circle" class="w-5 h-5" /> {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Upload Form --}}
            <div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-6">
                    <h2 class="font-bold text-base text-gray-900 mb-4 flex items-center gap-2">
                        <x-icon name="plus-circle" class="w-5 h-5 text-pink-500" />
                        Tambah Banner
                    </h2>
                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Promo Akhir Tahun" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Gambar <span class="text-red-500">*</span></label>
                            <input type="file" name="image" accept="image/*" required class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-navylight file:text-brand-navy hover:file:bg-brand-navy hover:file:text-white file:transition-colors file:cursor-pointer">
                            <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, WebP. Max 2MB. Rekomendasi 1200×400px</p>
                            @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Link URL</label>
                            <input type="url" name="link_url" value="{{ old('link_url') }}" placeholder="https://..." class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Urutan</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                        </div>
                        <button type="submit" class="w-full bg-brand-navy hover:bg-brand-navydark text-white font-bold py-2.5 rounded-xl text-sm transition-colors shadow-sm">
                            Upload Banner
                        </button>
                    </form>
                </div>
            </div>

            {{-- Banner List --}}
            <div class="lg:col-span-2">
                <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
                    @if($banners->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($banners as $banner)
                            <div class="p-5 hover:bg-gray-50/50 transition-colors" x-data="{ editing: false }">
                                <div x-show="!editing" class="flex flex-col sm:flex-row items-start gap-4">
                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full sm:w-48 h-24 rounded-xl object-cover border border-gray-100 shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-gray-900 flex items-center gap-2 flex-wrap">
                                            {{ $banner->title }}
                                            @if($banner->is_active)
                                                <span class="text-[10px] bg-green-100 text-green-600 px-1.5 py-0.5 rounded font-bold uppercase">Aktif</span>
                                            @else
                                                <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-bold uppercase">Nonaktif</span>
                                            @endif
                                        </div>
                                        @if($banner->link_url)
                                            <div class="text-xs text-brand-blue truncate mt-1">{{ $banner->link_url }}</div>
                                        @endif
                                        <div class="text-[10px] text-gray-400 mt-1">Urutan: {{ $banner->sort_order }} · {{ $banner->created_at->translatedFormat('d M Y') }}</div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button @click="editing = true" class="p-2 bg-white border border-gray-200 hover:border-brand-navy hover:text-brand-navy text-gray-500 rounded-lg transition-colors" title="Edit">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        <form action="{{ route('admin.banners.toggle', $banner->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-2 bg-white border border-gray-200 text-gray-500 rounded-lg transition-colors hover:border-orange-500 hover:text-orange-600" title="Toggle">
                                                <x-icon name="{{ $banner->is_active ? 'pause-circle' : 'play-circle' }}" class="w-4 h-4" />
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Hapus Banner', message: 'Banner akan dihapus permanen. Lanjutkan?', type: 'danger', confirmText: 'Ya, Hapus' })">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-red-500 hover:text-red-600 text-gray-500 rounded-lg transition-colors" title="Hapus">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div x-show="editing" x-cloak>
                                    <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Judul</label>
                                                <input type="text" name="title" value="{{ $banner->title }}" required class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Urutan</label>
                                                <input type="number" name="sort_order" value="{{ $banner->sort_order }}" min="0" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Ganti Gambar (opsional)</label>
                                            <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Link URL</label>
                                            <input type="url" name="link_url" value="{{ $banner->link_url }}" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white font-bold px-4 py-2 rounded-xl text-sm transition-colors">Simpan</button>
                                            <button type="button" @click="editing = false" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-4 py-2 rounded-xl text-sm transition-colors">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if($banners->hasPages())
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                                {{ $banners->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-20">
                            <x-icon name="megaphone" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                            <h3 class="text-lg font-bold text-gray-900">Belum Ada Banner</h3>
                            <p class="text-gray-500 mt-1">Gunakan form di samping untuk menambah banner promosi.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
