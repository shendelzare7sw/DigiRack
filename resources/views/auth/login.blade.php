<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-8">
        <h1 class="font-display font-bold text-2xl text-brand-navy">Selamat Datang Kembali</h1>
        <p class="text-sm text-gray-500 mt-1">Masuk ke akun DigiRack Anda</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email Address" class="text-gray-700 font-semibold" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon name="user" class="h-5 w-5 text-gray-400" />
                </div>
                <x-text-input id="email" class="block w-full pl-10 border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="contoh@email.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Kata Sandi" class="text-gray-700 font-semibold" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-brand-orange hover:text-orange-600 transition-colors" href="{{ route('password.request') }}">
                        Lupa sandi?
                    </a>
                @endif
            </div>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon name="cog-6-tooth" class="h-5 w-5 text-gray-400" />
                </div>
                <x-text-input id="password" class="block w-full pl-10 border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl"
                                type="password"
                                name="password"
                                required autocomplete="current-password" placeholder="••••••••" />
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

        <div class="mt-8">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-brand-navy hover:bg-brand-navy/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-navy transition-all active:scale-[0.98]">
                Masuk
            </button>
        </div>

        <div class="mt-6 text-center text-sm text-gray-600">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="font-bold text-brand-navy hover:text-brand-orange transition-colors">
                Daftar sekarang
            </a>
        </div>
    </form>
</x-guest-layout>
