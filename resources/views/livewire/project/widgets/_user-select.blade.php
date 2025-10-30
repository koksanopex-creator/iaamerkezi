@php
    // Kullanıcıları bir kez çekip cache'leyebiliriz, şimdilik direkt çekiyoruz
    $users = \App\Models\User::where('onaylandi_mi', true)->orderBy('name')->get();
@endphp
<div>
    <label for="widget-{{ $index }}-user" class="block text-lg font-semibold text-gray-800">
        {{ $config['title'] ?? 'Sorumlu Kişi' }}
        @if($config['required'] ?? false) <span class="text-red-500">*</span> @endif
    </label>
    <div class="mt-1">
        <select wire:model="formData.{{ $index }}.user_id" 
                id="widget-{{ $index }}-user" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                @if($config['required'] ?? false) required @endif
                >
            <option value="">-- Kullanıcı Seçin --</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    @error("formData.{$index}.user_id") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>