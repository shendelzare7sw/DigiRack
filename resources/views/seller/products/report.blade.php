@php
    $statusFilter = request('status') ? \Illuminate\Support\Str::title(request('status')) : 'Semua Status';
    $searchFilter = request('search') ? '"' . request('search') . '"' : null;
@endphp

<x-print-layout
    title="Laporan Stok Produk — {{ $store->name }}"
    subtitle="{{ $stats['total'] }} produk • {{ $statusFilter }}"
    doc-label="LAPORAN STOK"
    :back-url="route('admin.products.index', request()->only(['status','search','category']))">

    <div class="mb-5">
        <p class="text-sm font-bold text-gray-900">{{ $store->name }}</p>
        <p class="text-xs text-gray-500">
            Filter Status: {{ $statusFilter }}
            @if($searchFilter) &nbsp;•&nbsp; Pencarian: {{ $searchFilter }} @endif
        </p>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Total Produk</p>
            <p class="font-display font-extrabold text-lg text-gray-900 mt-0.5">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-xl border border-green-100 bg-green-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-green-600 font-bold">Aktif</p>
            <p class="font-display font-extrabold text-lg text-green-700 mt-0.5">{{ $stats['active'] }}</p>
        </div>
        <div class="rounded-xl border border-yellow-100 bg-yellow-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-yellow-600 font-bold">Stok Menipis</p>
            <p class="font-display font-extrabold text-lg text-yellow-700 mt-0.5">{{ $stats['lowStock'] }}</p>
        </div>
        <div class="rounded-xl border border-red-100 bg-red-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-red-500 font-bold">Stok Habis</p>
            <p class="font-display font-extrabold text-lg text-red-600 mt-0.5">{{ $stats['outOfStock'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-6">
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-blue-500 font-bold">Estimasi Nilai Stok</p>
            <p class="font-display font-extrabold text-base text-blue-700 mt-0.5">Rp {{ number_format($stats['stockValue'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Total Terjual</p>
            <p class="font-display font-extrabold text-base text-gray-900 mt-0.5">{{ number_format($stats['totalSold'], 0, ',', '.') }} unit</p>
        </div>
    </div>

    {{-- Table --}}
    {{-- Desktop / print: table --}}
    <div class="dr-print-table hidden md:block">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-y border-gray-200 bg-gray-50 text-left text-[11px] uppercase tracking-wide text-gray-500">
                    <th class="py-2.5 px-2 sm:px-3 font-bold">No</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold">Produk</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold">Kategori</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold text-right">Harga</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold text-center">Stok</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold text-center">Terjual</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $i => $p)
                    @php
                        $stockColor = $p->stock == 0 ? 'red' : ($p->stock <= 5 ? 'yellow' : 'gray');
                        $stColor = $p->status === 'active' ? 'green' : 'gray';
                    @endphp
                    <tr class="avoid-break">
                        <td class="py-2.5 px-2 sm:px-3 text-gray-400">{{ $i + 1 }}</td>
                        <td class="py-2.5 px-2 sm:px-3 font-semibold text-gray-900">{{ $p->name }}</td>
                        <td class="py-2.5 px-2 sm:px-3 text-gray-600">{{ $p->category->name ?? '-' }}</td>
                        <td class="py-2.5 px-2 sm:px-3 text-right text-gray-900 whitespace-nowrap">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-2 sm:px-3 text-center">
                            <span class="font-bold text-{{ $stockColor }}-{{ $stockColor === 'gray' ? '700' : '600' }}">{{ $p->stock }}</span>
                        </td>
                        <td class="py-2.5 px-2 sm:px-3 text-center text-gray-600">{{ $p->sold_count }}</td>
                        <td class="py-2.5 px-2 sm:px-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-{{ $stColor }}-100 text-{{ $stColor }}-700 border border-{{ $stColor }}-200 whitespace-nowrap">
                                {{ \Illuminate\Support\Str::title($p->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-10 text-center text-gray-400">Tidak ada produk untuk filter ini.</td></tr>
                @endforelse
            </tbody>
            @if($products->count())
                <tfoot>
                    <tr class="border-t-2 border-gray-200 bg-gray-50 font-bold text-gray-900">
                        <td colspan="3" class="py-3 px-2 sm:px-3 text-right">Estimasi Nilai Stok</td>
                        <td class="py-3 px-2 sm:px-3 text-right whitespace-nowrap" colspan="4">Rp {{ number_format($stats['stockValue'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    {{-- Mobile: cards --}}
    <div class="dr-print-cards md:hidden space-y-2.5">
        @forelse($products as $i => $p)
            @php
                $stockColor = $p->stock == 0 ? 'red' : ($p->stock <= 5 ? 'yellow' : 'gray');
                $stColor = $p->status === 'active' ? 'green' : 'gray';
            @endphp
            <div class="rounded-xl border border-gray-200 p-3 avoid-break">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-semibold text-gray-900 text-sm leading-snug">
                        <span class="text-gray-400 font-normal">{{ $i + 1 }}.</span> {{ $p->name }}
                    </p>
                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-{{ $stColor }}-100 text-{{ $stColor }}-700 border border-{{ $stColor }}-200">
                        {{ \Illuminate\Support\Str::title($p->status) }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">{{ $p->category->name ?? 'Tanpa Kategori' }}</p>
                <div class="grid grid-cols-3 gap-2 mt-3 text-center">
                    <div class="bg-gray-50 rounded-lg py-1.5">
                        <p class="text-[10px] text-gray-400">Harga</p>
                        <p class="text-xs font-bold text-gray-900">Rp {{ number_format($p->price, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg py-1.5">
                        <p class="text-[10px] text-gray-400">Stok</p>
                        <p class="text-sm font-bold text-{{ $stockColor }}-{{ $stockColor === 'gray' ? '700' : '600' }}">{{ $p->stock }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg py-1.5">
                        <p class="text-[10px] text-gray-400">Terjual</p>
                        <p class="text-sm font-semibold text-gray-700">{{ $p->sold_count }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 py-10 text-center text-gray-400 text-sm">Tidak ada produk untuk filter ini.</div>
        @endforelse

        @if($products->count())
            <div class="rounded-xl border-2 border-gray-200 bg-gray-50 p-3 flex items-center justify-between">
                <span class="font-bold text-gray-900 text-sm">Estimasi Nilai Stok</span>
                <span class="font-display font-extrabold text-brand-navy">Rp {{ number_format($stats['stockValue'], 0, ',', '.') }}</span>
            </div>
        @endif
    </div>
</x-print-layout>
