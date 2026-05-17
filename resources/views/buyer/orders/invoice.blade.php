@php
    $paymentLabel = match($order->payment_status) {
        'paid' => 'LUNAS',
        'pending' => 'MENUNGGU KONFIRMASI',
        'failed' => 'GAGAL',
        default => 'BELUM DIBAYAR',
    };
    $paymentColor = match($order->payment_status) {
        'paid' => 'green',
        'pending' => 'yellow',
        'failed' => 'red',
        default => 'gray',
    };
    $rawCourier = $order->shipping_address['courier'] ?? '-';
    $courierName = \Illuminate\Support\Str::startsWith(strtolower($rawCourier), 'toko_') ? 'KURIR TOKO' : strtoupper($rawCourier);
@endphp

<x-print-layout
    title="Invoice {{ $order->invoice_number }}"
    subtitle="{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB"
    doc-label="INVOICE"
    :watermark="true"
    :back-url="route('buyer.orders.show', $order->id)">

    {{-- Meta + status --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-xs text-gray-500">No. Invoice</p>
            <p class="font-display font-bold text-base sm:text-lg text-gray-900 break-all">{{ $order->invoice_number }}</p>
            @if($order->payment_reference)
                <p class="text-[11px] text-gray-400 mt-0.5 break-all">Ref: {{ $order->payment_reference }}</p>
            @endif
        </div>
        <div class="flex flex-wrap gap-2 sm:justify-end">
            <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 border border-{{ $order->status_color }}-200">
                {{ strtoupper($order->status_label) }}
            </span>
            <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-{{ $paymentColor }}-100 text-{{ $paymentColor }}-700 border border-{{ $paymentColor }}-200">
                {{ $paymentLabel }}
            </span>
        </div>
    </div>

    {{-- Parties --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">Penjual</p>
            <p class="font-bold text-gray-900 text-sm">{{ $order->store->name ?? 'Toko' }}</p>
            @if($order->store?->city)
                <p class="text-xs text-gray-500 mt-1">{{ $order->store->city }}</p>
            @endif
        </div>
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">Dikirim Kepada</p>
            <p class="font-bold text-gray-900 text-sm">{{ $order->shipping_address['name'] ?? $order->buyer->name }}</p>
            <p class="text-xs text-gray-500">{{ $order->shipping_address['phone'] ?? '-' }}</p>
            <p class="text-xs text-gray-600 mt-1 leading-relaxed">{{ $order->shipping_address['full_address'] ?? '-' }}</p>
        </div>
    </div>

    {{-- Shipping info --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6 text-sm">
        <div>
            <p class="text-[11px] text-gray-400">Kurir</p>
            <p class="font-semibold text-gray-900">{{ $courierName }}</p>
        </div>
        <div>
            <p class="text-[11px] text-gray-400">No. Resi</p>
            <p class="font-semibold text-gray-900 break-all">{{ $order->shipping_tracking_number ?: 'Belum tersedia' }}</p>
        </div>
        <div>
            <p class="text-[11px] text-gray-400">Metode Bayar</p>
            <p class="font-semibold text-gray-900">{{ strtoupper($order->payment_method ?? 'TRANSFER') }}</p>
        </div>
    </div>

    {{-- Items --}}
    <div class="overflow-x-auto -mx-1 sm:mx-0">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-y border-gray-200 bg-gray-50 text-left text-[11px] uppercase tracking-wide text-gray-500">
                    <th class="py-2.5 px-2 sm:px-3 font-bold">Produk</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold text-center">Qty</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold text-right">Harga</th>
                    <th class="py-2.5 px-2 sm:px-3 font-bold text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <tr class="avoid-break">
                        <td class="py-3 px-2 sm:px-3">
                            <p class="font-semibold text-gray-900">{{ $item->product_name_snapshot ?? $item->product->name ?? 'Produk' }}</p>
                        </td>
                        <td class="py-3 px-2 sm:px-3 text-center text-gray-600">{{ $item->quantity }}</td>
                        <td class="py-3 px-2 sm:px-3 text-right text-gray-600 whitespace-nowrap">Rp {{ number_format($item->price_snapshot, 0, ',', '.') }}</td>
                        <td class="py-3 px-2 sm:px-3 text-right font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format($item->price_snapshot * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div class="mt-6 flex justify-end">
        <div class="w-full sm:w-80 space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal Produk</span>
                <span class="font-medium text-gray-900">Rp {{ number_format($itemsSubtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Ongkos Kirim</span>
                <span class="font-medium text-gray-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            @foreach($buyerFees as $fee)
                <div class="flex justify-between text-gray-600">
                    <span>{{ $fee['name'] ?? 'Biaya Layanan' }}</span>
                    <span class="font-medium text-gray-900">Rp {{ number_format($fee['amount'] ?? 0, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <div class="flex justify-between items-center pt-3 mt-1 border-t-2 border-gray-200">
                <span class="font-bold text-gray-900">Total Tagihan</span>
                <span class="font-display font-extrabold text-lg text-brand-navy">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</x-print-layout>
