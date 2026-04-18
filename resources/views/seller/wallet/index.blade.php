<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-brand-navy shadow rounded-lg text-white">
                <h2 class="text-lg font-bold mb-2">My Seller Wallet ({{ $store->name }})</h2>
                <div class="text-3xl font-bold bg-white text-brand-navy w-fit px-4 py-2 rounded">
                    Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                </div>
            </div>

            <div class="p-6 bg-white shadow rounded-lg">
                <h3 class="font-bold mb-4">Penarikan Dana</h3>
                @if(session('success')) <div class="text-green-500 mb-4">{{ session('success') }}</div> @endif
                @if(session('error')) <div class="text-red-500 mb-4">{{ session('error') }}</div> @endif

                <form method="POST" action="{{ route('seller.wallet.payout') }}">
                    @csrf
                    <label class="block text-sm mb-1">Jumlah Tarik (Min: 10.000)</label>
                    <input type="number" name="amount" required class="border p-2 rounded mb-2">
                    <button type="submit" class="bg-brand-blue text-white px-4 py-2 rounded ml-2">Request Payout</button>
                    <p class="text-xs text-gray-500 mt-2">Pencairan akan dikenakan potongan Admin Fee yang akan dikalkulasi oleh Admin saat Approval.</p>
                </form>
            </div>

            <div class="p-6 bg-white shadow rounded-lg">
                <h3 class="font-bold mb-4">Riwayat Transaksi</h3>
                <table class="min-w-full text-sm">
                    @foreach($transactions as $t)
                    <tr class="border-b">
                        <td class="py-2">{{ $t->created_at->format('d M Y') }}</td>
                        <td class="py-2">{{ $t->description }}</td>
                        <td class="py-2 font-bold {{ $t->type == 'credit' ? 'text-green-500' : 'text-red-500' }}">
                            {{ $t->type == 'credit' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
