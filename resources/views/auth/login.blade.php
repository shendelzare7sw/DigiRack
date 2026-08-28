<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-8">
        <h1 class="font-display font-bold text-2xl text-brand-navy">Selamat Datang Kembali</h1>
        <p class="text-sm text-gray-500 mt-1">Masuk ke akun Digital Hook Anda</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Identifier (Username / Email / Phone) -->
        <div>
            <x-input-label for="identifier" value="Username, Email, atau No. Telepon" class="text-gray-700 font-semibold" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon name="user" class="h-5 w-5 text-gray-400" />
                </div>
                <x-text-input id="identifier" class="block w-full pl-10 border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl" type="text" name="identifier" :value="old('identifier')" required autofocus autocomplete="username" placeholder="username / email@contoh.com / 08xxxxxxxxx" />
            </div>
            <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Kata Sandi" class="text-gray-700 font-semibold" />
                <a class="text-xs font-semibold text-brand-blue hover:text-blue-600 transition-colors" href="{{ route('user.recovery.form') }}">
                    Lupa sandi?
                </a>
            </div>
            <div x-data="{ showPw: false }" class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon name="lock-closed" class="h-5 w-5 text-gray-400" />
                </div>
                <input id="password" :type="showPw ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="••••••••"
                    class="block w-full pl-10 pr-10 border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl shadow-sm" />
                <button type="button" @click="showPw = !showPw" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-brand-navy transition-colors focus:outline-none" tabindex="-1">
                    <svg x-show="!showPw" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <svg x-show="showPw" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-5">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-brand-navy shadow-sm focus:ring-brand-navy" name="remember">
                <span class="ms-2 text-sm text-gray-600 group-hover:text-brand-navy transition-colors">Ingat saya</span>
            </label>
        </div>

        @if($turnstileEnabled)
            <div class="mt-5">
                <div class="cf-turnstile w-full"
                    data-sitekey="{{ $turnstileSiteKey }}"
                    data-theme="light"
                    data-size="flexible"
                    data-language="id"
                    data-action="login"></div>
                <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
            </div>
        @endif

        <div class="mt-8">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-brand-navy hover:bg-brand-navy/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-navy transition-all active:scale-[0.98]">
                Masuk
            </button>
        </div>

        <div class="mt-6 text-center text-sm text-gray-600">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="font-bold text-brand-navy hover:text-brand-blue transition-colors">
                Daftar sekarang
            </a>
        </div>
    </form>

    @if($turnstileEnabled)
        @push('head')
            <link rel="preconnect" href="https://challenges.cloudflare.com">
        @endpush
        @push('scripts')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endpush
    @endif
</x-guest-layout>
