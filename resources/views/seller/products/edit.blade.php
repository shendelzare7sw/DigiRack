<x-app-layout>
    <x-slot name="title">Edit Produk — {{ $product->name }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('seller.products.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="font-display font-bold text-2xl text-gray-900">Edit Produk</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $product->name }}</p>
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
                        <div x-data="{ isNew: {{ old('new_category') ? 'true' : 'false' }} }">
                            <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                            
                            <div class="flex gap-2 w-full">
                                <select x-show="!isNew" name="category_id" id="category_id"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20"
                                    :required="!isNew"
                                    @change="if($event.target.value === 'new') { isNew = true; $event.target.value = ''; setTimeout(() => $refs.newCat.focus(), 50); }">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                    <option value="new" class="font-bold text-brand-blue">+ Tambah Kategori Baru</option>
                                </select>
                                
                                <div x-show="isNew" style="display: none;" class="flex w-full gap-2">
                                    <input type="text" x-ref="newCat" name="new_category" placeholder="Nama Kategori Baru"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20"
                                        :required="isNew" value="{{ old('new_category') }}">
                                    <button type="button" @click="isNew = false; $refs.newCat.value = ''" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shrink-0">
                                        Batal
                                    </button>
                                </div>
                            </div>
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
                    <x-icon name="banknotes" class="w-5 h-5 text-brand-blue" />
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
                    <p class="text-xs text-gray-500 mb-3">Foto saat ini:</p>
                    <div class="flex gap-3 flex-wrap mb-5" x-data="{ deletedImages: [] }">
                        @foreach($product->images as $img)
                            <div class="relative w-24 h-24 rounded-xl border-2 border-gray-200 transition-all {{ $img->is_primary ? 'ring-2 ring-brand-blue ring-offset-2' : '' }}" x-show="!deletedImages.includes({{ $img->id }})">
                                <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover rounded-lg">
                                @if($img->is_primary)
                                    <span class="absolute bottom-0 inset-x-0 bg-brand-blue text-white text-[9px] font-bold text-center py-0.5 rounded-b-lg">UTAMA</span>
                                @endif
                                <button type="button" @click="deletedImages.push({{ $img->id }})" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white p-1 rounded-full shadow-sm transition-colors z-10" title="Hapus foto">
                                    <x-icon name="x-mark" class="w-3 h-3" />
                                </button>
                                <template x-if="deletedImages.includes({{ $img->id }})">
                                    <input type="hidden" name="delete_images[]" value="{{ $img->id }}">
                                </template>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Upload New Images --}}
                <p class="text-xs text-gray-500 mb-3">Tambah foto baru (opsional, maks 10MB per file):</p>
                <div x-data="{ previews: [] }" class="space-y-4">
                    <input type="file" x-ref="fileInput" name="new_images[]" multiple accept="image/jpg,image/jpeg,image/png,image/webp"
                        @change="previews = []; for (let f of $event.target.files) { let r = new FileReader(); r.onload = e => { previews.push(e.target.result); previews = [...previews]; }; r.readAsDataURL(f); }"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-navylight file:text-brand-navy hover:file:bg-brand-navy hover:file:text-white transition-colors cursor-pointer">

                    <div x-show="previews.length > 0">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-600">Preview Gambar Baru:</span>
                            <button type="button" @click="$refs.fileInput.value = ''; previews = []" class="text-xs text-red-500 hover:text-red-700 font-semibold transition-colors">
                                Batal Upload
                            </button>
                        </div>
                        <div class="flex gap-3 flex-wrap">
                            <template x-for="(src, i) in previews" :key="i">
                                <div class="w-24 h-24 rounded-xl overflow-hidden border-2 border-brand-blue relative">
                                    <img :src="src" class="w-full h-full object-cover">
                                    <span class="absolute bottom-0 inset-x-0 bg-brand-blue text-white text-[9px] font-bold text-center py-0.5">BARU</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
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
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-stretch sm:items-start relative">
                            <input type="text" :name="'specs['+i+'][label]'" x-model="spec.label" placeholder="Label"
                                class="w-full sm:flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20 min-w-0">
                            <input type="text" :name="'specs['+i+'][value]'" x-model="spec.value" placeholder="Nilai"
                                class="w-full sm:flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-navy focus:ring-brand-navy/20 min-w-0">
                            <button type="button" @click="specs.splice(i, 1)" x-show="specs.length > 1"
                                class="self-end sm:self-auto p-2.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors shrink-0">
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
                <button type="submit" class="bg-brand-blue hover:bg-blue-600 text-white font-bold text-sm px-8 py-3 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <x-icon name="check" class="w-4 h-4" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
