<x-app-layout>
    <x-slot name="title">Review Identitas Toko</x-slot>

    @php
        $verificationStatus = $store->verification_status ?? ($store->is_verified ? 'approved' : 'pending');
        $documentUrl = $store->identity_document_url;
        $documentPath = $store->identity_document_path ?? '';
        $documentExtension = strtolower(pathinfo($documentPath, PATHINFO_EXTENSION));
        $documentIsImage = in_array($documentExtension, ['jpg', 'jpeg', 'png', 'webp'], true);
        $documentIsPdf = $documentExtension === 'pdf';
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
            <div class="flex items-start gap-3">
                <a href="{{ route('admin.stores.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                    <x-icon name="arrow-left" class="w-4 h-4" />
                </a>
                <div>
                    <h1 class="text-2xl font-bold font-display text-gray-900">Review Identitas Toko</h1>
                    <p class="text-gray-500 text-sm mt-1">Periksa dokumen dan data pemilik sebelum mengubah status verifikasi.</p>
                </div>
            </div>

            <a href="{{ route('store.show', $store->slug) }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-xl border border-brand-navy px-4 py-2 text-sm font-bold text-brand-navy hover:bg-brand-navy hover:text-white transition-colors">
                <x-icon name="arrow-top-right-on-square" class="w-4 h-4" />
                Lihat Etalase
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-2 shadow-sm">
                <x-icon name="check-circle" class="w-5 h-5" /> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Dokumen Identitas</h2>
                            <p class="text-sm text-gray-500">KTP atau ID Card yang dikirim saat pendaftaran toko.</p>
                        </div>

                        @if($documentUrl)
                            <a href="{{ $documentUrl }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-brand-navy px-4 py-2 text-sm font-bold text-white hover:bg-brand-navydark transition-colors">
                                <x-icon name="eye" class="w-4 h-4" />
                                Buka File
                            </a>
                        @endif
                    </div>

                    <div class="p-4 sm:p-6">
                        @if($documentUrl && $documentIsImage)
                            <div class="rounded-xl border border-gray-200 bg-slate-100 overflow-hidden">
                                <img src="{{ $documentUrl }}" alt="Dokumen identitas {{ $store->name }}" class="w-full max-h-[620px] object-contain">
                            </div>
                        @elseif($documentUrl && $documentIsPdf)
                            <div class="rounded-xl border border-gray-200 overflow-hidden bg-gray-50">
                                <iframe src="{{ $documentUrl }}" title="Dokumen identitas {{ $store->name }}" class="w-full h-[620px] bg-white"></iframe>
                            </div>
                        @elseif($documentUrl)
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-6 flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl bg-brand-navylight text-brand-navy flex items-center justify-center shrink-0">
                                    <x-icon name="document-text" class="w-7 h-7" />
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">File dokumen tersedia</p>
                                    <p class="text-sm text-gray-500">Preview tidak tersedia untuk format ini. Buka file di tab baru untuk meninjau.</p>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl border border-dashed border-red-200 bg-red-50 p-8 text-center">
                                <x-icon name="identification" class="w-14 h-14 text-red-300 mx-auto mb-3" />
                                <p class="font-bold text-red-700">Dokumen identitas belum tersedia</p>
                                <p class="text-sm text-red-600 mt-1">Toko ini belum memiliki file KTP atau ID Card untuk ditinjau.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Catatan Admin</h2>
                    <form action="{{ route('admin.stores.reject', $store->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Tolak Pengajuan Toko', message: 'Catatan penolakan akan dikirim dan terlihat oleh pemilik toko. Lanjutkan?', type: 'danger', confirmText: 'Ya, Tolak' })">
                        @csrf
                        <textarea name="verification_notes" rows="4" required maxlength="1000" class="w-full border-gray-300 focus:border-red-400 focus:ring-red-400 rounded-xl text-sm" placeholder="Tulis alasan penolakan atau data yang perlu diperbaiki...">{{ old('verification_notes', $verificationStatus === 'rejected' ? $store->verification_notes : '') }}</textarea>
                        <div class="mt-3 flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700 transition-colors">
                                <x-icon name="x-mark" class="w-4 h-4" />
                                Tolak Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">
                    <div class="flex items-start gap-3">
                        <img src="{{ $store->logo_url }}" alt="{{ $store->name }}" class="w-14 h-14 rounded-xl border border-gray-200 object-cover shrink-0">
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold text-gray-900 truncate">{{ $store->name }}</h2>
                            <p class="text-sm text-gray-500 truncate">{{ $store->user->name }}</p>
                        </div>
                    </div>

                    <div class="mt-5">
                        @if($store->is_verified)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1.5 text-xs font-bold text-green-700 border border-green-200">
                                <x-icon name="check-badge" class="w-4 h-4" />
                                Terverifikasi
                            </span>
                        @elseif($verificationStatus === 'rejected')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1.5 text-xs font-bold text-red-700 border border-red-200">
                                <x-icon name="x-circle" class="w-4 h-4" />
                                Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-1.5 text-xs font-bold text-orange-700 border border-orange-200">
                                <x-icon name="clock" class="w-4 h-4" />
                                Menunggu Validasi
                            </span>
                        @endif
                    </div>

                    <dl class="mt-5 space-y-3 text-sm">
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Diajukan</dt>
                            <dd class="font-semibold text-gray-800">{{ optional($store->identity_submitted_at ?? $store->created_at)->translatedFormat('d M Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Email Pemilik</dt>
                            <dd class="font-semibold text-gray-800 break-all">{{ $store->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Nomor Telepon</dt>
                            <dd class="font-semibold text-gray-800">{{ $store->user->phone ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Produk</dt>
                            <dd class="font-semibold text-gray-800">{{ $store->products_count }} produk</dd>
                        </div>
                        @if($store->description)
                            <div>
                                <dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Deskripsi</dt>
                                <dd class="text-gray-700 leading-relaxed">{{ $store->description }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Keputusan</h2>

                    @if(!$store->is_verified)
                        <form action="{{ route('admin.stores.verify', $store->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Loloskan Toko', message: 'Pastikan dokumen identitas dan data pemilik sudah sesuai sebelum meloloskan toko ini.', type: 'success', confirmText: 'Ya, Loloskan' })">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 px-4 py-3 text-sm font-bold text-white hover:bg-green-700 transition-colors">
                                <x-icon name="shield-check" class="w-5 h-5" />
                                Loloskan Toko
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.stores.verify', $store->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Cabut Verifikasi', message: 'Status seller akan dicabut dan toko kembali menunggu verifikasi. Lanjutkan?', type: 'danger', confirmText: 'Cabut Verifikasi' })">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-3 text-sm font-bold text-white hover:bg-orange-700 transition-colors">
                                <x-icon name="x-mark" class="w-5 h-5" />
                                Cabut Verifikasi
                            </button>
                        </form>
                    @endif

                    @if($store->is_verified)
                        <form action="{{ route('admin.stores.toggle', $store->id) }}" method="POST" class="mt-3" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: '{{ $store->is_active ? 'Banned Toko' : 'Pulihkan Toko' }}', message: '{{ $store->is_active ? 'Toko akan disembunyikan dari publik.' : 'Toko akan aktif kembali.' }} Lanjutkan?', type: '{{ $store->is_active ? 'danger' : 'success' }}', confirmText: '{{ $store->is_active ? 'Ya, Banned' : 'Ya, Pulihkan' }}' })">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-bold transition-colors {{ $store->is_active ? 'border-red-200 text-red-700 hover:bg-red-50' : 'border-green-200 text-green-700 hover:bg-green-50' }}">
                                <x-icon :name="$store->is_active ? 'no-symbol' : 'arrow-path'" class="w-5 h-5" />
                                {{ $store->is_active ? 'Banned Toko' : 'Pulihkan Toko' }}
                            </button>
                        </form>
                    @else
                        <p class="mt-3 rounded-xl bg-gray-50 border border-gray-200 px-4 py-3 text-sm text-gray-600">
                            Toko yang belum lolos verifikasi belum bisa diaktifkan. Gunakan keputusan verifikasi di atas terlebih dahulu.
                        </p>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
