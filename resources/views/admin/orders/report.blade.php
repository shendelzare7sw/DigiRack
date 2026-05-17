@php
    $periodParts = [];
    if (request('from')) $periodParts[] = \Illuminate\Support\Carbon::parse(request('from'))->translatedFormat('d M Y');
    if (request('to')) $periodParts[] = \Illuminate\Support\Carbon::parse(request('to'))->translatedFormat('d M Y');
    $period = count($periodParts) ? implode(' s/d ', $periodParts) : 'Semua Periode';
    $statusFilter = request('status') ? \Illuminate\Support\Str::title(str_replace('_', ' ', request('status'))) : 'Semua Status';
@endphp

<x-print-layout
    title="Laporan Transaksi Platform"
    subtitle="{{ $period }} • {{ $statusFilter }}"
    doc-label="LAPORAN TRANSAKSI"
    :back-url="route('admin.orders.index', request()->only(['status','payment','search']))">

    <div class="mb-5">
        <p class="text-sm font-bold text-gray-900">Rekapitulasi Transaksi Marketplace</p>
        <p class="text-xs text-gray-500">Periode: {{ $period }} &nbsp;•&nbsp; Filter Status: {{ $statusFilter }}</p>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Total Pesanan</p>
            <p class="font-display font-extrabold text-lg text-gray-900 mt-0.5">{{ $summary['count'] }}</p>
        </div>
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-blue-500 font-bold">GMV Terbayar</p>
            <p class="font-display font-extrabold text-base text-blue-700 mt-0.5">Rp {{ number_format($summary['paidGross'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-green-100 bg-green-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-green-600 font-bold">Selesai</p>
            <p class="font-display font-extrabold text-lg text-green-700 mt-0.5">{{ $summary['completed'] }}</p>
        </div>
        <div class="rounded-xl border border-red-100 bg-red-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-red-500 font-bold">Dibatalkan</p>
            <p class="font-display font-extrabold text-lg text-red-600 mt-0.5">{{ $summary['cancelled'] }}</p>
        </div>
    </div>

    {{-- Table --}}
    {{-- Desktop / print: table --}}
    <div class="dr-print-table hidden md:block">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-y border-gray-200 bg-gray-50 text-left text-[11px] uppercase tracking-wide text-gray-500">
                    <th class="py-2.5 px-2 sm:px-3 font-bold">No</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold">Invoice / Tanggal</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold">Pembeli</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold">Toko</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold text-right">Nilai</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $i => $order)
                    <tr class="avoid-break">
                        <td class="py-2.5 px-2 sm:px-3 text-gray-400">{{ $i + 1 }}</td>
                        <td class="py-2.5 px-2 sm:px-3">
                            <p class="font-semibold text-gray-900 break-all">{{ $order->invoice_number }}</p>
                            <p class="text-[11px] text-gray-400">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                        </td>
                        <td class="py-2.5 px-2 sm:px-3 text-gray-700">{{ $order->buyer->name ?? '-' }}</td>
                        <td class="py-2.5 px-2 sm:px-3 text-gray-700">{{ $order->store->name ?? '-' }}</td>
                        <td class="py-2.5 px-2 sm:px-3 text-right font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-2 sm:px-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 border border-{{ $order->status_color }}-200 whitespace-nowrap">
                                {{ $order->status_label }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-10 text-center text-gray-400">Tidak ada data transaksi untuk filter ini.</td></tr>
                @endforelse
            </tbody>
            @if($orders->count())
                <tfoot>
                    <tr class="border-t-2 border-gray-200 bg-gray-50 font-bold text-gray-900">
                        <td colspan="4" class="py-3 px-2 sm:px-3 text-right">Total Nilai Kotor (GMV)</td>
                        <td class="py-3 px-2 sm:px-3 text-right whitespace-nowrap">Rp {{ number_format($summary['gross'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    {{-- Mobile: cards --}}
    <div class="dr-print-cards md:hidden space-y-2.5">
        @forelse($orders as $i => $order)
            <div class="rounded-xl border border-gray-200 p-3 avoid-break">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-semibold text-gray-900 text-sm break-all leading-snug">
                        <span class="text-gray-400 font-normal">{{ $i + 1 }}.</span> {{ $order->invoice_number }}
                    </p>
                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 border border-{{ $order->status_color }}-200">
                        {{ $order->status_label }}
                    </span>
                </div>
                <p class="text-[11px] text-gray-400 mt-0.5">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                <div class="flex items-end justify-between gap-2 mt-3">
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400">Pembeli / Toko</p>
                        <p class="text-sm text-gray-700 truncate">{{ $order->buyer->name ?? '-' }}</p>
                        <p class="text-[11px] text-gray-500 truncate">{{ $order->store->name ?? '-' }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[10px] text-gray-400">Nilai</p>
                        <p class="font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 py-10 text-center text-gray-400 text-sm">Tidak ada data transaksi untuk filter ini.</div>
        @endforelse

        @if($orders->count())
            <div class="rounded-xl border-2 border-gray-200 bg-gray-50 p-3 flex items-center justify-between">
                <span class="font-bold text-gray-900 text-sm">Total Nilai Kotor (GMV)</span>
                <span class="font-display font-extrabold text-brand-navy">Rp {{ number_format($summary['gross'], 0, ',', '.') }}</span>
            </div>
        @endif
    </div>
</x-print-layout>
