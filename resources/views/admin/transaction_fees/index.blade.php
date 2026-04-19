<x-app-layout>
    <x-slot name="title">Biaya Transaksi Pembeli</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-bold mb-4">Biaya Transaksi Pembeli</h1>
        <p class="text-gray-500 mb-6 text-sm">Biaya ini akan ditambahkan ke Grand Total pesanan pembeli secara langsung saat Checkout.</p>

        @if(session('success')) <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div> @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="col-span-1">
                <form action="{{ route('admin.transaction_fees.store') }}" method="POST" class="bg-white p-6 rounded shadow">
                    @csrf
                    <h3 class="font-bold mb-4">Tambah Biaya Baru</h3>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Nama Biaya</label>
                        <input type="text" name="name" required placeholder="Mis. Biaya Jasa Aplikasi" class="border w-full p-2 rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Nominal (Rp)</label>
                        <input type="number" name="amount" required placeholder="Mis. 1000" class="border w-full p-2 rounded">
                    </div>
                    <button type="submit" class="bg-brand-navy w-full text-white py-2 rounded">Simpan Biaya</button>
                </form>
            </div>

            <div class="col-span-1 md:col-span-2">
                <div class="bg-white p-6 rounded shadow overflow-x-auto">
                    <table class="min-w-full text-left text-sm divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="py-2">Nama Biaya</th>
                                <th class="py-2">Nominal</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($fees as $fee)
                            <tr>
                                <td class="py-3 font-semibold">{{ $fee->name }}</td>
                                <td class="py-3 text-brand-blue font-bold">Rp {{ number_format($fee->amount, 0, ',', '.') }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded text-xs text-white {{ $fee->is_active ? 'bg-green-500' : 'bg-gray-400' }}">
                                        {{ $fee->is_active ? 'Aktif' : 'NonAktif' }}
                                    </span>
                                </td>
                                <td class="py-3 flex gap-2">
                                    <form action="{{ route('admin.transaction_fees.toggle', $fee->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs border px-2 py-1 rounded hover:bg-gray-50">Toggle Status</button>
                                    </form>
                                    <form action="{{ route('admin.transaction_fees.destroy', $fee->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Hapus Biaya', message: 'Hapus permanen konfigurasi biaya transaksi ini?', type: 'danger', confirmText: 'Ya, Hapus' })">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs border border-red-500 text-red-500 px-2 py-1 rounded hover:bg-red-50">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-gray-500">Belum ada pengaturan biaya tambahan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
