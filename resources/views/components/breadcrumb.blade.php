@props(['items' => []])

<nav class="flex items-center text-sm text-gray-500 font-medium flex-wrap gap-y-1 mb-5" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-brand-navy transition-colors">Beranda</a>
    @foreach($items as $item)
        <x-icon name="chevron-right" class="w-4 h-4 mx-1.5 text-gray-300 shrink-0" />
        @if(!empty($item['url']))
            <a href="{{ $item['url'] }}" class="hover:text-brand-navy transition-colors">{{ $item['label'] }}</a>
        @else
            <span class="text-gray-900 font-semibold truncate max-w-[200px]">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
