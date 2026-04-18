<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 p-6 bg-white shadow rounded-lg">
            <h2 class="text-2xl font-bold mb-4">Sistem Pencairan Dana (IRIS) - Admin</h2>
            @if(session('success')) <div class="text-green-500 mb-4">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="text-red-500 mb-4">{{ session('error') }}</div> @endif
            
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-2 bg-gray-50 text-left">Toko / Rekening</th>
                        <th class="px-4 py-2 bg-gray-50 text-left">Pencairan Koor (Net)</th>
                        <th class="px-4 py-2 bg-gray-50 text-left">Fee Admin</th>
                        <th class="px-4 py-2 bg-gray-50 text-left">Status</th>
                        <th class="px-4 py-2 bg-gray-50 text-left">Aksi Payout API</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($payouts as $p)
                    <tr>
                        <td class="px-4 py-3"><strong>{{ $p->store->name }}</strong><br>{{ $p->store->bank_name }} - {{ $p->store->bank_account_no }}</td>
                        <td class="px-4 py-3 text-green-600 font-bold">Rp {{ number_format($p->net_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-500">Rp {{ number_format($p->fee, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $p->status }}</td>
                        <td class="px-4 py-3">
                            @if($p->status == 'pending')
                            <form action="{{ route('admin.payouts.approve', $p->id) }}" method="POST" class="inline">
                                @csrf <button type="submit" class="bg-brand-orange text-white px-3 py-1 rounded text-xs" onclick="return confirm('Cairkan dana ke seller melalui IRIS?')">Approve & Transfer</button>
                            </form>
                            @else
                                <span class="text-xs text-green-500">Selesai ({{ $p->iris_reference_no }})</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
