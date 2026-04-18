<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 p-6 bg-white shadow rounded-lg">
            <h2 class="text-2xl font-bold mb-4">Kelola Pesanan (Seller)</h2>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-2 bg-gray-50 text-left">Invoice</th>
                        <th class="px-4 py-2 bg-gray-50 text-left">Pembeli</th>
                        <th class="px-4 py-2 bg-gray-50 text-left">Status</th>
                        <th class="px-4 py-2 bg-gray-50 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <tr>
                        <td class="px-4 py-3">{{ $order->invoice_number }}</td>
                        <td class="px-4 py-3">{{ $order->buyer->name }}</td>
                        <td class="px-4 py-3 text-{{ $order->status_color }}-600 font-bold">{{ $order->status_label }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('seller.orders.show', $order->id) }}" class="text-blue-600 underline">Update Resi & Status</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
