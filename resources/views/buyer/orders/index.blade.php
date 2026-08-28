<x-app-layout>
    <x-slot name="title">Riwayat Pesanan</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Riwayat Pesanan Saya</h1>
                <p class="text-gray-500 text-sm mt-1">Daftar semua transaksi dan status pesanan Anda.</p>
            </div>
        </div>

        @if(request('payment') === 'success')
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-2" x-data="{ show: true }" x-show="show" x-transition>
                <x-icon name="check-circle" class="w-5 h-5 shrink-0" /> Pembayaran berhasil! Status pesanan akan diperbarui secara otomatis.
                <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600"><x-icon name="x-mark" class="w-4 h-4" /></button>
            </div>
        @elseif(request('payment') === 'pending')
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-4 rounded-xl mb-6 flex items-center gap-2">
                <x-icon name="clock" class="w-5 h-5 shrink-0" /> Pembayaran Anda masih menunggu konfirmasi. Status akan diperbarui otomatis setelah pembayaran dikonfirmasi.
            </div>
        @elseif(request('payment') === 'error')
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-2">
                <x-icon name="x-circle" class="w-5 h-5 shrink-0" /> Pembayaran gagal. Silakan coba bayar kembali melalui halaman detail pesanan.
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 shrink-0" /> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-2">
                <x-icon name="x-circle" class="w-5 h-5 shrink-0" /> {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            @if($orders->count() > 0)
                {{-- Desktop table --}}
                <div class="hidden md:block overflow-x-auto">
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
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 border border-{{ $order->status_color }}-200">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    @if($order->status == 'shipped')
                                        <form action="{{ route('buyer.orders.confirm', $order->id) }}" method="POST" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Konfirmasi Pesanan Diterima', message: 'Pastikan barang sudah Anda terima dalam kondisi baik. Setelah dikonfirmasi, pesanan akan ditutup.', type: 'info', confirmText: 'Ya, Barang Diterima' })">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white font-bold px-4 py-2 rounded-lg transition-colors mr-2 shadow-sm">
                                                Diterima
                                            </button>
                                        </form>
                                    @endif
                                    @if(in_array($order->status, ['pending_payment', 'processing']))
                                        <form action="{{ route('buyer.orders.cancel', $order->id) }}" method="POST" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: '{{ $order->status === 'pending_payment' ? 'Batalkan Pesanan' : 'Ajukan Pembatalan' }}', message: '{{ $order->status === 'pending_payment' ? 'Pesanan belum dibayar dan akan langsung dibatalkan.' : 'Pesanan sudah diproses. Permintaan pembatalan akan ditinjau Digital Hook.' }}', type: 'danger', confirmText: '{{ $order->status === 'pending_payment' ? 'Ya, Batalkan' : 'Kirim Permintaan' }}' })">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 bg-white border border-red-200 hover:border-red-400 hover:text-red-600 text-red-500 font-bold px-4 py-2 rounded-lg transition-all shadow-sm mr-2">
                                                {{ $order->status === 'pending_payment' ? 'Batalkan' : 'Minta Batal' }}
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('buyer.orders.invoice', $order->id) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:border-brand-navy hover:text-brand-navy text-gray-600 font-bold px-4 py-2 rounded-lg transition-all shadow-sm mr-2" title="Lihat / Cetak Invoice">
                                        <x-icon name="document-text" class="w-4 h-4" /> Invoice
                                    </a>
                                    <a href="{{ route('buyer.orders.show', $order->id) }}" class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:border-brand-navy hover:text-brand-navy text-gray-600 font-bold px-4 py-2 rounded-lg transition-all shadow-sm">
                                        Detail
                                    </a>
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
                            onclick="window.location.href='{{ route('buyer.orders.show', $order->id) }}'"
                            onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); window.location.href='{{ route('buyer.orders.show', $order->id) }}'; }">
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
                            <div class="mt-1 text-xs text-gray-500">{{ $order->created_at->translatedFormat('d M Y') }}</div>
                            <div class="flex items-center justify-between gap-3 mt-3">
                                <span class="font-bold text-brand-blue text-base min-w-0 truncate">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if($order->status == 'shipped')
                                        <form action="{{ route('buyer.orders.confirm', $order->id) }}" method="POST" class="inline" onclick="event.stopPropagation()" onkeydown="event.stopPropagation()" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Konfirmasi Pesanan Diterima', message: 'Pastikan barang sudah Anda terima dalam kondisi baik. Setelah dikonfirmasi, pesanan akan ditutup.', type: 'info', confirmText: 'Ya, Barang Diterima' })">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold text-xs px-2.5 py-1.5 rounded-lg transition-colors">Diterima</button>
                                        </form>
                                    @endif
                                    @if(in_array($order->status, ['pending_payment', 'processing']))
                                        <form action="{{ route('buyer.orders.cancel', $order->id) }}" method="POST" class="inline" onclick="event.stopPropagation()" onkeydown="event.stopPropagation()" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: '{{ $order->status === 'pending_payment' ? 'Batalkan Pesanan' : 'Ajukan Pembatalan' }}', message: '{{ $order->status === 'pending_payment' ? 'Pesanan belum dibayar dan akan langsung dibatalkan.' : 'Pesanan sudah diproses. Permintaan pembatalan akan ditinjau Digital Hook.' }}', type: 'danger', confirmText: '{{ $order->status === 'pending_payment' ? 'Ya, Batalkan' : 'Kirim Permintaan' }}' })">
                                            @csrf
                                            <button type="submit" class="bg-white border border-red-200 hover:border-red-400 text-red-500 hover:text-red-600 font-bold text-xs px-2.5 py-1.5 rounded-lg transition-all">
                                                {{ $order->status === 'pending_payment' ? 'Batalkan' : 'Minta Batal' }}
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('buyer.orders.invoice', $order->id) }}" target="_blank" onclick="event.stopPropagation()" onkeydown="event.stopPropagation()" class="inline-flex items-center justify-center w-8 h-8 bg-white border border-gray-200 hover:border-brand-navy text-gray-600 hover:text-brand-navy rounded-lg transition-all" title="Unduh / Cetak Invoice" aria-label="Unduh / Cetak Invoice">
                                        <x-icon name="document-text" class="w-4 h-4" />
                                        <span class="sr-only">Unduh / Cetak Invoice</span>
                                    </a>
                                    <a href="{{ route('buyer.orders.show', $order->id) }}" onclick="event.stopPropagation()" onkeydown="event.stopPropagation()" class="inline-flex items-center justify-center w-8 h-8 bg-white border border-gray-200 hover:border-brand-navy text-gray-600 hover:text-brand-navy rounded-lg transition-all" title="Lihat Detail Pesanan" aria-label="Lihat Detail Pesanan">
                                        <x-icon name="eye-outline" class="w-4 h-4" />
                                        <span class="sr-only">Lihat Detail Pesanan</span>
                                    </a>
                                </div>
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
                    <x-icon name="shopping-bag" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-bold text-gray-900">Belum ada pesanan</h3>
                    <p class="text-gray-500 mt-1">Anda belum melakukan transaksi apapun. Mari mulai berbelanja!</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
