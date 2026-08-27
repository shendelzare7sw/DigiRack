<x-app-layout>
    <x-slot name="title">Detail Pembeli</x-slot>
    @php
        $verification = $user->identityVerification;
        $status = $verification?->status ?? 'not_submitted';
    @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white text-gray-500"><x-icon name="arrow-left" class="w-4 h-4" /></a>
            <div><p class="text-xs font-bold uppercase tracking-wider text-brand-blue">Detail Akun</p><h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1><p class="text-sm text-gray-500">{{ $user->email }} · Bergabung {{ $user->created_at->translatedFormat('d F Y') }}</p></div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4 border-b border-gray-100 pb-5"><div><h2 class="font-bold text-gray-900">Pemeriksaan KTP</h2><p class="text-xs text-gray-500">Dokumen privat hanya dapat dilihat pemilik akun dan admin.</p></div><span class="rounded-full px-3 py-1.5 text-xs font-bold {{ $status === 'verified' ? 'bg-green-100 text-green-700' : ($status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600')) }}">{{ $verification?->statusLabel() ?? 'Belum Mengirim' }}</span></div>

                @if($verification)
                    <dl class="mt-5 grid gap-4 rounded-xl bg-gray-50 p-4 text-sm sm:grid-cols-2"><div><dt class="text-xs text-gray-400">Nama sesuai KTP</dt><dd class="mt-1 font-bold">{{ $verification->legal_name }}</dd></div><div><dt class="text-xs text-gray-400">NIK</dt><dd class="mt-1 font-mono font-bold">{{ $verification->nik }}</dd></div><div><dt class="text-xs text-gray-400">Dikirim</dt><dd class="mt-1 font-bold">{{ $verification->submitted_at->format('d/m/Y H:i') }}</dd></div><div><dt class="text-xs text-gray-400">Ditinjau</dt><dd class="mt-1 font-bold">{{ $verification->reviewed_at?->format('d/m/Y H:i') ?? '-' }}</dd></div></dl>
                    <a href="{{ route('profile.identity.document', $verification) }}" target="_blank" class="mt-5 inline-flex items-center gap-2 rounded-xl border border-brand-blue px-4 py-2.5 text-sm font-bold text-brand-blue hover:bg-brand-bluelight"><x-icon name="eye" class="w-4 h-4" /> Buka Foto KTP</a>
                    @if($verification->review_note)<div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"><strong>Catatan sebelumnya:</strong><br>{{ $verification->review_note }}</div>@endif

                    @if($status === 'pending')
                        <div class="mt-6 grid gap-4 border-t border-gray-100 pt-5 md:grid-cols-2">
                            <form method="POST" action="{{ route('admin.users.identity.approve', $user) }}" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Setujui KTP?', message: 'Pastikan nama, NIK, dan foto KTP cocok.', type: 'success', confirmText: 'Ya, Setujui' })">@csrf @method('PATCH')<button class="w-full rounded-xl bg-green-600 px-5 py-3 font-bold text-white hover:bg-green-700">Setujui KTP</button></form>
                            <form method="POST" action="{{ route('admin.users.identity.reject', $user) }}">@csrf @method('PATCH')<textarea name="review_note" rows="3" required minlength="10" maxlength="1000" placeholder="Jelaskan alasan penolakan..." class="w-full rounded-xl border-red-200 text-sm focus:border-red-500 focus:ring-red-500"></textarea><button class="mt-2 w-full rounded-xl bg-red-600 px-5 py-3 font-bold text-white hover:bg-red-700">Tolak & Minta Perbaikan</button></form>
                        </div>
                    @endif
                @else
                    <div class="py-16 text-center text-gray-500"><x-icon name="identification" class="mx-auto w-14 h-14 text-gray-300" /><p class="mt-3 font-semibold">Pembeli belum mengirim KTP.</p></div>
                @endif
            </section>

            <aside class="space-y-5">
                <section class="rounded-2xl border border-gray-200 bg-white p-5"><h2 class="font-bold text-gray-900">Informasi Akun</h2><dl class="mt-4 space-y-4 text-sm"><div><dt class="text-xs text-gray-400">Role</dt><dd class="font-bold uppercase">{{ $user->role }}</dd></div><div><dt class="text-xs text-gray-400">Nomor HP</dt><dd class="font-bold">{{ $user->phone ?? '-' }}</dd></div><div><dt class="text-xs text-gray-400">Alamat</dt><dd class="font-bold">{{ $user->addresses->count() }}</dd></div><div><dt class="text-xs text-gray-400">Pesanan</dt><dd class="font-bold">{{ $user->orders->count() }}</dd></div></dl></section>
                <section class="rounded-2xl border border-yellow-200 bg-yellow-50 p-5 text-xs leading-5 text-yellow-800"><strong>Data pribadi sensitif</strong><p class="mt-2">Gunakan NIK dan foto KTP hanya untuk pemeriksaan identitas. Jangan mengunduh atau membagikannya ke kanal lain.</p></section>
            </aside>
        </div>
    </div>
</x-app-layout>
