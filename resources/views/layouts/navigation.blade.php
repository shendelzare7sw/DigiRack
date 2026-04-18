<nav x-data="{ mobileMenuOpen: false, searchOpen: false, notifOpen: false }" class="bg-white sticky top-0 z-50 shadow-sm border-b border-gray-100">
    
    <!-- Top Bar (Desktop Only) -->
    <div class="bg-gray-100 border-b border-gray-200 hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-9 text-xs text-gray-500 font-medium">
            <div class="flex gap-5">
                <a href="#" class="hover:text-brand-navy flex items-center gap-1.5 transition-colors">
                    <x-icon name="device-phone-mobile" class="w-4 h-4" /> Download App DigiRack
                </a>
                <span class="text-gray-300">|</span>
                <a href="#" class="hover:text-brand-navy transition-colors">Tentang DigiRack</a>
                <a href="{{ route('register') }}" class="hover:text-brand-navy transition-colors">Mulai Berjualan</a>
                <a href="#" class="hover:text-brand-navy transition-colors">Mitra B2B</a>
            </div>
            <div class="flex gap-5">
                <a href="#" class="hover:text-brand-navy transition-colors flex items-center gap-1">
                    Promo Spesial
                </a>
                <a href="#" class="hover:text-brand-navy transition-colors flex items-center gap-1">
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
                    @endphp
                    <x-dropdown align="right" width="w-96" contentClasses="py-0 overflow-hidden bg-white">
                        <x-slot name="trigger">
                            <button class="text-gray-500 hover:text-brand-navy relative group transition-colors focus:outline-none flex items-center justify-center p-1 mt-1 mr-2">
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
                        <a href="{{ route('buyer.cart.index') }}" class="text-gray-500 hover:text-brand-navy relative group transition-colors">
                            <x-icon name="shopping-cart" class="w-6 h-6 sm:w-7 sm:h-7" />
                            <span id="navCartBadge" class="absolute -top-1.5 -right-2 bg-brand-blue text-white text-[10px] sm:text-xs font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white {{ ($cartCount ?? 0) == 0 ? 'hidden' : '' }}">
                                {{ $cartCount ?? 0 }}
                            </span>
                        </a>
                        
                        <!-- Wishlist -->
                        <a href="{{ route('buyer.wishlist.index') }}" class="hidden sm:block text-gray-500 hover:text-red-500 transition-colors relative mr-2">
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
                                        <div class="text-[11px] font-medium text-gray-500 mt-1 uppercase tracking-wider">{{ Auth::user()->role }}</div>
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
                                        <a href="{{ route('seller.dashboard') }}" class="text-[10px] bg-brand-blue text-white px-2 py-1 rounded shadow-sm hover:bg-blue-600 font-bold transition">Buka</a>
                                    </div>
                                @endif
                                <x-dropdown-link :href="route('dashboard')">
                                    Dashboard
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('profile.edit')">
                                    Profil Saya
                                </x-dropdown-link>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
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
                    <div class="block relative mr-2" @click.away="notifOpen = false">
                        <button @click="notifOpen = !notifOpen" class="text-gray-500 hover:text-brand-navy relative group transition-colors focus:outline-none">
                            <x-icon name="bell" class="w-6 h-6 sm:w-7 sm:h-7" />
                        </button>
                        
                        <div x-show="notifOpen" x-transition x-cloak class="absolute right-[-10px] sm:right-0 mt-3 w-80 bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] border border-gray-100 z-50 overflow-hidden">
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
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-brand-navy transition-colors mr-2 sm:mr-4">
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
                <div class="px-4 flex items-center gap-3 mb-4">
                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full">
                    <div>
                        <div class="font-semibold text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-xs text-brand-blue uppercase">{{ Auth::user()->role }}</div>
                    </div>
                </div>
                
                <div class="space-y-1 px-2">
                    @if(!Auth::user()->isAdmin())
                        <div class="bg-gray-50 rounded-xl p-3 mb-2 flex justify-between items-center border border-gray-100">
                            <div class="flex items-center gap-2">
                                <x-icon name="building-storefront" class="w-5 h-5 text-brand-navy" />
                                <span class="text-sm font-bold text-gray-700">Toko Saya</span>
                            </div>
                            <a href="{{ route('seller.dashboard') }}" class="text-xs bg-brand-blue text-white px-3 py-1.5 rounded-lg shadow-sm hover:bg-blue-600 font-bold transition">Buka Toko</a>
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

                <div class="mt-4 border-t border-gray-100 pt-2 px-2 pb-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-red-600 hover:text-red-800 hover:bg-red-50 hover:border-red-600 focus:outline-none transition duration-150 ease-in-out">
                            Keluar
                        </button>
                    </form>
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
            </div>
        @endauth
    </div>
</nav>
