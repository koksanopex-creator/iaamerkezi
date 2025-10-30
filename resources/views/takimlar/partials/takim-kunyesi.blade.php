<div class="bg-gradient-to-br from-gray-50 via-white to-indigo-50 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
    {{-- TAKIM İSMİ (BAŞLIK) --}}
    <div class="p-6 bg-gradient-to-r from-indigo-600 to-blue-600 border-b-4 border-indigo-700">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <span class="text-3xl font-bold text-indigo-600">{{ Str::substr($takim->ad, 0, 1) }}</span>
            </div>
            <div>
                <p class="text-xs text-indigo-200 uppercase font-bold tracking-widest">Takım Adı</p>
                <h1 class="text-3xl font-black text-white tracking-tight">{{ $takim->ad }}</h1>
            </div>
        </div>
    </div>

    {{-- LİDER BÖLÜMÜ --}}
    <div class="p-6 text-center bg-gradient-to-br from-indigo-50 to-blue-100 border-b border-gray-200">
        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-indigo-600 to-blue-600 text-white font-bold text-xl mb-3 shadow-xl">
            {{ Str::substr($takim->lider->name, 0, 1) }}
        </div>
        <p class="text-xs text-indigo-700 uppercase font-bold tracking-widest mb-1">Takım Lideri</p>
        <h2 class="text-xl font-bold text-gray-900 truncate">{{ $takim->lider->name }}</h2>
    </div>

    {{-- ANA İÇERİK --}}
    <div class="p-6 space-y-6">
        {{-- İSTATİSTİKLER --}}
        <div class="grid grid-cols-2 gap-4">
            {{-- Üye Sayısı --}}
            <div class="bg-white border border-gray-200 p-4 rounded-xl text-center hover:shadow-md transition-shadow">
                <div class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-2">Üye Sayısı</div>
                <div class="text-3xl font-black text-gray-800">{{ $takim->uyeler->count() }}</div>
            </div>

            {{-- Takım Puanı --}}
            <div class="bg-gradient-to-br from-amber-50 to-orange-100 border border-amber-300 p-4 rounded-xl text-center shadow-lg hover:shadow-xl transition-shadow">
                <div class="text-xs text-amber-800 uppercase font-bold tracking-wider mb-2">Takım Puanı</div>
                {{-- Puan küsuratlarını kaldırma kararımızla tutarlı olması için 0'a yuvarlandı --}}
                <div class="text-3xl font-black text-amber-700">{{ number_format($takim->toplam_puan, 0) }}</div>
            </div>
        </div>

        {{-- DETAY BÖLÜMÜ --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-4">
            @php
                $detaylar = [
                    ['baslik' => 'Amaç', 'deger' => $takim->amac],
                    ['baslik' => 'Vizyon', 'deger' => $takim->vizyon],
                    ['baslik' => 'Misyon', 'deger' => $takim->misyon],
                    ['baslik' => 'Kurallar', 'deger' => $takim->kurallar],
                ];
            @endphp

            @foreach($detaylar as $detay)
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ $detay['baslik'] }}</h4>
                    <p class="text-gray-700 text-sm leading-relaxed font-medium">
                        {{ $detay['deger'] ?? 'Belirtilmemiş' }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- DÜZENLE DÜĞMESI (Yalnızca Lider İçin) --}}
    @if(Auth::id() === $takim->lider_user_id)
        <div class="p-6 bg-gray-50/50 border-t border-gray-200">
            <a href="{{ route('takimlar.edit', $takim) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z"></path></svg>
                Takım Bilgilerini Düzenle
            </a>
        </div>
    @endif
</div>