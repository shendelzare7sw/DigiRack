<nav x-data="{ mobileMenuOpen: false, searchOpen: false, notifOpen: false }" class="bg-white sticky top-0 z-50 shadow-sm border-b border-gray-100">
    <style>
        @media (max-width: 639px) {
            .mobile-dropdown-notif {
                position: fixed !important;
                top: 4rem !important;
                left: 50% !important;
                right: auto !important;
                transform: translateX(-50%) !important;
                width: calc(100vw - 2rem) !important;
                max-width: 384px !important;
            }
        }
    </style>
    <!-- Top Bar (Desktop Only) -->
    <div class="bg-gray-100 border-b border-gray-200 hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-9 text-xs text-gray-500 font-medium">
            <div class="flex gap-5">
                <a href="{{ route('pages.download-app') }}" class="hover:text-brand-navy flex items-center gap-1.5 transition-colors">
                    <x-icon name="device-phone-mobile" class="w-4 h-4" /> Download App DigiRack
                </a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('pages.about') }}" class="hover:text-brand-navy transition-colors">Tentang DigiRack</a>
                <a href="{{ route('pages.selling') }}" class="hover:text-brand-navy transition-colors">Mulai Berjualan</a>
                <a href="{{ route('pages.b2b') }}" class="hover:text-brand-navy transition-colors">Mitra B2B</a>
            </div>
            <div class="flex gap-5">
                <a href="{{ route('pages.promos') }}" class="hover:text-brand-navy transition-colors flex items-center gap-1">
                    Promo Spesial
                </a>
                <a href="{{ route('pages.help') }}" class="hover:text-brand-navy transition-colors flex items-center gap-1">
                    <x-icon name="question-mark-circle" class="w-4 h-4" /> Pusat Bantuan
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navbar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 sm:h-20 gap-4 sm:gap-8">
            
            <!-- 1. Logo (Left) -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('home') }}" class="focus:outline-none">
                    <img src="{{ asset('images/logo-digirack.png') }}" alt="DigiRack" class="h-10 sm:h-12 md:h-14 w-auto drop-shadow-sm transition-transform hover:scale-105">
                </a>
            </div>

            <!-- 2. Search Bar (Middle - Hidden on very small mobile, expands on click) -->
            <div class="flex-1 max-w-2xl hidden md:flex items-center">
                <form action="/products" method="GET" class="w-full relative">
                    <div class="flex w-full rounded-xl border-2 border-brand-navy/20 bg-gray-50 focus-within:bg-white focus-within:border-brand-navy focus-within:shadow-sm overflow-hidden transition-all">
                        <input type="text" name="q" placeholder="Cari router, switch, kabel LAN, server..." 
                            class="w-full border-none bg-transparent focus:ring-0 text-sm px-4 py-2.5 placeholder-gray-400 text-gray-800"
                        >
                        <button type="submit" class="bg-brand-navy hover:bg-brand-navy/90 text-white px-5 flex items-center justify-center transition-colors">
                            <x-icon name="magnifying-glass" class="w-5 h-5" />
                        </button>
                    </div>
                </form>
            </div>

            <!-- 3. Right Icons & Auth -->
            <div class="flex items-center justify-end gap-3 sm:gap-6">
                <!-- Mobile Search Toggle -->
                <button @click="searchOpen = !searchOpen" class="md:hidden text-gray-500 hover:text-brand-navy">
                    <x-icon name="magnifying-glass" class="w-6 h-6" />
                </button>

                @auth
                    <!-- Notification User (Dynamic) -->
                    @php
                        $unreadNotifs = Auth::user()->unreadNotifications->take(8);
                        $unreadCount = Auth::user()->unreadNotifications->count();
                        $activeRole = Auth::user()->isAdmin() ? 'admin' : session('active_role', Auth::user()->role);
                        $roleLabel = match($activeRole) {
                            'seller' => 'Seller',
                            'admin' => 'Admin',
                            default => 'Buyer',
                        };
                        $sellerEntryUrl = Auth::user()->store ? route('switch.role', 'seller') : route('seller.register.form');
                        $sellerEntryLabel = Auth::user()->store ? 'Seller' : 'Daftar';
                    @endphp
                    <x-dropdown align="right" width="w-80 sm:w-96" contentClasses="py-0 overflow-hidden bg-white" class="mobile-dropdown-notif">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center text-gray-500 hover:text-brand-navy relative group transition-colors mt-1 focus:outline-none">
                                <x-icon name="bell" class="w-6 h-6 sm:w-7 sm:h-7" />
                                @if($unreadCount > 0)
                                    <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white animate-pulse">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                                @endif
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                                <p class="text-sm font-bold text-gray-800">Notifikasi</p>
                                @if($unreadCount > 0)
                                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-medium text-brand-blue hover:text-blue-600 transition-colors">Tandai semua dibaca</button>
                                    </form>
                                @endif
                            </div>
                            @if($unreadNotifs->count() > 0)
                                <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                                    @foreach($unreadNotifs as $notif)
                                        <a href="{{ route('notifications.read', $notif->id) }}" class="block px-4 py-3 hover:bg-brand-navylight/20 transition-colors group">
                                            <div class="flex items-start gap-3">
                                                <span class="text-xl mt-0.5 shrink-0">{{ $notif->data['icon'] ?? '🔔' }}</span>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-gray-900 group-hover:text-brand-navy truncate">{{ $notif->data['title'] ?? 'Notifikasi' }}</p>
                                                    <p class="text-xs text-gray-500 line-clamp-2 mt-0.5">{{ $notif->data['message'] ?? '' }}</p>
                                                    <p class="text-[10px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-8 text-center flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3 text-gray-300">
                                        <x-icon name="bell-slash" class="w-8 h-8" />
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada notifikasi baru</p>
                                    <p class="text-[11px] text-gray-400 mt-1">Kami akan mengabari Anda jika ada pembaruan pesanan.</p>
                                </div>
                            @endif
                            <div class="px-4 py-2 border-t border-gray-100 bg-gray-50 text-center">
                                <span class="text-[11px] font-bold text-gray-400">{{ $unreadCount }} notifikasi belum dibaca</span>
                            </div>
                        </x-slot>
                    </x-dropdown>

                    <!-- Cart -->
                    @if(!Auth::user()->isAdmin())
                        <a href="{{ route('buyer.cart.index') }}" class="inline-flex items-center text-gray-500 hover:text-brand-navy relative group transition-colors">
                            <x-icon name="shopping-cart" class="w-6 h-6 sm:w-7 sm:h-7" />
                            <span id="navCartBadge" class="absolute -top-1.5 -right-2 bg-brand-blue text-white text-[10px] sm:text-xs font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white {{ ($cartCount ?? 0) == 0 ? 'hidden' : '' }}">
                                {{ $cartCount ?? 0 }}
                            </span>
                        </a>
                        
                        <!-- Wishlist -->
                        <a href="{{ route('buyer.wishlist.index') }}" class="hidden sm:inline-flex items-center text-gray-500 hover:text-red-500 transition-colors relative mr-2">
                            <x-icon name="heart" class="w-6 h-6 sm:w-7 sm:h-7" />
                            @if(($wishlistCount ?? 0) > 0)
                                <span class="absolute -top-1.5 -right-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white">
                                    {{ $wishlistCount }}
                                </span>
                            @endif
                        </a>
                    @endif

                    <div class="hidden sm:flex items-center border-l-2 border-gray-100 pl-6 gap-3">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-2 hover:bg-gray-50 p-2 rounded-xl transition-colors focus:outline-none group">
                                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-8 h-8 rounded-full border border-gray-200 group-hover:border-brand-navy transition-colors">
                                    <div class="text-left hidden lg:block">
                                        <div class="text-sm font-semibold text-gray-800 leading-none group-hover:text-brand-navy transition-colors">{{ Str::limit(Auth::user()->name, 15) }}</div>
                                        <div class="text-[11px] font-medium text-gray-500 mt-1 uppercase tracking-wider">{{ $roleLabel }}</div>
                                    </div>
                                    <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 hidden lg:block" />
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-3 border-b border-gray-100 lg:hidden">
                                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>
                                @if(!Auth::user()->isAdmin())
                                    <div class="px-3 py-2 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <x-icon name="building-storefront" class="w-4 h-4 text-brand-navy" />
                                            <span class="text-xs font-bold text-gray-700">Toko Saya</span>
                                        </div>
                                        <a href="{{ $sellerEntryUrl }}" class="text-[10px] bg-brand-blue text-white px-2 py-1 rounded shadow-sm hover:bg-blue-600 font-bold transition">{{ $sellerEntryLabel }}</a>
                                    </div>
                                @endif
                                @if(!Auth::user()->isAdmin() && Auth::user()->store)
                                    <div class="px-3 py-3 border-b border-gray-100">
                                        <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Akun aktif</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <a href="{{ route('switch.role', 'buyer') }}" class="text-center rounded-lg px-3 py-2 text-xs font-bold border transition {{ $activeRole === 'buyer' ? 'bg-brand-navy text-white border-brand-navy' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-navy hover:text-brand-navy' }}">
                                                Pembeli
                                            </a>
                                            <a href="{{ route('switch.role', 'seller') }}" class="text-center rounded-lg px-3 py-2 text-xs font-bold border transition {{ $activeRole === 'seller' ? 'bg-brand-navy text-white border-brand-navy' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-navy hover:text-brand-navy' }}">
                                                Seller
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                <x-dropdown-link :href="route('dashboard')">
                                    Dashboard
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('profile.edit')">
                                    Profil Saya
                                </x-dropdown-link>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Konfirmasi Keluar', message: 'Apakah Anda yakin ingin mengakhiri sesi ini?', type: 'danger', confirmText: 'Ya, Keluar Sesi' })">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 focus:outline-none transition duration-150 ease-in-out font-medium">
                                        Keluar
                                    </button>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <!-- Notification Guest -->
                    <div class="block relative" @click.away="notifOpen = false">
                        <button @click="notifOpen = !notifOpen" class="text-gray-500 hover:text-brand-navy relative group transition-colors focus:outline-none mt-1">
                            <x-icon name="bell" class="w-6 h-6 sm:w-7 sm:h-7" />
                        </button>
                        
                        <div x-show="notifOpen" x-transition x-cloak class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] border border-gray-100 z-50 overflow-hidden mobile-dropdown-notif">
                            <div class="p-6 text-center">
                                <div class="w-20 h-20 bg-brand-bluelight rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-sm">
                                    <x-icon name="user" class="w-8 h-8 text-brand-blue" />
                                </div>
                                <p class="text-sm text-gray-500 mb-6 px-4">Log in untuk lihat notifikasi pembaruan pesanan dan promo.</p>
                                <div class="grid grid-cols-2 text-sm font-bold border-t border-gray-100 -mx-6 -mb-6 bg-gray-50/50">
                                    <a href="{{ route('register') }}" class="py-3.5 text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-100">Daftar</a>
                                    <a href="{{ route('login') }}" class="py-3.5 text-brand-blue hover:bg-brand-bluelight transition-colors">Log In</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Guest -->
                    <a href="{{ route('login') }}" class="inline-flex items-center text-gray-500 hover:text-brand-navy relative group transition-colors">
                        <x-icon name="shopping-cart" class="w-6 h-6 sm:w-7 sm:h-7" />
                    </a>

                    <!-- Guest Auth Links (Desktop) -->
                    <div class="hidden sm:flex items-center gap-3 border-l-2 border-gray-100 pl-6">
                        <a href="{{ route('login') }}" class="text-brand-navy hover:bg-brand-navylight font-bold text-sm px-5 py-2.5 rounded-xl transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="bg-brand-navy hover:bg-brand-navy/90 text-white font-bold text-sm px-5 py-2.5 shadow-sm rounded-xl transition-all">
                            Daftar
                        </a>
                    </div>
                @endauth

                <!-- Hamburger (Mobile) -->
                <div class="flex items-center sm:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex justify-center p-2 rounded-md text-gray-500 hover:text-brand-navy hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                        <x-icon name="bars-3" class="w-6 h-6" x-show="!mobileMenuOpen" />
                        <x-icon name="x-mark" class="w-6 h-6" x-show="mobileMenuOpen" x-cloak />
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Search Dropdown -->
        <div x-show="searchOpen" x-transition x-cloak class="md:hidden pb-4">
            <form action="/products" method="GET" class="w-full relative">
                <div class="flex w-full rounded-xl border border-brand-navy focus-within:shadow-sm overflow-hidden">
                    <input type="text" name="q" placeholder="Cari produk..." class="w-full border-none focus:ring-0 text-sm px-4 py-2 text-gray-800">
                    <button type="submit" class="bg-brand-navy text-white px-4">
                        <x-icon name="magnifying-glass" class="w-5 h-5" />
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mobile Menu Container -->
    <div x-show="mobileMenuOpen" x-transition x-cloak class="sm:hidden border-t border-gray-200 bg-white">
        @auth
            <!-- Authenticated Mobile Menu -->
            <div class="pt-4 pb-1">
                <div class="px-4 flex items-center justify-between gap-3 mb-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full shrink-0">
                        <div class="min-w-0">
                            <div class="truncate font-semibold text-base text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-xs text-brand-blue uppercase">{{ $roleLabel }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Konfirmasi Keluar', message: 'Apakah Anda yakin ingin mengakhiri sesi ini?', type: 'danger', confirmText: 'Ya, Keluar Sesi' })" class="shrink-0">
                        @csrf
                        <button type="submit" class="inline-flex min-h-10 items-center gap-1.5 rounded-full border border-red-100 bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 hover:border-red-200 hover:bg-red-100 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500/30 transition-colors" aria-label="Keluar" title="Keluar">
                            <x-icon name="arrow-right-on-rectangle-outline" class="w-5 h-5" />
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
                @if(!Auth::user()->isAdmin() && Auth::user()->store)
                    <div class="px-4 mb-3">
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-2">
                            <p class="px-2 pb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">Akun aktif</p>
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('switch.role', 'buyer') }}" class="text-center rounded-lg px-3 py-2 text-xs font-bold border transition {{ $activeRole === 'buyer' ? 'bg-brand-navy text-white border-brand-navy' : 'bg-white text-gray-600 border-gray-200' }}">
                                    Pembeli
                                </a>
                                <a href="{{ route('switch.role', 'seller') }}" class="text-center rounded-lg px-3 py-2 text-xs font-bold border transition {{ $activeRole === 'seller' ? 'bg-brand-navy text-white border-brand-navy' : 'bg-white text-gray-600 border-gray-200' }}">
                                    Seller
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="space-y-1 px-2">
                    @if(!Auth::user()->isAdmin())
                        <div class="bg-gray-50 rounded-xl p-3 mb-2 flex justify-between items-center border border-gray-100">
                            <div class="flex items-center gap-2">
                                <x-icon name="building-storefront" class="w-5 h-5 text-brand-navy" />
                                <span class="text-sm font-bold text-gray-700">Toko Saya</span>
                            </div>
                            <a href="{{ $sellerEntryUrl }}" class="text-xs bg-brand-blue text-white px-3 py-1.5 rounded-lg shadow-sm hover:bg-blue-600 font-bold transition">{{ Auth::user()->store ? 'Buka Toko' : 'Daftar Toko' }}</a>
                        </div>
                        <x-responsive-nav-link :href="route('buyer.cart.index')">
                            Keranjang ({{ $cartCount ?? 0 }})
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('buyer.wishlist.index')">
                            Wishlist ({{ $wishlistCount ?? 0 }})
                        </x-responsive-nav-link>
                    @endif
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard Utama
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.edit')">
                        Profil Saya
                    </x-responsive-nav-link>
                </div>

                <div class="mt-4 mx-4 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Info DigiRack</p>
                    <div class="grid grid-cols-2 gap-2 text-sm font-semibold">
                        <a href="{{ route('pages.download-app') }}" class="rounded-xl bg-white px-3 py-2.5 text-gray-700 border border-gray-100 hover:text-brand-blue hover:border-brand-blue/30 transition-colors">
                            Download App
                        </a>
                        <a href="{{ route('pages.about') }}" class="rounded-xl bg-white px-3 py-2.5 text-gray-700 border border-gray-100 hover:text-brand-blue hover:border-brand-blue/30 transition-colors">
                            Tentang
                        </a>
                        <a href="{{ route('pages.selling') }}" class="rounded-xl bg-white px-3 py-2.5 text-gray-700 border border-gray-100 hover:text-brand-blue hover:border-brand-blue/30 transition-colors">
                            Mulai Berjualan
                        </a>
                        <a href="{{ route('pages.b2b') }}" class="rounded-xl bg-white px-3 py-2.5 text-gray-700 border border-gray-100 hover:text-brand-blue hover:border-brand-blue/30 transition-colors">
                            Mitra B2B
                        </a>
                        <a href="{{ route('pages.promos') }}" class="rounded-xl bg-white px-3 py-2.5 text-gray-700 border border-gray-100 hover:text-brand-blue hover:border-brand-blue/30 transition-colors">
                            Promo
                        </a>
                        <a href="{{ route('pages.help') }}" class="rounded-xl bg-white px-3 py-2.5 text-gray-700 border border-gray-100 hover:text-brand-blue hover:border-brand-blue/30 transition-colors">
                            Bantuan
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Guest Mobile Menu -->
            <div class="px-4 py-6 space-y-4">
                <a href="{{ route('login') }}" class="block w-full text-center text-brand-navy font-bold text-base px-4 py-3 border-2 border-brand-navy rounded-xl transition-colors hover:bg-brand-navylight">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="block w-full text-center bg-brand-blue text-white font-bold text-base px-4 py-3 rounded-xl shadow-sm transition-colors hover:bg-blue-600">
                    Daftar Akun Baru
                </a>

                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Info DigiRack</p>
                    <div class="grid grid-cols-2 gap-2 text-sm font-semibold">
                        <a href="{{ route('pages.download-app') }}" class="rounded-xl bg-white px-3 py-2.5 text-gray-700 border border-gray-100 hover:text-brand-blue hover:border-brand-blue/30 transition-colors">
                            Download App
                        </a>
                        <a href="{{ route('pages.about') }}" class="rounded-xl bg-white px-3 py-2.5 text-gray-700 border border-gray-100 hover:text-brand-blue hover:border-brand-blue/30 transition-colors">
                            Tentang
                        </a>
                        <a href="{{ route('pages.selling') }}" class="rounded-xl bg-white px-3 py-2.5 text-gray-700 border border-gray-100 hover:text-brand-blue hover:border-brand-blue/30 transition-colors">
                            Mulai Berjualan
                        </a>
                        <a href="{{ route('pages.b2b') }}" class="rounded-xl bg-white px-3 py-2.5 text-gray-700 border border-gray-100 hover:text-brand-blue hover:border-brand-blue/30 transition-colors">
                            Mitra B2B
                        </a>
                        <a href="{{ route('pages.promos') }}" class="rounded-xl bg-white px-3 py-2.5 text-gray-700 border border-gray-100 hover:text-brand-blue hover:border-brand-blue/30 transition-colors">
                            Promo
                        </a>
                        <a href="{{ route('pages.help') }}" class="rounded-xl bg-white px-3 py-2.5 text-gray-700 border border-gray-100 hover:text-brand-blue hover:border-brand-blue/30 transition-colors">
                            Bantuan
                        </a>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</nav>
