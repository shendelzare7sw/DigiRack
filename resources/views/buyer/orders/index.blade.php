<x-app-layout>
    <x-slot name="title">Riwayat Pesanan</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold font-display text-gray-900 mb-6">Riwayat Pesanan Saya</h1>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5" /> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-2">
                <x-icon name="x-circle" class="w-5 h-5" /> {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produk Pembelian</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($orders as $order)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-400 mb-1 flex items-center gap-2">
                                        <span>{{ $order->invoice_number }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $order->created_at->translatedFormat('d M y') }}</span>
                                    </div>
                                    <div class="font-bold text-gray-900 line-clamp-1 mb-1">{{ $order->items->first()->product->name ?? 'Produk Terhapus' }}</div>
                                    @if($order->items->count() > 1)
                                        <div class="text-xs text-brand-navy font-semibold">+{{ $order->items->count() - 1 }} produk lainnya</div>
                                    @endif
                                    <div class="text-sm text-gray-600 mt-2 flex items-center gap-1.5 focus:outline-none">
                                        <x-icon name="building-storefront" class="w-3.5 h-3.5 text-gray-400" />
                                        {{ $order->store->name ?? 'Toko' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                    <div class="text-xs text-brand-orange mt-1">Midtrans Escrow</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 border border-{{ $order->status_color }}-200">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    @if($order->status == 'shipped')
                                        <form action="{{ route('buyer.orders.confirm', $order->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white font-bold px-4 py-2 rounded-lg transition-colors mr-2 shadow-sm">
                                                Diterima
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('buyer.orders.show', $order->id) }}" class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:border-brand-navy hover:text-brand-navy text-gray-600 font-bold px-4 py-2 rounded-lg transition-all shadow-sm">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($orders->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $orders->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-20">
                    <x-icon name="shopping-bag" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-bold text-gray-900">Belum ada pesanan</h3>
                    <p class="text-gray-500 mt-1">Anda belum melakukan transaksi apapun. Mari mulai berbelanja!</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
