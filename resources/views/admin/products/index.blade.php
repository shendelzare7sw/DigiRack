<x-app-layout>
    <x-slot name="title">Moderasi Produk</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Moderasi Produk</h1>
                <p class="text-gray-500 text-sm mt-1">Monitor dan moderasi seluruh produk dari semua toko di platform.</p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="font-display font-bold text-2xl text-gray-900">{{ $totalProducts }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Total Produk</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="font-display font-bold text-2xl text-green-600">{{ $activeProducts }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Aktif</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="font-display font-bold text-2xl text-orange-500">{{ $inactiveProducts }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Nonaktif</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="font-display font-bold text-2xl text-red-500">{{ $outOfStock }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Stok Habis</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-2 shadow-sm">
                <x-icon name="check-circle" class="w-5 h-5" /> {{ session('success') }}
            </div>
        @endif

        {{-- Filter & Search --}}
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..." class="flex-1 border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">

                <select name="status" class="border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm bg-white">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>

                <select name="category" class="border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white px-5 py-2 rounded-xl text-sm font-bold shadow-sm transition-colors">Filter</button>
            </form>
        </div>

        {{-- Data Table --}}
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            @if($products->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Toko</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-sm">
                            @foreach($products as $product)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-lg border border-gray-100 object-cover shrink-0">
                                        <div class="min-w-0">
                                            <div class="font-bold text-gray-900 truncate max-w-[200px]">{{ $product->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $product->category->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-700">{{ $product->store->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $product->formatted_price }}</td>
                                <td class="px-6 py-4">
                                    <span class="{{ $product->stock <= 0 ? 'text-red-500 font-bold' : ($product->stock <= 5 ? 'text-orange-500 font-semibold' : 'text-gray-700') }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->status === 'active')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                            <x-icon name="check-circle" class="w-3.5 h-3.5" /> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            <x-icon name="pause-circle" class="w-3.5 h-3.5" /> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Toggle Status --}}
                                        @if($product->status === 'active')
                                            <form action="{{ route('admin.products.toggle', $product->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Nonaktifkan Produk', message: 'Produk ini akan dinonaktifkan dari katalog. Lanjutkan?', type: 'danger', confirmText: 'Ya, Nonaktifkan' })">
                                                @csrf
                                                <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-red-500 hover:text-red-600 text-gray-500 rounded-lg transition-colors" title="Nonaktifkan">
                                                    <x-icon name="pause-circle" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.products.toggle', $product->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Aktifkan Produk', message: 'Aktifkan kembali produk ini di katalog?', type: 'success', confirmText: 'Ya, Aktifkan' })">
                                                @csrf
                                                <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-green-500 hover:text-green-600 text-gray-500 rounded-lg transition-colors" title="Aktifkan">
                                                    <x-icon name="play-circle" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        @endif

                                        {{-- View in Catalog --}}
                                        <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="p-2 bg-white border border-brand-navy text-brand-navy hover:bg-brand-navy hover:text-white rounded-lg transition-colors" title="Lihat di Katalog">
                                            <x-icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($products->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-20">
                    <x-icon name="cube" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-bold text-gray-900">Tidak Ada Produk</h3>
                    <p class="text-gray-500 mt-1">Tidak ditemukan produk dengan kriteria ini.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
