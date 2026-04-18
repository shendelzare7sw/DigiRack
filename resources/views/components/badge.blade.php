@props(['color' => 'navy', 'size' => 'sm'])

@php
$colors = [
    'navy' => 'bg-brand-navylight text-brand-navy',
    'orange' => 'bg-brand-bluelight text-brand-blue',
    'green' => 'bg-green-100 text-green-700',
    'red' => 'bg-red-100 text-red-700',
    'yellow' => 'bg-yellow-100 text-yellow-700',
    'gray' => 'bg-gray-100 text-gray-600',
];
$sizes = [
    'xs' => 'text-[10px] px-1.5 py-0.5',
    'sm' => 'text-xs px-2 py-0.5',
    'md' => 'text-sm px-2.5 py-1',
];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center font-semibold rounded-full ' . ($colors[$color] ?? $colors['navy']) . ' ' . ($sizes[$size] ?? $sizes['sm'])]) }}>
    {{ $slot }}
</span>
