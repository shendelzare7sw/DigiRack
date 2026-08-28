@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="w-full">
        {{-- Mobile pagination keeps the context visible without overflowing narrow screens. --}}
        <div class="sm:hidden">
            <p class="mb-3 text-center text-xs font-medium text-gray-500">
                Menampilkan <span class="font-bold text-gray-700">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</span>
                dari <span class="font-bold text-gray-700">{{ $paginator->total() }}</span> hasil
            </p>

            <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-2 rounded-2xl border border-blue-100 bg-blue-50/70 p-2 shadow-sm">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex min-h-11 items-center justify-center gap-1.5 rounded-xl px-3 text-sm font-semibold text-gray-400" aria-disabled="true">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M11.78 14.78a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 1 1 1.06 1.06L8.06 10l3.72 3.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" /></svg>
                        <span class="hidden min-[360px]:inline">Kembali</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex min-h-11 items-center justify-center gap-1.5 rounded-xl border border-blue-200 bg-white px-3 text-sm font-bold text-brand-navy shadow-sm transition hover:border-brand-blue hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-brand-blue/30">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M11.78 14.78a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 1 1 1.06 1.06L8.06 10l3.72 3.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" /></svg>
                        <span class="hidden min-[360px]:inline">Kembali</span>
                    </a>
                @endif

                <span class="whitespace-nowrap rounded-xl bg-gradient-to-br from-brand-blue to-blue-600 px-3 py-2.5 text-center text-xs font-bold text-white shadow-sm shadow-blue-200">
                    {{ $paginator->currentPage() }} <span class="font-medium text-blue-200">/ {{ $paginator->lastPage() }}</span>
                </span>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex min-h-11 items-center justify-center gap-1.5 rounded-xl border border-blue-200 bg-white px-3 text-sm font-bold text-brand-navy shadow-sm transition hover:border-brand-blue hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-brand-blue/30">
                        <span class="hidden min-[360px]:inline">Lanjut</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                    </a>
                @else
                    <span class="inline-flex min-h-11 items-center justify-center gap-1.5 rounded-xl px-3 text-sm font-semibold text-gray-400" aria-disabled="true">
                        <span class="hidden min-[360px]:inline">Lanjut</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                    </span>
                @endif
            </div>
        </div>

        {{-- Desktop pagination exposes page numbers and a compact results summary. --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between sm:gap-5">
            <p class="text-sm text-gray-500">
                Menampilkan <span class="font-bold text-gray-800">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</span>
                dari <span class="font-bold text-gray-800">{{ $paginator->total() }}</span> hasil
            </p>

            <div class="inline-flex items-center gap-1 rounded-2xl border border-blue-100 bg-white p-1.5 shadow-sm">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-gray-300" aria-disabled="true" aria-label="Halaman sebelumnya">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M11.78 14.78a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 1 1 1.06 1.06L8.06 10l3.72 3.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" /></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-gray-500 transition hover:bg-blue-50 hover:text-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/30" aria-label="Halaman sebelumnya">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M11.78 14.78a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 1 1 1.06 1.06L8.06 10l3.72 3.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" /></svg>
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex h-10 min-w-8 items-center justify-center px-1 text-sm text-gray-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-blue to-blue-600 px-3 text-sm font-bold text-white shadow-sm shadow-blue-200 ring-2 ring-blue-100">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl px-3 text-sm font-semibold text-gray-600 transition hover:bg-blue-50 hover:text-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/30" aria-label="Buka halaman {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-gray-500 transition hover:bg-blue-50 hover:text-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/30" aria-label="Halaman berikutnya">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                    </a>
                @else
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-gray-300" aria-disabled="true" aria-label="Halaman berikutnya">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
