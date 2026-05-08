<x-app-layout>
    <x-slot name="title">Semua Pesanan</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Semua Pesanan</h1>
                <p class="text-gray-500 text-sm mt-1">Monitoring seluruh transaksi dan pesanan di platform.</p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 text-center">
                <p class="font-display font-bold text-xl text-gray-900">{{ $totalOrders }}</p>
                <p class="text-[10px] text-gray-500 font-medium mt-0.5">Total</p>
            </div>
            <div class="bg-white rounded-xl border border-yellow-100 shadow-sm p-3 text-center">
                <p class="font-display font-bold text-xl text-yellow-600">{{ $pendingPayment }}</p>
                <p class="text-[10px] text-gray-500 font-medium mt-0.5">Pending</p>
            </div>
            <div class="bg-white rounded-xl border border-blue-100 shadow-sm p-3 text-center">
                <p class="font-display font-bold text-xl text-blue-600">{{ $processing }}</p>
                <p class="text-[10px] text-gray-500 font-medium mt-0.5">Diproses</p>
            </div>
            <div class="bg-white rounded-xl border border-indigo-100 shadow-sm p-3 text-center">
                <p class="font-display font-bold text-xl text-indigo-600">{{ $shipped }}</p>
                <p class="text-[10px] text-gray-500 font-medium mt-0.5">Dikirim</p>
            </div>
            <div class="bg-white rounded-xl border border-green-100 shadow-sm p-3 text-center">
                <p class="font-display font-bold text-xl text-green-600">{{ $completed }}</p>
                <p class="text-[10px] text-gray-500 font-medium mt-0.5">Selesai</p>
            </div>
            <div class="bg-white rounded-xl border border-red-100 shadow-sm p-3 text-center">
                <p class="font-display font-bold text-xl text-red-500">{{ $cancelled }}</p>
                <p class="text-[10px] text-gray-500 font-medium mt-0.5">Batal</p>
            </div>
        </div>

        {{-- Filter & Search --}}
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice, buyer, atau toko..." class="flex-1 border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">

                <select name="status" class="border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm bg-white">
                    <option value="">Semua Status</option>
                    <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>

                <select name="payment" class="border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm bg-white">
                    <option value="">Semua Pembayaran</option>
                    <option value="unpaid" {{ request('payment') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="paid" {{ request('payment') == 'paid' ? 'selected' : '' }}>Sudah Bayar</option>
                </select>

                <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white px-5 py-2 rounded-xl text-sm font-bold shadow-sm transition-colors">Filter</button>
            </form>
        </div>

        {{-- Data Table --}}
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Invoice</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Buyer</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Toko</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-sm">
                            @foreach($orders as $order)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-brand-navy font-mono text-xs">{{ $order->invoice_number }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                        {{ $order->items->count() }} item
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $order->buyer->avatar_url ?? '' }}" class="w-7 h-7 rounded-full border border-gray-100 shrink-0">
                                        <div class="text-sm font-semibold text-gray-700 truncate max-w-[120px]">{{ $order->buyer->name ?? '-' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-700 truncate max-w-[120px]">{{ $order->store->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $order->formatted_total }}</td>
                                <td class="px-6 py-4">
                                    @php $sc = $order->status_color; @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $sc }}-100 text-{{ $sc }}-700 border border-{{ $sc }}-200">
                                        {{ $order->status_label }}
                                    </span>
                                    @if($order->payment_status === 'paid')
                                        <div class="text-[10px] text-green-600 font-semibold mt-1 flex items-center gap-0.5">
                                            <x-icon name="check-circle" class="w-3 h-3" /> Paid
                                        </div>
                                    @else
                                        <div class="text-[10px] text-yellow-600 font-semibold mt-1">Unpaid</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    {{ $order->created_at->translatedFormat('d M Y') }}
                                    <div class="text-[10px] text-gray-400">{{ $order->created_at->format('H:i') }}</div>
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
                    <x-icon name="clipboard-document-list" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-bold text-gray-900">Tidak Ada Pesanan</h3>
                    <p class="text-gray-500 mt-1">Tidak ditemukan pesanan dengan kriteria ini.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
