<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DigiRack') }} - Login / Register</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            <!-- Decorative Background Background -->
            <div class="absolute inset-0 bg-brand-navy/5 pattern-dots pattern-gray-200 pattern-bg-transparent pattern-size-4 pattern-opacity-10 pointer-events-none"></div>
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-brand-orange/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-brand-navy/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 w-full sm:max-w-md flex flex-col items-center">
                <!-- Logo -->
                <a href="/" class="mb-8 hover:scale-105 transition-transform">
                    <img src="{{ asset('images/logo-digirack.png') }}" alt="DigiRack Logo" class="h-14 w-auto drop-shadow-sm" />
                </a>

                <!-- Card -->
                <div class="w-full px-8 py-10 bg-white shadow-xl sm:rounded-3xl border border-gray-100">
                    {{ $slot }}
                </div>
                
                <div class="mt-8 text-center text-sm font-medium text-gray-400">
                    &copy; {{ date('Y') }} DigiRack Marketplace.<br/>Pusat Infrastruktur Jaringan & IT.
                </div>
            </div>
        </div>
        <x-toast />
    </body>
</html>
