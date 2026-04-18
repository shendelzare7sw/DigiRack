{{-- Sidebar Filter untuk Halaman Katalog Produk --}}
@props(['categories', 'class' => ''])

<div class="{{ $class }}">
    <form id="filterForm" method="GET" action="{{ route('products.index') }}">
        {{-- Preserve search query --}}
        @if(request('q'))
            <input type="hidden" name="q" value="{{ request('q') }}">
        @endif

        {{-- Kategori --}}
        <div x-data="{ open: true }" class="mb-6">
            <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left mb-3">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Kategori</h3>
                <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 transition-transform" ::class="open ? 'rotate-180' : ''" />
            </button>
            <div x-show="open" x-collapse>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <label class="flex items-center gap-3 px-3 py-2 rounded-lg cursor-pointer hover:bg-brand-navylight/50 transition-colors group">
                            <input type="checkbox" name="category[]" value="{{ $cat->slug }}"
                                {{ in_array($cat->slug, (array) request('category', [])) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-brand-navy focus:ring-brand-navy/30 w-4 h-4"
                            >
                            <span class="flex items-center gap-2 flex-1 min-w-0">
                                <span class="w-7 h-7 bg-brand-navylight rounded-full flex items-center justify-center shrink-0 group-hover:bg-brand-navy group-hover:text-white text-brand-navy transition-colors">
                                    <span class="w-4 h-4 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">{!! $cat->icon_svg !!}</span>
                                </span>
                                <span class="text-sm text-gray-700 truncate">{{ $cat->name }}</span>
                            </span>
                            <span class="text-xs text-gray-400 font-medium shrink-0">{{ $cat->products_count }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Rentang Harga --}}
        <div x-data="{ open: true }" class="mb-6">
            <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left mb-3">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Rentang Harga</h3>
                <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 transition-transform" ::class="open ? 'rotate-180' : ''" />
            </button>
            <div x-show="open" x-collapse>
                <div class="space-y-3">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">Rp</span>
                        <input type="number" name="min_price" value="{{ request('min_price') }}"
                            placeholder="Harga Minimum"
                            class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:border-brand-navy focus:ring-brand-navy/20 bg-gray-50 focus:bg-white transition-colors"
                        >
                    </div>
                    <div class="flex items-center justify-center">
                        <span class="text-xs text-gray-400">sampai</span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">Rp</span>
                        <input type="number" name="max_price" value="{{ request('max_price') }}"
                            placeholder="Harga Maksimum"
                            class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:border-brand-navy focus:ring-brand-navy/20 bg-gray-50 focus:bg-white transition-colors"
                        >
                    </div>
                </div>
            </div>
        </div>

        {{-- Rating Minimum --}}
        <div x-data="{ open: true, selected: '{{ request('rating', '') }}' }" class="mb-6">
            <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left mb-3">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Rating Minimum</h3>
                <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 transition-transform" ::class="open ? 'rotate-180' : ''" />
            </button>
            <div x-show="open" x-collapse>
                <input type="hidden" name="rating" :value="selected">
                <div class="space-y-1">
                    @for($r = 4; $r >= 1; $r--)
                        <button type="button"
                            @click="selected = selected == '{{ $r }}' ? '' : '{{ $r }}'"
                            class="flex items-center gap-2 w-full px-3 py-2 rounded-lg transition-colors"
                            :class="selected == '{{ $r }}' ? 'bg-brand-bluelight border border-brand-blue/30' : 'hover:bg-gray-50'"
                        >
                            <div class="flex items-center gap-0.5">
                                @for($s = 1; $s <= 5; $s++)
                                    @if($s <= $r)
                                        <x-icon name="star" class="w-4 h-4 text-yellow-400" />
                                    @else
                                        <x-icon name="star-outline" class="w-4 h-4 text-gray-300" />
                                    @endif
                                @endfor
                            </div>
                            <span class="text-xs text-gray-500">& ke atas</span>
                        </button>
                    @endfor
                </div>
            </div>
        </div>

        {{-- Kondisi --}}
        <div x-data="{ open: true }" class="mb-6">
            <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left mb-3">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Kondisi</h3>
                <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 transition-transform" ::class="open ? 'rotate-180' : ''" />
            </button>
            <div x-show="open" x-collapse>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="condition" value="new"
                            {{ request('condition') === 'new' ? 'checked' : '' }}
                            class="text-brand-navy focus:ring-brand-navy/30"
                        >
                        <span class="text-sm text-gray-700">Baru</span>
                    </label>
                    <label class="flex items-center gap-3 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="condition" value="used"
                            {{ request('condition') === 'used' ? 'checked' : '' }}
                            class="text-brand-navy focus:ring-brand-navy/30"
                        >
                        <span class="text-sm text-gray-700">Bekas</span>
                    </label>
                    <label class="flex items-center gap-3 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="condition" value=""
                            {{ !request('condition') ? 'checked' : '' }}
                            class="text-brand-navy focus:ring-brand-navy/30"
                        >
                        <span class="text-sm text-gray-700">Semua</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Sort (hidden, synced from top dropdown via JS) --}}
        <input type="hidden" name="sort" id="filterSortInput" value="{{ request('sort', 'newest') }}">

        {{-- Action Buttons --}}
        <div class="space-y-3 pt-4 border-t border-gray-100">
            <button type="submit" class="w-full bg-brand-navy hover:bg-brand-navydark text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-sm">
                Terapkan Filter
            </button>
            <a href="{{ route('products.index') }}" class="block w-full text-center text-sm text-gray-500 hover:text-red-500 font-medium py-2 transition-colors">
                Reset Semua Filter
            </a>
        </div>
    </form>
</div>
