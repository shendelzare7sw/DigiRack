<x-app-layout>
    <x-slot name="title">Edit Produk — {{ $product->name }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('seller.products.index') }}" class="p-2 text-gray-400 hover:text-brand-navy hover:bg-brand-navylight rounded-xl transition-colors">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display font-bold text-2xl text-gray-900">Edit Produk</h1>
                <p class="text-sm text-gray-500">{{ $product->name }}</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">
                <p class="font-semibold text-sm mb-2">Terdapat kesalahan:</p>
                <ul class="text-sm space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('seller.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Informasi Dasar --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-bold text-lg text-gray-900 mb-5 flex items-center gap-2">
                    <x-icon name="information-circle" class="w-5 h-5 text-brand-navy" />
                    Informasi Produk
                </h2>

                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                            <select name="category_id" id="category_id" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="condition" class="block text-sm font-semibold text-gray-700 mb-1.5">Kondisi <span class="text-red-500">*</span></label>
                            <select name="condition" id="condition" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                                <option value="new" {{ old('condition', $product->condition) === 'new' ? 'selected' : '' }}>Baru</option>
                                <option value="used" {{ old('condition', $product->condition) === 'used' ? 'selected' : '' }}>Bekas</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                            <select name="status" id="status" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                                <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Draf</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi Produk <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="5" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Harga & Stok --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-bold text-lg text-gray-900 mb-5 flex items-center gap-2">
                    <x-icon name="banknotes" class="w-5 h-5 text-brand-orange" />
                    Harga & Stok
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-700 mb-1.5">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" required min="1000"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                    </div>
                    <div>
                        <label for="stock" class="block text-sm font-semibold text-gray-700 mb-1.5">Stok <span class="text-red-500">*</span></label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" required min="0"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                    </div>
                    <div>
                        <label for="weight_gram" class="block text-sm font-semibold text-gray-700 mb-1.5">Berat (gram) <span class="text-red-500">*</span></label>
                        <input type="number" name="weight_gram" id="weight_gram" value="{{ old('weight_gram', $product->weight_gram) }}" required min="1"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                    </div>
                </div>
            </div>

            {{-- Foto Produk Existing --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-bold text-lg text-gray-900 mb-5 flex items-center gap-2">
                    <x-icon name="photo" class="w-5 h-5 text-brand-navy" />
                    Foto Produk
                </h2>

                {{-- Existing Images --}}
                @if($product->images->count() > 0)
                    <p class="text-xs text-gray-500 mb-3">Foto saat ini (centang untuk menghapus):</p>
                    <div class="flex gap-3 flex-wrap mb-5">
                        @foreach($product->images as $img)
                            <label class="relative cursor-pointer group">
                                <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="sr-only peer">
                                <div class="w-24 h-24 rounded-xl overflow-hidden border-2 border-gray-200 peer-checked:border-red-500 peer-checked:opacity-40 transition-all {{ $img->is_primary ? 'ring-2 ring-brand-orange ring-offset-2' : '' }}">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover">
                                </div>
                                @if($img->is_primary)
                                    <span class="absolute bottom-0 inset-x-0 bg-brand-orange text-white text-[9px] font-bold text-center py-0.5 rounded-b-lg">UTAMA</span>
                                @endif
                                <div class="absolute inset-0 bg-red-500/20 rounded-xl hidden peer-checked:flex items-center justify-center">
                                    <x-icon name="trash" class="w-6 h-6 text-red-600" />
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif

                {{-- Upload New Images --}}
                <p class="text-xs text-gray-500 mb-3">Tambah foto baru (opsional):</p>
                <input type="file" name="new_images[]" multiple accept="image/jpg,image/jpeg,image/png,image/webp"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-navylight file:text-brand-navy hover:file:bg-brand-navy hover:file:text-white transition-colors cursor-pointer">
            </div>

            {{-- Spesifikasi --}}
            @php
                $existingSpecs = is_array($product->specs) ? $product->specs : [];
                if (empty($existingSpecs)) $existingSpecs = [['label' => '', 'value' => '']];
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6"
                x-data="{ specs: {{ Js::from($existingSpecs) }} }">
                <h2 class="font-bold text-lg text-gray-900 mb-5 flex items-center gap-2">
                    <x-icon name="clipboard-document-list" class="w-5 h-5 text-brand-navy" />
                    Spesifikasi <span class="text-xs text-gray-400 font-normal">(Opsional)</span>
                </h2>

                <div class="space-y-3">
                    <template x-for="(spec, i) in specs" :key="i">
                        <div class="flex gap-3 items-start">
                            <input type="text" :name="'specs['+i+'][label]'" x-model="spec.label" placeholder="Label"
                                class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                            <input type="text" :name="'specs['+i+'][value]'" x-model="spec.value" placeholder="Nilai"
                                class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20">
                            <button type="button" @click="specs.splice(i, 1)" x-show="specs.length > 1"
                                class="p-2.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors shrink-0">
                                <x-icon name="x-mark" class="w-4 h-4" />
                            </button>
                        </div>
                    </template>
                </div>
                <button type="button" @click="specs.push({ label: '', value: '' })"
                    class="mt-4 inline-flex items-center gap-1.5 text-brand-navy hover:text-brand-navydark text-sm font-semibold transition-colors">
                    <x-icon name="plus-circle" class="w-4 h-4" />
                    Tambah Spesifikasi
                </button>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('seller.products.index') }}" class="text-gray-500 hover:text-gray-700 font-semibold text-sm px-6 py-3 transition-colors">
                    Batal
                </a>
                <button type="submit" class="bg-brand-orange hover:bg-orange-600 text-white font-bold text-sm px-8 py-3 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <x-icon name="check" class="w-4 h-4" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
