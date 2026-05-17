@php
    $periodParts = [];
    if (request('from')) $periodParts[] = \Illuminate\Support\Carbon::parse(request('from'))->translatedFormat('d M Y');
    if (request('to')) $periodParts[] = \Illuminate\Support\Carbon::parse(request('to'))->translatedFormat('d M Y');
    $period = count($periodParts) ? implode(' s/d ', $periodParts) : 'Semua Periode';
    $statusFilter = request('status') ? \Illuminate\Support\Str::title(str_replace('_', ' ', request('status'))) : 'Semua Status';
@endphp

<x-print-layout
    title="Laporan Penjualan — {{ $store->name }}"
    subtitle="{{ $period }} • {{ $statusFilter }}"
    doc-label="LAPORAN PENJUALAN"
    :back-url="route('seller.orders.index', request()->only(['status']))">

    <div class="mb-5">
        <p class="text-sm font-bold text-gray-900">{{ $store->name }}</p>
        <p class="text-xs text-gray-500">Periode: {{ $period }} &nbsp;•&nbsp; Filter Status: {{ $statusFilter }}</p>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Total Pesanan</p>
            <p class="font-display font-extrabold text-lg text-gray-900 mt-0.5">{{ $summary['count'] }}</p>
        </div>
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-blue-500 font-bold">Nilai Terbayar</p>
            <p class="font-display font-extrabold text-base text-blue-700 mt-0.5">Rp {{ number_format($summary['paidGross'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-green-100 bg-green-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-green-600 font-bold">Selesai</p>
            <p class="font-display font-extrabold text-lg text-green-700 mt-0.5">{{ $summary['completed'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
            <p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Nilai Kotor</p>
            <p class="font-display font-extrabold text-base text-gray-900 mt-0.5">Rp {{ number_format($summary['gross'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto -mx-1 sm:mx-0">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-y border-gray-200 bg-gray-50 text-left text-[11px] uppercase tracking-wide text-gray-500">
                    <th class="py-2.5 px-2 sm:px-3 font-bold">No</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold">Invoice / Tanggal</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold">Pembeli</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold text-center">Item</th>
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
                        <td class="py-2.5 px-2 sm:px-3 text-center text-gray-600">{{ $order->items->sum('quantity') }}</td>
                        <td class="py-2.5 px-2 sm:px-3 text-right font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-2 sm:px-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 border border-{{ $order->status_color }}-200 whitespace-nowrap">
                                {{ $order->status_label }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-10 text-center text-gray-400">Tidak ada data pesanan untuk filter ini.</td></tr>
                @endforelse
            </tbody>
            @if($orders->count())
                <tfoot>
                    <tr class="border-t-2 border-gray-200 bg-gray-50 font-bold text-gray-900">
                        <td colspan="4" class="py-3 px-2 sm:px-3 text-right">Total Nilai Kotor</td>
                        <td class="py-3 px-2 sm:px-3 text-right whitespace-nowrap">Rp {{ number_format($summary['gross'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</x-print-layout>
