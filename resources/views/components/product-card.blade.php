@props(['product'])

<div class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-brand-navy/20 transition-all duration-300">
    {{-- Image --}}
    <a href="/products/{{ $product->slug }}" class="block relative overflow-hidden aspect-square bg-gray-50">
        <img
            src="{{ $product->primary_image_url }}"
            alt="{{ $product->name }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
        />
        @if($product->flashSale)
            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                -{{ $product->flashSale->discount_percent }}%
            </div>
        @endif
        @if($product->stock <= 5 && $product->stock > 0)
            <div class="absolute top-2 right-2 bg-brand-orange text-white text-xs font-semibold px-2 py-1 rounded-full">
                Stok Terbatas
            </div>
        @endif
    </a>

    {{-- Content --}}
    <div class="p-3 sm:p-4">
        {{-- Category --}}
        <p class="text-xs text-gray-400 font-medium mb-1">{{ $product->category->name ?? '' }}</p>

        {{-- Name --}}
        <a href="/products/{{ $product->slug }}" class="block">
            <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-brand-navy transition-colors">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Price --}}
        <div class="mt-2">
            @if($product->flashSale)
                <p class="text-xs text-gray-400 line-through">{{ $product->formatted_price }}</p>
                <p class="text-base font-bold text-brand-orange">{{ $product->flashSale->formatted_sale_price }}</p>
            @else
                <p class="text-base font-bold text-brand-orange">{{ $product->formatted_price }}</p>
            @endif
        </div>

        {{-- Rating & Sold --}}
        <div class="mt-2 flex items-center justify-between">
            <div class="flex items-center gap-1">
                <x-star-rating :value="$product->avg_rating" size="w-3.5 h-3.5" />
                <span class="text-xs text-gray-400">{{ number_format($product->avg_rating, 1) }}</span>
            </div>
            @if($product->sold_count > 0)
                <span class="text-xs text-gray-400">{{ $product->sold_count }} terjual</span>
            @endif
        </div>

        {{-- Store --}}
        <div class="mt-2 pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 truncate">
                <x-icon name="building-storefront" class="w-3 h-3 inline -mt-0.5" />
                {{ $product->store->name ?? '' }}
            </p>
        </div>
    </div>
</div>
