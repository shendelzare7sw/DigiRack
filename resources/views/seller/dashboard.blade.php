<x-app-layout>
    <x-slot name="title">Dashboard Seller</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        @php
            $store = Auth::user()->store;
        @endphp

        {{-- Welcome Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900">
                    Dashboard Seller
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    @if($store)
                        Toko: <span class="font-semibold text-brand-navy">{{ $store->name }}</span>
                    @else
                        Anda belum memiliki toko.
                    @endif
                </p>
            </div>
            @if($store)
                <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-2 bg-brand-blue hover:bg-blue-600 text-white font-bold text-sm px-6 py-3 rounded-xl shadow-sm transition-colors self-start">
                    <x-icon name="plus" class="w-4 h-4" />
                    Tambah Produk Baru
                </a>
            @endif
        </div>

        @if(!$store)
            {{-- No Store State --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 sm:p-16 text-center">
                <div class="w-28 h-28 bg-brand-navylight rounded-full flex items-center justify-center mx-auto mb-6">
                    <x-icon name="building-storefront" class="w-14 h-14 text-brand-navy/50" />
                </div>
                <h2 class="font-display font-bold text-xl text-gray-700 mb-2">Belum Ada Toko</h2>
                <p class="text-gray-500 text-sm mb-8 max-w-md mx-auto">Buat toko Anda untuk mulai menjual produk infrastruktur IT di DigiRack.</p>
                <span class="inline-flex items-center gap-2 bg-gray-200 text-gray-500 font-bold text-sm px-6 py-3 rounded-xl cursor-not-allowed">
                    <x-icon name="plus" class="w-4 h-4" />
                    Buat Toko (Segera Hadir)
                </span>
            </div>
        @else
            {{-- Quick Stats --}}
            @php
                $totalProducts = \App\Models\Product::where('store_id', $store->id)->count();
                $activeProducts = \App\Models\Product::where('store_id', $store->id)->where('status', 'active')->count();
                $totalSold = $store->total_sold;
                $totalRevenue = \App\Models\Product::where('store_id', $store->id)->sum(\DB::raw('price * sold_count'));
                $lowStock = \App\Models\Product::where('store_id', $store->id)->where('stock', '<=', 5)->where('stock', '>', 0)->count();
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="w-10 h-10 bg-brand-navylight rounded-xl flex items-center justify-center text-brand-navy mb-3">
                        <x-icon name="cube" class="w-5 h-5" />
                    </div>
                    <p class="font-display font-bold text-2xl text-gray-900">{{ $totalProducts }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Total Produk</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600 mb-3">
                        <x-icon name="check-circle" class="w-5 h-5" />
                    </div>
                    <p class="font-display font-bold text-2xl text-gray-900">{{ $activeProducts }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Produk Aktif</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="w-10 h-10 bg-brand-bluelight rounded-xl flex items-center justify-center text-brand-blue mb-3">
                        <x-icon name="shopping-bag" class="w-5 h-5" />
                    </div>
                    <p class="font-display font-bold text-2xl text-gray-900">{{ $totalSold }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Total Terjual</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 mb-3">
                        <x-icon name="banknotes" class="w-5 h-5" />
                    </div>
                    <p class="font-display font-bold text-lg text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Estimasi Pendapatan</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 {{ $lowStock > 0 ? 'border-blue-200' : '' }}">
                    <div class="w-10 h-10 {{ $lowStock > 0 ? 'bg-blue-50' : 'bg-gray-50' }} rounded-xl flex items-center justify-center {{ $lowStock > 0 ? 'text-blue-500' : 'text-gray-400' }} mb-3">
                        <x-icon name="exclamation-triangle" class="w-5 h-5" />
                    </div>
                    <p class="font-display font-bold text-2xl {{ $lowStock > 0 ? 'text-blue-500' : 'text-gray-900' }}">{{ $lowStock }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Stok Menipis</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Quick Actions --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h2 class="font-bold text-lg text-gray-900 mb-5 flex items-center gap-2">
                            <x-icon name="bolt" class="w-5 h-5 text-brand-blue" />
                            Menu Seller
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <a href="{{ route('seller.products.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-brand-navy/30 hover:shadow-sm transition-all group">
                                <div class="w-12 h-12 bg-brand-navylight rounded-xl flex items-center justify-center text-brand-navy group-hover:bg-brand-navy group-hover:text-white transition-colors">
                                    <x-icon name="cube" class="w-6 h-6" />
                                </div>
                                <span class="text-xs font-semibold text-gray-700 text-center">Kelola Produk</span>
                            </a>
                            <a href="{{ route('seller.products.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-brand-blue/30 hover:shadow-sm transition-all group">
                                <div class="w-12 h-12 bg-brand-bluelight rounded-xl flex items-center justify-center text-brand-blue group-hover:bg-brand-blue group-hover:text-white transition-colors">
                                    <x-icon name="plus-circle" class="w-6 h-6" />
                                </div>
                                <span class="text-xs font-semibold text-gray-700 text-center">Tambah Produk</span>
                            </a>
                            <a href="{{ route('seller.orders.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-orange-300 hover:shadow-sm transition-all group">
                                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500 group-hover:bg-orange-500 group-hover:text-white transition-colors">
                                    <x-icon name="clipboard-document-list" class="w-6 h-6" />
                                </div>
                                <span class="text-xs font-semibold text-gray-700 text-center">Pesanan Masuk</span>
                            </a>
                            <a href="{{ route('seller.wallet.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-green-300 hover:shadow-sm transition-all group">
                                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors">
                                    <x-icon name="banknotes" class="w-6 h-6" />
                                </div>
                                <span class="text-xs font-semibold text-gray-700 text-center">Saldo / Wallet</span>
                            </a>
                            <a href="{{ route('seller.store.show') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-purple-300 hover:shadow-sm transition-all group">
                                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-500 group-hover:bg-purple-500 group-hover:text-white transition-colors">
                                    <x-icon name="building-storefront" class="w-6 h-6" />
                                </div>
                                <span class="text-xs font-semibold text-gray-700 text-center">Profil Toko</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-blue-300 hover:shadow-sm transition-all group">
                                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                                    <x-icon name="user-circle" class="w-6 h-6" />
                                </div>
                                <span class="text-xs font-semibold text-gray-700 text-center">Edit Profil</span>
                            </a>
                            <a href="{{ route('seller.couriers.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-teal-300 hover:shadow-sm transition-all group">
                                <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center text-teal-500 group-hover:bg-teal-500 group-hover:text-white transition-colors">
                                    <x-icon name="truck" class="w-6 h-6" />
                                </div>
                                <span class="text-xs font-semibold text-gray-700 text-center">Kelola Kurir</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Store Info Card --}}
                <div>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div class="flex flex-col items-center text-center">
                            <img src="{{ $store->logo_url }}" alt="{{ $store->name }}" class="w-20 h-20 rounded-full border-4 border-brand-navylight mb-4 object-cover">
                            <h3 class="font-bold text-gray-900">{{ $store->name }}</h3>
                            @if($store->is_verified)
                                <span class="mt-2 inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-full border border-green-200">
                                    <x-icon name="check-badge" class="w-3.5 h-3.5" />
                                    Toko Terverifikasi
                                </span>
                            @endif
                        </div>
                        <div class="mt-5 pt-5 border-t border-gray-100 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Rating Toko</span>
                                <span class="font-semibold text-gray-900 flex items-center gap-1">
                                    <x-icon name="star" class="w-4 h-4 text-yellow-400" />
                                    {{ number_format($store->avg_rating, 1) }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Total Terjual</span>
                                <span class="font-semibold text-gray-900">{{ $store->total_sold }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Bergabung</span>
                                <span class="font-semibold text-gray-900">{{ $store->created_at->translatedFormat('M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
