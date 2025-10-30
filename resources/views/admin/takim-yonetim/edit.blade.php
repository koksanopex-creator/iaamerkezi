<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Takımı Düzenle: <span class="text-indigo-600">{{ $takim->ad }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="p-6 sm:p-8">
                    
                    <h3 class="text-2xl font-bold text-gray-800 tracking-tight mb-6">Takım Bilgilerini Güncelle</h3>

                    <form action="{{ route('admin.takim-yonetim.update', $takim) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-6">
                            
                            {{-- Takım Adı --}}
                            <div>
                                <label for="ad" class="block text-sm font-medium text-gray-700">Takım Adı <span class="text-red-500">*</span></label>
                                <input type="text" name="ad" id="ad" value="{{ old('ad', $takim->ad) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('ad')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Takım Lideri Seçimi --}}
                            <div>
                                <label for="lider_user_id" class="block text-sm font-medium text-gray-700">Takım Lideri <span class="text-red-500">*</span></label>
                                <select name="lider_user_id" id="lider_user_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach ($kullanicilar as $kullanici)
                                        <option value="{{ $kullanici->id }}" {{ old('lider_user_id', $takim->lider_user_id) == $kullanici->id ? 'selected' : '' }}>
                                            {{ $kullanici->name }} ({{ $kullanici->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('lider_user_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Takım Amacı (İsteğe Bağlı) --}}
                            <div>
                                <label for="amac" class="block text-sm font-medium text-gray-700">Takımın Amacı</label>
                                <textarea name="amac" id="amac" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('amac', $takim->amac) }}</textarea>
                            </div>
                            
                            {{-- Diğer İsteğe Bağlı Alanlar --}}
                            <div><label for="vizyon" class="block text-sm font-medium text-gray-700">Vizyon</label><textarea name="vizyon" id="vizyon" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('vizyon', $takim->vizyon) }}</textarea></div>
                            <div><label for="misyon" class="block text-sm font-medium text-gray-700">Misyon</label><textarea name="misyon" id="misyon" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('misyon', $takim->misyon) }}</textarea></div>
                            <div><label for="kurallar" class="block text-sm font-medium text-gray-700">Kurallar</label><textarea name="kurallar" id="kurallar" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('kurallar', $takim->kurallar) }}</textarea></div>

                            {{-- Form Butonları --}}
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                                <a href="{{ route('admin.takim-yonetim.index') }}" class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm">İptal</a>
                                <button type="submit" class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-500 text-white font-semibold py-2 px-4 rounded-lg shadow-sm">Değişiklikleri Kaydet</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>