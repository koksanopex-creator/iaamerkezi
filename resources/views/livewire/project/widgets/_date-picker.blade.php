<div>
    <label for="widget-{{ $index }}-date" class="block text-lg font-semibold text-gray-800">
        {{ $config['title'] ?? 'Termin Tarihi' }}
        @if($config['required'] ?? false) <span class="text-red-500">*</span> @endif
    </label>
    <div class="mt-1">
        <input type="date" 
               wire:model="formData.{{ $index }}.date" 
               id="widget-{{ $index }}-date" 
               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
               @if($config['required'] ?? false) required @endif
               >
    </div>
    @error("formData.{$index}.date") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>