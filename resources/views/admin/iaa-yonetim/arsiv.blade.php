<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Tamamlanmış ve Arşivlenmiş Projeler') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Başarıyla tamamlanan ve arşivlenen tüm projelerin listesi</p>
            </div>
            <a href="{{ route('admin.iaa-yonetim.index') }}" class="inline-flex items-center px-4 py-2.5 bg-white text-gray-700 font-medium text-sm rounded-lg border border-gray-300 hover:bg-gray-50 shadow-sm">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Yönetim Paneline Dön
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- İstatistik Kartları --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 px-4 sm:px-0">
                {{-- Toplam Proje Kartı --}}
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-indigo-100 text-sm font-medium uppercase tracking-wider">Toplam Proje</p>
                            <p class="text-4xl font-bold mt-2">{{ $tamamlananProjeler->total() }}</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
                {{-- Lider Takım Kartı --}}
                <div class="bg-gradient-to-r from-teal-500 to-cyan-600 rounded-xl shadow-lg p-6 text-white">
                    @if($enIyiTakim)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-teal-100 text-sm font-medium uppercase tracking-wider">Lider Takım</p>
                                <p class="text-2xl font-bold mt-2">{{ $enIyiTakim['ad'] }}</p>
                                <p class="text-teal-200 text-sm font-medium">{{ $enIyiTakim['proje_sayisi'] }} proje ile</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                               <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center"><p>Lider takım istatistiği için yeterli veri yok.</p></div>
                    @endif
                </div>
            </div>

            {{-- Ana Tablo Kartı --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-200">
                <div class="p-2 lg:p-4 bg-white">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            {{-- MASAÜSTÜ GÖRÜNÜMÜ İÇİN BAŞLIK --}}
                            <thead class="hidden sm:table-header-group bg-gradient-to-r from-gray-100 to-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Proje / Takım</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Başlangıç</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Onaylanma</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Süre</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Puan</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            {{-- MOBİL VE MASAÜSTÜ İÇİN ORTAK GÖVDE --}}
                            <tbody class="block sm:table-row-group">
                                @forelse ($tamamlananProjeler as $iaa)
                                    <tr class="block mb-6 bg-white border border-gray-200 rounded-lg shadow sm:table-row sm:mb-0 sm:shadow-none sm:border-0 sm:border-b">
                                        
                                        {{-- YENİ EKLENEN NUMARA SATIRI/SÜTUNU --}}
                                        <td class="flex justify-between items-center p-4 font-semibold sm:table-cell sm:px-6 sm:py-5">
                                             <span class="text-xs text-gray-500 uppercase sm:hidden"># No</span>
                                             <span class="text-sm text-gray-700">
                                                 {{ $loop->iteration + ($tamamlananProjeler->currentPage() - 1) * $tamamlananProjeler->perPage() }}
                                             </span>
                                        </td>

                                        <td class="flex justify-between items-center p-4 border-t sm:table-cell sm:p-6 sm:border-t-0">
                                            <span class="font-semibold text-xs text-gray-500 uppercase sm:hidden">Proje / Takım</span>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $iaa->baslik }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $iaa->atananTakim->ad ?? 'Takım atanmamış' }}</div>
                                            </div>
                                        </td>

                                        <td class="flex justify-between items-center p-4 border-t sm:table-cell sm:p-6">
                                            <span class="font-semibold text-xs text-gray-500 uppercase sm:hidden">Başlangıç</span>
                                            <span class="font-medium text-gray-800 text-sm">{{ $iaa->iaaTalebi ? \Carbon\Carbon::parse($iaa->iaaTalebi->start_date)->format('d.m.Y') : '-' }}</span>
                                        </td>
                                        
                                        <td class="flex justify-between items-center p-4 border-t sm:table-cell sm:p-6">
                                            <span class="font-semibold text-xs text-gray-500 uppercase sm:hidden">Onaylanma</span>
                                            <span class="font-medium text-gray-800 text-sm">{{ $iaa->onaylanma_tarihi ? \Carbon\Carbon::parse($iaa->onaylanma_tarihi)->format('d.m.Y') : '-' }}</span>
                                        </td>

                                        <td class="flex justify-between items-center p-4 border-t sm:table-cell sm:p-6 sm:text-center">
                                            <span class="font-semibold text-xs text-gray-500 uppercase sm:hidden">Süre</span>
                                            <span>
                                                @if($iaa->completion_duration_in_days !== null)
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                        {{ $iaa->completion_duration_in_days }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-sm">-</span>
                                                @endif
                                            </span>
                                        </td>
                                        
                                        <td class="flex justify-between items-center p-4 border-t sm:table-cell sm:p-6 sm:text-center">
                                            <span class="font-semibold text-xs text-gray-500 uppercase sm:hidden">Puan</span>
                                            <span class="text-sm font-bold text-indigo-600">{{ number_format($iaa->puan, 0, ',', '.') }}</span>
                                        </td>
                                        
                                        <td class="p-4 border-t sm:table-cell sm:p-6 sm:border-t-0 text-right">
                                            <a href="{{ route('proje.workspace.show', $iaa) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                                                 <span class="sm:hidden">Detayları Gör</span>
                                                 <span class="hidden sm:inline">Detay</span>
                                             </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                                            Arşivlenecek tamamlanmış bir proje bulunmuyor.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if ($tamamlananProjeler->hasPages())
                    <div class="px-6 lg:px-8 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $tamamlananProjeler->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>