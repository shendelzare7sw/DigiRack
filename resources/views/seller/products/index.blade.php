<x-app-layout>
    <x-slot name="title">Kelola Produk</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                class="mb-6 bg-green-50 border border-green-200 text-green-700 px-5 py-3.5 rounded-xl flex items-center justify-between">
                <span class="text-sm font-medium">{{ session('success') }}</span>
                <button @click="show = false" class="text-green-400 hover:text-green-600"><x-icon name="x-mark" class="w-5 h-5" /></button>
            </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900">Kelola Produk</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $stats['total'] }} produk • {{ $stats['active'] }} aktif • {{ $stats['lowStock'] }} stok menipis</p>
            </div>
            <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-2 bg-brand-blue hover:bg-blue-600 text-white font-bold text-sm px-6 py-3 rounded-xl shadow-sm transition-colors self-start">
                <x-icon name="plus" class="w-4 h-4" />
                Tambah Produk
            </a>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('seller.products.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                        class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:border-brand-navy focus:ring-brand-navy/20">
                </div>
                <div class="w-40">
                    <select name="status" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:border-brand-navy focus:ring-brand-navy/20">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draf</option>
                    </select>
                </div>
                <div class="w-48">
                    <select name="category" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:border-brand-navy focus:ring-brand-navy/20">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white font-bold text-sm px-5 py-2.5 rounded-xl transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'category']))
                    <a href="{{ route('seller.products.index') }}" class="text-sm text-red-500 hover:text-red-700 font-medium px-3 py-2.5 transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Products: Desktop Table + Mobile Cards --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if($products->isEmpty())
                <div class="p-16 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="cube" class="w-10 h-10 text-gray-300" />
                    </div>
                    <h3 class="font-bold text-lg text-gray-700 mb-2">Belum Ada Produk</h3>
                    <p class="text-gray-500 text-sm mb-6">Mulai berjualan dengan menambahkan produk pertama Anda.</p>
                    <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-2 bg-brand-blue hover:bg-blue-600 text-white font-bold text-sm px-6 py-3 rounded-xl transition-colors">
                        <x-icon name="plus" class="w-4 h-4" /> Tambah Produk
                    </a>
                </div>
            @else
                {{-- Desktop Table (hidden on mobile) --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/80 border-b border-gray-100">
                            <tr>
                                <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="text-right px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="text-center px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="text-center px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Terjual</th>
                                <th class="text-center px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="text-center px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($products as $product)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                                class="w-12 h-12 rounded-lg object-cover border border-gray-100 shrink-0">
                                            <div class="min-w-0">
                                                <a href="{{ route('products.show', $product->slug) }}" target="_blank"
                                                    class="font-semibold text-gray-900 hover:text-brand-navy transition-colors line-clamp-1">
                                                    {{ $product->name }}
                                                </a>
                                                <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($product->slug, 30) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-gray-600">{{ $product->category->name ?? '-' }}</td>
                                    <td class="px-5 py-4 text-right font-semibold text-brand-blue">{{ $product->formatted_price }}</td>
                                    <td class="px-5 py-4">
                                        <div class="text-center">
                                            <span class="font-semibold {{ $product->stock === 0 ? 'text-red-500' : ($product->stock <= 5 ? 'text-orange-500' : 'text-gray-900') }}">
                                                {{ $product->stock }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center text-gray-600 font-medium">{{ $product->sold_count }}</td>
                                    <td class="px-5 py-4 text-center">
                                        @if($product->status === 'active')
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-200">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Aktif
                                            </span>
                                        @elseif($product->status === 'inactive')
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-red-50 text-red-600 border border-red-200">
                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Nonaktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 border border-gray-200">
                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Draf
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('products.show', $product->slug) }}" target="_blank"
                                                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat">
                                                <x-icon name="eye" class="w-4 h-4" />
                                            </a>
                                            <a href="{{ route('seller.products.edit', $product->id) }}"
                                                class="p-2 text-gray-400 hover:text-brand-navy hover:bg-brand-navylight rounded-lg transition-colors" title="Edit">
                                                <x-icon name="pencil-square" class="w-4 h-4" />
                                            </a>
                                            <form method="POST" action="{{ route('seller.products.destroy', $product->id) }}" class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards (hidden on desktop) --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach($products as $product)
                        <div class="p-4">
                            <div class="flex items-start gap-3 mb-3">
                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                    class="w-16 h-16 rounded-xl object-cover border border-gray-100 shrink-0">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="font-bold text-sm text-gray-900 hover:text-brand-navy line-clamp-2">{{ $product->name }}</a>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $product->category->name ?? '-' }}</p>
                                    <p class="text-sm font-bold text-brand-blue mt-1">{{ $product->formatted_price }}</p>
                                </div>
                                @if($product->status === 'active')
                                    <span class="shrink-0 inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200">
                                        <span class="w-1 h-1 bg-green-500 rounded-full"></span> Aktif
                                    </span>
                                @elseif($product->status === 'inactive')
                                    <span class="shrink-0 inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-50 text-red-600 border border-red-200">Nonaktif</span>
                                @else
                                    <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Draf</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>Stok: <strong class="{{ $product->stock === 0 ? 'text-red-500' : ($product->stock <= 5 ? 'text-orange-500' : 'text-gray-900') }}">{{ $product->stock }}</strong></span>
                                    <span>Terjual: <strong class="text-gray-900">{{ $product->sold_count }}</strong></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="Lihat">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('seller.products.edit', $product->id) }}" class="p-2 bg-brand-navylight text-brand-navy rounded-lg hover:bg-brand-navy hover:text-white transition-colors" title="Edit">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('seller.products.destroy', $product->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition-colors" title="Hapus">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($products->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
