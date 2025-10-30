{{-- Bu modal, tamamlanmış bir projeyi reddetmek ve havuza geri göndermek için kullanılır --}}
<x-modal name="reddet-tamamlandi-modal-{{ $iaa->id }}" focusable>
    <form method="post" action="{{ route('admin.iaa-yonetim.tamamlandiReddet', $iaa) }}" class="p-6">
        @csrf
        @method('patch')

        <h2 class="text-lg font-medium text-gray-900">
            Projeyi Reddet ve Havuza Gönder
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            "{{ $iaa->baslik }}" başlıklı projeyi neden reddettiğinizi açıklayın. Proje, takımdan alınarak tekrar "Havuzda" durumuna getirilecektir.
        </p>

        <div class="mt-6">
            <label for="yonetici_notu_reddet_{{ $iaa->id }}" class="sr-only">Reddetme Gerekçesi</label>
            <textarea 
                id="yonetici_notu_reddet_{{ $iaa->id }}" 
                name="yonetici_notu" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                rows="4" 
                placeholder="Reddetme gerekçenizi buraya yazın..." 
                required 
                minlength="10"></textarea>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                İptal
            </x-secondary-button>

            <x-danger-button class="ml-3">
                Projeyi Reddet
            </x-danger-button>
        </div>
    </form>
</x-modal>