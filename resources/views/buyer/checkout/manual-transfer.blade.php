<x-app-layout>
    <x-slot name="title">Transfer Manual</x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('buyer.orders.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Pembayaran Transfer Manual</h1>
                <p class="text-gray-500 text-sm mt-1">Selesaikan pembayaran dengan mentransfer ke rekening di bawah ini.</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden p-8 sm:p-12 text-center relative z-10">
            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <x-icon name="banknotes" class="w-10 h-10 text-brand-blue" />
            </div>
            
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900 mb-2">Pesanan Dibuat!</h1>
            <p class="text-gray-500 text-sm mb-6">Silakan transfer sesuai total tagihan, lalu upload bukti transfer.</p>
            
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6 mb-6 text-left inline-block w-full max-w-sm mx-auto">
                <div class="flex justify-between items-center mb-3 text-sm">
                    <span class="text-gray-500">No. Invoice</span>
                    <span class="font-bold text-gray-900">{{ $order->invoice_number }}</span>
                </div>
                <div class="flex justify-between items-center mb-3 text-sm">
                    <span class="text-gray-500">Status</span>
                    <span class="font-bold text-yellow-600 uppercase">Menunggu Pembayaran</span>
                </div>
                <div class="border-t border-gray-200 my-3 pt-3 flex justify-between items-center">
                    <span class="font-bold text-gray-900 text-sm">Total Tagihan</span>
                    <span class="font-display font-bold text-xl text-brand-navy">Rp {{ number_format($grandTotalGross, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Bank Transfer Info --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 mb-6 text-left w-full max-w-sm mx-auto">
                <h3 class="font-bold text-sm text-brand-navy mb-3 flex items-center gap-2">
                    <x-icon name="credit-card" class="w-5 h-5" />
                    Informasi Rekening Transfer
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bank</span>
                        <span class="font-bold text-gray-900">BCA</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">No. Rekening</span>
                        <span class="font-mono font-bold text-gray-900">1234567890</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Atas Nama</span>
                        <span class="font-bold text-gray-900">PT Infrakarsa Sinergi Digital</span>
                    </div>
                </div>
                <p class="text-[10px] text-blue-600 mt-3">* Transfer sesuai nominal tagihan agar verifikasi lebih cepat.</p>
            </div>

            {{-- Upload Bukti Transfer --}}
            <div class="bg-white border-2 border-dashed border-gray-300 rounded-2xl p-6 mb-6 w-full max-w-sm mx-auto">
                <form action="{{ route('buyer.orders.upload-proof', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Bukti Transfer</label>
                        <input type="file" name="payment_proof" accept="image/*" required
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-navy file:text-white hover:file:bg-brand-navydark cursor-pointer">
                        <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, WebP. Maks 2MB.</p>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl text-sm flex items-center justify-center gap-2 transition-all shadow-sm">
                        <x-icon name="arrow-up-tray" class="w-5 h-5" />
                        Kirim Bukti Transfer
                    </button>
                </form>
            </div>
            
            <a href="{{ route('buyer.orders.show', $order->id) }}" class="block text-sm text-gray-400 hover:text-brand-navy font-medium transition-colors">
                Upload Nanti (Lihat Detail Pesanan)
            </a>
        </div>
    </div>
</x-app-layout>
