<x-app-layout>
    <x-slot name="header">
        {{-- ================== DEĞİŞİKLİK BURADA ================== --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
                Talepleri Yönet: <span class="text-indigo-600">{{ $iaa->baslik }}</span>
            </h2>

            {{-- YENİ EKLENEN GERİ DÖN BUTONU --}}
            <a href="{{ route('admin.iaa-yonetim.index') }}" class="inline-flex items-center space-x-2 bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Yönetim Paneline Geri Dön</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="p-6 sm:p-8">
                    
                    {{-- Modern Sayfa Başlığı --}}
                    <div class="flex items-center space-x-4 mb-6 pb-6 border-b border-gray-200">
                        <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                             <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Bu Öneriye Gelen Talepler</h3>
                            <p class="text-gray-600">Aşağıdaki takımlardan birini seçerek projeyi atayabilirsiniz.</p>
                        </div>
                    </div>
                    
                    {{-- Talep Listesi --}}
                    <div class="space-y-4">
                        @forelse ($iaa->talepEdenTakimlar as $takim)
                            <div class="bg-gray-50 rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between hover:shadow-md hover:bg-indigo-50 transition-all duration-200">
                                <div class="w-full">
                                    <button x-data @click="$dispatch('open-modal', 'takim-ozet-modal-{{ $takim->id }}')" class="text-left w-full">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-sm font-bold">{{ substr($takim->ad, 0, 1) }}</div>
                                            <div>
                                                <p class="font-bold text-lg text-indigo-700 hover:underline">{{ $takim->ad }}</p>
                                                <p class="text-sm text-gray-500">Lider: {{ $takim->lider->name }} | Talep Tarihi: {{ $takim->pivot->created_at->format('d.m.Y') }}</p>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                                    <div class="mt-3 sm:mt-0 w-full sm:w-auto flex-shrink-0 flex items-center space-x-2">
                                        <a href="{{ route('admin.iaa-yonetim.atamaFormu', ['iaa' => $iaa, 'takim' => $takim]) }}" class="flex-grow sm:flex-grow-0 text-center bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-green-700 transition-transform hover:scale-105">
                                            Bu Takıma Ata
                                        </a>

                                        <form action="{{ route('admin.iaa-yonetim.talepReddet', ['iaa' => $iaa, 'takim' => $takim]) }}" method="POST" onsubmit="return confirm('Bu takımın talebini reddetmek istediğinize emin misiniz?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full sm:w-auto bg-red-50 text-red-600 font-semibold py-2 px-3 rounded-lg border border-red-200 hover:bg-red-100 transition-colors shadow-sm" title="Talebi Reddet">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    </div>
                        </div>
                        @empty
                            <p class="text-center text-gray-500 p-8">Bu öneri için henüz bir talep gelmemiş.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALLAR --}}
    @foreach ($iaa->talepEdenTakimlar as $takim)
        <x-modal name="takim-ozet-modal-{{ $takim->id }}" focusable>
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900">{{ $takim->ad }} - Takım Özeti</h2>
                <p class="mt-1 text-sm text-gray-600">Lider: <span class="font-semibold">{{ $takim->lider->name }}</span></p>
                <hr class="my-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Takım Üyeleri ({{ $takim->uyeler->count() }})</h3>
                <ul class="divide-y divide-gray-200">
                    @foreach ($takim->uyeler as $uye)
                        <li class="py-2 flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-sm font-bold">{{ substr($uye->name, 0, 1) }}</div>
                            <span>{{ $uye->name }} @if ($uye->id === $takim->lider_user_id)<span class="text-xs text-white bg-indigo-500 px-2 py-0.5 rounded-full ml-1">Lider</span>@endif</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">Kapat</x-secondary-button>
                </div>
            </div>
        </x-modal>
    @endforeach
</x-app-layout>