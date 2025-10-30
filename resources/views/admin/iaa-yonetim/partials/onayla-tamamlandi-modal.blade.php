{{-- Bu modal, tamamlanmış bir projeyi onaylamak ve puanını takıma eklemek için kullanılır --}}
<x-modal name="onayla-tamamlandi-modal-{{ $iaa->id }}" focusable>
    <form method="post" action="{{ route('admin.iaa-yonetim.onaylaTamamlandi', $iaa) }}" class="p-6">
        @csrf
        @method('patch')

        <h2 class="text-lg font-medium text-gray-900">
            Projeyi Onayla ve Puanı Ekle
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            "{{ $iaa->baslik }}" başlıklı projeyi onaylamak üzeresiniz. Bu işlem, proje puanını ({{ number_format($iaa->puan, 2) }}) takımın hanesine ekleyecek ve projeyi "Tamamlandı" olarak işaretleyecektir.
        </p>

        <div class="mt-6">
            <label for="yonetici_notu_onay_{{ $iaa->id }}" class="block text-sm font-medium text-gray-700">Onay Notu (İsteğe Bağlı)</label>
            <textarea 
                id="yonetici_notu_onay_{{ $iaa->id }}" 
                name="yonetici_notu" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                rows="3" 
                placeholder="Takıma iletmek istediğiniz bir tebrik veya not..."></textarea>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on-click="$dispatch('close')">
                İptal
            </x-secondary-button>

            <x-primary-button class="ml-3 bg-green-600 hover:bg-green-700">
                Onayla ve Tamamla
            </x-primary-button>
        </div>
    </form>
</x-modal>