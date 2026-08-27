<x-app-layout>
    <x-slot name="title">Kelola Pesanan Penjualan</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col gap-4 mb-6">
            <div class="flex items-start gap-3">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                    <x-icon name="arrow-left" class="w-4 h-4" />
                </a>
                <div>
                    <h1 class="text-2xl font-bold font-display text-gray-900">Daftar Transaksi</h1>
                    <p class="text-gray-500 text-sm mt-1">Pantau, proses, dan kirimkan pesanan pelanggan Anda.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="flex-1">
                    <select name="status" onchange="this.form.submit()" class="w-full sm:w-auto border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm bg-white">
                        <option value="">Semua Status</option>
                        <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Perlu Dikirim</option>
                        <option value="cancellation_requested" {{ request('status') == 'cancellation_requested' ? 'selected' : '' }}>Permintaan Batal</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Sedang Dikirim</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </form>
                <a href="{{ route('admin.orders.report', request()->only(['status'])) }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 bg-brand-navy hover:bg-brand-navydark text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-sm transition-colors shrink-0">
                    <x-icon name="document-chart-bar" class="w-4 h-4" />
                    Cetak Laporan
                </a>
            </div>
        </div>

        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            @if($orders->count() > 0)
                {{-- Desktop table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produk & Pembeli</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nilai Transaksi</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($orders as $order)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-400 mb-1 flex items-center justify-between">
                                        <span>{{ $order->invoice_number }}</span>
                                        <span>{{ $order->created_at->translatedFormat('d M y') }}</span>
                                    </div>
                                    <div class="font-bold text-gray-900 line-clamp-1 mb-1">{{ $order->items->first()->product->name ?? 'Produk Terhapus' }}</div>
                                    @if($order->items->count() > 1)
                                        <div class="text-xs text-brand-navy font-semibold">+{{ $order->items->count() - 1 }} produk lainnya</div>
                                    @endif
                                    <div class="text-sm text-gray-600 mt-2 flex items-center gap-1.5">
                                        <x-icon name="user" class="w-3.5 h-3.5 text-gray-400" />
                                        {{ $order->buyer->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                    @php $c = $order->shipping_address['courier'] ?? ''; @endphp
                                    <div class="text-xs text-gray-500 mt-1">{{ str_starts_with(strtolower($c), 'toko_') ? 'Kurir Toko' : strtoupper($c) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 border border-{{ $order->status_color }}-200">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($order->status == 'cancellation_requested')
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white font-bold px-4 py-2 rounded-lg transition-colors">
                                            <x-icon name="exclamation-circle" class="w-4 h-4" /> Review Batal
                                        </a>
                                    @elseif($order->status == 'processing')
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1.5 bg-orange-600 hover:bg-orange-700 text-white font-bold px-4 py-2 rounded-lg transition-colors">
                                            <x-icon name="truck" class="w-4 h-4" /> Proses Resi
                                        </a>
                                    @else
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:border-brand-navy hover:text-brand-navy text-gray-600 font-bold px-4 py-2 rounded-lg transition-all shadow-sm">
                                            <x-icon name="eye-outline" class="w-4 h-4" /> Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile cards --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach($orders as $order)
                        <div class="p-4 cursor-pointer transition-colors active:bg-gray-50 focus:outline-none focus:bg-gray-50"
                            role="link"
                            tabindex="0"
                            aria-label="Lihat detail pesanan {{ $order->invoice_number }}"
                            onclick="window.location.href='{{ route('admin.orders.show', $order->id) }}'"
                            onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); window.location.href='{{ route('admin.orders.show', $order->id) }}'; }">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs text-gray-400 font-mono break-all truncate max-w-[55%]">{{ $order->invoice_number }}</span>
                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 border border-{{ $order->status_color }}-200">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                            <p class="font-bold text-sm text-gray-900 line-clamp-1">{{ $order->items->first()->product->name ?? 'Produk Terhapus' }}</p>
                            @if($order->items->count() > 1)
                                <p class="text-xs text-brand-navy font-semibold">+{{ $order->items->count() - 1 }} produk lainnya</p>
                            @endif
                            <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500">
                                <x-icon name="user" class="w-3 h-3" /> {{ $order->buyer->name }}
                                <span>&bull;</span>
                                {{ $order->created_at->translatedFormat('d M Y') }}
                            </div>
                            <div class="flex items-center justify-between gap-3 mt-3">
                                <div class="min-w-0">
                                    <span class="block font-bold text-brand-blue truncate">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                    @if($order->shipping_address['courier'] ?? false)
                                        @php $mc = $order->shipping_address['courier']; @endphp
                                        <span class="text-[10px] text-gray-400">{{ str_starts_with(strtolower($mc), 'toko_') ? 'Kurir Toko' : strtoupper($mc) }}</span>
                                    @endif
                                </div>
                                @if($order->status == 'cancellation_requested')
                                    <a href="{{ route('admin.orders.show', $order->id) }}" onclick="event.stopPropagation()" onkeydown="event.stopPropagation()" class="shrink-0 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                                        <x-icon name="exclamation-circle" class="w-3.5 h-3.5" /> Review
                                    </a>
                                @elseif($order->status == 'processing')
                                    <a href="{{ route('admin.orders.show', $order->id) }}" onclick="event.stopPropagation()" onkeydown="event.stopPropagation()" class="shrink-0 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                                        <x-icon name="truck" class="w-3.5 h-3.5" /> Proses
                                    </a>
                                @else
                                    <a href="{{ route('admin.orders.show', $order->id) }}" onclick="event.stopPropagation()" onkeydown="event.stopPropagation()" class="shrink-0 bg-white border border-gray-200 hover:border-brand-navy text-gray-600 hover:text-brand-navy font-bold text-xs px-3 py-1.5 rounded-lg transition-all">Detail</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($orders->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $orders->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-20">
                    <x-icon name="inbox" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-bold text-gray-900">Tidak ada pesanan</h3>
                    <p class="text-gray-500 mt-1">Belum ada pesanan yang masuk dengan kriteria ini.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
