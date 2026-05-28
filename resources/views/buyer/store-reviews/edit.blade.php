<x-app-layout>
    <x-slot name="title">{{ $review ? 'Edit Ulasan Toko' : 'Tulis Ulasan Toko' }}</x-slot>

    @php
        $initialRating = (int) old('rating', $review->rating ?? 5);
    @endphp

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('buyer.orders.show', $order->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div class="min-w-0">
                <h1 class="font-display font-bold text-2xl text-gray-900">{{ $review ? 'Edit Ulasan Toko' : 'Tulis Ulasan Toko' }}</h1>
                <p class="mt-1 text-sm text-gray-500">Pesanan #{{ $order->invoice_number }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6 mb-5">
            <div class="flex items-center gap-4">
                <img src="{{ $order->store->logo_url }}" alt="{{ $order->store->name }}" class="h-16 w-16 rounded-full border border-gray-100 object-cover shrink-0">
                <div class="min-w-0">
                    <p class="font-bold text-gray-900 truncate">{{ $order->store->name }}</p>
                    <p class="mt-1 text-xs text-gray-500">Nilai pelayanan toko, pengemasan, komunikasi, dan pengalaman belanja.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('buyer.store-reviews.store', $order->id) }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6 space-y-6" x-data="{ rating: {{ $initialRating }} }">
            @csrf
            <input type="hidden" name="rating" x-model="rating">

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Rating Toko</label>
                <div class="grid grid-cols-5 gap-2">
                    @for($rating = 1; $rating <= 5; $rating++)
                        <button type="button" @click="rating = {{ $rating }}" class="h-10 rounded-lg border px-1 text-sm font-bold transition-colors touch-manipulation" :class="rating === {{ $rating }} ? 'border-yellow-400 bg-yellow-100 text-yellow-800' : 'border-gray-200 bg-white text-gray-600 hover:border-yellow-300'">
                            <span class="inline-flex items-center justify-center gap-1">
                                <x-icon name="star" class="w-4 h-4 text-yellow-400" />
                                {{ $rating }}
                            </span>
                        </button>
                    @endfor
                </div>
                <x-input-error :messages="$errors->get('rating')" class="mt-2" />
            </div>

            <div>
                <label for="comment" class="block text-sm font-bold text-gray-900">Komentar</label>
                <textarea id="comment" name="comment" rows="5" maxlength="1000" class="mt-2 w-full rounded-xl border-gray-200 text-sm focus:border-yellow-400 focus:ring-yellow-400" placeholder="Ceritakan pelayanan toko, pengemasan, kecepatan respons, atau pengalaman belanja Anda.">{{ old('comment', $review->comment ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('comment')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <button type="submit" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-brand-blue px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-600 transition-colors">
                    <x-icon name="paper-airplane" class="w-4 h-4" />
                    {{ $review ? 'Perbarui Ulasan Toko' : 'Kirim Ulasan Toko' }}
                </button>
                <a href="{{ route('buyer.orders.show', $order->id) }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-gray-200 px-5 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
