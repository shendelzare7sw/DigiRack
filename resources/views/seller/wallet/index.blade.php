<x-app-layout>
    <x-slot name="title">Saldo & Pencairan Dana</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5" /> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-2">
                <x-icon name="x-circle" class="w-5 h-5" /> {{ session('error') }}
            </div>
        @endif

        {{-- Balance Hero Card --}}
        <div class="bg-gradient-to-br from-brand-navy via-brand-navy to-brand-blue rounded-2xl p-8 text-white mb-8 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full"></div>
            <div class="absolute -bottom-16 -left-16 w-52 h-52 bg-white/5 rounded-full"></div>
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div>
                        <p class="text-white/70 text-sm font-semibold mb-1 flex items-center gap-2">
                            <x-icon name="building-storefront" class="w-4 h-4" /> {{ $store->name }}
                        </p>
                        <p class="text-xs text-white/50 uppercase tracking-wider mb-3">Saldo Tersedia (Escrow Midtrans → Wallet)</p>
                        <p class="text-4xl sm:text-5xl font-bold font-display">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
                        <p class="text-white/60 text-xs mt-2">Dana ini berasal dari pesanan yang telah dikonfirmasi pembeli. Tarik ke rekening bank Anda kapan saja.</p>
                    </div>
                    <div class="shrink-0">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/10">
                            <x-icon name="banknotes" class="w-10 h-10 text-white/70 mx-auto mb-2" />
                            <p class="text-center text-xs text-white/80 font-bold">Escrow System</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alur Infographic --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <x-icon name="arrow-path" class="w-5 h-5 text-brand-navy" /> Alur Dana Anda
            </h3>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-0 text-xs font-semibold">
                <div class="flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2.5 rounded-xl border border-blue-100">
                    <x-icon name="credit-card" class="w-4 h-4" /> Buyer Bayar (Midtrans)
                </div>
                <x-icon name="chevron-right" class="w-4 h-4 text-gray-300 hidden sm:block" />
                <div class="flex items-center gap-2 bg-orange-50 text-orange-700 px-4 py-2.5 rounded-xl border border-orange-100">
                    <x-icon name="lock-closed" class="w-4 h-4" /> Dana Tertahan (Escrow Admin)
                </div>
                <x-icon name="chevron-right" class="w-4 h-4 text-gray-300 hidden sm:block" />
                <div class="flex items-center gap-2 bg-green-50 text-green-700 px-4 py-2.5 rounded-xl border border-green-100">
                    <x-icon name="check-circle" class="w-4 h-4" /> Buyer Konfirmasi Terima
                </div>
                <x-icon name="chevron-right" class="w-4 h-4 text-gray-300 hidden sm:block" />
                <div class="flex items-center gap-2 bg-brand-navylight text-brand-navy px-4 py-2.5 rounded-xl border border-brand-navy/20">
                    <x-icon name="wallet" class="w-4 h-4" /> Masuk Saldo Toko
                </div>
                <x-icon name="chevron-right" class="w-4 h-4 text-gray-300 hidden sm:block" />
                <div class="flex items-center gap-2 bg-purple-50 text-purple-700 px-4 py-2.5 rounded-xl border border-purple-100">
                    <x-icon name="building-library" class="w-4 h-4" /> Tarik ke Rekening
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kiri: Penarikan & Riwayat --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Riwayat Transaksi Wallet --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <x-icon name="clock" class="w-5 h-5 text-brand-navy" />
                        <h3 class="font-bold text-gray-900">Riwayat Mutasi Saldo</h3>
                    </div>
                    @if($transactions->count() > 0)
                        <div class="divide-y divide-gray-50">
                            @foreach($transactions as $t)
                                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ $t->type == 'credit' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}">
                                            <x-icon name="{{ $t->type == 'credit' ? 'arrow-down-tray' : 'arrow-up-tray' }}" class="w-4 h-4" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 line-clamp-1">{{ $t->description }}</p>
                                            <p class="text-xs text-gray-400">{{ $t->created_at->translatedFormat('d M Y, H:i') }} · {{ $t->reference }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-sm {{ $t->type == 'credit' ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $t->type == 'credit' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($transactions->hasPages())
                            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">{{ $transactions->links() }}</div>
                        @endif
                    @else
                        <div class="p-12 text-center">
                            <x-icon name="clock" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                            <p class="text-sm text-gray-500">Belum ada mutasi saldo.</p>
                        </div>
                    @endif
                </div>

                {{-- Riwayat Pencairan --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <x-icon name="banknotes" class="w-5 h-5 text-purple-600" />
                        <h3 class="font-bold text-gray-900">Riwayat Permintaan Pencairan</h3>
                    </div>
                    @if($payouts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Gross → Net</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Fee Admin</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    @foreach($payouts as $p)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3 text-gray-600">{{ $p->created_at->translatedFormat('d M Y') }}</td>
                                        <td class="px-6 py-3">
                                            <span class="text-gray-500 line-through text-xs">Rp {{ number_format($p->amount, 0, ',', '.') }}</span>
                                            <span class="font-bold text-green-600 ml-1">Rp {{ number_format($p->net_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-gray-500">Rp {{ number_format($p->fee, 0, ',', '.') }}</td>
                                        <td class="px-6 py-3">
                                            @php
                                                $colors = ['pending' => 'yellow', 'completed' => 'green', 'rejected' => 'red'];
                                                $labels = ['pending' => 'Menunggu', 'completed' => 'Berhasil', 'rejected' => 'Ditolak'];
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $colors[$p->status] ?? 'gray' }}-100 text-{{ $colors[$p->status] ?? 'gray' }}-700 border border-{{ $colors[$p->status] ?? 'gray' }}-200">
                                                {{ $labels[$p->status] ?? $p->status }}
                                            </span>
                                            @if($p->iris_reference_no)
                                                <p class="text-[10px] text-gray-400 mt-1 font-mono">Ref: {{ Str::limit($p->iris_reference_no, 20) }}</p>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-10 text-center">
                            <p class="text-sm text-gray-500">Anda belum pernah mengajukan pencairan.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kanan: Form Tarik Dana + Info Bank --}}
            <div class="space-y-6">
                {{-- Bank Info --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <x-icon name="building-library" class="w-5 h-5 text-brand-navy" /> Rekening Tujuan
                    </h3>
                    @if($store->bank_name && $store->bank_account_no)
                        <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl p-5 text-white relative overflow-hidden">
                            <div class="absolute top-3 right-4 text-white/20">
                                <x-icon name="credit-card" class="w-10 h-10" />
                            </div>
                            <p class="text-xs text-white/60 uppercase tracking-wider mb-1">{{ $store->bank_name }}</p>
                            <p class="text-lg font-mono font-bold tracking-wider mb-3">{{ $store->bank_account_no }}</p>
                            <p class="text-sm text-white/80">a.n {{ $store->bank_account_name ?? $store->user->name }}</p>
                        </div>
                    @else
                        <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 text-center">
                            <x-icon name="exclamation-triangle" class="w-8 h-8 text-orange-400 mx-auto mb-2" />
                            <p class="text-sm text-orange-800 font-bold">Rekening belum diatur</p>
                            <p class="text-xs text-orange-600 mt-1">Lengkapi data bank di <a href="{{ route('seller.store.show') }}" class="underline font-bold">Profil Toko</a>.</p>
                        </div>
                    @endif
                </div>

                {{-- Withdrawal Form --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <x-icon name="arrow-up-tray" class="w-5 h-5 text-green-600" /> Tarik Dana
                    </h3>

                    @if($wallet->balance >= 10000)
                        <form method="POST" action="{{ route('seller.wallet.payout') }}" onsubmit="return confirm('Yakin ingin mengajukan pencairan dana?');">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Penarikan</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-bold">Rp</span>
                                    <input type="number" name="amount" min="10000" max="{{ $wallet->balance }}" required class="block w-full pl-10 border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm" placeholder="Min. 10.000">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Maksimal: Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
                                <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl shadow-sm transition-all text-sm flex items-center justify-center gap-2">
                                <x-icon name="paper-airplane" class="w-4 h-4" /> Ajukan Pencairan
                            </button>
                        </form>
                        <p class="text-[10px] text-gray-400 mt-3 text-center">Pencairan dikenakan potongan admin fee. Dana dikirim via Midtrans IRIS ke rekening Anda setelah disetujui Admin.</p>
                    @else
                        <div class="text-center py-6">
                            <x-icon name="banknotes" class="w-10 h-10 text-gray-300 mx-auto mb-2" />
                            <p class="text-sm text-gray-500">Saldo minimum Rp 10.000 untuk penarikan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
