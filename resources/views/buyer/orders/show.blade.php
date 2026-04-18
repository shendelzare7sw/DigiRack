<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 p-6 bg-white shadow rounded-lg">
            <h2 class="text-xl font-bold mb-4">Detail Pesanan {{ $order->invoice_number }}</h2>
            
            <div class="mb-4 text-sm bg-gray-50 p-4 rounded">
                Toko: <strong>{{ $order->store->name }}</strong><br>
                Status Saat Ini: <strong class="text-{{ $order->status_color }}-600">{{ $order->status_label }}</strong><br>
                Kurir: {{ $order->shipping_address['courier'] ?? 'N/A' }}<br>
                Resi: <span class="font-bold border bg-white px-2 rounded">{{ $order->shipping_tracking_number ?? 'Belum ada' }}</span>
            </div>

            <table class="min-w-full text-sm">
                @foreach($order->items as $item)
                <tr class="border-b">
                    <td class="py-2">{{ $item->product->name }}</td>
                    <td class="py-2">x{{ $item->quantity }}</td>
                    <td class="py-2">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </table>

            @if($order->status == 'shipped')
            <div class="mt-6 border-t pt-4">
                <form action="{{ route('buyer.orders.confirm', $order->id) }}" method="POST">
                    @csrf
                    <p class="text-sm mb-2 text-gray-600">Pastikan barang telah tiba dengan aman sebelum menyelesaikan pesanan. Dana akan langsung diteruskan ke penjual.</p>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded font-bold">Pesanan Diterima (Selesaikan)</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
