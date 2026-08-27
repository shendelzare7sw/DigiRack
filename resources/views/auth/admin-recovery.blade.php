<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-red-100">
            <x-icon name="shield-check" class="w-7 h-7 text-red-600" />
        </div>
        <h1 class="font-display font-bold text-xl text-gray-900">Pemulihan Akun Administrator</h1>
        <p class="text-xs text-gray-500 mt-1">Verifikasi berlapis diperlukan. Tidak menggunakan email.</p>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.recovery.reset') }}">
        @csrf

        <!-- Identifier -->
        <div class="mb-4">
            <x-input-label for="identifier" value="Email atau Username Admin" class="text-gray-700 font-semibold text-sm" />
            <x-text-input id="identifier" class="block mt-1 w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl text-sm" type="text" name="identifier" :value="old('identifier')" required autofocus placeholder="admin@digihook.com" />
            <x-input-error :messages="$errors->get('identifier')" class="mt-1" />
        </div>

        <!-- Security Question -->
        <div class="mb-4">
            <x-input-label for="security_question" value="Pertanyaan Keamanan" class="text-gray-700 font-semibold text-sm" />
            <x-text-input id="security_question" class="block mt-1 w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl text-sm" type="text" name="security_question" :value="old('security_question')" required placeholder="Masukkan pertanyaan yang Anda set" />
            <x-input-error :messages="$errors->get('security_question')" class="mt-1" />
        </div>

        <!-- Security Answer -->
        <div class="mb-4">
            <x-input-label for="security_answer" value="Jawaban Keamanan" class="text-gray-700 font-semibold text-sm" />
            <x-text-input id="security_answer" class="block mt-1 w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl text-sm" type="password" name="security_answer" required placeholder="Jawaban rahasia Anda" />
            <x-input-error :messages="$errors->get('security_answer')" class="mt-1" />
        </div>

        <!-- Security PIN -->
        <div class="mb-4">
            <x-input-label for="security_pin" value="PIN Keamanan (6 Digit)" class="text-gray-700 font-semibold text-sm" />
            <x-text-input id="security_pin" class="block mt-1 w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl text-sm tracking-[0.5em] text-center" type="password" name="security_pin" required maxlength="6" placeholder="● ● ● ● ● ●" />
            <x-input-error :messages="$errors->get('security_pin')" class="mt-1" />
        </div>

        <hr class="my-5 border-gray-200">

        <!-- New Password -->
        <div class="mb-4">
            <x-input-label for="password" value="Password Baru" class="text-gray-700 font-semibold text-sm" />
            <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl text-sm" type="password" name="password" required placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" class="text-gray-700 font-semibold text-sm" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl text-sm" type="password" name="password_confirmation" required placeholder="Ketik ulang password" />
        </div>

        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl shadow-sm transition-all text-sm">
            Reset Password Administrator
        </button>

        <div class="mt-5 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700 font-semibold">&larr; Kembali ke Login</a>
        </div>
    </form>
</x-guest-layout>
