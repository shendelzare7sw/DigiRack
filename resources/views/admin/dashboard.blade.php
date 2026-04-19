<x-app-layout>
    <x-slot name="title">Dashboard Admin</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Welcome Header --}}
        <div class="mb-8">
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-gray-900">
                Dashboard Admin
            </h1>
            <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ Auth::user()->name }}. Berikut ringkasan sistem DigiRack Enterprise.</p>
        </div>

        {{-- System Stats --}}
        @php
            extract($stats);
            $chartDatesJson = json_encode($chartDates);
            $chartRevenuesJson = json_encode($chartRevenues);
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="w-10 h-10 bg-brand-navylight rounded-xl flex items-center justify-center text-brand-navy mb-3">
                    <x-icon name="users" class="w-5 h-5" />
                </div>
                <p class="font-display font-bold text-2xl text-gray-900">{{ $totalUsers }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Total User</p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $totalBuyers }} buyer • {{ $totalSellers }} seller</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-500 mb-3">
                    <x-icon name="building-storefront" class="w-5 h-5" />
                </div>
                <p class="font-display font-bold text-2xl text-gray-900">{{ $totalStores }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Total Toko</p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $verifiedStores }} terverifikasi</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="w-10 h-10 bg-brand-bluelight rounded-xl flex items-center justify-center text-brand-blue mb-3">
                    <x-icon name="cube" class="w-5 h-5" />
                </div>
                <p class="font-display font-bold text-2xl text-gray-900">{{ $totalProducts }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Total Produk</p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $activeProducts }} aktif</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600 mb-3">
                    <x-icon name="tag" class="w-5 h-5" />
                </div>
                <p class="font-display font-bold text-2xl text-gray-900">{{ $totalCategories }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Kategori</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 mb-3">
                    <x-icon name="banknotes" class="w-5 h-5" />
                </div>
                <p class="font-display font-bold text-lg text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1" title="Gross Merchandise Value (GMV)">Total Transaksi (GMV)</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Perputaran dari {{ $totalOrders }} pesanan</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Admin Menu --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-lg text-gray-900 mb-5 flex items-center gap-2">
                        <x-icon name="squares-2x2" class="w-5 h-5 text-brand-navy" />
                        Menu Administrasi
                    </h2>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        {{-- Lihat Katalog (Live) --}}
                        <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-brand-navy/30 hover:shadow-sm transition-all group">
                            <div class="w-12 h-12 bg-brand-bluelight rounded-xl flex items-center justify-center text-brand-blue group-hover:bg-brand-blue group-hover:text-white transition-colors">
                                <x-icon name="cube" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-semibold text-gray-700 text-center">Lihat Katalog</span>
                        </a>

                        {{-- Pengaturan Sistem (Live) --}}
                        <a href="{{ route('admin.settings.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-brand-navy/30 hover:shadow-sm transition-all group">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 group-hover:bg-gray-800 group-hover:text-white transition-colors">
                                <x-icon name="cog-6-tooth" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-semibold text-gray-700 text-center">Pengaturan Sistem</span>
                        </a>

                        {{-- Kelola Toko (Live) --}}
                        <a href="{{ route('admin.stores.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-purple-100 bg-purple-50 hover:bg-purple-100 hover:shadow-sm transition-all group">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-purple-600 shadow-sm">
                                <x-icon name="building-storefront" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-bold text-purple-900 text-center">Kelola Toko</span>
                        </a>

                        {{-- Pencairan Dana (Live) --}}
                        <a href="{{ route('admin.payouts.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-green-300 hover:shadow-sm transition-all group">
                            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors">
                                <x-icon name="banknotes" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-semibold text-gray-700 text-center">Pencairan Dana</span>
                        </a>

                        {{-- Biaya Transaksi (Live) --}}
                        <a href="{{ route('admin.transaction_fees.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-orange-300 hover:shadow-sm transition-all group">
                            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500 group-hover:bg-orange-500 group-hover:text-white transition-colors">
                                <x-icon name="receipt-percent" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-semibold text-gray-700 text-center">Biaya Transaksi</span>
                        </a>

                        {{-- Coming Soon Items --}}
                        @php
                            $comingSoon = [
                                ['icon' => 'users', 'label' => 'Kelola Users', 'color' => 'brand-navy', 'bg' => 'brand-navylight'],
                                ['icon' => 'clipboard-document-check', 'label' => 'Moderasi Produk', 'color' => 'teal-600', 'bg' => 'teal-50'],
                                ['icon' => 'tag', 'label' => 'Kelola Kategori', 'color' => 'indigo-600', 'bg' => 'indigo-50'],
                                ['icon' => 'clipboard-document-list', 'label' => 'Semua Pesanan', 'color' => 'blue-500', 'bg' => 'blue-50'],
                                ['icon' => 'megaphone', 'label' => 'Kelola Banner', 'color' => 'pink-500', 'bg' => 'pink-50'],
                                ['icon' => 'fire', 'label' => 'Flash Sale', 'color' => 'red-500', 'bg' => 'red-50'],
                            ];
                        @endphp
                        @foreach($comingSoon as $item)
                            <div class="flex flex-col items-center gap-2 p-4 rounded-xl border border-dashed border-gray-200 opacity-50">
                                <div class="w-12 h-12 bg-{{ $item['bg'] }} rounded-xl flex items-center justify-center text-{{ $item['color'] }}">
                                    <x-icon name="{{ $item['icon'] }}" class="w-6 h-6" />
                                </div>
                                <span class="text-xs font-semibold text-gray-400 text-center">{{ $item['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Chart Area --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-6">
                    <h2 class="font-bold text-lg text-gray-900 mb-2 flex items-center gap-2">
                        <x-icon name="chart-bar" class="w-5 h-5 text-brand-navy" />
                        Tren Pertumbuhan Transaksi (30 Hari)
                    </h2>
                    <p class="text-xs text-gray-500 mb-6">Gross Merchandise Value (GMV) Platform berdasarkan order yang sukses dibayar.</p>
                    
                    <div id="revenueChart" class="w-full h-80"></div>
                </div>
            </div>

            {{-- Recent Users & System Info --}}
            <div class="space-y-6">
                {{-- Recent Users --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-base text-gray-900 mb-4 flex items-center gap-2">
                        <x-icon name="user-plus" class="w-5 h-5 text-brand-navy" />
                        User Terbaru
                    </h2>
                    @php
                        $recentUsers = \App\Models\User::latest()->take(5)->get();
                    @endphp
                    <div class="space-y-3">
                        @foreach($recentUsers as $user)
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full border border-gray-100">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full uppercase
                                    {{ $user->role === 'admin' ? 'bg-red-50 text-red-600' : ($user->role === 'seller' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600') }}">
                                    {{ $user->role }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- System Info --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-base text-gray-900 mb-4 flex items-center gap-2">
                        <x-icon name="server" class="w-5 h-5 text-brand-navy" />
                        Informasi Sistem
                    </h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Laravel</span>
                            <span class="font-semibold text-gray-900">v{{ app()->version() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">PHP</span>
                            <span class="font-semibold text-gray-900">v{{ PHP_VERSION }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Environment</span>
                            <span class="font-semibold text-gray-900">{{ app()->environment() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Timezone</span>
                            <span class="font-semibold text-gray-900">{{ config('app.timezone') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [{
                    name: 'GMV (Rp)',
                    data: {!! $chartRevenuesJson !!}
                }],
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#0d2757'], // Brand Navy
                fill: {
                    type: "gradient",
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: {
                    categories: {!! $chartDatesJson !!},
                    tooltip: { enabled: false },
                    labels: { style: { colors: '#9CA3AF' } }
                },
                yaxis: {
                    labels: { 
                        formatter: function (val) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                        },
                        style: { colors: '#6B7280' }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 4,
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function (val) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#revenueChart"), options);
            chart.render();
        });
    </script>
    @endpush
</x-app-layout>
