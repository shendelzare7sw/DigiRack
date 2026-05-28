<x-app-layout>
    <x-slot name="title">{{ $review ? 'Edit Ulasan Toko' : 'Tulis Ulasan Toko' }}</x-slot>

    @php
        $initialRating = (int) old('rating', $review->rating ?? 5);
    @endphp

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('buyer.orders.show', $order->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div class="min-w-0">
                <h1 class="font-display font-bold text-2xl text-gray-900">{{ $review ? 'Edit Ulasan Toko' : 'Tulis Ulasan Toko' }}</h1>
                <p class="mt-1 text-sm text-gray-500">Pesanan #{{ $order->invoice_number }}</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-bold mb-1">Ada data yang perlu diperbaiki.</p>
                <ul class="list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('buyer.store-reviews.store', $order->id) }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6 space-y-6" x-data="{ rating: {{ $initialRating }} }">
                    @csrf
                    <input type="hidden" name="rating" x-model="rating">

                    <div>
                        <p class="text-sm font-bold text-gray-900 mb-3">Rating Toko</p>
                        <div style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 0.5rem;">
                            @for($rating = 1; $rating <= 5; $rating++)
                                <button type="button" @click="rating = {{ $rating }}" class="h-10 rounded-lg border px-1 text-sm font-bold transition-colors touch-manipulation" :class="rating === {{ $rating }} ? 'border-yellow-400 bg-yellow-100 text-yellow-800' : 'border-gray-200 bg-white text-gray-600 hover:border-yellow-300'">
                                    <span class="flex items-center justify-center gap-0.5">
                                        <x-icon name="star" class="w-3.5 h-3.5 text-yellow-500" />
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
                        <a href="{{ route('buyer.orders.show', $order->id) }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-600 hover:border-gray-300 hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-24">
                    <img src="{{ $order->store->logo_url }}" alt="{{ $order->store->name }}" class="w-full aspect-square rounded-xl border border-gray-100 object-cover bg-gray-50">
                    <h2 class="mt-4 font-bold text-gray-900 leading-snug">{{ $order->store->name }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $order->store->is_verified ? 'Toko terverifikasi' : 'Toko belum terverifikasi' }}</p>
                    <p class="mt-3 text-xs leading-relaxed text-gray-400">Nilai pelayanan toko, pengemasan, komunikasi, dan pengalaman belanja.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
