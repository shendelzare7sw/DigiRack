<x-app-layout>
    <x-slot name="title">Kelola Users</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Kelola Users</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola akun pembeli, admin, dan status verifikasi KTP.</p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="font-display font-bold text-2xl text-gray-900">{{ $totalUsers }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Total User</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="font-display font-bold text-2xl text-blue-600">{{ $totalBuyers }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Buyer</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="font-display font-bold text-2xl text-brand-navy">{{ $totalAdmins }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Admin</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="font-display font-bold text-2xl text-yellow-600">{{ $pendingIdentityCount }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">KTP Menunggu</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-2 shadow-sm">
                <x-icon name="check-circle" class="w-5 h-5" /> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-2 shadow-sm">
                <x-icon name="x-circle" class="w-5 h-5" /> {{ session('error') }}
            </div>
        @endif

        {{-- Filter & Search --}}
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau username..." class="flex-1 border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm">

                <select name="role" class="border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm bg-white">
                    <option value="">Semua Role</option>
                    <option value="buyer" {{ request('role') == 'buyer' ? 'selected' : '' }}>Buyer</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>

                <select name="verification" class="border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm bg-white">
                    <option value="">Semua Status KTP</option>
                    <option value="not_submitted" @selected(request('verification') === 'not_submitted')>Belum Mengirim</option>
                    <option value="pending" @selected(request('verification') === 'pending')>Menunggu</option>
                    <option value="verified" @selected(request('verification') === 'verified')>Terverifikasi</option>
                    <option value="rejected" @selected(request('verification') === 'rejected')>Ditolak</option>
                </select>

                <select name="status" class="border-gray-300 focus:border-brand-navy focus:ring-brand-navy rounded-xl text-sm bg-white">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                </select>

                <button type="submit" class="bg-brand-navy hover:bg-brand-navydark text-white px-5 py-2 rounded-xl text-sm font-bold shadow-sm transition-colors">Filter</button>
            </form>
        </div>

        {{-- Data Table --}}
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">KTP</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Bergabung</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-sm">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors {{ !$user->is_active ? 'bg-red-50/30' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full border border-gray-200 object-cover shrink-0">
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            @if($user->username)
                                                <div class="text-[10px] text-gray-400">{{'@'}}{{ $user->username }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $roleColors = ['admin' => 'red', 'buyer' => 'blue'];
                                        $c = $roleColors[$user->role] ?? 'gray';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $c }}-100 text-{{ $c }}-700 border border-{{ $c }}-200 uppercase">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                            <x-icon name="check-circle" class="w-3.5 h-3.5" /> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                            <x-icon name="no-symbol" class="w-3.5 h-3.5" /> Banned
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php $identityStatus = $user->identityVerification?->status ?? 'not_submitted'; @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $identityStatus === 'verified' ? 'bg-green-100 text-green-700' : ($identityStatus === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($identityStatus === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600')) }}">
                                        {{ $user->isAdmin() ? 'Tidak diperlukan' : ($user->identityVerification?->statusLabel() ?? 'Belum Mengirim') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    {{ $user->created_at->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}" class="p-2 bg-white border border-gray-200 hover:border-brand-blue hover:text-brand-blue text-gray-500 rounded-lg" title="Detail & verifikasi KTP"><x-icon name="eye" class="w-4 h-4" /></a>
                                        @if($user->id !== auth()->id())
                                            @if($user->is_active)
                                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Ban User', message: 'User ini akan dibanned dan tidak bisa mengakses platform. Lanjutkan?', type: 'danger', confirmText: 'Ya, Ban User' })">
                                                    @csrf
                                                    <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-red-500 hover:text-red-600 text-gray-500 rounded-lg transition-colors" title="Ban User">
                                                        <x-icon name="no-symbol" class="w-4 h-4" />
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Pulihkan User', message: 'Pulihkan akses user ini?', type: 'success', confirmText: 'Ya, Pulihkan' })">
                                                    @csrf
                                                    <button type="submit" class="p-2 bg-white border border-gray-200 hover:border-green-500 hover:text-green-600 text-red-500 rounded-lg transition-colors" title="Unban User">
                                                        <x-icon name="arrow-path" class="w-4 h-4" />
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-[10px] text-gray-400 italic">Anda</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-20">
                    <x-icon name="users" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-bold text-gray-900">Tidak Ada User</h3>
                    <p class="text-gray-500 mt-1">Tidak ditemukan user dengan kriteria ini.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
