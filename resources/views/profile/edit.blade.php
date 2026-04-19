<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
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
