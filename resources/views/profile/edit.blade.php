<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        @php
            $backUrl = match(Auth::user()->role) {
                'admin' => route('admin.dashboard'),
                'seller' => route('seller.dashboard'),
                default => route('buyer.dashboard'),
            };
        @endphp
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ $backUrl }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-brand-navy hover:text-white hover:border-brand-navy text-gray-500 transition-all shadow-sm shrink-0 mt-0.5" title="Kembali">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold font-display text-gray-900">Profil Saya</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola informasi akun, alamat, dan keamanan Anda.</p>
            </div>
        </div>

        <div class="space-y-8">
            {{-- Personal Data --}}
            <div class="p-6 sm:p-8 bg-white shadow-sm border border-gray-100 rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Address Management --}}
            <div class="p-6 sm:p-8 bg-white shadow-sm border border-gray-100 rounded-2xl" id="address-section">
                @include('profile.partials.address-management')
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Security --}}
                <div class="p-6 sm:p-8 bg-white shadow-sm border border-gray-100 rounded-2xl">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                {{-- Deletion --}}
                <div class="p-6 sm:p-8 bg-red-50/50 shadow-sm border border-red-100 rounded-2xl">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
