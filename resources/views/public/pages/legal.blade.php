<x-app-layout>
    <x-slot name="title">{{ $title }}</x-slot>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-breadcrumb :items="[['label' => $title]]" />

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-10">
            <div class="max-w-3xl">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-brand-navylight text-brand-navy border border-brand-navy/10">
                    {{ $eyebrow }}
                </span>
                <h1 class="font-display font-bold text-3xl sm:text-4xl text-gray-900 mt-5 leading-tight">
                    {{ $heading }}
                </h1>
                <p class="text-gray-600 text-base sm:text-lg leading-relaxed mt-4">
                    {{ $description }}
                </p>
                <p class="text-xs text-gray-400 mt-4">Terakhir diperbarui: {{ $updatedAt }}</p>
            </div>
        </section>

        <section class="mt-6 space-y-4">
            @foreach($sections as $section)
                <article class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                    <h2 class="font-display font-bold text-xl text-gray-900">{{ $section['title'] }}</h2>
                    <ul class="mt-4 space-y-3 text-sm sm:text-base text-gray-600 leading-relaxed">
                        @foreach($section['items'] as $item)
                            <li class="flex gap-3">
                                <span class="mt-2 h-1.5 w-1.5 rounded-full bg-brand-blue shrink-0"></span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </section>
    </main>
</x-app-layout>
