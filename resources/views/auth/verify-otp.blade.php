<x-guest-layout>
    <div class="text-center mb-8">
        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-blue-50 flex items-center justify-center">
            <x-icon name="envelope-open" class="w-7 h-7 text-brand-blue" />
        </div>
        <h1 class="font-display font-bold text-2xl text-brand-navy">Verifikasi Email</h1>
        <p class="text-sm text-gray-500 mt-2">
            Kami mengirim kode OTP 6 digit ke<br>
            <span class="font-semibold text-gray-700">{{ $email }}</span>
        </p>
    </div>

    @if (session('status'))
        <div class="mb-5 p-3 bg-green-50 border border-green-100 rounded-xl text-xs text-green-700 flex items-start gap-2">
            <x-icon name="check-circle" class="w-4 h-4 shrink-0 mt-0.5" />
            <span>{{ session('status') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-5 p-3 bg-red-50 border border-red-100 rounded-xl text-xs text-red-700 flex items-start gap-2">
            <x-icon name="exclamation-triangle" class="w-4 h-4 shrink-0 mt-0.5" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('register.otp.verify') }}">
        @csrf
        <div>
            <x-input-label for="code" value="Kode OTP" class="text-gray-700 font-semibold" />
            <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                maxlength="6" pattern="\d{6}" required autofocus placeholder="••••••"
                oninput="this.value=this.value.replace(/\D/g,'')"
                class="block mt-1 w-full text-center text-2xl font-bold tracking-[0.5em] border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl shadow-sm py-3" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-brand-blue hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue transition-all active:scale-[0.98]">
                Verifikasi & Buat Akun
            </button>
        </div>
    </form>

    <div class="mt-6 text-center" x-data="{ wait: {{ (int) $canResendIn }} }" x-init="if (wait > 0) { let t = setInterval(() => { wait--; if (wait <= 0) clearInterval(t); }, 1000) }">
        <form method="POST" action="{{ route('register.otp.resend') }}">
            @csrf
            <p class="text-sm text-gray-600">
                Tidak menerima kode?
                <button type="submit" x-bind:disabled="wait > 0"
                    class="font-bold text-brand-navy hover:text-brand-blue transition-colors disabled:text-gray-400 disabled:cursor-not-allowed">
                    <span x-show="wait > 0">Kirim ulang (<span x-text="wait"></span>s)</span>
                    <span x-show="wait <= 0">Kirim ulang kode</span>
                </button>
            </p>
        </form>
    </div>

    <div class="mt-6 text-center text-sm text-gray-600">
        Salah data?
        <a href="{{ route('register') }}" class="font-bold text-brand-navy hover:text-brand-blue transition-colors">
            Daftar ulang
        </a>
    </div>
</x-guest-layout>
