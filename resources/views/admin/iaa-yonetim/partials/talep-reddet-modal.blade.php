{{-- Talep Reddet Modal --}}
<x-modal name="talep-reddet-modal-{{ $iaa->id }}" focusable>
    <form method="post" action="{{ route('admin.iaa-yonetim.talepReddet', $iaa) }}" class="p-6">
        @csrf
        @method('patch')
        <h2 class="text-lg font-medium text-gray-900">Talebi Reddet</h2>
        <p class="mt-1 text-sm text-gray-600">"{{ $iaa->atanan->name ?? 'Kullanıcı' }}" kullanıcısının talebini reddetmek üzeresiniz. Öneri havuza geri dönecektir.</p>
        <div class="mt-6">
            <x-input-label for="yonetici_notu_talep_{{ $iaa->id }}" value="Talep Reddetme Gerekçesi" class="sr-only" />
            <textarea id="yonetici_notu_talep_{{ $iaa->id }}" name="yonetici_notu" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" placeholder="Talep Reddetme Gerekçesi (Zorunlu)..." required></textarea>
        </div>
        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">İptal</x-secondary-button>
            <x-danger-button class="ms-3">Talebi Reddet</x-danger-button>
        </div>
    </form>
</x-modal>