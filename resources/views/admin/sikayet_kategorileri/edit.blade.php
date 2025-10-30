<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kategoriyi Düzenle: {{ $sikayetKategori->ad }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('admin.sikayet-kategorileri.update', $sikayetKategori) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Güncelleme için PUT metodu --}}
                    <div class="space-y-6">
                        <div>
                            <label for="ad">Kategori Adı</label>
                            <input type="text" name="ad" id="ad" value="{{ old('ad', $sikayetKategori->ad) }}" required class="mt-1 block w-full ...">
                            <x-input-error :messages="$errors->get('ad')" class="mt-2" />
                        </div>
                        <div>
                            <label for="varsayilan_takim_id">Varsayılan Çözüm Takımı</label>
                            <select name="varsayilan_takim_id" id="varsayilan_takim_id" class="mt-1 block w-full ...">
                                <option value="">Takım Seçilmedi</option>
                                @foreach($takimlar as $takim)
                                    <option value="{{ $takim->id }}" {{ old('varsayilan_takim_id', $sikayetKategori->varsayilan_takim_id) == $takim->id ? 'selected' : '' }}>
                                        {{ $takim->ad }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="...">Değişiklikleri Kaydet</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>