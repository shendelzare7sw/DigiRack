<x-app-layout>
    <x-slot name="title">Kelola Pesanan #{{ $order->invoice_number }}</x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('seller.orders.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h1 class="font-display font-bold text-xl sm:text-2xl text-gray-900">Pesanan #{{ $order->invoice_number }}</h1>
                    <span class="px-4 py-1.5 rounded-full text-sm font-bold bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 border border-{{ $order->status_color }}-200">
                        {{ $order->status_label }}
                    </span>
                </div>
            </div>
        </div>

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Kiri: Detail & Item --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Info Pembeli --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4 flex items-center gap-2">
                        <x-icon name="document-text" class="w-5 h-5 text-brand-navy" /> Rincian Invoice
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 mb-1">Nomor Invoice</p>
                            <p class="font-bold text-gray-900">{{ $order->invoice_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Tanggal Pesanan</p>
                            <p class="font-semibold text-gray-800">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                        <div class="col-span-2 mt-2">
                            <p class="text-gray-500 mb-1">Data Pengiriman</p>
                            <p class="font-bold text-gray-900 mb-1">{{ $order->shipping_address['name'] }} <span class="font-normal text-gray-600">({{ $order->shipping_address['phone'] }})</span></p>
                            <p class="text-gray-700 leading-relaxed">{{ $order->shipping_address['full_address'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Item Produk --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4 flex items-center gap-2">
                        <x-icon name="shopping-bag" class="w-5 h-5 text-brand-orange" /> Produk Dibeli
                    </h3>
                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                        <div class="py-4 flex gap-4 items-center">
                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-16 h-16 rounded-lg object-cover border border-gray-100">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 line-clamp-1">{{ $item->product->name }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ number_format($item->quantity) }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-brand-orange">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Kanan: Aksi & Kurir --}}
            <div class="space-y-6">
                <div class="bg-brand-navylight/30 rounded-2xl p-6 border border-brand-navy/10">
                    <h3 class="text-lg font-bold text-brand-navy mb-4 border-b border-brand-navy/20 pb-2">Informasi Kurir</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ekspedisi</span>
                            @php
                                $isToko = str_starts_with(strtolower($order->shipping_address['courier']), 'toko_');
                                $kurirName = $isToko ? str_replace('toko_', 'Kurir Toko (', $order->shipping_address['courier']) . ')' : strtoupper($order->shipping_address['courier']);
                            @endphp
                            <span class="font-bold text-gray-900">{{ $kurirName }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex flex-col mt-4 pt-4 border-t border-brand-navy/20">
                            <span class="text-gray-600 mb-1">Nomor Resi</span>
                            <span class="font-mono text-lg font-bold {{ $order->shipping_tracking_number ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $order->shipping_tracking_number ?? 'Belum Diinput' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Card --}}
                @if($order->status === 'processing')
                    <div class="bg-white rounded-2xl shadow-sm border border-brand-orange/30 p-6 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-brand-orange"></div>
                        <h3 class="font-bold text-gray-900 mb-2">Tindakan Diperlukan</h3>
                        
                        @if($isToko)
                            <p class="text-sm text-gray-600 mb-4">Pembeli memilih opsi **Kurir Internal Toko**. Pastikan armada Anda segera mengirimkan paket. Klik tombol di bawah jika barang sudah di perjalanan.</p>
                            <form action="{{ route('seller.orders.status', $order->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="shipped">
                                <input type="hidden" name="shipping_tracking_number" value="DIKIRIM-KURIR-TOKO">
                                <button type="submit" class="w-full bg-brand-orange hover:bg-orange-600 text-white font-bold py-3 text-sm rounded-xl transition-all shadow-sm">
                                    Tandai Sedang Dikirim
                                </button>
                            </form>
                        @else
                            <p class="text-sm text-gray-600 mb-4">Silakan input nomor resi pengiriman asli setelah paket diserahkan ke pihak ekspedisi.</p>
                            <form action="{{ route('seller.orders.status', $order->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="shipped">
                                <div class="mb-3">
                                    <input type="text" name="shipping_tracking_number" required class="w-full border-gray-300 focus:border-brand-orange focus:ring-brand-orange rounded-lg text-sm" placeholder="Input No Resi Valid...">
                                </div>
                                <button type="submit" class="w-full bg-brand-navy hover:bg-brand-navydark text-white font-bold py-3 text-sm rounded-xl transition-all shadow-sm">
                                    Kirim Pesanan
                                </button>
                            </form>
                        @endif
                    </div>
                @elseif($order->status === 'shipped')
                    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 text-center">
                        <x-icon name="truck" class="w-10 h-10 text-blue-400 mx-auto mb-2" />
                        <h3 class="font-bold text-blue-900 mb-1">Pesanan Dalam Perjalanan</h3>
                        <p class="text-sm text-blue-700">Menunggu pembeli melakukan konfirmasi penerimaan barang ("Pesanan Diterima") untuk mencairkan saldo Anda.</p>
                    </div>
                @elseif($order->status === 'completed')
                    <div class="bg-green-50 rounded-2xl p-6 border border-green-100 text-center">
                        <x-icon name="check-badge" class="w-10 h-10 text-green-500 mx-auto mb-2" />
                        <h3 class="font-bold text-green-900 mb-1">Selesai Berhasil</h3>
                        <p class="text-sm text-green-700">Dana telah dimasukkan ke dalam Saldo Dompet Toko Anda.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
