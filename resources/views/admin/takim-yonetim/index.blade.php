<x-app-layout>
    {{-- ======================== SAYFA BAŞLIĞI (HEADER) ======================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Takım Yönetimi') }}
        </h2>
    </x-slot>

    {{-- ======================== ANA SAYFA İÇERİĞİ ======================== --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="p-6 sm:p-8">

                    {{-- Modern Sayfa Başlığı ve "Yeni Takım" Butonu --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Tüm Takımlar</h3>
                                <p class="mt-1 text-base text-gray-600">Sistemde kayıtlı tüm takımları buradan yönetebilirsiniz.</p>
                            </div>
                        </div>
                        <div class="mt-4 sm:mt-0 flex-shrink-0">
                            <a href="{{ route('admin.takim-yonetim.create') }}" class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-500 text-white font-semibold py-2 px-4 rounded-lg shadow-sm hover:from-indigo-700 hover:to-blue-600 transform hover:-translate-y-0.5 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Yeni Takım Oluştur
                            </a>
                        </div>
                    </div>
                    
                    {{-- Başarı/Hata Mesajları --}}
                    @if(session('success'))<div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert"><p>{{ session('success') }}</p></div>@endif
                    @if(session('error'))<div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert"><p>{{ session('error') }}</p></div>@endif

                    {{-- Duyarlı Kart Tablo --}}
                    <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-inner border border-gray-200/80 overflow-hidden">
                        <table class="block sm:table min-w-full">
                            <thead class="hidden sm:table-header-group">
                                <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                    <th class="px-6 py-4">Takım Adı</th>
                                    <th class="px-6 py-4">Lider</th>
                                    <th class="px-6 py-4 text-center">Üye Sayısı</th>
                                    <th class="px-6 py-4">Oluşturulma Tarihi</th>
                                    <th class="px-6 py-4 text-right">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="block sm:table-row-group">
                                @forelse ($takimlar as $takim)
                                    <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-indigo-50 transition-colors">
                                        <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Takım:</span><span class="text-right sm:text-left font-medium text-indigo-600">{{ $takim->ad }}</span></td>
                                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Lider:</span><span class="text-right sm:text-left text-gray-600">{{ $takim->lider->name }}</span></td>
                                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Üyeler:</span><div class="w-full text-right sm:text-center"><span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $takim->uyeler_count }} Üye</span></div></td>
                                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Tarih:</span><span class="text-right sm:text-left text-sm text-gray-500">{{ $takim->created_at->format('d.m.Y') }}</span></td>
                                        <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                            <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center sm:space-x-2 space-y-2 sm:space-y-0">
                                                <a href="{{ route('admin.takim-yonetim.show', $takim) }}" class="w-full sm:w-auto inline-flex justify-center text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 hover:bg-gray-50">
                                                    Detay
                                                </a>
                                                <a href="{{ route('admin.takim-yonetim.edit', $takim) }}" class="w-full sm:w-auto inline-flex justify-center text-sm font-semibold text-white bg-indigo-600 border border-transparent rounded-md shadow-sm px-3 py-2 hover:bg-indigo-700">
                                                    Düzenle
                                                </a>

                                                {{-- ================== YENİ SİL BUTONU FORMU ================== --}}
                                                <form class="inline-block w-full sm:w-auto"
                                                    method="POST"
                                                    action="{{ route('admin.takim-yonetim.destroy', $takim) }}"
                                                    onsubmit="return confirm('\'{{ $takim->ad }}\' takımını kalıcı olarak silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full inline-flex justify-center text-sm font-semibold text-white bg-red-600 border border-transparent rounded-md shadow-sm px-3 py-2 hover:bg-red-700">
                                                        Sil
                                                    </button>
                                                </form>
                                                {{-- ========================================================== --}} 
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="block sm:table-row"><td colspan="5" class="p-12 text-center text-gray-500">Sistemde henüz oluşturulmuş bir takım bulunmamaktadır.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Sayfalama Linkleri --}}
                    <div class="mt-6">
                        {{ $takimlar->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>