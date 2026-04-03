@props(['value' => 0, 'max' => 5, 'size' => 'w-4 h-4', 'showValue' => false])

<div class="flex items-center gap-0.5">
    @for ($i = 1; $i <= $max; $i++)
        @if ($i <= floor($value))
            <x-icon name="star" class="{{ $size }} text-yellow-400" />
        @elseif ($i - $value < 1 && $i - $value > 0)
            <x-icon name="star" class="{{ $size }} text-yellow-400" />
        @else
            <x-icon name="star-outline" class="{{ $size }} text-gray-300" />
        @endif
    @endfor
    @if ($showValue && $value > 0)
        <span class="ml-1 text-sm font-medium text-gray-600">{{ number_format($value, 1) }}</span>
    @endif
</div>
