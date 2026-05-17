<x-app-layout>
    <x-slot name="title">Kelola Pesanan</x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('seller.orders.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div class="flex-1 min-w-0">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h1 class="font-display font-bold text-lg sm:text-2xl text-gray-900">Pesanan <span class="text-sm sm:text-lg text-gray-500 font-semibold break-all">#{{ $order->invoice_number }}</span></h1>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 border border-{{ $order->status_color }}-200 shrink-0 w-fit">
                        {{ $order->status_label }}
                    </span>
                </div>
            </div>
        </div>

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Kiri: Detail & Item --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Rincian Invoice --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
                    <h3 class="text-base font-bold text-gray-900 border-b pb-3 mb-4 flex items-center gap-2">
                        <x-icon name="document-text" class="w-5 h-5 text-brand-navy" /> Rincian Invoice
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-1">
                            <span class="text-gray-500">Nomor Invoice</span>
                            <span class="font-bold text-gray-900 break-all">{{ $order->invoice_number }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-1">
                            <span class="text-gray-500">Tanggal Pesanan</span>
                            <span class="font-semibold text-gray-800">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        <div class="border-t border-gray-100 pt-3 mt-3">
                            @php $ship = $order->shipping_address ?? []; @endphp
                            <p class="text-gray-500 mb-1">Data Pengiriman</p>
                            <p class="font-bold text-gray-900 mb-1">{{ $ship['name'] ?? $order->buyer->name ?? '-' }} <span class="font-normal text-gray-600">({{ $ship['phone'] ?? '-' }})</span></p>
                            <p class="text-gray-700 leading-relaxed">{{ $ship['full_address'] ?? 'Alamat pengiriman tidak tersedia.' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Item Produk --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
                    <h3 class="text-base font-bold text-gray-900 border-b pb-3 mb-4 flex items-center gap-2">
                        <x-icon name="shopping-bag" class="w-5 h-5 text-brand-orange" /> Produk Dibeli
                    </h3>
                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                        <div class="py-3 flex gap-3 items-center">
                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-14 h-14 rounded-lg object-cover border border-gray-100 shrink-0">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 text-sm line-clamp-1">{{ $item->product->name }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">{{ number_format($item->quantity) }} x Rp {{ number_format($item->price_snapshot, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-bold text-brand-orange text-sm">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Kanan: Info Kurir & Aksi --}}
            <div class="space-y-6">
                {{-- Info Kurir --}}
                @php
                    $rawCourier = $order->shipping_address['courier'] ?? '-';
                    $isToko = str_starts_with(strtolower($rawCourier), 'toko_');
                    $kurirName = $isToko ? 'Kurir Toko' : strtoupper($rawCourier);
                @endphp
                <div class="bg-brand-navylight/30 rounded-2xl p-5 border border-brand-navy/10">
                    <h3 class="text-base font-bold text-brand-navy mb-4 border-b border-brand-navy/20 pb-2">Informasi Kurir</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ekspedisi</span>
                            <span class="font-bold text-gray-900">{{ $kurirName }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Pesanan</span>
                            <span class="font-bold text-brand-orange text-lg">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex flex-col mt-3 pt-3 border-t border-brand-navy/20">
                            <span class="text-gray-600 mb-1 text-xs">Nomor Resi</span>
                            @if($order->shipping_tracking_number)
                                <span class="font-mono text-sm font-bold text-gray-900 bg-white px-3 py-2 rounded-lg border border-gray-200 break-all">{{ $order->shipping_tracking_number }}</span>
                            @else
                                <span class="text-gray-400 italic text-sm">Belum diinput</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Card --}}
                @if($order->status === 'pending_payment')
                    <div class="bg-yellow-50/50 rounded-2xl shadow-sm border border-yellow-200 p-5 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-yellow-400"></div>
                        <h3 class="font-bold text-yellow-800 mb-2 flex items-center gap-2 text-sm">
                            <x-icon name="clock" class="w-5 h-5" />
                            Menunggu Pembayaran Buyer
                        </h3>
                        <p class="text-xs text-gray-600">Pesanan ini sedang menunggu pembayaran dari pembeli via Midtrans.</p>
                    </div>
                @elseif($order->status === 'cancellation_requested')
                    <div class="bg-orange-50 rounded-2xl shadow-sm border border-orange-200 p-5 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-orange-500"></div>
                        <h3 class="font-bold text-orange-900 mb-2 flex items-center gap-2 text-sm">
                            <x-icon name="exclamation-circle" class="w-5 h-5 text-orange-500" />
                            Permintaan Pembatalan
                        </h3>
                        <p class="text-xs text-orange-700 mb-4">Pembeli meminta pesanan ini dibatalkan. Anda dapat menyetujui pembatalan atau menolak dan melanjutkan proses pengiriman.</p>

                        <div class="bg-white border border-orange-100 rounded-xl p-3 mb-4">
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">Alasan Pembeli</p>
                            <p class="text-sm text-gray-800">{{ $order->cancellation_reason ?: 'Tidak ada alasan tambahan.' }}</p>
                            @if($order->cancellation_requested_at)
                                <p class="text-[10px] text-gray-400 mt-2">Diajukan {{ $order->cancellation_requested_at->diffForHumans() }}</p>
                            @endif
                        </div>

                        <form action="{{ route('seller.orders.cancellation', $order->id) }}" method="POST" class="space-y-3" x-data>
                            @csrf
                            <input type="hidden" name="decision" x-ref="decision" value="">
                            <textarea name="cancellation_response" rows="3" maxlength="500" class="w-full border-orange-200 focus:border-brand-orange focus:ring-brand-orange rounded-xl text-sm" placeholder="Catatan untuk pembeli (opsional)"></textarea>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <button type="button" @click="$refs.decision.value = 'reject'; $dispatch('open-confirm-modal', { form: $el.form, title: 'Tolak Pembatalan', message: 'Pesanan akan kembali ke status Diproses dan dapat Anda kirim. Lanjutkan?', type: 'info', confirmText: 'Tolak & Proses' })" class="w-full bg-brand-navy hover:bg-brand-navydark text-white font-bold py-3 text-sm rounded-xl transition-all flex items-center justify-center gap-2">
                                    <x-icon name="truck" class="w-5 h-5" /> Tolak & Proses
                                </button>
                                <button type="button" @click="$refs.decision.value = 'approve'; $dispatch('open-confirm-modal', { form: $el.form, title: 'Setujui Pembatalan', message: 'Pesanan akan dibatalkan. Jika stok sudah terpotong, stok produk akan dikembalikan. Lanjutkan?', type: 'danger', confirmText: 'Setujui Batal' })" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 text-sm rounded-xl transition-all flex items-center justify-center gap-2">
                                    <x-icon name="x-circle" class="w-5 h-5" /> Setujui Batal
                                </button>
                            </div>
                        </form>
                    </div>
                @elseif($order->status === 'processing')
                    <div class="bg-white rounded-2xl shadow-sm border border-brand-orange/30 p-5 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-brand-orange"></div>
                        <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2 text-sm">
                            <x-icon name="exclamation-circle" class="w-5 h-5 text-brand-orange" />
                            Tindakan Diperlukan
                        </h3>
                        
                        @if($isToko)
                            <p class="text-xs text-gray-600 mb-4">Pembeli memilih <strong>Kurir Toko</strong>. Segera kirimkan paket. Klik tombol di bawah jika barang sudah dikirim.</p>
                            <form action="{{ route('seller.orders.status', $order->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Konfirmasi Kirim', message: 'Apakah paket sudah dikirim menggunakan Kurir Toko? Status pesanan akan berubah menjadi Dikirim.', type: 'info', confirmText: 'Ya, Sudah Dikirim' })">
                                @csrf
                                <input type="hidden" name="status" value="shipped">
                                <input type="hidden" name="shipping_tracking_number" value="KURIR-TOKO">
                                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 focus:bg-orange-700 text-white font-bold py-3 text-sm rounded-xl transition-all shadow-lg shadow-orange-500/25 flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                                    <x-icon name="truck" class="w-5 h-5" /> Tandai Sudah Dikirim
                                </button>
                            </form>
                        @else
                            <p class="text-xs text-gray-600 mb-4">Input nomor resi pengiriman setelah paket diserahkan ke ekspedisi.</p>
                            <form action="{{ route('seller.orders.status', $order->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Konfirmasi Kirim', message: 'Apakah paket sudah diserahkan ke kurir dan nomor resi sudah benar? Status pesanan akan berubah menjadi Dikirim.', type: 'info', confirmText: 'Ya, Kirim Sekarang' })">
                                @csrf
                                <input type="hidden" name="status" value="shipped">
                                <div class="mb-3">
                                    <input type="text" name="shipping_tracking_number" required class="w-full border-gray-300 focus:border-brand-orange focus:ring-brand-orange rounded-xl text-sm py-2.5" placeholder="Masukkan Nomor Resi...">
                                </div>
                                <button type="submit" class="w-full bg-brand-navy hover:bg-brand-navydark text-white font-bold py-3 text-sm rounded-xl transition-all shadow-lg shadow-brand-navy/20 flex items-center justify-center gap-2">
                                    <x-icon name="paper-airplane" class="w-5 h-5" /> Kirim Pesanan
                                </button>
                            </form>
                        @endif
                    </div>
                @elseif($order->status === 'shipped')
                    @php
                        $autoCompleteHours = (int) \App\Models\SystemSetting::val('auto_complete_hours', 24);
                        $autoCompleteAt = $order->delivered_at && $autoCompleteHours > 0
                            ? $order->delivered_at->copy()->addHours($autoCompleteHours)
                            : null;
                    @endphp
                    <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                        <x-icon :name="$order->delivered_at ? 'check-circle' : 'truck'" class="w-10 h-10 text-blue-400 mx-auto mb-2" />
                        <h3 class="font-bold text-blue-900 mb-1 text-sm text-center">
                            {{ $order->delivered_at ? 'Paket Tercatat Sampai' : 'Pesanan Dalam Perjalanan' }}
                        </h3>
                        <p class="text-xs text-blue-700 text-center">
                            {{ $order->delivered_at
                                ? 'Menunggu pembeli konfirmasi penerimaan barang. Saldo akan cair setelah dikonfirmasi atau saat batas auto-selesai tercapai.'
                                : 'Auto-selesai belum berjalan. Tandai paket sudah sampai hanya jika kurir toko/kurir reguler sudah mengonfirmasi sampai di alamat.' }}
                        </p>
                        @if($order->delivered_at)
                            <div class="text-[11px] text-blue-800 bg-white/70 border border-blue-100 rounded-xl px-3 py-2 mt-3">
                                <p>Paket sampai: {{ $order->delivered_at->translatedFormat('d M Y, H:i') }}</p>
                                @if($autoCompleteAt)
                                    <p class="mt-1">Auto-selesai jika belum dikonfirmasi sampai {{ $autoCompleteAt->translatedFormat('d M Y, H:i') }}.</p>
                                @endif
                                @if($order->delivery_confirmation_note)
                                    <p class="mt-2 text-gray-600">{{ $order->delivery_confirmation_note }}</p>
                                @endif
                                @if($order->delivery_proof_path)
                                    <a href="{{ asset('storage/' . $order->delivery_proof_path) }}" target="_blank" class="block mt-3 overflow-hidden rounded-xl border border-blue-100 bg-white">
                                        <img src="{{ asset('storage/' . $order->delivery_proof_path) }}" alt="Bukti paket sampai untuk {{ $order->invoice_number }}" class="w-full max-h-56 object-cover">
                                    </a>
                                @endif
                            </div>
                        @else
                            <form action="{{ route('seller.orders.delivered', $order->id) }}" method="POST" enctype="multipart/form-data" class="mt-4" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Tandai Paket Sampai', message: 'Gunakan hanya jika paket sudah terkonfirmasi sampai di alamat penerima dan bukti foto sudah benar. Setelah ini timer auto-selesai pembeli akan dimulai.', type: 'info', confirmText: 'Ya, Paket Sampai' })">
                                @csrf
                                <textarea name="delivery_confirmation_note" rows="3" maxlength="500" class="w-full border-blue-200 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm" placeholder="Catatan opsional: diterima oleh siapa, bukti dari tracking, atau konfirmasi kurir..."></textarea>
                                <div class="mt-3 text-left">
                                    <label for="delivery_proof" class="block text-xs font-bold text-blue-900 mb-1">Foto bukti sampai <span class="text-red-500">*</span></label>
                                    <input id="delivery_proof" name="delivery_proof" type="file" accept="image/jpeg,image/png,image/webp" required class="block w-full text-sm text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-white file:px-3 file:py-2 file:text-sm file:font-bold file:text-brand-navy hover:file:bg-blue-50">
                                    <p class="text-[11px] text-blue-700 mt-1">Format JPG, PNG, atau WebP. Maksimal 8 MB.</p>
                                </div>
                                <button type="submit" class="mt-3 w-full bg-brand-navy hover:bg-brand-navydark focus:bg-brand-navydark text-white font-bold py-3 text-sm rounded-xl transition-all shadow-lg shadow-brand-navy/20 flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-navy focus:ring-offset-2">
                                    <x-icon name="check-circle" class="w-5 h-5" /> Tandai Paket Sudah Sampai
                                </button>
                            </form>
                            <p class="text-[11px] text-blue-700 mt-3 text-center">
                                Untuk TIKI/POS/JNE dan kurir reguler, cek tracking manual lalu tandai saat status kurir sudah delivered.
                            </p>
                        @endif
                    </div>
                @elseif($order->status === 'completed')
                    <div class="bg-green-50 rounded-2xl p-5 border border-green-100 text-center">
                        <x-icon name="check-badge" class="w-10 h-10 text-green-500 mx-auto mb-2" />
                        <h3 class="font-bold text-green-900 mb-1 text-sm">Transaksi Selesai</h3>
                        <p class="text-xs text-green-700">Dana telah dimasukkan ke Saldo Dompet Toko Anda.</p>
                    </div>
                @elseif($order->status === 'cancelled')
                    <div class="bg-red-50 rounded-2xl p-5 border border-red-100 text-center">
                        <x-icon name="x-circle" class="w-10 h-10 text-red-400 mx-auto mb-2" />
                        <h3 class="font-bold text-red-900 mb-1 text-sm">Pesanan Dibatalkan</h3>
                        <p class="text-xs text-red-700">Pesanan ini telah dibatalkan atau kadaluarsa.</p>
                        @if($order->cancellation_response)
                            <div class="mt-4 bg-white border border-red-100 rounded-xl p-3 text-left">
                                <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">Catatan Pembatalan</p>
                                <p class="text-xs text-gray-700">{{ $order->cancellation_response }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
