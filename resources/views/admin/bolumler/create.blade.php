<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Yeni Bölüm Ekle') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- ======================== HATA MESAJLARI ======================== --}}
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Hata!</strong>
                            <span class="block sm:inline">Lütfen formdaki hataları düzeltin.</span>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ======================== YENİ BÖLÜM EKLEME FORMU ======================== --}}
                    <form action="{{ route('admin.bolumler.store') }}" method="POST">
                        @csrf
                        
                        {{-- Bölüm Adı --}}
                        <div class="mb-4">
                            <label for="ad" class="block text-gray-700 text-sm font-bold mb-2">Bölüm Adı:</label>
                            <input type="text" name="ad" id="ad" value="{{ old('ad') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        
                        {{-- Durum --}}
                        <div class="mb-4">
                            <label for="is_active" class="block text-gray-700 text-sm font-bold mb-2">Durum:</label>
                            <select name="is_active" id="is_active" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="1" selected>Aktif</option>
                                <option value="0">Pasif</option>
                            </select>
                        </div>
                        
                        {{-- Butonlar --}}
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Kaydet
                            </button>
                            <a href="{{ route('admin.bolumler.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                İptal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>