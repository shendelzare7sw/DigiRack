<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-14 h-14 bg-brand-bluelight rounded-2xl flex items-center justify-center mx-auto mb-4 border border-blue-100">
            <x-icon name="key" class="w-7 h-7 text-brand-blue" />
        </div>
        <h1 class="font-display font-bold text-xl text-gray-900">Pemulihan Akun</h1>
        <p class="text-sm text-gray-500 mt-1">Cari akun Anda menggunakan email, username, atau nomor telepon.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-4 text-sm flex items-start gap-2">
            <x-icon name="check-circle" class="w-5 h-5 shrink-0 mt-0.5" /> {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="bg-orange-50 border border-orange-200 text-orange-700 p-4 rounded-xl mb-4 text-sm flex items-start gap-2">
            <x-icon name="exclamation-triangle" class="w-5 h-5 shrink-0 mt-0.5" /> {{ session('warning') }}
        </div>
    @endif
    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 p-4 rounded-xl mb-4 text-sm flex items-start gap-2">
            <x-icon name="information-circle" class="w-5 h-5 shrink-0 mt-0.5" /> {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('user.recovery.store') }}">
        @csrf

        <div class="mb-5">
            <x-input-label for="identifier" value="Email, Username, atau No. Telepon" class="text-gray-700 font-semibold" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon name="magnifying-glass" class="h-5 w-5 text-gray-400" />
                </div>
                <x-text-input id="identifier" class="block w-full pl-10 border-gray-300 focus:border-brand-blue focus:ring-brand-blue rounded-xl" type="text" name="identifier" :value="old('identifier')" required autofocus placeholder="contoh@email.com / 08xx / username" />
            </div>
            <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
        </div>

        {{-- How it works --}}
        <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 mb-6">
            <p class="text-xs font-bold text-gray-700 mb-2">Bagaimana cara kerjanya?</p>
            <div class="space-y-2 text-xs text-gray-500">
                <div class="flex items-start gap-2">
                    <span class="bg-green-100 text-green-600 rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold shrink-0">A</span>
                    <span><strong>Jika email tersedia</strong> — Link reset akan dikirim otomatis ke email Anda.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="bg-orange-100 text-orange-600 rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold shrink-0">B</span>
                    <span><strong>Jika email tidak tersedia / gagal</strong> — Tiket pemulihan diteruskan ke Customer Service untuk verifikasi manual.</span>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-brand-blue hover:bg-blue-600 text-white font-bold py-3 rounded-xl shadow-sm transition-all text-sm flex items-center justify-center gap-2">
            <x-icon name="paper-airplane" class="w-5 h-5" />
            Cari & Kirim Pemulihan
        </button>

        <div class="mt-5 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-brand-navy font-semibold transition-colors">&larr; Kembali ke Login</a>
        </div>
    </form>
</x-guest-layout>
