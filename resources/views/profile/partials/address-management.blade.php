<section x-data="{ 
    editAddress: null,
    showModal: false,
    openAddModal() {
        this.editAddress = null;
        this.showModal = true;
        // Dispatch to modal component to init map if needed
        $dispatch('open-address-modal', { isEdit: false, address: null });
    },
    openEditModal(addr) {
        this.editAddress = addr;
        this.showModal = true;
        $dispatch('open-address-modal', { isEdit: true, address: addr });
    }
}">
    <header class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-1 flex items-center gap-2">
                <x-icon name="map-pin" class="w-5 h-5 text-brand-blue" />
                Daftar Alamat
            </h2>
            <p class="text-sm text-gray-600">
                Kelola alamat pengiriman atau alamat penjemputan toko Anda.
            </p>
        </div>
        <button @click="openAddModal()" type="button" class="bg-white border-2 border-brand-navy text-brand-navy hover:bg-brand-navy hover:text-white font-bold py-2.5 px-5 rounded-xl text-sm flex items-center gap-2 transition-colors">
            <x-icon name="plus" class="w-4 h-4" />
            Tambah Alamat
        </button>
    </header>

    @if(session('success'))
        <div class="mb-5 bg-green-50 text-green-700 p-3 rounded-lg border border-green-200 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(Auth::user()->addresses->count() === 0)
        <div class="bg-gray-50 border border-dashed border-gray-300 rounded-2xl p-8 text-center">
            <x-icon name="map" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
            <p class="text-gray-500 font-medium">Anda belum menyimpan alamat apapun.</p>
            <p class="text-xs text-gray-400 mt-1">Tambahkan alamat sekarang untuk mempercepat proses checkout.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach(Auth::user()->addresses as $addr)
                <div class="border rounded-2xl relative transition-colors duration-200 shadow-sm overflow-hidden {{ $addr->is_primary ? 'border-brand-blue ring-1 ring-brand-blue/20 bg-blue-50/20' : 'border-gray-200 hover:border-gray-300' }}">
                    @if($addr->is_primary)
                        <div class="absolute top-0 right-0 bg-brand-blue text-white text-[10px] uppercase font-bold tracking-wider px-3 py-1 rounded-bl-lg">
                            Utama
                        </div>
                    @endif
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-bold text-gray-900 border-r border-gray-300 pr-2 mr-1">{{ $addr->label }}</span>
                            <span class="text-sm font-semibold text-gray-700">{{ $addr->recipient_name }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-700 mb-1">{{ $addr->phone }}</p>
                        <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed h-10 mb-3">{{ $addr->full_address }}</p>
                        <p class="text-xs text-gray-500 bg-gray-100 rounded-md px-2 py-1 inline-block mb-4">
                            {{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}
                        </p>

                        <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                            <button @click="openEditModal({{ json_encode($addr) }})" type="button" class="text-sm font-semibold text-brand-blue hover:text-blue-700">Ubah</button>
                            
                            @if(!$addr->is_primary)
                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                <form action="{{ route('profile.addresses.set-primary', $addr) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-sm font-semibold text-gray-600 hover:text-brand-navy">Jadikan Utama</button>
                                </form>
                            @endif

                            <span class="w-1 h-1 rounded-full bg-gray-300 ml-auto"></span>
                            <form action="{{ route('profile.addresses.destroy', $addr) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Alamat',
                                                text: 'Alamat {{ $addr->label }} akan dihapus secara permanen.',
                                                confirmLabel: 'Hapus',
                                                cancelLabel: 'Batal',
                                                confirmClass: 'bg-red-600 hover:bg-red-700 text-white',
                                                action: () => $el.submit()
                                            })">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-700">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Include Modal Component -->
    @include('profile.partials.address-modal')
</section>
