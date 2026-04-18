<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DigiRack') }} - {{ $title ?? 'Pusat Peralatan Jaringan & IT' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50 flex flex-col min-h-screen">
        
        <!-- Navbar -->
        @include('layouts.navigation')

        <!-- Page Heading (Optional, mostly for dashboards) -->
        @if (isset($header))
            <header class="bg-white border-b border-gray-100 shadow-sm">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-brand-navy text-white pt-16 pb-8 border-t-4 border-brand-blue mt-auto font-sans">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-12 gap-10 lg:gap-8 mb-12">
                    
                    <!-- Col 1: Brand Info (4 cols on lg) -->
                    <div class="col-span-1 md:col-span-4 lg:col-span-4 lg:pr-8">
                        <img src="{{ asset('images/logo-digirack.png') }}" alt="DigiRack" class="h-10 w-auto bg-white px-3 py-1.5 rounded-xl mb-6 shadow-sm">
                        <h3 class="text-xl font-bold text-white mb-3">DigiRack Enterprise</h3>
                        <p class="text-gray-300 text-[15px] leading-relaxed mb-6">
                            Marketplace B2B/B2C khusus infrastruktur jaringan, server, dan peralatan IT enterprise terpercaya di Indonesia yang dapat bersaing di kelas internasional.
                        </p>
                        
                        <!-- Social Icons -->
                        <div class="flex gap-4">
                            <a href="#" class="text-gray-300 hover:text-brand-blue transition-colors duration-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="text-gray-300 hover:text-brand-blue transition-colors duration-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                            <a href="#" class="text-gray-300 hover:text-brand-blue transition-colors duration-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Col 2: Jelajahi DigiRack (2 cols on lg) -->
                    <div class="col-span-1 md:col-span-2 lg:col-span-2">
                        <h4 class="font-bold text-[17px] mb-6 text-blue-400 tracking-wide">Jelajahi DigiRack</h4>
                        <ul class="space-y-4 text-[15px] text-gray-300">
                            <li class="flex items-center gap-2 group">
                                <span class="text-white/50 group-hover:text-white transition-colors">›</span> 
                                <a href="#" class="hover:text-white transition-colors">Tentang DigiRack</a>
                            </li>
                            <li class="flex items-center gap-2 group">
                                <span class="text-white/50 group-hover:text-white transition-colors">›</span> 
                                <a href="#" class="hover:text-white transition-colors">Official Store</a>
                            </li>
                            <li class="flex items-center gap-2 group">
                                <span class="text-white/50 group-hover:text-white transition-colors">›</span> 
                                <a href="#" class="hover:text-white transition-colors">Flash Sale</a>
                            </li>
                            <li class="flex items-center gap-2 group">
                                <span class="text-white/50 group-hover:text-white transition-colors">›</span> 
                                <a href="#" class="hover:text-white transition-colors">Produk Enterprise</a>
                            </li>
                            <li class="flex items-center gap-2 group">
                                <span class="text-white/50 group-hover:text-white transition-colors">›</span> 
                                <a href="#" class="hover:text-white transition-colors">Berita Terkini</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Col 3: Layanan Pelanggan (3 cols on lg) -->
                    <div class="col-span-1 md:col-span-2 lg:col-span-3">
                        <h4 class="font-bold text-[17px] mb-6 text-green-400 tracking-wide">Layanan Pelanggan</h4>
                        <ul class="space-y-4 text-[15px] text-gray-300">
                            <li class="flex items-center gap-2 group">
                                <span class="text-white/50 group-hover:text-white transition-colors">›</span> 
                                <a href="#" class="hover:text-white transition-colors">Bantuan Pembeli</a>
                            </li>
                            <li class="flex items-center gap-2 group">
                                <span class="text-white/50 group-hover:text-white transition-colors">›</span> 
                                <a href="#" class="hover:text-white transition-colors">Pusat Edukasi Seller</a>
                            </li>
                            <li class="flex items-center gap-2 group">
                                <span class="text-white/50 group-hover:text-white transition-colors">›</span> 
                                <a href="#" class="hover:text-white transition-colors">Pengiriman & Logistik</a>
                            </li>
                            <li class="flex items-center gap-2 group">
                                <span class="text-white/50 group-hover:text-white transition-colors">›</span> 
                                <a href="#" class="hover:text-white transition-colors">Garansi Produk</a>
                            </li>
                            <li class="flex items-center gap-2 group">
                                <span class="text-white/50 group-hover:text-white transition-colors">›</span> 
                                <a href="#" class="hover:text-white transition-colors">Hubungi Kami</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Col 4: Pengiriman & Pembayaran (3 cols on lg) -->
                    <div class="col-span-1 md:col-span-4 lg:col-span-3">
                        <h4 class="font-bold text-[17px] mb-6 text-brand-blue tracking-wide">Metode Pengiriman</h4>
                        <!-- Tampilan Grid (Kebawah) dengan Faux-Logo Cards -->
                        <div class="grid grid-cols-3 gap-2 mb-8 pr-4 sm:pr-0">
                            <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm">
                                <span class="text-[10px] font-black tracking-tight text-green-600">go<span class="text-black">send</span></span>
                            </div>
                            <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm">
                                <span class="text-[10px] font-black tracking-tight text-green-500">Grab</span>
                            </div>
                            <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm">
                                <span class="text-[12px] font-black tracking-tighter text-blue-800 italic">JNE</span>
                            </div>
                            <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm">
                                <span class="text-[12px] font-black tracking-tighter text-red-600">J&T</span>
                            </div>
                            <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm">
                                <span class="text-[10px] font-black tracking-tight text-red-600">SiCepat</span>
                            </div>
                            <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm">
                                <span class="text-[10px] font-black tracking-tight text-pink-500">AnterAja</span>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-white/10">
                            <h4 class="font-bold text-[17px] mb-6 text-brand-blue tracking-wide">Metode Pembayaran</h4>
                            <div class="grid grid-cols-3 gap-2 pr-4 sm:pr-0">
                                <!-- Midtrans takes full width of its row or 2 cols -->
                                <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm col-span-3 sm:col-span-2">
                                    <span class="text-[11px] font-black text-brand-navy flex items-center gap-1">
                                        <x-icon name="shield-check" class="w-4 h-4 text-green-500" /> Midtrans Gateway
                                    </span>
                                </div>
                                <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm">
                                    <span class="text-[12px] font-black tracking-tighter text-blue-800 italic">BCA</span>
                                </div>
                                <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm">
                                    <span class="text-[12px] font-black tracking-tighter text-blue-500 italic">BNI</span>
                                </div>
                                <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm">
                                    <span class="text-[11px] font-black tracking-tight text-yellow-500">mandiri</span>
                                </div>
                                <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm">
                                    <span class="text-[11px] font-bold tracking-tight text-blue-500">gopay</span>
                                </div>
                                <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm">
                                    <span class="text-[11px] font-black tracking-tight text-purple-600">OVO</span>
                                </div>
                                <div class="bg-white rounded p-1.5 flex items-center justify-center h-9 shadow-sm">
                                    <span class="text-[11px] font-black tracking-tighter text-red-600">QRIS</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Bottom -->
                <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center text-[14px] text-gray-400 gap-4 mt-8 pb-4">
                    <p>&copy; {{ date('Y') }} PT DigiRack Infrastruktur Digital. All rights reserved.</p>
                    <div class="flex flex-wrap justify-center gap-4 sm:gap-6 mt-4 md:mt-0">
                        <a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a>
                        <a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a>
                        <a href="#" class="hover:text-white transition-colors">Sitemap</a>
                    </div>
                </div>
            </div>
        </footer>

        <x-toast />
        @stack('scripts')
    </body>
</html>
