<x-app-layout>
    <x-slot name="title">Manajemen Kurir Toko</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('seller.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Pengaturan Kurir Toko</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola kurir internal toko Anda dengan tarif rata (Flat Rate).</p>
            </div>
        </div>

        @if(session('success')) <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div> @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="col-span-1">
                <form action="{{ route('seller.couriers.store') }}" method="POST" class="bg-white p-6 rounded shadow">
                    @csrf
                    <h3 class="font-bold mb-4 text-brand-navy">Tambah Kurir Internal</h3>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Nama Layanan</label>
                        <input type="text" name="name" required placeholder="Mis. Kurir Motor Toko" class="border w-full p-2 rounded focus:ring-brand-navy">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Tarif Flat (Rp)</label>
                        <input type="number" name="price" required placeholder="Mis. 15000" class="border w-full p-2 rounded focus:ring-brand-navy">
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-semibold mb-1">Estimasi Tiba (Opsional)</label>
                        <input type="text" name="estimation" placeholder="Mis. 1-2 Jam" class="border w-full p-2 rounded focus:ring-brand-navy">
                    </div>
                    <button type="submit" class="bg-brand-navy w-full text-white py-2 rounded font-bold hover:bg-brand-navydark">Simpan Layanan</button>
                </form>
            </div>

            <div class="col-span-1 md:col-span-2">
                <div class="bg-white p-6 rounded shadow overflow-x-auto">
                    <table class="min-w-full text-left text-sm divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="py-2">Layanan</th>
                                <th class="py-2">Tarif</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($couriers as $courier)
                            <tr>
                                <td class="py-3">
                                    <div class="font-semibold">{{ $courier->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $courier->estimation ? 'Est: ' . $courier->estimation : '' }}</div>
                                </td>
                                <td class="py-3 text-brand-blue font-bold">Rp {{ number_format($courier->price, 0, ',', '.') }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded text-xs text-white {{ $courier->is_active ? 'bg-green-500' : 'bg-gray-400' }}">
                                        {{ $courier->is_active ? 'Aktif' : 'NonAktif' }}
                                    </span>
                                </td>
                                <td class="py-3 flex gap-2">
                                    <form action="{{ route('seller.couriers.toggle', $courier->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs border px-2 py-1 rounded hover:bg-gray-50">Toggle Status</button>
                                    </form>
                                    <form action="{{ route('seller.couriers.destroy', $courier->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Hapus Kurir', message: 'Hapus opsi kurir pengiriman ini dari toko Anda?', type: 'danger', confirmText: 'Ya, Hapus' })">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs border border-red-500 text-red-500 px-2 py-1 rounded hover:bg-red-50">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-gray-500">Belum ada kurir internal untuk toko Anda.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
