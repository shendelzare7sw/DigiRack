<x-app-layout>
    <x-slot name="title">Tiket Pemulihan Akun</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold font-display text-gray-900">Tiket Pemulihan Akun</h1>
                <p class="text-gray-500 text-sm mt-1">Tangani permintaan reset password yang gagal terkirim email atau perlu verifikasi manual Customer Service.</p>
            </div>
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

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @php
                $summary = [
                    ['label' => 'Menunggu CS', 'value' => $counts['pending_admin'] ?? 0, 'class' => 'text-red-600 bg-red-50'],
                    ['label' => 'Email Terkirim', 'value' => $counts['sent'] ?? 0, 'class' => 'text-blue-600 bg-blue-50'],
                    ['label' => 'Selesai', 'value' => $counts['resolved'] ?? 0, 'class' => 'text-green-600 bg-green-50'],
                    ['label' => 'Ditutup', 'value' => $counts['expired'] ?? 0, 'class' => 'text-gray-600 bg-gray-50'],
                ];
            @endphp
            @foreach($summary as $item)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <div class="w-10 h-10 {{ $item['class'] }} rounded-xl flex items-center justify-center mb-3">
                        <x-icon name="lifebuoy" class="w-5 h-5" />
                    </div>
                    <p class="font-display font-bold text-2xl text-gray-900">{{ $item['value'] }}</p>
                    <p class="text-xs text-gray-500 font-medium">{{ $item['label'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <form action="{{ route('admin.recovery-tickets.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, username, atau telepon..." class="w-full border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">
                <select name="status" class="border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm bg-white">
                    <option value="">Semua Status</option>
                    <option value="pending_admin" {{ request('status') === 'pending_admin' ? 'selected' : '' }}>Menunggu CS</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Email Terkirim</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Selesai</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Ditutup</option>
                </select>
                <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm">Filter</button>
            </form>
        </div>

        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            @if($tickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tiket & User</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kontak</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-sm">
                            @foreach($tickets as $ticket)
                                @php
                                    $colors = [
                                        'processing' => 'yellow',
                                        'sent' => 'blue',
                                        'pending_admin' => 'red',
                                        'resolved' => 'green',
                                        'expired' => 'gray',
                                    ];
                                    $labels = [
                                        'processing' => 'Diproses',
                                        'sent' => 'Email Terkirim',
                                        'pending_admin' => 'Menunggu CS',
                                        'resolved' => 'Selesai',
                                        'expired' => 'Ditutup',
                                    ];
                                    $color = $colors[$ticket->status] ?? 'gray';
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors {{ $ticket->status === 'pending_admin' ? 'bg-red-50/20' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">#{{ $ticket->id }} - {{ ucfirst(str_replace('_', ' ', $ticket->tipe_recovery)) }}</div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $ticket->created_at->translatedFormat('d M Y, H:i') }}</div>
                                        <div class="mt-2 flex items-center gap-2">
                                            <img src="{{ $ticket->user->avatar_url }}" alt="{{ $ticket->user->name }}" class="w-8 h-8 rounded-full border border-gray-100">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $ticket->user->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $ticket->user->username }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-500">Email</div>
                                        <div class="font-semibold text-gray-900 break-all">{{ $ticket->user->email ?: '-' }}</div>
                                        <div class="text-xs text-gray-500 mt-2">Telepon</div>
                                        <div class="font-semibold text-gray-900">{{ $ticket->user->phone ?: '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $color }}-100 text-{{ $color }}-700 border border-{{ $color }}-200">
                                            {{ $labels[$ticket->status] ?? $ticket->status }}
                                        </span>
                                        @if($ticket->expires_at)
                                            <div class="text-xs text-gray-400 mt-2">
                                                Kadaluarsa: {{ $ticket->expires_at->translatedFormat('d M Y, H:i') }}
                                            </div>
                                        @endif
                                        @if($ticket->admin_notes)
                                            <div class="text-xs text-gray-500 mt-2 max-w-xs">
                                                Catatan: {{ $ticket->admin_notes }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex flex-col items-end gap-2">
                                            @if($ticket->user->email && in_array($ticket->status, ['pending_admin', 'processing', 'expired']))
                                                <form action="{{ route('admin.recovery-tickets.resend-reset', $ticket->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Kirim Reset Password', message: 'Kirim link reset password sekali pakai ke email user ini?', type: 'info', confirmText: 'Kirim Email' })">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1.5 bg-brand-blue hover:bg-blue-600 text-white font-bold px-3 py-2 rounded-lg text-xs transition-colors">
                                                        <x-icon name="envelope" class="w-3.5 h-3.5" /> Kirim Reset
                                                    </button>
                                                </form>
                                            @endif

                                            @if(!in_array($ticket->status, ['resolved', 'expired']))
                                                <form action="{{ route('admin.recovery-tickets.resolve', $ticket->id) }}" method="POST" class="flex items-center gap-2" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Selesaikan Tiket', message: 'Tandai tiket ini selesai setelah Customer Service memverifikasi user?', type: 'success', confirmText: 'Tandai Selesai' })">
                                                    @csrf
                                                    <input type="text" name="admin_notes" maxlength="1000" placeholder="Catatan opsional" class="w-36 border-gray-300 focus:border-green-400 focus:ring-green-400 rounded-lg text-xs py-2">
                                                    <button type="submit" class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white font-bold px-3 py-2 rounded-lg text-xs transition-colors">
                                                        <x-icon name="check" class="w-3.5 h-3.5" /> Selesai
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.recovery-tickets.expire', $ticket->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Tutup Tiket', message: 'Tutup tiket tanpa tindakan lanjutan?', type: 'danger', confirmText: 'Tutup Tiket' })">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:border-red-400 text-gray-600 hover:text-red-600 font-bold px-3 py-2 rounded-lg text-xs transition-colors">
                                                        <x-icon name="x-mark" class="w-3.5 h-3.5" /> Tutup
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($tickets->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $tickets->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-20">
                    <x-icon name="lifebuoy" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-bold text-gray-900">Tidak ada tiket pemulihan</h3>
                    <p class="text-gray-500 mt-1">Belum ada permintaan pemulihan akun dengan filter ini.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
