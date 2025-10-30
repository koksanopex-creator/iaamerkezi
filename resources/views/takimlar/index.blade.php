<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
                    {{ __('Takımlar') }}
                </h2>
                <p class="text-gray-600 mt-1">Takımlarınızı yönetin, yeni takımlar keşfedin ve katılın.</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('takimlar.davetlerim') }}" class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm transition-colors">Davetlerim</a>
                <a href="{{ route('takimlar.create') }}" class="group relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Yeni Takım Oluştur
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 p-4 rounded-r-xl shadow-sm"><div class="flex"><div class="flex-shrink-0"><svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg></div><div class="ml-3"><p class="text-green-800 font-medium">{{ session('success') }}</p></div></div></div>
            @endif
            @if(session('error'))
                 <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-400 p-4 rounded-r-xl shadow-sm"><div class="flex"><div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg></div><div class="ml-3"><p class="text-red-800 font-medium">{{ session('error') }}</p></div></div></div>
            @endif

            @if($gonderdigimIstekler->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-yellow-200">
                <div class="bg-gradient-to-r from-yellow-50 to-white px-6 py-5 border-b border-yellow-200"><div class="flex items-center justify-between"><h3 class="text-lg font-semibold text-yellow-800">Gönderdiğim Katılma İstekleri</h3><div class="flex items-center space-x-2 text-sm text-yellow-600"><span>{{ $gonderdigimIstekler->count() }} Bekleyen İstek</span></div></div></div>
                <div class="bg-white/60 backdrop-blur-sm overflow-hidden">
                    <table class="block sm:table min-w-full">
                        <thead class="hidden sm:table-header-group">
                            <tr class="text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                                <th class="px-6 py-4">Takım Adı</th><th class="px-6 py-4">İstek Tarihi</th><th class="px-6 py-4">Durum</th><th class="relative px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="block sm:table-row-group">
                            @foreach ($gonderdigimIstekler as $istek)
                                <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-yellow-50 transition-colors">
                                    <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Takım:</span><span class="text-right sm:text-left font-semibold text-gray-800">{{ $istek->takim->ad }}</span></td>
                                    <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Tarih:</span><span class="text-right sm:text-left text-sm text-gray-600">{{ $istek->created_at->format('d.m.Y') }}</span></td>
                                    <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Durum:</span><span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ Str::ucfirst($istek->durum) }}</span></td>
                                    <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                        <div class="flex justify-end">
                                            <form action="{{ route('takimlar.istegiGeriCek', $istek) }}" method="POST" onsubmit="return confirm('Bu katılma isteğini geri çekmek istediğinizden emin misiniz?');">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">İsteği Geri Çek</button></form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-5 border-b border-gray-200"><div class="flex items-center justify-between"><h3 class="text-lg font-semibold text-gray-900">Katıldığım Takımlar</h3><div class="flex items-center space-x-2 text-sm text-gray-500"><span>{{ $katildigimTakimlar->count() }} Takım</span></div></div></div>
                @include('takimlar.partials.takim-table', ['takimlar' => $katildigimTakimlar, 'type' => 'katildigim'])
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-5 border-b border-gray-200"><div class="flex items-center justify-between"><h3 class="text-lg font-semibold text-gray-900">Diğer Takımlar</h3><div class="flex items-center space-x-2 text-sm text-gray-500"><span>{{ $digerTakimlar->count() }} Takım</span></div></div></div>
                @include('takimlar.partials.takim-table', ['takimlar' => $digerTakimlar, 'type' => 'diger', 'istekGonderilenTakimIdleri' => $gonderdigimIstekler->pluck('takim_id'), 'davetAlinanTakimIdleri' => $gelenDavetler->pluck('takim_id')])
            </div>
        </div>
    </div>
</x-app-layout>