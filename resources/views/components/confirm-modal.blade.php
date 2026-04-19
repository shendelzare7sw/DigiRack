<div x-data="{ 
        show: false, 
        message: '', 
        title: 'Konfirmasi', 
        formElement: null,
        confirmType: 'danger',
        confirmText: 'Ya, Lanjutkan'
    }" 
    @open-confirm-modal.window="
        show = true; 
        title = $event.detail.title || 'Konfirmasi Tindakan'; 
        message = $event.detail.message; 
        formElement = $event.detail.form;
        confirmType = $event.detail.type || 'danger';
        confirmText = $event.detail.confirmText || 'Ya, Lanjutkan';
    ">
    
    <x-modal show="show" maxWidth="sm">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center bg-red-100 text-red-600" x-show="confirmType === 'danger'">
                    <x-icon name="exclamation-triangle" class="w-6 h-6" />
                </div>
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center bg-blue-100 text-blue-600" x-show="confirmType === 'info'">
                    <x-icon name="information-circle" class="w-6 h-6" />
                </div>
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center bg-green-100 text-green-600" x-show="confirmType === 'success'">
                    <x-icon name="check-circle" class="w-6 h-6" />
                </div>
                <h3 class="text-lg font-bold text-gray-900" x-text="title"></h3>
            </div>
            
            <div class="mt-2" :class="{'pl-14': true}">
                <p class="text-sm text-gray-600 leading-relaxed" x-text="message"></p>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-full font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                    Batal
                </button>
                
                <button type="button" 
                    x-on:click="if(formElement) { formElement.submit(); show = false; }"
                    class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none transition ease-in-out duration-150"
                    x-show="confirmType === 'danger'"
                    x-text="confirmText"
                ></button>

                <button type="button" 
                    x-on:click="if(formElement) { formElement.submit(); show = false; }"
                    class="inline-flex items-center justify-center px-4 py-2 bg-brand-navy border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-blue focus:outline-none transition ease-in-out duration-150"
                    x-show="confirmType === 'info' || confirmType === 'success'"
                    x-text="confirmText"
                ></button>
            </div>
        </div>
    </x-modal>
</div>
