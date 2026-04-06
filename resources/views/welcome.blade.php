<x-app-layout>
    <x-slot name="title">Pusat Peralatan Jaringan & IT</x-slot>

    <!-- 1. Hero Banner Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="relative bg-brand-navy rounded-2xl overflow-hidden shadow-xl min-h-[350px] md:min-h-[450px] lg:min-h-[500px] flex items-center">
            <!-- Decorative Elements -->
            <div class="absolute right-0 top-0 w-1/2 h-full bg-gradient-to-l from-brand-orange/20 to-transparent"></div>
            <div class="absolute -right-20 -bottom-20 w-96 h-96 border-8 border-white/5 rounded-full"></div>
            
            <div class="relative z-10 w-full max-w-4xl px-6 sm:px-10 md:px-16 lg:px-20 text-white py-12 md:py-16">
                <h1 class="font-display font-bold text-3xl sm:text-4xl md:text-5xl lg:text-[3.5rem] leading-[1.2] md:leading-[1.1] mb-4 md:mb-6 tracking-tight">
                    Infrastruktur IT Enterprise
                    <span class="block text-brand-orange mt-1 md:mt-2">Terpercaya.</span>
                </h1>
                <p class="text-sm sm:text-base md:text-lg text-gray-200 mb-8 max-w-2xl hidden sm:block leading-relaxed">
                    Temukan segala kebutuhan jaringan, server, dan alat telekomunikasi dari distributor resmi — dijamin original.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#" class="bg-brand-orange hover:bg-orange-600 text-white font-bold px-6 md:px-8 py-3 md:py-4 rounded-xl shadow-lg transition-transform hover:scale-105 active:scale-95 text-sm sm:text-base">
                        Mulai Belanja
                    </a>
                    <a href="#kategori" class="bg-white/10 hover:bg-white/20 text-white font-semibold border border-white/20 px-6 md:px-8 py-3 md:py-4 rounded-xl backdrop-blur-sm transition-colors text-sm sm:text-base block">
                        Jelajahi Kategori
                    </a>
                </div>
            </div>
            
            <!-- Graphic/Image placeholder -->
            <div class="absolute right-0 bottom-0 h-full w-1/2 md:w-5/12 hidden md:flex items-center justify-end pr-8 lg:pr-16 pointer-events-none">
                <x-icon name="server-stack" class="w-48 h-48 lg:w-80 lg:h-80 text-white/5 drop-shadow-2xl translate-y-4 lg:translate-y-8" />
            </div>
        </div>
    </section>

    <!-- 2. Categories Section -->
    <section id="kategori" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-display font-bold text-xl sm:text-2xl text-gray-900">Kategori Pilihan</h2>
            <a href="#" class="text-brand-orange font-semibold text-sm hover:underline">Lihat Semua</a>
        </div>
        
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 sm:gap-6">
            @php
                $categories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->take(6)->get();
            @endphp
            
            @forelse($categories as $category)
                <a href="#" class="group flex flex-col items-center p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-brand-navy/30 transition-all duration-300">
                    <div class="w-14 h-14 bg-brand-navylight rounded-full flex items-center justify-center mb-3 group-hover:bg-brand-orange transition-colors duration-300 text-brand-navy group-hover:text-white">
                        {!! $category->icon_svg !!}
                    </div>
                    <span class="text-xs sm:text-sm font-semibold text-gray-700 text-center leading-tight group-hover:text-brand-navy">
                        {{ $category->name }}
                    </span>
                </a>
            @empty
                <!-- Fallback if no db data -->
                <div class="col-span-full">
                    <x-empty-state title="Belum ada kategori" description="Silakan jalankan seeder." />
                </div>
            @endforelse
        </div>
    </section>

    <!-- 3. Flash Sale Section -->
    @php
        $flashSales = \App\Models\FlashSale::with(['product', 'product.store', 'product.category'])->where('is_active', true)->where('end_time', '>=', now())->get();
    @endphp

    @if($flashSales->isNotEmpty())
    <section class="mt-4 mb-8">
        <div class="bg-gradient-to-r from-red-600 to-brand-orange py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8">
                    <div class="text-white">
                        <div class="flex items-center gap-2 mb-2">
                            <x-icon name="fire" class="w-8 h-8 text-yellow-300" />
                            <h2 class="font-display font-bold text-2xl sm:text-3xl italic">FLASH SALE</h2>
                        </div>
                        <p class="text-red-100 font-medium">Diskon spesial waktu terbatas, jangan sampai kehabisan!</p>
                    </div>
                    
                    <div class="mt-4 sm:mt-0 flex items-center gap-2 bg-black/20 backdrop-blur px-4 py-2 rounded-xl text-white font-mono font-bold text-xl">
                        <x-icon name="clock" class="w-5 h-5 text-white/70" />
                        <span>02</span> : <span>45</span> : <span>18</span>
                    </div>
                </div>

                <!-- Products Carousel -->
                <div x-data="{
                        scrollLeft() { $refs.slider.scrollBy({ left: -300, behavior: 'smooth' }) },
                        scrollRight() { $refs.slider.scrollBy({ left: 300, behavior: 'smooth' }) }
                    }" class="relative group">
                    
                    <!-- Prev Button -->
                    <button @click="scrollLeft" class="absolute -left-5 top-1/2 -translate-y-1/2 z-10 bg-white w-12 h-12 rounded-full shadow-[0_4px_20px_rgba(0,0,0,0.15)] text-gray-700 hover:text-brand-orange hidden sm:flex items-center justify-center transition-all duration-300 opacity-0 group-hover:opacity-100 hover:scale-110">
                        <x-icon name="chevron-left" class="w-6 h-6 border-0" />
                    </button>
                    <!-- Next Button -->
                    <button @click="scrollRight" class="absolute -right-5 top-1/2 -translate-y-1/2 z-10 bg-white w-12 h-12 rounded-full shadow-[0_4px_20px_rgba(0,0,0,0.15)] text-gray-700 hover:text-brand-orange hidden sm:flex items-center justify-center transition-all duration-300 opacity-0 group-hover:opacity-100 hover:scale-110">
                        <x-icon name="chevron-right" class="w-6 h-6 border-0" />
                    </button>

                    <div x-ref="slider" class="flex overflow-x-auto pb-4 -mx-4 px-4 sm:mx-0 sm:px-1 gap-4 sm:gap-6 snap-x snap-mandatory hide-scroll scroll-smooth">
                        @foreach($flashSales as $fs)
                            <div class="w-[240px] sm:w-[260px] md:w-[280px] shrink-0 snap-start">
                                <x-product-card :product="$fs->product" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- 4. Recommended Products Grid Section -->
    <section class="bg-gray-50 py-10 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="font-display font-bold text-xl sm:text-2xl text-gray-900 mb-6 flex items-center gap-2">
                <x-icon name="arrow-trending-up" class="w-6 h-6 text-brand-orange" />
                Rekomendasi Spesial
            </h2>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-6">
                @php
                    $products = \App\Models\Product::with(['store', 'category'])
                        ->whereDoesntHave('flashSale')
                        ->inRandomOrder()
                        ->take(10)
                        ->get();
                @endphp
                
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <!-- Fallback -->
                    <div class="col-span-full">
                        <x-empty-state title="Belum ada produk" description="Toko belum menambahkan produk." />
                    </div>
                @endforelse
            </div>
            
            <div class="mt-10 text-center">
                <a href="#" class="inline-block border-2 border-brand-navy text-brand-navy font-bold hover:bg-brand-navy hover:text-white transition-colors px-10 py-3 rounded-full">
                    Muat Lebih Banyak
                </a>
            </div>
        </div>
    </section>

    @push('scripts')
    <style>
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @endpush
</x-app-layout>
