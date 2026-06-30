{{-- Önce/Sonra Karşılaştırma Widget --}}
<div>
    <h4 class="text-lg font-semibold text-gray-800 mb-1">{{ $config['title'] ?? 'Önce/Sonra Karşılaştırma' }}</h4>
    <p class="text-sm text-gray-500 mb-4">Mevcut durumu ve iyileştirme sonrası hedeflenen durumu karşılaştırın.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- ÖNCE --}}
        <div class="relative">
            <div class="absolute -top-3 left-4 z-10">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full shadow-md">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    ÖNCE
                </span>
            </div>
            <div class="bg-red-50 border-2 border-red-200 rounded-xl p-5 pt-6">
                {{-- Öncesi Görseli --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-red-700 mb-1.5">Önceki Durum Görseli</label>

                    {{-- Yüklenecek Resim --}}
                    @if(isset($newImageUploads[$index]['before']) && $newImageUploads[$index]['before'])
                        <div class="relative inline-block mt-2">
                            <img src="{{ $newImageUploads[$index]['before']->temporaryUrl() }}"
                                class="h-32 object-contain rounded border-2 border-red-300">
                            <button type="button" wire:click="removePreviewImage({{ $index }}, 'before')"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        {{-- Kayıtlı Resim --}}
                    @elseif(!empty($formData[$index]['before_image_path']))
                        <div class="relative inline-block mt-2">
                            <a href="{{ asset('storage/' . $formData[$index]['before_image_path']) }}" target="_blank">
                                <img src="{{ asset('storage/' . $formData[$index]['before_image_path']) }}"
                                    class="h-32 object-contain rounded border border-gray-300 hover:opacity-80">
                            </a>
                            <button type="button"
                                onclick="confirm('Görseli silmek istediğinize emin misiniz?') || event.stopImmediatePropagation()"
                                wire:click="removeExistingImage({{ $index }}, 'before')"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <input type="file" wire:model="newImageUploads.{{ $index }}.before" accept="image/*"
                            class="mt-2 block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-red-100 file:text-red-700 hover:file:bg-red-200">
                    @else
                        <input type="file" wire:model="newImageUploads.{{ $index }}.before" accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-100 file:text-red-700 hover:file:bg-red-200 cursor-pointer">
                    @endif
                    <div wire:loading wire:target="newImageUploads.{{ $index }}.before"
                        class="text-xs text-red-500 mt-1">Yükleniyor...</div>
                </div>

                <label class="block text-sm font-medium text-red-700 mb-1.5">Açıklama</label>
                <textarea wire:model="formData.{{ $index }}.before_text" rows="3"
                    class="block w-full rounded-lg border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm bg-white"
                    placeholder="Mevcut durumu açıklayın..."></textarea>
            </div>
        </div>

        {{-- SONRA --}}
        <div class="relative">
            <div class="absolute -top-3 left-4 z-10">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-md">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    SONRA
                </span>
            </div>
            <div class="bg-green-50 border-2 border-green-200 rounded-xl p-5 pt-6">
                {{-- Sonrası Görseli --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-green-700 mb-1.5">Sonraki Durum Görseli</label>

                    {{-- Yüklenecek Resim --}}
                    @if(isset($newImageUploads[$index]['after']) && $newImageUploads[$index]['after'])
                        <div class="relative inline-block mt-2">
                            <img src="{{ $newImageUploads[$index]['after']->temporaryUrl() }}"
                                class="h-32 object-contain rounded border-2 border-green-300">
                            <button type="button" wire:click="removePreviewImage({{ $index }}, 'after')"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        {{-- Kayıtlı Resim --}}
                    @elseif(!empty($formData[$index]['after_image_path']))
                        <div class="relative inline-block mt-2">
                            <a href="{{ asset('storage/' . $formData[$index]['after_image_path']) }}" target="_blank">
                                <img src="{{ asset('storage/' . $formData[$index]['after_image_path']) }}"
                                    class="h-32 object-contain rounded border border-gray-300 hover:opacity-80">
                            </a>
                            <button type="button"
                                onclick="confirm('Görseli silmek istediğinize emin misiniz?') || event.stopImmediatePropagation()"
                                wire:click="removeExistingImage({{ $index }}, 'after')"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <input type="file" wire:model="newImageUploads.{{ $index }}.after" accept="image/*"
                            class="mt-2 block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200">
                    @else
                        <input type="file" wire:model="newImageUploads.{{ $index }}.after" accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200 cursor-pointer">
                    @endif
                    <div wire:loading wire:target="newImageUploads.{{ $index }}.after"
                        class="text-xs text-green-500 mt-1">Yükleniyor...</div>
                </div>

                <label class="block text-sm font-medium text-green-700 mb-1.5">Hedeflenen / İyileştirilmiş Durum</label>
                <textarea wire:model="formData.{{ $index }}.after_text" rows="3"
                    class="block w-full rounded-lg border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm bg-white"
                    placeholder="Hedeflenen durumu açıklayın..."></textarea>
            </div>
        </div>
    </div>
</div>