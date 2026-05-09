<x-app-layout>
    <x-slot name="title">Pesanan #{{ $order->invoice_number }}</x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('buyer.orders.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
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
                {{-- Info Pengiriman --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4 flex items-center gap-2">
                        <x-icon name="map-pin" class="w-5 h-5 text-brand-navy" /> Alamat Pengiriman
                    </h3>
                    <div class="text-sm">
                        <p class="font-bold text-gray-900 mb-1">{{ $order->shipping_address['name'] }} <span class="font-normal text-gray-600">({{ $order->shipping_address['phone'] }})</span></p>
                        <p class="text-gray-700 leading-relaxed">{{ $order->shipping_address['full_address'] }}</p>
                    </div>
                </div>

                {{-- Item Produk & Toko --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 border-b pb-3 mb-4">
                        <x-icon name="building-storefront" class="w-5 h-5 text-brand-orange" />
                        <h3 class="text-lg font-bold text-gray-900 flex-1">{{ $order->store->name }}</h3>
                        <a href="{{ route('store.show', $order->store->slug) }}" class="text-xs font-bold text-brand-navy hover:underline">Kunjungi Toko</a>
                    </div>
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
                {{-- Payment / Tracking --}}
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Informasi Pembelian</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">No. Invoice</span>
                            <span class="font-bold text-gray-900">{{ $order->invoice_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal</span>
                            <span class="font-semibold text-gray-900">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Kurir</span>
                            @php
                                $kurirName = str_starts_with(strtolower($order->shipping_address['courier']), 'toko_') 
                                    ? str_replace('toko_', 'Kurir Toko (', $order->shipping_address['courier']) . ')' 
                                    : strtoupper($order->shipping_address['courier']);
                            @endphp
                            <span class="font-bold text-gray-900">{{ $kurirName }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-t border-b border-gray-200 my-2">
                            <span class="text-gray-600">No. Resi</span>
                            @if($order->shipping_tracking_number)
                                <span class="font-mono text-sm font-bold bg-white px-2 py-1 border border-gray-300 rounded">{{ $order->shipping_tracking_number }}</span>
                            @else
                                <span class="text-gray-400 italic">Belum Diinput</span>
                            @endif
                        </div>
                        <div class="flex justify-between pt-2">
                            <span class="text-gray-900 font-bold">Total Pembayaran</span>
                            <span class="font-bold text-brand-orange text-lg">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Action Card --}}
                @if($order->status === 'pending_payment')
                    <div class="bg-yellow-50/50 rounded-2xl shadow-sm border border-yellow-200 p-6 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-yellow-400"></div>
                        <h3 class="font-bold text-yellow-800 mb-2 flex items-center gap-2">
                            <x-icon name="clock" class="w-5 h-5" />
                            Menunggu Pembayaran
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Selesaikan pembayaran Anda agar pesanan bisa segera diproses oleh penjual.</p>

                        @if($order->payment_token)
                            {{-- Midtrans payment --}}
                            <a href="{{ route('buyer.orders.show', $order->id) }}" 
                                id="pay-again-btn"
                                class="w-full bg-brand-blue hover:bg-blue-600 text-white font-bold py-3 text-sm rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 mb-3">
                                <x-icon name="credit-card" class="w-5 h-5" /> Bayar via Midtrans
                            </a>
                        @endif

                        {{-- Upload / Show Payment Proof --}}
                        @if($order->payment_proof)
                            <div class="bg-white rounded-xl border border-green-200 p-4 mb-3">
                                <p class="text-xs font-semibold text-green-700 mb-2 flex items-center gap-1">
                                    <x-icon name="check-circle" class="w-4 h-4" />
                                    Bukti transfer sudah diunggah
                                </p>
                                <img src="{{ Storage::url($order->payment_proof) }}" alt="Bukti Transfer" class="w-full rounded-lg border border-gray-200 max-h-48 object-contain bg-gray-50">
                                <p class="text-[10px] text-gray-400 mt-2">Menunggu verifikasi dari penjual/admin.</p>
                            </div>
                        @endif

                        <form action="{{ route('buyer.orders.upload-proof', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    {{ $order->payment_proof ? 'Upload Ulang Bukti Transfer' : 'Upload Bukti Transfer' }}
                                </label>
                                <input type="file" name="payment_proof" accept="image/*" required
                                    class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-navy file:text-white hover:file:bg-brand-navydark cursor-pointer">
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 text-xs rounded-xl transition-all flex items-center justify-center gap-2">
                                <x-icon name="arrow-up-tray" class="w-4 h-4" />
                                Kirim Bukti
                            </button>
                        </form>
                    </div>
                @elseif($order->status === 'shipped')
                    <div class="bg-brand-navylight/20 rounded-2xl shadow-sm border border-brand-navy/30 p-6 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-brand-navy"></div>
                        <h3 class="font-bold text-brand-navy mb-2 flex items-center gap-2">
                            <x-icon name="gift" class="w-5 h-5" /> Pesanan Tiba?
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Pastikan paket sudah Anda terima dalam kondisi baik. Dana asuransi (escrow) akan dicairkan ke penjual setelah Anda mengklik tombol di bawah ini.</p>
                        
                        <form action="{{ route('buyer.orders.confirm', $order->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Konfirmasi Pesanan Diterima', message: 'Apakah Anda yakin barang pesanan sudah diterima dengan baik dan sesuai? Dana akan diteruskan ke penjual dan pesanan difinalisasi.', type: 'info', confirmText: 'Ya, Selesai' })">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 text-sm rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                                <x-icon name="check-circle" class="w-5 h-5" /> Pesanan Diterima Selesai
                            </button>
                        </form>
                    </div>
                @elseif($order->status === 'completed')
                    <div class="bg-green-50 rounded-2xl p-6 border border-green-100 text-center">
                        <x-icon name="check-badge" class="w-10 h-10 text-green-500 mx-auto mb-2" />
                        <h3 class="font-bold text-green-900 mb-1">Transaksi Selesai</h3>
                        <p class="text-sm text-green-700">Terima kasih telah berbelanja! Dana telah diteruskan ke pihak toko.</p>
                        {{-- FUTURE: Add Review Button Here --}}
                    </div>
                @elseif($order->status === 'processing')
                    <div class="bg-orange-50 rounded-2xl p-6 border border-orange-100 text-center">
                        <x-icon name="clock" class="w-10 h-10 text-brand-orange mx-auto mb-2" />
                        <h3 class="font-bold text-orange-900 mb-1">Menunggu Dikirim</h3>
                        <p class="text-sm text-orange-700">Penjual sedang menyiapkan pesanan Anda.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
