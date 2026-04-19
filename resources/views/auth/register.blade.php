<x-guest-layout>
    <div class="text-center mb-8">
        <h1 class="font-display font-bold text-2xl text-brand-navy">Buat Akun Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Gabung di ekosistem DigiRack — belanja & jualan dalam satu akun</p>
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
            <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl"
                            type="password"
                            name="password"
                            required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" class="text-gray-700 font-semibold" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" placeholder="Ketik ulang sandi" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Info -->
        <div class="mt-5 p-3 bg-blue-50 border border-blue-100 rounded-xl">
            <p class="text-xs text-blue-700 flex items-start gap-2">
                <x-icon name="information-circle" class="w-4 h-4 shrink-0 mt-0.5" />
                <span>Username akan dibuat otomatis oleh sistem. Anda bisa mengubahnya nanti di halaman Profil. Ingin menjadi Penjual? Aktifkan fitur "Buka Toko" setelah registrasi.</span>
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
