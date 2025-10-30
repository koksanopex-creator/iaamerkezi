<x-modal name="talep-et-modal-{{ $iaa->id }}" focusable>
    <form method="post" action="{{ route('iaa.takimlaTalepEt', $iaa) }}" class="p-6">
        @csrf

        <h2 class="text-lg font-medium text-gray-900">
            "{{ Str::limit($iaa->baslik, 40) }}" için Talep Oluştur
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Bu iyileştirme önerisini hangi takımınız adına talep etmek istediğinizi seçin.
        </p>

        <div class="mt-6">
            <x-input-label for="takim_id_{{ $iaa->id }}" value="Takımınız" />
            <select name="takim_id" id="takim_id_{{ $iaa->id }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">Lideri olduğunuz bir takımı seçin...</option>
                @foreach ($liderOlduguTakimlar as $takim)
                    <option value="{{ $takim->id }}">{{ $takim->ad }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                İptal
            </x-secondary-button>

            <x-primary-button class="ms-3">
                Talebi Gönder
            </x-primary-button>
        </div>
    </form>
</x-modal>