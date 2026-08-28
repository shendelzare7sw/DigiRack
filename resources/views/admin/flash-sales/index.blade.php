<x-app-layout>
    <x-slot name="title">Flash Sale</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Flash Sale</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola event flash sale untuk produk di platform.</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="font-display font-bold text-2xl text-gray-900">{{ $totalFlashSales }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Total</p>
            </div>
            <div class="bg-white rounded-xl border border-green-100 shadow-sm p-4 text-center">
                <p class="font-display font-bold text-2xl text-green-600">{{ $ongoingCount }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Sedang Berjalan</p>
            </div>
            <div class="bg-white rounded-xl border border-blue-100 shadow-sm p-4 text-center">
                <p class="font-display font-bold text-2xl text-blue-600">{{ $upcomingCount }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Akan Datang</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="font-display font-bold text-2xl text-gray-400">{{ $expiredCount }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Berakhir</p>
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
                        <x-icon name="fire" class="w-5 h-5 text-red-500" />
                        Buat Flash Sale
                    </h2>
                    <form action="{{ route('admin.flash-sales.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Produk <span class="text-red-500">*</span></label>
                            <select name="product_id" required class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                                <option value="">Pilih produk...</option>
                                @foreach($availableProducts as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            @error('product_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Diskon (%) <span class="text-red-500">*</span></label>
                                <input type="number" name="discount_percent" value="{{ old('discount_percent') }}" required min="1" max="90" placeholder="10" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Stok FS <span class="text-red-500">*</span></label>
                                <input type="number" name="stock_flash" value="{{ old('stock_flash') }}" required min="1" placeholder="50" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Mulai <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" required class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Berakhir <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" required class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                        </div>
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors shadow-sm">
                            Buat Flash Sale
                        </button>
                    </form>
                </div>
            </div>

            {{-- Flash Sale List --}}
            <div class="lg:col-span-2">
                {{-- Filter --}}
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-4">
                    <form action="{{ route('admin.flash-sales.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..." class="flex-1 border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                        <select name="filter" class="border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm bg-white">
                            <option value="">Semua</option>
                            <option value="ongoing" {{ request('filter') == 'ongoing' ? 'selected' : '' }}>Sedang Berjalan</option>
                            <option value="upcoming" {{ request('filter') == 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                            <option value="expired" {{ request('filter') == 'expired' ? 'selected' : '' }}>Berakhir</option>
                            <option value="inactive" {{ request('filter') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white px-5 py-2 rounded-xl text-sm font-bold shadow-sm transition-colors">Filter</button>
                    </form>
                </div>

                <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
                    @if($flashSales->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Diskon</th>
                                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Periode</th>
                                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-5 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    @foreach($flashSales as $fs)
                                    @php
                                        $isOngoing = $fs->is_active && $fs->start_time <= now() && $fs->end_time >= now();
                                        $isUpcoming = $fs->start_time > now();
                                        $isExpired = $fs->end_time < now();
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 transition-colors {{ $isOngoing ? 'bg-green-50/30' : '' }}">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($fs->product && $fs->product->primaryImage)
                                                    <img src="{{ $fs->product->primary_image_url }}" class="w-10 h-10 rounded-lg border border-gray-100 object-cover shrink-0">
                                                @endif
                                                <div class="min-w-0">
                                                    <div class="font-bold text-gray-900 truncate max-w-[150px]">{{ $fs->product->name ?? 'Produk dihapus' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="font-bold text-red-500 text-lg">-{{ $fs->discount_percent }}%</div>
                                            <div class="text-[10px] text-gray-400 line-through">{{ $fs->formatted_original_price }}</div>
                                            <div class="text-xs font-bold text-green-600">{{ $fs->formatted_sale_price }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-xs text-gray-600">
                                            <div>{{ $fs->start_time->translatedFormat('d M Y H:i') }}</div>
                                            <div class="text-gray-400">s/d</div>
                                            <div>{{ $fs->end_time->translatedFormat('d M Y H:i') }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if($isOngoing)
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">🔥 Live</span>
                                            @elseif($isUpcoming)
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">Akan Datang</span>
                                            @elseif($isExpired)
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">Berakhir</span>
                                            @endif
                                            @if(!$fs->is_active)
                                                <span class="ml-1 text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-bold">OFF</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <form action="{{ route('admin.flash-sales.toggle', $fs->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-{{ $fs->is_active ? 'orange' : 'green' }}-500 hover:text-{{ $fs->is_active ? 'orange' : 'green' }}-600 text-gray-500 rounded-lg transition-colors" title="{{ $fs->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                        <x-icon name="{{ $fs->is_active ? 'pause-circle' : 'play-circle' }}" class="w-4 h-4" />
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.flash-sales.destroy', $fs->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Hapus Flash Sale', message: 'Flash sale ini akan dihapus permanen. Lanjutkan?', type: 'danger', confirmText: 'Ya, Hapus' })">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-red-500 hover:text-red-600 text-gray-500 rounded-lg transition-colors" title="Hapus">
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

                        @if($flashSales->hasPages())
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                                {{ $flashSales->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-20">
                            <x-icon name="fire" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                            <h3 class="text-lg font-bold text-gray-900">Belum Ada Flash Sale</h3>
                            <p class="text-gray-500 mt-1">Gunakan form di samping untuk membuat flash sale baru.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
