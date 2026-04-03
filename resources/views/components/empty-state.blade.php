@props(['title' => 'Tidak ada data', 'description' => 'Belum ada item untuk ditampilkan.', 'icon' => 'cube'])

<div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
        <x-icon :name="$icon" class="w-8 h-8 text-gray-400" />
    </div>
    <h3 class="text-lg font-semibold text-gray-600">{{ $title }}</h3>
    <p class="text-sm text-gray-400 mt-1 max-w-sm">{{ $description }}</p>
    @if($slot->isNotEmpty())
        <div class="mt-4">
            {{ $slot }}
        </div>
    @endif
</div>
