<x-app-layout>
    <x-slot name="title">Upload Dokumen Identitas</x-slot>

    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-brand-navy to-brand-blue p-8 text-center text-white">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <x-icon name="identification" class="w-8 h-8" />
                    </div>
                    <h1 class="text-2xl font-bold font-display">Kirim Dokumen Identitas</h1>
                    <p class="text-white/80 text-sm mt-2">Toko <span class="font-semibold">{{ $store->name }}</span> butuh KTP / ID Card agar admin bisa memverifikasi.</p>
                </div>

                {{-- Form --}}
                <div class="p-8">
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
                    @endif

                    @if($store->verification_status === 'rejected' && $store->verification_notes)
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                            <p class="text-xs font-bold uppercase text-red-700 mb-1">Catatan Penolakan Admin</p>
                            <p class="text-sm text-red-800">{{ $store->verification_notes }}</p>
                        </div>
                    @endif

                    <div class="flex items-center gap-2 mb-5 text-sm">
                        <x-icon name="document-text" class="w-4 h-4 text-gray-400" />
                        <span class="text-gray-500">Dokumen saat ini:</span>
                        @if($store->identity_document_path)
                            <span class="inline-flex items-center gap-1 font-semibold text-green-700">
                                <x-icon name="check-circle" class="w-4 h-4" />
                                Sudah diunggah
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 font-semibold text-red-600">
                                <x-icon name="x-circle" class="w-4 h-4" />
                                Belum tersedia
                            </span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('seller.identity.submit') }}" enctype="multipart/form-data"
                        x-data="{
                            fileName: '',
                            fileType: '',
                            previewUrl: '',
                            updatePreview(event) {
                                const file = event.target.files[0];
                                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                                this.fileName = file ? file.name : '';
                                this.fileType = file ? file.type : '';
                                this.previewUrl = file ? URL.createObjectURL(file) : '';
                            }
                        }"
                        @submit="if (previewUrl) URL.revokeObjectURL(previewUrl)">
                        @csrf

                        <div class="mb-5">
                            <x-input-label for="identity_document" value="Upload KTP / ID Card" class="text-gray-700 font-semibold" />
                            <input id="identity_document" type="file" name="identity_document" required accept=".jpg,.jpeg,.png,.webp,.pdf"
                                @change="updatePreview($event)"
                                class="block mt-1 w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-navylight file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-brand-navy hover:file:bg-brand-navy hover:file:text-white transition-colors border border-gray-300 rounded-xl bg-white">
                            <p class="mt-1 text-[11px] text-gray-500">Format JPG, PNG, WEBP, atau PDF. Maksimal 6MB.</p>
                            <x-input-error :messages="$errors->get('identity_document')" class="mt-2" />

                            <div x-show="previewUrl" x-cloak class="mt-3 rounded-xl border border-gray-200 bg-gray-50 overflow-hidden">
                                <div class="flex items-center justify-between gap-3 px-3 py-2 border-b border-gray-200 bg-white">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-700">Preview dokumen identitas</p>
                                        <p class="text-[11px] text-gray-500 truncate" x-text="fileName"></p>
                                    </div>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-[11px] font-bold text-green-700 border border-green-200">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                        Terpilih
                                    </span>
                                </div>

                                <template x-if="fileType.startsWith('image/')">
                                    <img :src="previewUrl" alt="Preview KTP atau ID Card" class="w-full max-h-72 object-contain bg-slate-100">
                                </template>

                                <template x-if="fileType === 'application/pdf'">
                                    <div class="p-4 flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                                            <x-icon name="document-text" class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">File PDF siap diunggah</p>
                                            <a :href="previewUrl" target="_blank" class="text-xs font-bold text-brand-blue hover:text-blue-700">Buka preview PDF</a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Info card --}}
                        <div class="p-4 bg-orange-50 border border-orange-100 rounded-xl mb-6">
                            <div class="flex gap-3">
                                <x-icon name="exclamation-triangle" class="w-5 h-5 text-orange-500 shrink-0 mt-0.5" />
                                <div class="text-xs text-orange-800 space-y-1">
                                    <p class="font-bold">Informasi Penting:</p>
                                    <ul class="list-disc pl-4 space-y-0.5">
                                        <li>Setelah dikirim, toko Anda <strong>kembali masuk antrean verifikasi Admin</strong>.</li>
                                        <li>Menu penjualan tetap terkunci sampai dokumen disetujui.</li>
                                        <li>Pastikan foto KTP / ID Card jelas dan tidak terpotong.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-brand-navy hover:bg-brand-navydark text-white font-bold py-3 rounded-xl shadow-sm transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <x-icon name="paper-airplane" class="w-5 h-5" />
                            Kirim Dokumen Identitas
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <a href="{{ route('seller.dashboard') }}" class="text-sm text-gray-500 hover:text-brand-navy font-semibold transition-colors">
                            &larr; Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
