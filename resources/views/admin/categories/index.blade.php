<x-app-layout>
    <x-slot name="title">Kelola Kategori</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Kelola Kategori</h1>
                <p class="text-gray-500 text-sm mt-1">Tambah, edit, dan kelola kategori produk untuk katalog platform.</p>
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
            {{-- Create Form --}}
            <div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-6">
                    <h2 class="font-bold text-base text-gray-900 mb-4 flex items-center gap-2">
                        <x-icon name="plus-circle" class="w-5 h-5 text-brand-blue" />
                        Tambah Kategori
                    </h2>
                    <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Komponen PC" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" rows="2" placeholder="Deskripsi singkat..." class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">{{ old('description') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Urutan</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                        </div>
                        <button type="submit" class="w-full bg-brand-navy hover:bg-brand-navydark text-white font-bold py-2.5 rounded-xl text-sm transition-colors shadow-sm">
                            Tambah Kategori
                        </button>
                    </form>
                </div>
            </div>

            {{-- Category List --}}
            <div class="lg:col-span-2">
                {{-- Search --}}
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-4">
                    <form action="{{ route('admin.categories.index') }}" method="GET" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..." class="flex-1 border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                        <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white px-5 py-2 rounded-xl text-sm font-bold shadow-sm transition-colors">Cari</button>
                    </form>
                </div>

                <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
                    @if($categories->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($categories as $category)
                            <div class="p-5 hover:bg-gray-50/50 transition-colors" x-data="{ editing: false }">
                                {{-- Display Mode --}}
                                <div x-show="!editing" class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shrink-0">
                                            <x-icon name="tag" class="w-5 h-5" />
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-gray-900 flex items-center gap-2">
                                                {{ $category->name }}
                                                @if(!$category->is_active)
                                                    <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-bold uppercase">Nonaktif</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 truncate">{{ $category->description ?? 'Tidak ada deskripsi' }}</div>
                                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $category->products_count }} produk · Urutan: {{ $category->sort_order }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button @click="editing = true" class="p-2 bg-white border border-gray-200 hover:border-brand-navy hover:text-brand-navy text-gray-500 rounded-lg transition-colors" title="Edit">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>

                                        {{-- Toggle Active --}}
                                        <form action="{{ route('admin.categories.toggle', $category->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-{{ $category->is_active ? 'orange' : 'green' }}-500 hover:text-{{ $category->is_active ? 'orange' : 'green' }}-600 text-gray-500 rounded-lg transition-colors" title="{{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <x-icon name="{{ $category->is_active ? 'pause-circle' : 'play-circle' }}" class="w-4 h-4" />
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        @if($category->products_count === 0)
                                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Hapus Kategori', message: 'Kategori ini akan dihapus permanen. Lanjutkan?', type: 'danger', confirmText: 'Ya, Hapus' })">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-red-500 hover:text-red-600 text-gray-500 rounded-lg transition-colors" title="Hapus">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                {{-- Edit Mode --}}
                                <div x-show="editing" x-cloak>
                                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama</label>
                                                <input type="text" name="name" value="{{ $category->name }}" required class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Urutan</label>
                                                <input type="number" name="sort_order" value="{{ $category->sort_order }}" min="0" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi</label>
                                            <textarea name="description" rows="2" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">{{ $category->description }}</textarea>
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

                        @if($categories->hasPages())
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                                {{ $categories->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-20">
                            <x-icon name="tag" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                            <h3 class="text-lg font-bold text-gray-900">Belum Ada Kategori</h3>
                            <p class="text-gray-500 mt-1">Gunakan form di samping untuk menambah kategori baru.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
