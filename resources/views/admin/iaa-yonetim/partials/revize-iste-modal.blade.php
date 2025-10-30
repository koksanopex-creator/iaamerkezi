<x-modal name="revize-iste-modal-{{ $iaa->id }}" focusable>
    <form method="post" action="{{ route('admin.iaa-yonetim.revizeIste', $iaa) }}" class="p-6">
        @csrf
        @method('patch')
        <h2 class="text-lg font-medium text-gray-900">Projeyi Revize İçin Geri Gönder</h2>
        <p class="mt-1 text-sm text-gray-600">"{{ $iaa->baslik }}" projesinde hangi adımın neden revize edilmesi gerektiğini belirtin. Proje tekrar "Atandı" durumuna dönecek ve seçtiğiniz adım takım için yeniden aktif olacaktır.</p>

        <div class="mt-6">
            <label for="step_id_{{ $iaa->id }}" class="block text-sm font-medium text-gray-700">Revize Edilecek Adım</label>
            <select name="step_id" id="step_id_{{ $iaa->id }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                <option value="">Lütfen bir adım seçin...</option>
                @if($iaa->atananTakim && $iaa->atananTakim->talepEttigiIaalar->firstWhere('id', $iaa->id))
                    @foreach($iaa->atananTakim->talepEttigiIaalar->firstWhere('id', $iaa->id)->workflow->steps as $step)
                        <option value="{{ $step->id }}">{{ $step->order }}. {{ $step->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="mt-6">
            <label for="yonetici_notu_revize_{{ $iaa->id }}" class="block text-sm font-medium text-gray-700">Revize Gerekçesi</label>
            <textarea id="yonetici_notu_revize_{{ $iaa->id }}" name="yonetici_notu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="3" placeholder="Revize gerekçenizi buraya yazın..." required minlength="10"></textarea>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">İptal</x-secondary-button>
            <x-primary-button class="ml-3 bg-yellow-500 hover:bg-yellow-600">Revize İste</x-primary-button>
        </div>
    </form>
</x-modal>