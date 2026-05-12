<x-app-layout>
    <x-slot name="title">{{ $title }}</x-slot>

    <style>
        .public-info-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(340px, 0.92fr);
            align-items: stretch;
        }

        .public-info-copy,
        .public-info-side,
        .public-info-card {
            min-width: 0;
        }

        .public-info-title {
            max-width: 760px;
            overflow-wrap: normal;
        }

        .public-info-highlights,
        .public-info-sections {
            display: grid;
            gap: 1.25rem;
        }

        .public-info-sections {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        @media (max-width: 1023px) {
            .public-info-hero {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .public-info-title {
                max-width: none;
            }

            .public-info-sections {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
        $accentMap = [
            'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-brand-blue', 'button' => 'bg-brand-blue hover:bg-blue-600', 'ring' => 'border-blue-100'],
            'navy' => ['bg' => 'bg-brand-navylight', 'text' => 'text-brand-navy', 'button' => 'bg-brand-navy hover:bg-brand-navydark', 'ring' => 'border-brand-navy/10'],
            'orange' => ['bg' => 'bg-orange-50', 'text' => 'text-brand-orange', 'button' => 'bg-brand-orange hover:bg-orange-600', 'ring' => 'border-orange-100'],
            'green' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'button' => 'bg-green-600 hover:bg-green-700', 'ring' => 'border-green-100'],
            'red' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'button' => 'bg-red-600 hover:bg-red-700', 'ring' => 'border-red-100'],
            'gray' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'button' => 'bg-brand-navy hover:bg-brand-navydark', 'ring' => 'border-gray-100'],
        ];
        $colors = $accentMap[$accent ?? 'navy'] ?? $accentMap['navy'];
    @endphp

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-breadcrumb :items="[['label' => $title]]" />

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="public-info-hero">
                <div class="public-info-copy p-6 sm:p-10 lg:p-12">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $colors['bg'] }} {{ $colors['text'] }} border {{ $colors['ring'] }}">
                        {{ $eyebrow }}
                    </span>
                    <h1 class="public-info-title font-display font-bold text-3xl sm:text-4xl lg:text-5xl text-gray-900 mt-5 leading-tight">
                        {{ $heading }}
                    </h1>
                    <p class="text-gray-600 text-base sm:text-lg leading-relaxed mt-5 max-w-3xl">
                        {{ $description }}
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 mt-8">
                        @if(!empty($primaryAction))
                            <a href="{{ $primaryAction['url'] }}" class="inline-flex items-center justify-center gap-2 {{ $colors['button'] }} text-white font-bold px-6 py-3 rounded-xl shadow-sm transition-colors">
                                {{ $primaryAction['label'] }}
                                <x-icon name="arrow-right" class="w-4 h-4" />
                            </a>
                        @endif
                        @if(!empty($secondaryAction))
                            <a href="{{ $secondaryAction['url'] }}" class="inline-flex items-center justify-center gap-2 bg-white border border-gray-200 hover:border-brand-navy hover:text-brand-navy text-gray-700 font-bold px-6 py-3 rounded-xl shadow-sm transition-colors">
                                {{ $secondaryAction['label'] }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="public-info-side {{ $colors['bg'] }} p-6 sm:p-10 lg:p-12 flex items-center">
                    <div class="public-info-highlights w-full">
                        @foreach($highlights as $item)
                            <div class="public-info-card bg-white/85 rounded-2xl border {{ $colors['ring'] }} p-5 shadow-sm">
                                <div class="flex items-start gap-4">
                                    <div class="w-11 h-11 rounded-xl {{ $colors['bg'] }} {{ $colors['text'] }} flex items-center justify-center shrink-0">
                                        <x-icon :name="$item['icon']" class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <h2 class="font-bold text-gray-900">{{ $item['title'] }}</h2>
                                        <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $item['body'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        @if(!empty($sections))
            <section class="public-info-sections mt-6">
                @foreach($sections as $section)
                    <article id="{{ $section['id'] ?? null }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                        <h2 class="font-display font-bold text-xl text-gray-900">{{ $section['title'] }}</h2>
                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed mt-3">{{ $section['body'] }}</p>
                    </article>
                @endforeach
            </section>
        @endif
    </main>
</x-app-layout>
