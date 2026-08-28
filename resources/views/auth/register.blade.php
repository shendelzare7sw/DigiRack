<x-guest-layout>
    <div class="text-center mb-8">
        <h1 class="font-display font-bold text-2xl text-brand-navy">Buat Akun Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar sebagai pembeli Digital Hook untuk belanja di Tangerang dan sekitarnya.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" class="text-gray-700 font-semibold" />
            <x-text-input id="name" class="block mt-1 w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Email Address" class="text-gray-700 font-semibold" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl" type="email" name="email" :value="old('email')" required autocomplete="email" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone" value="Nomor Telepon" class="text-gray-700 font-semibold" />
            <x-text-input id="phone" class="block mt-1 w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl" type="tel" name="phone" :value="old('phone')" required autocomplete="tel" placeholder="08xxxxxxxxxx" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Kata Sandi" class="text-gray-700 font-semibold" />
            <div x-data="{ showPw: false }" class="relative mt-1">
                <input id="password" :type="showPw ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter"
                    class="block w-full pr-10 border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl shadow-sm" />
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

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" class="text-gray-700 font-semibold" />
            <div x-data="{ showPw: false }" class="relative mt-1">
                <input id="password_confirmation" :type="showPw ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="Ketik ulang sandi"
                    class="block w-full pr-10 border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl shadow-sm" />
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
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Info -->
        <div class="mt-5 p-3 bg-blue-50 border border-blue-100 rounded-xl">
            <p class="text-xs text-blue-700 flex items-start gap-2">
                <x-icon name="information-circle" class="w-4 h-4 shrink-0 mt-0.5" />
                <span>Username akan dibuat otomatis oleh sistem dan dapat diubah nanti melalui halaman Profil.</span>
            </p>
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-brand-blue hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue transition-all active:scale-[0.98]">
                Daftar Sekarang
            </button>
        </div>
        
        <div class="mt-6 text-center text-sm text-gray-600">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="font-bold text-brand-navy hover:text-brand-blue transition-colors">
                Masuk di sini
            </a>
        </div>
    </form>
</x-guest-layout>
