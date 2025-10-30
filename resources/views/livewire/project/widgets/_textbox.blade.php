<div>
    <label for="widget-{{ $index }}-text" class="block text-lg font-semibold text-gray-800">
        {{ $config['title'] ?? 'Açıklama' }}
        @if($config['required'] ?? false) <span class="text-red-500">*</span> @endif
    </label>
    <p class="text-sm text-gray-500 mb-2">Lütfen bu adımla ilgili detayları, raporları veya notlarınızı buraya girin.</p>
    <div class="mt-1">
        <textarea wire:model="formData.{{ $index }}.text" 
                  id="widget-{{ $index }}-text" 
                  rows="10" 
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  @if($config['required'] ?? false) required @endif
        ></textarea>
    </div>
    @error("formData.{$index}.text") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>