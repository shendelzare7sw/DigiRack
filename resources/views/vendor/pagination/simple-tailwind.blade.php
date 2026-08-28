@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex w-full items-center justify-between gap-3 rounded-2xl border border-blue-100 bg-blue-50/70 p-2 shadow-sm">
        @if ($paginator->onFirstPage())
            <span class="inline-flex min-h-11 items-center gap-2 rounded-xl px-4 text-sm font-semibold text-gray-400" aria-disabled="true">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M11.78 14.78a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 1 1 1.06 1.06L8.06 10l3.72 3.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" /></svg>
                Sebelumnya
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-blue-200 bg-white px-4 text-sm font-bold text-brand-navy shadow-sm transition hover:border-brand-blue hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-brand-blue/30">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M11.78 14.78a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 1 1 1.06 1.06L8.06 10l3.72 3.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" /></svg>
                Sebelumnya
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-blue-200 bg-white px-4 text-sm font-bold text-brand-navy shadow-sm transition hover:border-brand-blue hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-brand-blue/30">
                Berikutnya
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
            </a>
        @else
            <span class="inline-flex min-h-11 items-center gap-2 rounded-xl px-4 text-sm font-semibold text-gray-400" aria-disabled="true">
                Berikutnya
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
            </span>
        @endif
    </nav>
@endif
