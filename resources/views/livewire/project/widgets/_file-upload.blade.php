@props(['index', 'config'])

{{-- DÜZELTME: Fancybox script'i (layout'ta yoksa diye) pushOnce ile eklendi --}}
@pushOnce('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        // Sayfa ilk yüklendiğinde ve Livewire güncellemesinden sonra Fancybox'ı bağla
        document.addEventListener('livewire:navigated', () => {
             Fancybox.bind("[data-fancybox]", { /* Özel ayarlar */ });
        });
        document.addEventListener('DOMContentLoaded', () => {
             Fancybox.bind("[data-fancybox]", { /* Özel ayarlar */ });
        });
    </script>
@endpushOnce

<div x-data="fileUploadWidget({ index: {{ $index }}, componentId: '{{ $this->getId() }}' })" x-init="init()">
    <label for="widget-{{ $index }}-files" class="block text-lg font-semibold text-gray-800">
        {{ $config['title'] ?? 'Dosya Yükle' }}
        @if($config['required'] ?? false) <span class="text-red-500">*</span> @endif
    </label>

    {{-- 1. MEVCUT DOSYALAR (Kayıtlı Olanlar) --}}
    @if (!empty($formData[$index]['files']))
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-600 mb-2">Mevcut Dosyalar:</p>
            <div class="flex flex-wrap gap-3">
                @foreach($formData[$index]['files'] as $filePath)
                    @php
                        $isImage = Str::endsWith(strtolower($filePath), ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.webp']);
                    @endphp
                    <div class="relative group w-24">
                        @if($isImage)
                            {{-- Storage::url($filePath) YERİNE asset() KULLANILDI --}}
                            <a href="{{ asset('storage/' . $filePath) }}" data-fancybox="gallery-active-{{$index}}" data-caption="{{ basename($filePath) }}">
                                <img src="{{ asset('storage/' . $filePath) }}" alt="{{ basename($filePath) }}" class="h-24 w-24 object-cover rounded-lg border border-gray-200 shadow-sm">
                            </a>
                        @else
                            {{-- Storage::url($filePath) YERİNE asset() KULLANILDI --}}
                            <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="h-24 w-24 flex flex-col items-center justify-center bg-gray-100 rounded-lg border border-gray-200 p-2" title="{{ basename($filePath) }}">
                                <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.41l-7.81 7.81a1.5 1.5 0 002.122 2.122l7.81-7.81" /></svg>
                                <span class="text-xs text-gray-500 mt-1 truncate">{{ basename($filePath) }}</span>
                            </a>
                        @endif
                        <button type="button"
                                wire:click="markFileForDeletion({{ $index }}, '{{ $filePath }}')"
                                wire:confirm="Bu dosyayı kalıcı olarak silmek istediğinizden emin misiniz? (Kaydet'e basınca uygulanır)"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-0.5 w-5 h-5 flex items-center justify-center opacity-75 group-hover:opacity-100 transition-opacity z-10">
                            &times;
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 2. YENİ DOSYALAR (Geçici Önizleme) --}}
    @if (!empty($newUploads[$index]))
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-600 mb-2">Yeni Yüklenenler (Kaydetmeyi bekliyor):</p>
            <div class="flex flex-wrap gap-3">
                @foreach($newUploads[$index] as $fileKey => $file)
                    <div class="relative group w-24">
                        @if (method_exists($file, 'temporaryUrl') && Str::startsWith($file->getMimeType(), 'image'))
                            <a href="{{ $file->temporaryUrl() }}" data-fancybox="gallery-active-{{$index}}" data-caption="{{ $file->getClientOriginalName() }}">
                                <img src="{{ $file->temporaryUrl() }}" class="h-24 w-24 object-cover rounded-lg border border-blue-300">
                            </a>
                        @elseif (method_exists($file, 'temporaryUrl'))
                            <div class="h-24 w-24 flex flex-col items-center justify-center bg-blue-50 rounded-lg border border-blue-300 p-2" title="{{ $file->getClientOriginalName() }}">
                                <svg class="w-8 h-8 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.41l-7.81 7.81a1.5 1.5 0 002.122 2.122l7.81-7.81" /></svg>
                                <span class="text-xs text-blue-500 mt-1 truncate">{{ $file->getClientOriginalName() }}</span>
                            </div>
                        @endif
                        <button type="button"
                                wire:click="removeNewUpload({{ $index }}, {{ $fileKey }})"
                                class="absolute -top-2 -right-2 bg-gray-500 text-white rounded-full p-0.5 w-5 h-5 flex items-center justify-center opacity-75 group-hover:opacity-100 transition-opacity z-10">
                            &times;
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 3. YÜKLEME INPUT'U --}}
    <div class="mt-4">
        <label for="widget-{{ $index }}-files" class="sr-only">Dosya Seç</label>
        
        <input
            type="file"
            wire:model="newUploads.{{ $index }}"
            id="widget-{{ $index }}-files"
            class="block w-full text-sm text-gray-500
                   file:mr-4 file:py-2 file:px-4
                   file:rounded-lg file:border-0
                   file:text-sm file:font-semibold
                   file:bg-indigo-50 file:text-indigo-700
                   hover:file:bg-indigo-100"
            @if($config['multiple'] ?? false) multiple @endif
        />

        
        <div wire:loading wire:target="newUploads.{{ $index }}" class="text-sm text-gray-500 mt-1">Yükleniyor...</div>
    </div>
    @error("newUploads.{$index}") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error("newUploads.{$index}.*") <p class="text-red-500 text-xs mt-1">{{ $message }}</p @enderror
</div>

@push('scripts')
<script>
    // Alpine.js component'ini tanımla (sadece bir kez tanımlanması için kontrol)
    if (typeof window.fileUploadWidget === 'undefined') {
        window.fileUploadWidget = (options) => ({
            index: options.index,
            componentId: options.componentId,
            gallerySelector: `[data-fancybox="gallery-active-${options.index}"]`,
            
            init() {
                // Sayfa ilk yüklendiğinde Fancybox'ı bağla
                this.$nextTick(() => {
                    Fancybox.bind(this.gallerySelector);
                });

                // Livewire component'i güncellendiğinde (dosya eklendi/silindi) Fancybox'ı tekrar bağla
                Livewire.hook('element.updated', (el, component) => {
                    if (component.id === this.componentId) {
                        this.$nextTick(() => {
                            Fancybox.unbind(this.gallerySelector);
                            Fancybox.bind(this.gallerySelector);
                        });
                    }
                });
            }
        });
    }
</script>
@endpush