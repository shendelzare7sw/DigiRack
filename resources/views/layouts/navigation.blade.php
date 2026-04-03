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
                    <!-- Notification User -->
                    <x-dropdown align="right" width="80">
                        <x-slot name="trigger">
                            <button class="text-gray-500 hover:text-brand-navy relative group transition-colors focus:outline-none hidden sm:block">
                                <x-icon name="bell" class="w-6 h-6 sm:w-7 sm:h-7" />
                                <span class="absolute -top-1 -right-1 bg-brand-orange text-white text-[10px] sm:text-xs font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white">
                                    1
                                </span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-bold text-gray-800">Notifikasi</p>
                            </div>
                            <div class="p-6 text-center text-gray-500 text-sm">
                                <x-icon name="bell-slash" class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                                Belum ada notifikasi baru.
                            </div>
                        </x-slot>
                    </x-dropdown>

                    <!-- Cart -->
                    <a href="#" class="text-gray-500 hover:text-brand-navy relative group transition-colors">
                        <x-icon name="shopping-cart" class="w-6 h-6 sm:w-7 sm:h-7" />
                        <span class="absolute -top-1.5 -right-2 bg-brand-orange text-white text-[10px] sm:text-xs font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white">
                            3
                        </span>
                    </a>
                    
                    <!-- Wishlist -->
                    <a href="#" class="hidden sm:block text-gray-500 hover:text-red-500 transition-colors">
                        <x-icon name="heart" class="w-6 h-6 sm:w-7 sm:h-7" />
                    </a>

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
                    <div class="hidden sm:block relative mr-2" @click.away="notifOpen = false">
                        <button @click="notifOpen = !notifOpen" class="text-gray-500 hover:text-brand-navy relative group transition-colors focus:outline-none">
                            <x-icon name="bell" class="w-6 h-6 sm:w-7 sm:h-7" />
                        </button>
                        
                        <div x-show="notifOpen" x-transition x-cloak class="absolute right-[-80px] mt-3 w-80 bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] border border-gray-100 z-50 overflow-hidden">
                            <div class="p-6 text-center">
                                <div class="w-20 h-20 bg-brand-orangelight rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-sm">
                                    <x-icon name="user" class="w-8 h-8 text-brand-orange" />
                                </div>
                                <p class="text-sm text-gray-500 mb-6 px-4">Log in untuk lihat notifikasi pembaruan pesanan dan promo.</p>
                                <div class="grid grid-cols-2 text-sm font-bold border-t border-gray-100 -mx-6 -mb-6 bg-gray-50/50">
                                    <a href="{{ route('register') }}" class="py-3.5 text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-100">Daftar</a>
                                    <a href="{{ route('login') }}" class="py-3.5 text-brand-orange hover:bg-brand-orangelight transition-colors">Log In</a>
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
                        <div class="font-medium text-xs text-brand-orange uppercase">{{ Auth::user()->role }}</div>
                    </div>
                </div>
                
                <div class="space-y-1 px-2">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="#">
                        Wishlist
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
                <a href="{{ route('register') }}" class="block w-full text-center bg-brand-orange text-white font-bold text-base px-4 py-3 rounded-xl shadow-sm transition-colors hover:bg-orange-600">
                    Daftar Akun Baru
                </a>
            </div>
        @endauth
    </div>
</nav>
