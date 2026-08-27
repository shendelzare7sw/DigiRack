<x-app-layout>
    <x-slot name="title">Sitemap</x-slot>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-breadcrumb :items="[['label' => 'Sitemap']]" />

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-10">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-brand-navylight text-brand-navy border border-brand-navy/10">
                Navigasi
            </span>
            <h1 class="font-display font-bold text-3xl sm:text-4xl text-gray-900 mt-5 leading-tight">
                Sitemap Digital Hook
            </h1>
            <p class="text-gray-600 text-base sm:text-lg leading-relaxed mt-4 max-w-3xl">
                Daftar halaman utama Digital Hook untuk menemukan katalog, akun, bantuan, dan dokumen legal.
            </p>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
            @foreach($groups as $title => $links)
                <article class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-display font-bold text-xl text-gray-900">{{ $title }}</h2>
                    <ul class="mt-4 space-y-3">
                        @foreach($links as $link)
                            <li>
                                <a href="{{ $link['url'] }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-brand-navy transition-colors">
                                    <x-icon name="arrow-right" class="w-4 h-4 text-brand-blue" />
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </section>
    </main>
</x-app-layout>
