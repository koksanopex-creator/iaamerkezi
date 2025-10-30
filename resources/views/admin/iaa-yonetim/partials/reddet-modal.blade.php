{{-- Reddet Modal --}}
<x-modal name="reddet-modal-{{ $iaa->id }}" focusable>
    <form 
        method="post" 
        action="{{ route('admin.iaa-yonetim.reddet', $iaa) }}" 
        class="p-6"
        x-data="{ rejectionReason: '', get isDisabled() { return this.rejectionReason.trim().length < 10 } }"
        x-init="$watch('rejectionReason', value => {})"
    >
        @csrf
        @method('patch')
        <h2 class="text-lg font-medium text-gray-900">Öneriyi Reddet</h2>
        <p class="mt-1 text-sm text-gray-600">"{{ $iaa->baslik }}" başlıklı öneriyi reddetmek üzeresiniz. Lütfen gerekçenizi belirtin.</p>
        
        <div class="mt-6">
            <x-input-label for="yonetici_notu_{{ $iaa->id }}" value="Reddetme Gerekçesi" class="sr-only" />
            <textarea 
                id="yonetici_notu_{{ $iaa->id }}" 
                name="yonetici_notu" 
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" 
                placeholder="Reddetme Gerekçesi (Zorunlu)..." 
                required 
                x-model="rejectionReason"
            ></textarea>

            <div class="mt-2 text-sm text-gray-600" :class="{ 'text-red-600': isDisabled, 'text-green-600': !isDisabled }">
                Karakter sayısı: <span x-text="rejectionReason.length"></span> / 10
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">İptal</x-secondary-button>
            
            <button type="submit"
                class="ms-3 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                :disabled="isDisabled"
                :class="{ 'opacity-50 cursor-not-allowed': isDisabled }"
            >
                Öneriyi Reddet
            </button>
        </div>
    </form>
</x-modal>