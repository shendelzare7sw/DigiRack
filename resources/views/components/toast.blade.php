<div
    x-data="{ show: false, message: '', type: 'success' }"
    x-on:toast.window="message = $event.detail.message; type = $event.detail.type || 'success'; show = true; setTimeout(() => show = false, 3000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="fixed bottom-20 sm:bottom-6 right-4 sm:right-6 z-[60] max-w-sm"
    x-cloak
>
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border"
        x-bind:class="{
            'bg-green-50 border-green-200 text-green-800': type === 'success',
            'bg-red-50 border-red-200 text-red-800': type === 'error',
            'bg-yellow-50 border-yellow-200 text-yellow-800': type === 'warning',
            'bg-blue-50 border-blue-200 text-blue-800': type === 'info'
        }">
        <template x-if="type === 'success'">
            <x-icon name="check-circle" class="w-5 h-5 text-green-500 shrink-0" />
        </template>
        <span class="text-sm font-medium" x-text="message"></span>
        <button x-on:click="show = false" class="ml-auto shrink-0">
            <x-icon name="x-mark" class="w-4 h-4 opacity-60 hover:opacity-100" />
        </button>
    </div>
</div>
