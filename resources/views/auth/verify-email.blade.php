<x-guest-layout>
    <div class="text-center mb-8">
        <h1 class="font-display font-bold text-2xl text-brand-navy">Verifikasi Email Anda</h1>
        <p class="text-sm text-gray-500 mt-1">Satu langkah lagi sebelum akun Digital Hook aktif sepenuhnya.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800">
        Kami sudah mengirim link verifikasi ke email Anda. Klik link tersebut untuk mengaktifkan fitur belanja dan buka toko.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            Link verifikasi baru sudah dikirim ke email Anda.
        </div>
    @endif

    <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <button type="submit" class="w-full sm:w-auto inline-flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-brand-navy hover:bg-brand-navy/90 transition-all">
                    Kirim Ulang Email
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="w-full sm:w-auto text-sm font-semibold text-gray-500 hover:text-red-600 transition-colors">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
