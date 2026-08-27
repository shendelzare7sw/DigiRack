<x-app-layout>
    <x-slot name="title">Verifikasi KTP</x-slot>

    @php
        $status = $verification?->status ?? 'not_submitted';
        $verified = $status === \App\Models\IdentityVerification::STATUS_VERIFIED;
    @endphp

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white text-gray-500">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-brand-blue">Keamanan transaksi</p>
                <h1 class="text-2xl font-bold font-display text-gray-900">Verifikasi Identitas Pembeli</h1>
                <p class="text-sm text-gray-500 mt-1">Checkout tersedia setelah KTP disetujui admin. Dokumen disimpan privat dan tidak memiliki URL publik.</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[300px_minmax(0,1fr)]">
            <aside class="space-y-5">
                <section class="rounded-2xl border p-5 {{ $verified ? 'border-green-200 bg-green-50' : ($status === 'rejected' ? 'border-red-200 bg-red-50' : 'border-yellow-200 bg-yellow-50') }}">
                    <x-icon name="identification" class="w-10 h-10 {{ $verified ? 'text-green-600' : ($status === 'rejected' ? 'text-red-600' : 'text-yellow-600') }}" />
                    <h2 class="mt-4 font-bold text-gray-900">{{ $verification?->statusLabel() ?? 'Belum Mengirim KTP' }}</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">
                        @if($verified) Identitas disetujui dan akun dapat checkout.
                        @elseif($status === 'pending') Dokumen sedang diperiksa admin.
                        @elseif($status === 'rejected') Periksa catatan lalu kirim ulang KTP.
                        @else Kirim foto KTP untuk memulai pemeriksaan.
                        @endif
                    </p>
                    @if($verification?->review_note)
                        <div class="mt-4 rounded-xl border border-red-200 bg-white p-3 text-xs text-red-700"><strong>Catatan admin:</strong><br>{{ $verification->review_note }}</div>
                    @endif
                </section>

                @if($verification)
                    <section class="rounded-2xl border border-gray-200 bg-white p-5">
                        <h2 class="font-bold text-gray-900">Dokumen tersimpan</h2>
                        <p class="mt-3 text-sm"><span class="block text-xs text-gray-400">Nama pada KTP</span><strong>{{ $verification->legal_name }}</strong></p>
                        <p class="mt-3 text-sm"><span class="block text-xs text-gray-400">NIK</span><strong class="font-mono">{{ $verification->maskedNik() }}</strong></p>
                        <a href="{{ route('profile.identity.document', $verification) }}" target="_blank" class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-brand-blue hover:underline">
                            <x-icon name="eye" class="w-4 h-4" /> Lihat dokumen privat
                        </a>
                    </section>
                @endif
            </aside>

            @if($verified)
                <section class="h-fit rounded-2xl border border-green-200 bg-white p-8 shadow-sm">
                    <div class="flex gap-4"><x-icon name="shield-check" class="w-12 h-12 text-green-600 shrink-0" /><div><h2 class="text-xl font-bold text-gray-900">Verifikasi selesai</h2><p class="mt-2 text-sm leading-6 text-gray-600">Data yang sudah disetujui dikunci untuk mencegah penggantian identitas tanpa pemeriksaan. Hubungi admin bila ada kesalahan.</p><a href="{{ route('products.index') }}" class="mt-5 inline-flex rounded-xl bg-brand-navy px-5 py-3 font-bold text-white">Mulai Belanja</a></div></div>
                </section>
            @else
                <form method="POST" action="{{ route('profile.identity.update') }}" enctype="multipart/form-data" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                    @csrf
                    <h2 class="text-lg font-bold text-gray-900">{{ $verification ? 'Kirim Ulang Dokumen' : 'Data Identitas' }}</h2>
                    <p class="text-xs text-gray-500 mt-1">Isi data sama persis seperti yang tercetak pada KTP.</p>

                    @if($errors->any())
                        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                    @endif

                    <div class="mt-6 space-y-5" x-data="{ preview: null }">
                        <label class="block text-sm font-semibold text-gray-700">Nama lengkap sesuai KTP
                            <input name="legal_name" value="{{ old('legal_name', $verification?->legal_name ?? auth()->user()->name) }}" required maxlength="255" class="mt-1 w-full rounded-xl border-gray-300 focus:border-brand-navy focus:ring-brand-navy">
                        </label>
                        <label class="block text-sm font-semibold text-gray-700">NIK
                            <input name="nik" value="{{ old('nik') }}" required inputmode="numeric" pattern="[0-9]{16}" minlength="16" maxlength="16" autocomplete="off" placeholder="16 digit NIK" class="mt-1 w-full rounded-xl border-gray-300 font-mono tracking-wider focus:border-brand-navy focus:ring-brand-navy">
                            <span class="mt-1 block text-xs font-normal text-gray-400">NIK dienkripsi; hash terpisah mencegah satu NIK dipakai di beberapa akun.</span>
                        </label>
                        <label class="block text-sm font-semibold text-gray-700">Foto KTP
                            <input name="identity_document" type="file" required accept="image/jpeg,image/png,image/webp" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null" class="mt-1 block w-full rounded-xl border border-gray-300 px-3 py-3 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-bluelight file:px-3 file:py-2 file:font-bold file:text-brand-blue">
                            <span class="mt-1 block text-xs font-normal text-gray-400">JPG, PNG, atau WebP; maksimal 5 MB. Seluruh sisi KTP harus terlihat.</span>
                        </label>
                        <img x-cloak x-show="preview" :src="preview" alt="Pratinjau KTP" class="max-h-72 w-full rounded-xl border bg-gray-50 object-contain">
                        <label class="flex items-start gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm"><input type="checkbox" name="consent" value="1" required class="mt-1 rounded border-gray-300 text-brand-blue"><span><strong class="block text-gray-900">Saya menyetujui pemeriksaan identitas</strong><span class="mt-1 block text-xs text-gray-500">KTP digunakan khusus untuk verifikasi akun dan pencegahan transaksi fiktif oleh admin Digital Hook.</span></span></label>
                    </div>

                    <div class="mt-7 flex justify-end"><button class="rounded-xl bg-brand-navy px-5 py-3 font-bold text-white hover:bg-brand-navydark">Kirim ke Admin</button></div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
