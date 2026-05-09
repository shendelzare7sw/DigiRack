@props(['product', 'wishlisted' => false])

<div class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-brand-navy/20 transition-all duration-300 relative flex flex-col">

    {{-- Wishlist Heart — Native Form, always works --}}
    @auth
        <form action="{{ route('buyer.wishlist.toggle') }}" method="POST" class="absolute top-2.5 right-2.5 z-30">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <button type="submit"
                class="w-9 h-9 bg-white rounded-full flex items-center justify-center shadow-md transition-all duration-200 hover:scale-110 active:scale-95 touch-manipulation border border-gray-100 {{ $wishlisted ? 'text-red-500' : 'text-gray-400' }}">
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="{{ $wishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </button>
        </form>
    @else
        <a href="{{ route('login') }}"
            class="absolute top-2.5 right-2.5 z-30 w-9 h-9 bg-white rounded-full flex items-center justify-center shadow-md transition-all duration-200 hover:scale-110 active:scale-95 touch-manipulation border border-gray-100 text-gray-400">
            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
            </svg>
        </a>
    @endauth

    {{-- Image --}}
    <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden aspect-square bg-gray-50">
        <img
            src="{{ $product->primary_image_url }}"
            alt="{{ $product->name }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
        />
        @if($product->flashSale)
            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full pointer-events-none">
                -{{ $product->flashSale->discount_percent }}%
            </div>
        @endif
        @if($product->stock <= 5 && $product->stock > 0)
            <div class="absolute top-2 {{ $product->flashSale ? 'left-16' : 'left-2' }} bg-brand-blue text-white text-xs font-semibold px-2 py-1 rounded-full pointer-events-none">
                Stok Terbatas
            </div>
        @endif
    </a>

    {{-- Content --}}
    <div class="p-3 sm:p-4 flex-1 flex flex-col">
        {{-- Category --}}
        <p class="text-xs text-gray-400 font-medium mb-1">{{ $product->category->name ?? '' }}</p>

        {{-- Name --}}
        <a href="{{ route('products.show', $product->slug) }}" class="block">
            <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-brand-navy transition-colors">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Price --}}
        <div class="mt-2">
            @if($product->flashSale)
                <p class="text-xs text-gray-400 line-through">{{ $product->formatted_price }}</p>
                <p class="text-base font-bold text-brand-blue">{{ $product->flashSale->formatted_sale_price }}</p>
            @else
                <p class="text-base font-bold text-brand-blue">{{ $product->formatted_price }}</p>
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

        {{-- Add to Cart Button — Native Form, separate from image, always works --}}
        @if($product->isInStock() && (!Auth::check() || Auth::user()->role !== 'admin'))
            <div class="mt-3 pt-2">
                @auth
                    <form action="{{ route('buyer.cart.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit"
                            class="w-full bg-brand-blue hover:bg-blue-600 text-white text-xs font-bold py-2.5 rounded-lg flex items-center justify-center gap-1.5 transition-colors active:scale-[0.97] touch-manipulation shadow-sm">
                            <x-icon name="shopping-cart" class="w-3.5 h-3.5" />
                            + Keranjang
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="w-full bg-brand-blue hover:bg-blue-600 text-white text-xs font-bold py-2.5 rounded-lg flex items-center justify-center gap-1.5 transition-colors active:scale-[0.97] touch-manipulation shadow-sm">
                        <x-icon name="shopping-cart" class="w-3.5 h-3.5" />
                        + Keranjang
                    </a>
                @endauth
            </div>
        @endif
    </div>
</div>
