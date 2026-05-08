<x-app-layout>
    <x-slot name="title">Kelola Toko Penjual</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold font-display text-gray-900">Manajemen Toko</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola dan verifikasi toko penjual di platform.</p>
            </div>
            @if($pendingCount > 0)
                <div class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold border border-red-100 flex items-center gap-1.5 shadow-sm shrink-0">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                    </span>
                    {{ $pendingCount }} Menunggu
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-2 shadow-sm">
                <x-icon name="check-circle" class="w-5 h-5" /> {{ session('success') }}
            </div>
        @endif

        {{-- Filter & Search --}}
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col sm:flex-row items-center gap-4 justify-between">
            <form action="{{ route('admin.stores.index') }}" method="GET" class="flex flex-1 w-full gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama toko atau pemilik..." class="w-full sm:max-w-md border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                
                <select name="status" class="border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm bg-white">
                    <option value="">Semua Status</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi Lolos</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Validasi</option>
                </select>
                
                <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm">Filter</button>
            </form>
        </div>

        {{-- Data Table --}}
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            @if($stores->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Toko & Pemilik</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status Validasi</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Rekening Tujuan</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Tindakan Cepat</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-sm">
                            @foreach($stores as $store)
                            <tr class="hover:bg-gray-50/50 transition-colors {{ !$store->is_verified ? 'bg-orange-50/30' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $store->logo_url }}" class="w-10 h-10 rounded-full border border-gray-200 object-cover shrink-0">
                                        <div>
                                            <div class="font-bold text-gray-900 group">
                                                {{ $store->name }}
                                                @if(!$store->is_active)
                                                    <span class="ml-1 text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-bold uppercase">Banned</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                                <x-icon name="user" class="w-3 h-3" /> {{ $store->user->name }}
                                                &bull; {{ $store->products_count }} Produk
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($store->is_verified)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                            <x-icon name="check-badge" class="w-3.5 h-3.5" /> Terverifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-200">
                                            <x-icon name="clock" class="w-3.5 h-3.5" /> Menunggu Validasi
                                        </span>
                                    @endif
                                    <div class="text-xs text-gray-400 mt-1">Sejak {{ $store->created_at->format('M Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($store->bank_name)
                                        <div class="font-bold text-gray-900 text-xs uppercase">{{ $store->bank_name }}</div>
                                        <div class="text-[11px] text-gray-600 font-mono">{{ $store->bank_account_no }}</div>
                                        <div class="text-[10px] text-gray-400 max-w-[120px] truncate">a.n {{ $store->bank_account_name }}</div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum melengkapi info bank</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                                    {{-- Tombol Toggle Verification --}}
                                    @if($store->is_verified)
                                        <form action="{{ route('admin.stores.verify', $store->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Cabut Verifikasi', message: 'Cabut status Verified toko ini? Toko mungkin tidak dapat mencairkan dana lagi.', type: 'danger', confirmText: 'Cabut Verifikasi' })">
                                            @csrf
                                            <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-orange-500 hover:text-orange-600 text-gray-500 rounded-lg transition-colors group relative" title="Cabut Verifikasi">
                                                <x-icon name="x-mark" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.stores.verify', $store->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Loloskan Toko', message: 'Peringatan: Pastikan Anda telah mencek kelengkapan data pemilik. Loloskan toko ini?', type: 'success', confirmText: 'Ya, Loloskan' })">
                                            @csrf
                                            <button type="submit" class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white font-bold text-xs rounded-lg transition-all shadow-sm flex items-center gap-1">
                                                <x-icon name="check" class="w-3.5 h-3.5" /> Loloskan
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Tombol Banner / Toggle Active --}}
                                    @if($store->is_active)
                                        <form action="{{ route('admin.stores.toggle', $store->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Banned Toko', message: 'Toko akan dibanned dan disembunyikan dari publik, Anda yakin?', type: 'danger', confirmText: 'Ya, Banned' })">
                                            @csrf
                                            <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-red-500 hover:text-red-600 text-gray-500 rounded-lg transition-colors" title="Banned Toko">
                                                <x-icon name="no-symbol" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.stores.toggle', $store->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Pulihkan Toko', message: 'Pulihkan toko ini agar aktif kembali?', type: 'success', confirmText: 'Ya, Pulihkan' })">
                                            @csrf
                                            <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-green-500 hover:text-green-600 text-red-500 rounded-lg transition-colors" title="Pulihkan Toko">
                                                <x-icon name="arrow-path" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @endif
                                    
                                    {{-- Kunjungi Etalase --}}
                                    <a href="{{ route('store.show', $store->slug) }}" target="_blank" class="p-2 bg-white border border-brand-navy text-brand-navy hover:bg-brand-navy hover:text-white rounded-lg transition-colors" title="Lihat Etalase Publik">
                                        <x-icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($stores->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $stores->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-20">
                    <x-icon name="building-storefront" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-bold text-gray-900">Tidak Ada Data Toko</h3>
                    <p class="text-gray-500 mt-1">Belum ada penjual yang terdaftar dengan kriteria ini.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
