<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-900 tracking-tight">
            Tutanak Sorumlusu Yönetimi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Bilgilendirme Kartı --}}
            <div class="bg-indigo-50 border-l-4 border-indigo-600 p-5 mb-8 rounded-r-xl shadow-sm flex items-start gap-4">
                <div class="bg-indigo-100 p-2 rounded-full text-indigo-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-indigo-900">Yetki Kapsamı Hakkında</h4>
                    <p class="text-sm text-indigo-700 mt-1">
                        Burada yetkilendirdiğiniz "Disiplin Sorumluları", 
                        @if(Auth::user()->bolum->is_disciplinary_global)
                            <strong class="bg-indigo-200 px-1 rounded">bölümünüz "Global Yetkili" olduğu için TÜM FABRİKAYA</strong>
                        @else
                            <strong class="bg-indigo-200 px-1 rounded">sadece sizin bölümünüzdeki diğer personellere</strong>
                        @endif
                        tutanak tutabilecektir.
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Personel Bilgisi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mevcut Durum</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($personeller as $personel)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center font-bold text-gray-600">
                                            {{ substr($personel->name, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $personel->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $personel->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($personel->can_issue_disciplinary)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                            Tutanak Sorumlusu
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-500 border border-gray-200">
                                            Standart Personel
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    {{-- ROTA İSMİ GÜNCELLENDİ --}}
                                    <form action="{{ route('admin.disiplin.sorumlular.update', $personel->id) }}" method="POST">
                                        @csrf
                                        @if($personel->can_issue_disciplinary)
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-bold bg-red-50 hover:bg-red-100 px-4 py-2 rounded-lg transition border border-red-200">
                                                Yetkiyi Al
                                            </button>
                                        @else
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 font-bold bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-lg transition border border-indigo-200">
                                                Sorumlu Yap
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>