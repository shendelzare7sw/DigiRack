<x-app-layout>
    <x-slot name="title">Dashboard Pembeli</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Welcome Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900">
                    Selamat Datang, {{ Str::words(Auth::user()->name, 2, '') }}! ðŸ‘‹
                </h1>
                <p class="text-sm text-gray-500 mt-1">Kelola belanja dan aktivitas akun Anda di sini.</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-brand-blue hover:bg-blue-600 text-white font-bold text-sm px-6 py-3 rounded-xl shadow-sm transition-colors self-start">
                <x-icon name="magnifying-glass" class="w-4 h-4" />
                Jelajahi Produk
            </a>
        </div>

        {{-- Quick Stats --}}
        @php
            $cartCount = \App\Models\Cart::where('user_id', Auth::id())->sum('quantity');
            $wishlistCount = \App\Models\Wishlist::where('user_id', Auth::id())->count();
            $orderCount = \App\Models\Order::where('buyer_id', Auth::id())->count();
            $reviewCount = \App\Models\Review::where('buyer_id', Auth::id())->count();
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {{-- Cart --}}
            <a href="{{ route('buyer.cart.index') }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-brand-blue/30 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-11 h-11 bg-brand-bluelight rounded-xl flex items-center justify-center group-hover:bg-brand-blue group-hover:text-white text-brand-blue transition-colors">
                        <x-icon name="shopping-cart" class="w-6 h-6" />
                    </div>
                    <x-icon name="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-brand-blue transition-colors" />
                </div>
                <p class="font-display font-bold text-2xl text-gray-900">{{ $cartCount }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Item di Keranjang</p>
            </a>

            {{-- Wishlist --}}
            <a href="{{ route('buyer.wishlist.index') }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-red-300 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-11 h-11 bg-red-50 rounded-xl flex items-center justify-center group-hover:bg-red-500 group-hover:text-white text-red-500 transition-colors">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                    </div>
                    <x-icon name="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-red-500 transition-colors" />
                </div>
                <p class="font-display font-bold text-2xl text-gray-900">{{ $wishlistCount }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Produk Wishlist</p>
            </a>

            {{-- Orders --}}
            <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-5 opacity-70">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-11 h-11 bg-brand-navylight rounded-xl flex items-center justify-center text-brand-navy">
                        <x-icon name="clipboard-document-list" class="w-6 h-6" />
                    </div>
                    <span class="text-[10px] bg-gray-100 text-gray-400 font-bold px-2 py-1 rounded-full">SEGERA</span>
                </div>
                <p class="font-display font-bold text-2xl text-gray-900">{{ $orderCount }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Total Pesanan</p>
            </div>

            {{-- Reviews --}}
            <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-5 opacity-70">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-11 h-11 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-500">
                        <x-icon name="star" class="w-6 h-6" />
                    </div>
                    <span class="text-[10px] bg-gray-100 text-gray-400 font-bold px-2 py-1 rounded-full">SEGERA</span>
                </div>
                <p class="font-display font-bold text-2xl text-gray-900">{{ $reviewCount }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Ulasan Saya</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Quick Actions --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-lg text-gray-900 mb-5 flex items-center gap-2">
                        <x-icon name="bolt" class="w-5 h-5 text-brand-blue" />
                        Aksi Cepat
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-brand-navy/30 hover:shadow-sm transition-all group">
                            <div class="w-12 h-12 bg-brand-navylight rounded-xl flex items-center justify-center text-brand-navy group-hover:bg-brand-navy group-hover:text-white transition-colors">
                                <x-icon name="magnifying-glass" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-semibold text-gray-700 text-center">Cari Produk</span>
                        </a>
                        <a href="{{ route('buyer.cart.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-brand-blue/30 hover:shadow-sm transition-all group">
                            <div class="w-12 h-12 bg-brand-bluelight rounded-xl flex items-center justify-center text-brand-blue group-hover:bg-brand-blue group-hover:text-white transition-colors">
                                <x-icon name="shopping-cart" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-semibold text-gray-700 text-center">Keranjang</span>
                        </a>
                        <a href="{{ route('buyer.wishlist.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-red-300 hover:shadow-sm transition-all group">
                            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-500 group-hover:bg-red-500 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-gray-700 text-center">Wishlist</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-blue-300 hover:shadow-sm transition-all group">
                            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                                <x-icon name="user-circle" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-semibold text-gray-700 text-center">Edit Profil</span>
                        </a>
                        <div class="flex flex-col items-center gap-2 p-4 rounded-xl border border-dashed border-gray-200 opacity-50">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">
                                <x-icon name="clipboard-document-list" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-semibold text-gray-400 text-center">Pesanan Saya</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 p-4 rounded-xl border border-dashed border-gray-200 opacity-50">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">
                                <x-icon name="map-pin" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-semibold text-gray-400 text-center">Alamat Saya</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Profile Card --}}
            <div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex flex-col items-center text-center">
                        <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-20 h-20 rounded-full border-4 border-brand-navylight mb-4">
                        <h3 class="font-bold text-gray-900">{{ Auth::user()->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ Auth::user()->email }}</p>
                        <span class="mt-2 inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-full border border-green-200">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            Pembeli Aktif
                        </span>
                    </div>

                    <div class="mt-5 pt-5 border-t border-gray-100 space-y-3">
                        <div class="flex items-center gap-3 text-sm">
                            <x-icon name="phone" class="w-4 h-4 text-gray-400" />
                            <span class="text-gray-600">{{ Auth::user()->phone ?? 'Belum diisi' }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <x-icon name="calendar-days" class="w-4 h-4 text-gray-400" />
                            <span class="text-gray-600">Bergabung {{ Auth::user()->created_at->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="mt-5 block w-full text-center border-2 border-brand-navy text-brand-navy hover:bg-brand-navy hover:text-white font-bold text-sm py-2.5 rounded-xl transition-colors">
                        Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
