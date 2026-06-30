{{-- Açıklamalı Resim Yükleme Widget'ı --}}
<div>
    <h4 class="text-lg font-semibold text-gray-800 mb-1">{{ $config['title'] ?? 'Açıklamalı Resim Yükleme' }}</h4>
    @if(!empty($config['description']))
        <p class="text-sm text-gray-500 mb-4">{{ $config['description'] }}</p>
    @endif

    <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">

        {{-- Yükleme Alanı --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Resim Seçin</label>
            <input type="file" @if(isset($config['multiple']) && $config['multiple']) multiple @endif
                wire:model="newImageUploads.{{ $index }}.files" accept="image/*" class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100 cursor-pointer">
            <div wire:loading wire:target="newImageUploads.{{ $index }}.files" class="text-xs text-indigo-500 mt-2">
                Resimler yükleniyor, lütfen bekleyin...
            </div>
        </div>

        {{-- Yüklenmeye Hazır (Önizleme) Resimler --}}
        @if(isset($newImageUploads[$index]['files']) && count($newImageUploads[$index]['files']) > 0)
            <div class="mb-4">
                <h5 class="text-xs font-bold text-gray-500 uppercase mb-2">Yüklenmeye Hazır Resimler (Henüz Kaydedilmedi)
                </h5>
                <div class="flex flex-wrap gap-4">
                    @foreach($newImageUploads[$index]['files'] as $fileIndex => $file)
                        <div class="relative group">
                            <img src="{{ $file->temporaryUrl() }}"
                                class="h-24 w-24 object-cover rounded-lg border-2 border-indigo-200 shadow-sm">
                            <button type="button" wire:click="removePreviewImage({{ $index }}, 'files', {{ $fileIndex }})"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Önceden Kaydedilmiş Resimler --}}
        @if(isset($formData[$index]['files']) && count($formData[$index]['files']) > 0)
            <div>
                <h5 class="text-xs font-bold text-gray-500 uppercase mb-2">Kaydedilmiş Resimler</h5>
                <div class="flex flex-wrap gap-4">
                    @foreach($formData[$index]['files'] as $savedFilePath)
                        <div class="relative group">
                            <a href="{{ asset('storage/' . $savedFilePath) }}" target="_blank">
                                <img src="{{ asset('storage/' . $savedFilePath) }}"
                                    class="h-24 w-24 object-cover rounded-lg border border-gray-300 shadow-sm hover:opacity-75 transition">
                            </a>
                            <button type="button"
                                onclick="confirm('Bu resmi silmek istediğinize emin misiniz? (Adımı kaydettiğinizde silinir)') || event.stopImmediatePropagation()"
                                wire:click="removeExistingImage({{ $index }}, 'files', '{{ $savedFilePath }}')"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600 opacity-0 group-hover:opacity-100 transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>