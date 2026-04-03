<x-guest-layout>
    <div class="text-center mb-8">
        <h1 class="font-display font-bold text-2xl text-brand-navy">Buat Akun Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Gabung di ekosistem DigiRack</p>
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
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
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

        <!-- Role Selection -->
        <div class="mt-5 pt-3 border-t border-gray-100">
            <x-input-label value="Daftar sebagai" class="text-gray-700 font-semibold mb-2" />
            <div class="flex gap-4" x-data="{ role: '{{ old('role', 'buyer') }}' }">
                <label class="flex-1 cursor-pointer group">
                    <input type="radio" name="role" value="buyer" x-model="role" class="sr-only peer" />
                    <div class="p-4 text-center rounded-xl border-2 transition-all duration-200 peer-checked:border-brand-navy peer-checked:bg-brand-navylight border-gray-200 group-hover:border-brand-navy/50 relative overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-1 bg-brand-navy transform origin-left transition-transform duration-300 scale-x-0 peer-checked:scale-x-100"></div>
                        <x-icon name="user" class="w-6 h-6 mx-auto mb-2" x-bind:class="role === 'buyer' ? 'text-brand-navy' : 'text-gray-400'" />
                        <span class="text-sm font-bold block" x-bind:class="role === 'buyer' ? 'text-brand-navy' : 'text-gray-600'">Pembeli</span>
                    </div>
                </label>
                <label class="flex-1 cursor-pointer group">
                    <input type="radio" name="role" value="seller" x-model="role" class="sr-only peer" />
                    <div class="p-4 text-center rounded-xl border-2 transition-all duration-200 peer-checked:border-brand-orange peer-checked:bg-brand-orangelight border-gray-200 group-hover:border-brand-orange/50 relative overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-1 bg-brand-orange transform origin-left transition-transform duration-300 scale-x-0 peer-checked:scale-x-100"></div>
                        <x-icon name="building-storefront" class="w-6 h-6 mx-auto mb-2" x-bind:class="role === 'seller' ? 'text-brand-orange' : 'text-gray-400'" />
                        <span class="text-sm font-bold block" x-bind:class="role === 'seller' ? 'text-brand-orange' : 'text-gray-600'">Penjual</span>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="mt-8">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-brand-orange hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-orange transition-all active:scale-[0.98]">
                Daftar Sekarang
            </button>
        </div>
        
        <div class="mt-6 text-center text-sm text-gray-600">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="font-bold text-brand-navy hover:text-brand-orange transition-colors">
                Masuk di sini
            </a>
        </div>
    </form>
</x-guest-layout>
