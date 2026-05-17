<x-app-layout>
    <x-slot name="title">Manajemen Kurir Toko</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('seller.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Pengaturan Kurir Toko</h1>
                <p class="text-gray-500 text-sm mt-1">Aktifkan ekspedisi & kurir yang ingin Anda tawarkan. Jika tidak ada yang aktif, pembeli tidak bisa checkout dari toko Anda.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-2" x-data="{ show: true }" x-show="show" x-transition>
                <x-icon name="check-circle" class="w-5 h-5 shrink-0" /> {{ session('success') }}
                <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600"><x-icon name="x-mark" class="w-4 h-4" /></button>
            </div>
        @endif

        @if(!\App\Models\StoreCourier::where('store_id', Auth::user()->store->id)->where('is_active', true)->exists() && empty($enabledExpeditions))
            <div class="bg-orange-50 border border-orange-200 text-orange-800 p-4 rounded-xl mb-6 flex items-start gap-3">
                <x-icon name="exclamation-triangle" class="w-5 h-5 shrink-0 mt-0.5 text-orange-500" />
                <div class="text-sm">
                    <p class="font-bold">Belum ada metode pengiriman aktif</p>
                    <p class="mt-0.5">Pembeli akan melihat "tidak ada pengiriman tersedia" saat checkout. Aktifkan minimal satu ekspedisi reguler atau tambahkan kurir internal/instan di bawah.</p>
                </div>
            </div>
        @endif

        {{-- Ekspedisi Reguler --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <x-icon name="truck" class="w-5 h-5 text-brand-navy" />
                <div>
                    <h3 class="font-bold text-gray-900">Ekspedisi Reguler</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Ongkir dihitung otomatis berdasarkan berat & jarak. Aktifkan yang melayani area toko Anda.</p>
                </div>
            </div>
            <form action="{{ route('seller.couriers.expeditions') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($expeditions as $code => $label)
                        @php $isOn = in_array($code, $enabledExpeditions); @endphp
                        <label class="flex items-center justify-between gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all {{ $isOn ? 'border-brand-navy bg-brand-navylight/10' : 'border-gray-200 hover:border-gray-300' }}">
                            <span class="flex items-center gap-3 min-w-0">
                                <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $isOn ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                    <x-icon name="truck" class="w-5 h-5" />
                                </span>
                                <span class="min-w-0">
                                    <span class="font-bold text-gray-900 text-sm block truncate">{{ $label }}</span>
                                    <span class="text-[10px] text-gray-400">Kode: {{ strtoupper($code) }}</span>
                                </span>
                            </span>
                            <input type="checkbox" name="expeditions[]" value="{{ $code }}" {{ $isOn ? 'checked' : '' }}
                                class="rounded border-gray-300 text-brand-navy focus:ring-brand-navy w-5 h-5 shrink-0">
                        </label>
                    @endforeach
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white font-bold py-2.5 px-6 rounded-xl text-sm transition-colors shadow-sm flex items-center gap-2">
                        <x-icon name="check-circle" class="w-4 h-4" /> Simpan Ekspedisi
                    </button>
                </div>
            </form>
        </div>

        {{-- Kurir Internal & Instan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ name: '', price: '', estimation: '' }">
            {{-- Form Tambah Kurir --}}
            <div class="col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-6">
                    <h3 class="font-bold text-base text-gray-900 mb-1 flex items-center gap-2">
                        <x-icon name="plus-circle" class="w-5 h-5 text-brand-navy" />
                        Tambah Kurir Internal / Instan
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">Tarif flat yang Anda atur sendiri (Kurir Toko, GoSend, GrabExpress, dsb).</p>

                    <div class="flex flex-wrap gap-2 mb-4">
                        <button type="button" @click="name='Kurir Toko'; estimation='1-3 Jam'" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-600 hover:border-brand-navy hover:text-brand-navy transition-colors">+ Kurir Toko</button>
                        <button type="button" @click="name='GoSend'; estimation='1-2 Jam'" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-600 hover:border-green-600 hover:text-green-600 transition-colors">+ GoSend</button>
                        <button type="button" @click="name='GrabExpress'; estimation='1-2 Jam'" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-600 hover:border-green-600 hover:text-green-600 transition-colors">+ GrabExpress</button>
                    </div>

                    <form action="{{ route('seller.couriers.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Layanan <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="name" required placeholder="Mis. GoSend Instant" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tarif Flat (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" x-model="price" required placeholder="Mis. 15000" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Estimasi Tiba <span class="text-xs text-gray-400 font-normal">— Opsional</span></label>
                            <input type="text" name="estimation" x-model="estimation" placeholder="Mis. 1-2 Jam" class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                        </div>
                        <button type="submit" class="w-full bg-brand-navy hover:bg-brand-navydark text-white font-bold py-2.5 rounded-xl text-sm transition-colors shadow-sm flex items-center justify-center gap-2">
                            <x-icon name="plus" class="w-4 h-4" /> Simpan Layanan
                        </button>
                    </form>
                    <p class="text-[11px] text-gray-400 mt-3 leading-relaxed">
                        <x-icon name="information-circle" class="w-3.5 h-3.5 inline -mt-0.5" />
                        Untuk GoSend/GrabExpress, panggil kurir lewat aplikasi Gojek/Grab Anda setelah pesanan masuk; tarif yang ditampilkan ke pembeli adalah tarif flat di atas.
                    </p>
                </div>
            </div>

            {{-- Daftar Kurir --}}
            <div class="col-span-1 md:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <x-icon name="truck" class="w-5 h-5 text-brand-navy" />
                        <h3 class="font-bold text-gray-900">Daftar Kurir Internal / Instan</h3>
                    </div>
                    @forelse($couriers as $courier)
                        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50/50 transition-colors {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $courier->is_active ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                    <x-icon name="truck" class="w-5 h-5" />
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-900 text-sm truncate">{{ $courier->name }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="font-bold text-brand-blue text-sm">Rp {{ number_format($courier->price, 0, ',', '.') }}</span>
                                        @if($courier->estimation)
                                            <span class="text-[10px] text-gray-400">• Est: {{ $courier->estimation }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $courier->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                                    {{ $courier->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                <form action="{{ route('seller.couriers.toggle', $courier->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-brand-navy hover:text-brand-navy text-gray-400 rounded-lg transition-colors" title="{{ $courier->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <x-icon name="{{ $courier->is_active ? 'pause-circle' : 'play-circle' }}" class="w-4 h-4" />
                                    </button>
                                </form>
                                <form action="{{ route('seller.couriers.destroy', $courier->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Hapus Kurir', message: 'Hapus opsi kurir pengiriman ini dari toko Anda?', type: 'danger', confirmText: 'Ya, Hapus' })">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-red-500 hover:text-red-500 text-gray-400 rounded-lg transition-colors" title="Hapus">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <x-icon name="truck" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                            <p class="text-sm text-gray-500">Belum ada kurir internal / instan untuk toko Anda.</p>
                            <p class="text-xs text-gray-400 mt-1">Gunakan form di samping (atau preset GoSend/GrabExpress) untuk menambahkan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
