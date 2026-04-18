<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 p-6 bg-white shadow rounded-lg">
            <h2 class="text-xl font-bold mb-4">Detail Pesanan {{ $order->invoice_number }}</h2>
            @if(session('success')) <div class="text-green-500 mb-4">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="text-red-500 mb-4">{{ session('error') }}</div> @endif
            
            <div class="mb-4 text-sm bg-gray-50 p-4 rounded">
                Status Saat Ini: <strong>{{ $order->status_label }}</strong><br>
                Kurir: {{ $order->shipping_address['courier'] ?? 'N/A' }}<br>
                Resi: {{ $order->shipping_tracking_number ?? 'Belum diisi' }}
            </div>

            @if($order->status === 'processing')
                <form action="{{ route('seller.orders.status', $order->id) }}" method="POST" class="mt-4 p-4 border rounded">
                    @csrf
                    <input type="hidden" name="status" value="shipped">
                    <label class="block mb-2 font-bold text-sm">Nomor Resi Pengiriman</label>
                    <input type="text" name="shipping_tracking_number" required class="border p-2 rounded w-full mb-3" placeholder="Contoh: JP239293123">
                    <button type="submit" class="bg-brand-navy text-white px-4 py-2 rounded">Kirim Pesanan</button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
