<x-app-layout>
    <x-slot name="title">Pembayaran</x-slot>

    {{-- Midtrans Snap JS --}}
    @php
        $isProduction = \App\Models\SystemSetting::val('midtrans_is_production', env('MIDTRANS_IS_PRODUCTION', false)) === 'true';
        $scriptUrl = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
        $clientKey = \App\Models\SystemSetting::val('midtrans_client_key', env('MIDTRANS_CLIENT_KEY'));
    @endphp

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('buyer.orders.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Pembayaran</h1>
                <p class="text-gray-500 text-sm mt-1">Selesaikan pembayaran untuk pesanan Anda.</p>
            </div>
        </div>
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden p-8 sm:p-12 text-center relative z-10">
            <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <x-icon name="check-circle" class="w-10 h-10 text-green-500" />
            </div>
            
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900 mb-2">Pesanan Dibuat!</h1>
            <p class="text-gray-500 text-sm mb-6">Satu langkah lagi. Silakan selesaikan pembayaran Anda via Midtrans.</p>
            
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6 mb-8 text-left inline-block w-full max-w-sm mx-auto">
                <div class="flex justify-between items-center mb-3 text-sm">
                    <span class="text-gray-500">No. Invoice</span>
                    <span class="font-bold text-gray-900">{{ $order->invoice_number }}</span>
                </div>
                <div class="flex justify-between items-center mb-3 text-sm">
                    <span class="text-gray-500">Status</span>
                    <span class="font-bold text-brand-blue uppercase">{{ $order->payment_status }}</span>
                </div>
                <div class="border-t border-gray-200 my-3 pt-3 flex justify-between items-center">
                    <span class="font-bold text-gray-900 text-sm">Total Tagihan</span>
                    <span class="font-display font-bold text-xl text-brand-navy">Rp {{ number_format($grandTotalGross, 0, ',', '.') }}</span>
                </div>
            </div>

            <button id="pay-button" class="w-full sm:w-auto bg-brand-blue hover:bg-blue-600 text-white font-bold py-4 px-10 rounded-xl text-base shadow-lg shadow-orange-500/30 transition-all flex items-center justify-center gap-2 mx-auto">
                <x-icon name="credit-card" class="w-5 h-5" />
                Bayar Sekarang
            </button>
            
            <a href="{{ route('buyer.orders.index') }}" class="block mt-6 text-sm text-gray-400 hover:text-brand-navy font-medium transition-colors">
                Bayar Nanti (Lihat Pesanan Saya)
            </a>
        </div>
    </div>

    @push('scripts')
    <script type="text/javascript" src="{{ $scriptUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function () {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    window.location.href = "{{ route('buyer.orders.index') }}?payment=success";
                },
                onPending: function(result) {
                    window.location.href = "{{ route('buyer.orders.index') }}?payment=pending";
                },
                onError: function(result) {
                    window.location.href = "{{ route('buyer.orders.index') }}?payment=error";
                },
                onClose: function() {
                    alert('Anda menutup popup sebelum menyelesaikan pembayaran.');
                }
            });
        };
    </script>
    @endpush
</x-app-layout>
