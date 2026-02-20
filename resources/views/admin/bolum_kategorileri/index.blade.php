<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bölüm Kategorileri') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Başarı Mesajı --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong class="font-bold">Başarılı!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Hata!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Yeni Kategori Ekleme Formu --}}
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border">
                        <h3 class="font-bold text-lg mb-4">Yeni Kategori Ekle</h3>
                        <form action="{{ route('admin.bolum-kategorileri.store') }}" method="POST"
                            class="flex gap-4 items-end">
                            @csrf
                            <div class="flex-grow">
                                <label for="ad" class="block text-gray-700 text-sm font-bold mb-2">Kategori Adı:</label>
                                <input type="text" name="ad" id="ad"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required placeholder="Örn: Üretim, İdari, Teknik...">
                            </div>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                style="height: 42px;">
                                Ekle
                            </button>
                        </form>
                    </div>

                    {{-- Kategori Listesi --}}
                    <h3 class="font-bold text-lg mb-4">Mevcut Kategoriler</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th class="py-2 px-4 text-left">ID</th>
                                    <th class="py-2 px-4 text-left">Kategori Adı</th>
                                    <th class="py-2 px-4 text-left">Bölüm Sayısı</th>
                                    <th class="py-2 px-4 text-right">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kategoriler as $kategori)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 px-4">{{ $kategori->id }}</td>
                                        <td class="py-2 px-4">
                                            <form action="{{ route('admin.bolum-kategorileri.update', $kategori->id) }}"
                                                method="POST" class="flex gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="ad" value="{{ $kategori->ad }}"
                                                    class="border rounded px-2 py-1 text-sm w-full focus:outline-none focus:border-blue-500">
                                                <button type="submit"
                                                    class="text-blue-600 hover:text-blue-800 text-xs font-bold">Güncelle</button>
                                            </form>
                                        </td>
                                        <td class="py-2 px-4">
                                            <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs">
                                                {{ $kategori->bolumler->count() }} Bölüm
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 text-right">
                                            <form action="{{ route('admin.bolum-kategorileri.destroy', $kategori->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 font-bold text-sm">Sil</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">Henüz kategori eklenmemiş.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>