<x-app-layout>
    <x-slot name="title">Pencairan Dana Seller (IRIS)</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold font-display text-gray-900">Pencairan Dana (IRIS)</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola permintaan pencairan saldo seller via Midtrans IRIS.</p>
            </div>
            @php $pendingPayouts = $payouts->where('status', 'pending')->count(); @endphp
            @if($pendingPayouts > 0)
                <div class="bg-orange-50 text-orange-600 px-4 py-2 rounded-xl text-sm font-bold border border-orange-100 flex items-center gap-2 shadow-sm shrink-0">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                    </span>
                    {{ $pendingPayouts }} Menunggu
                </div>
            @endif
        </div>

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

        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            @if($payouts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Toko & Rekening</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nominal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fee Platform</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Net Transfer</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($payouts as $p)
                            <tr class="hover:bg-gray-50/50 transition-colors {{ $p->status === 'pending' ? 'bg-orange-50/30' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $p->store->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        <x-icon name="building-library" class="w-3 h-3" />
                                        {{ $p->store->bank_name ?? 'N/A' }} · {{ $p->store->bank_account_no ?? '-' }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">a.n {{ $p->store->bank_account_name ?? $p->store->user->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-red-500 font-semibold">- Rp {{ number_format($p->fee, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-green-600 text-base">Rp {{ number_format($p->net_amount, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = ['pending' => 'yellow', 'completed' => 'green', 'rejected' => 'red'];
                                        $statusLabels = ['pending' => 'Menunggu', 'completed' => 'Berhasil', 'rejected' => 'Ditolak'];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-{{ $statusColors[$p->status] ?? 'gray' }}-100 text-{{ $statusColors[$p->status] ?? 'gray' }}-700 border border-{{ $statusColors[$p->status] ?? 'gray' }}-200">
                                        {{ $statusLabels[$p->status] ?? $p->status }}
                                    </span>
                                    @if($p->iris_reference_no)
                                        <p class="text-[10px] text-gray-400 mt-1.5 font-mono">IRIS: {{ Str::limit($p->iris_reference_no, 25) }}</p>
                                    @endif
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $p->created_at->translatedFormat('d M Y, H:i') }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($p->status === 'pending')
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.payouts.approve', $p->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Approve Pencairan', message: 'Cairkan dana Rp {{ number_format($p->net_amount, 0, ',', '.') }} ke rekening {{ $p->store->bank_name }} {{ $p->store->bank_account_no }}?', type: 'success', confirmText: 'Ya, Cairkan' })">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white font-bold px-4 py-2 rounded-lg text-xs transition-colors shadow-sm">
                                                    <x-icon name="check" class="w-3.5 h-3.5" /> Approve & Transfer
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.payouts.reject', $p->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Tolak Pencairan', message: 'Tolak pencairan ini? Dana akan dikembalikan ke saldo toko.', type: 'danger', confirmText: 'Ya, Tolak' })">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 bg-white border border-red-200 hover:bg-red-50 text-red-600 font-bold px-3 py-2 rounded-lg text-xs transition-colors">
                                                    <x-icon name="x-mark" class="w-3.5 h-3.5" /> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($p->status === 'completed')
                                        <span class="text-xs text-green-600 font-semibold flex items-center justify-end gap-1">
                                            <x-icon name="check-badge" class="w-4 h-4" /> Selesai
                                        </span>
                                    @else
                                        <span class="text-xs text-red-500 font-semibold">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($payouts->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $payouts->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-20">
                    <x-icon name="banknotes" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-bold text-gray-900">Belum Ada Permintaan</h3>
                    <p class="text-gray-500 mt-1">Belum ada seller yang mengajukan pencairan dana.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
