<form action="{{ isset($workflow) ? route('admin.workflows.update', $workflow) : route('admin.workflows.store') }}" method="POST">
    @csrf
    @if(isset($workflow))
        @method('PUT')
    @endif
    <div class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Şablon Adı</label>
            <div class="mt-1">
                <input type="text" name="name" id="name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('name', $workflow->name ?? '') }}" required>
            </div>
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Açıklama</label>
            <div class="mt-1">
                <textarea id="description" name="description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $workflow->description ?? '') }}</textarea>
            </div>
            <p class="mt-2 text-sm text-gray-500">Bu şablonun amacını ve kullanım alanını kısaca açıklayın.</p>
            @error('description')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="relative flex items-start">
            <div class="flex h-5 items-center">
                <input id="is_default" name="is_default" type="checkbox" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_default', $workflow->is_default ?? false))>
            </div>
            <div class="ml-3 text-sm">
                <label for="is_default" class="font-medium text-gray-700">Varsayılan Şablon Olarak Ayarla</label>
                <p class="text-gray-500">Bunu seçerseniz, diğer varsayılan şablonun işareti kaldırılacaktır.</p>
            </div>
        </div>
    </div>

    <div class="mt-8 border-t border-gray-200 pt-5">
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.workflows.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                İptal
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ isset($workflow) ? 'Güncelle' : 'Kaydet' }}
            </button>
        </div>
    </div>
</form>