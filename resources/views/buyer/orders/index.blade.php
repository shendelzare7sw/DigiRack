<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Pesanan Saya</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 p-6">
                @if(session('success')) <div class="text-green-500 mb-4">{{ session('success') }}</div> @endif
                @if(session('error')) <div class="text-red-500 mb-4">{{ session('error') }}</div> @endif
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left shadow-sm bg-gray-50 text-xs">Invoice</th>
                            <th class="px-6 py-3 text-left shadow-sm bg-gray-50 text-xs">Status</th>
                            <th class="px-6 py-3 text-left shadow-sm bg-gray-50 text-xs">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @foreach($orders as $order)
                        <tr>
                            <td class="px-6 py-4">{{ $order->invoice_number }}</td>
                            <td class="px-6 py-4 font-bold text-{{ $order->status_color }}-600">{{ $order->status_label }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('buyer.orders.show', $order->id) }}" class="text-blue-600 hover:underline">Detail</a>
                                @if($order->status == 'shipped')
                                <form action="{{ route('buyer.orders.confirm', $order->id) }}" method="POST" class="inline ml-2">
                                    @csrf
                                    <button type="submit" class="text-green-600 font-bold hover:underline">Pesanan Diterima (Selesai)</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
