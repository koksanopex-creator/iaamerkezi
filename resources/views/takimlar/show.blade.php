<x-app-layout>
    <x-slot name="header">
        {{-- Header (Biraz daha sade "Geri Dön" butonu) --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">
                    {{ $takim->ad }}
                </h2>
                <p class="text-gray-600 mt-1 font-medium">Takım üyelerinizi ve projelerinizi buradan yönetin.</p>
            </div>
            <a href="{{ route('takimlar.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Tüm Takımlara Geri Dön
            </a>
        </div>
    </x-slot>

    {{-- SAYFA ARKAPLANI (Referans tasarımdaki gibi) --}}
    <div class="py-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- ========================================================== --}}
            {{-- === SOL SÜTUN (YENİDEN TASARLANMIŞ KARTLAR) ================ --}}
            {{-- ========================================================== --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Başarı/Hata Mesajları --}}
                @if(session('success')) 
                    <div class="bg-green-100 border border-green-200 text-green-800 p-4 rounded-xl" role="alert">
                        <p class="font-semibold">{{ session('success') }}</p>
                    </div> 
                @endif
                @if(session('error')) 
                    <div class="bg-red-100 border border-red-200 text-red-800 p-4 rounded-xl" role="alert">
                        <p class="font-semibold">{{ session('error') }}</p>
                    </div> 
                @endif
                
                {{-- YENİ KART: GELEN KATILMA İSTEKLERİ (LİDER İÇİN) --}}
                @if (Auth::id() === $takim->lider_user_id && $gelenIstekler->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                Gelen Katılma İstekleri ({{ $gelenIstekler->count() }})
                            </h3>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            @foreach ($gelenIstekler as $istek)
                                <li class="p-4 sm:p-6 hover:bg-gray-50/70 flex flex-col sm:flex-row items-start sm:items-center justify-between">
                                    <div class="flex items-center mb-3 sm:mb-0">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-sm font-bold text-gray-600">{{ Str::substr($istek->davetEden->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-semibold text-gray-800">{{ $istek->davetEden->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $istek->davetEden->bolum->ad ?? 'Bölüm Atanmamış' }}</p>
                                        </div>
                                    </div>
                                    {{-- YENİ PASTEL BUTONLAR --}}
                                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                                        <form class="flex-1" action="{{ route('takimlar.istekKabulEt', $istek) }}" method="POST"> 
                                            @csrf 
                                            <button type="submit" class="w-full px-3 py-2 bg-green-100 text-green-800 rounded-lg hover:bg-green-200 text-xs font-semibold transition-colors">Kabul Et</button>
                                        </form>
                                        <form class="flex-1" action="{{ route('takimlar.istegiReddet', $istek) }}" method="POST"> 
                                            @csrf 
                                            <button type="submit" class="w-full px-3 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 text-xs font-semibold transition-colors">Reddet</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- YENİ KART: AKTİF PROJELER --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.368-.822a4 4 0 00-3.654 1.967 4 4 0 01-3.654-1.967l-2.368.822a2 2 0 00-1.022.547H3V19a2 2 0 002 2h14a2 2 0 002-2v-3.572h-2.572zM12 12a4 4 0 01-4-4h8a4 4 0 01-4 4z"></path></svg>
                            Takımın Aktif Projeleri ({{ $aktifProjeler->count() }})
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($aktifProjeler as $proje)
                            
                            {{-- 1. REVIZYONDAKI PROJE TASARIMI (Pastel Sarı/Amber) --}}
                            @if ($proje->durum == 'Revize Ediliyor')
                                <div class="bg-yellow-50/70 p-5 rounded-xl border border-yellow-200 shadow-sm">
                                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                        <div>
                                            {{-- Proje Tipi Etiketi --}}
                                            @if($proje->musteriSikayeti)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 text-rose-800 mb-1.5">
                                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 3.001-1.742 3.001H4.42c-1.53 0-2.493-1.667-1.743-3.001l5.58-9.92zM10 5a1 1 0 011 1v3a1 1 0 11-2 0V6a1 1 0 011-1zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                </svg>
                                                    Müşteri Şikayeti
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 mb-1.5">
                                                    IAA Önerisi
                                                </span>
                                            @endif
                                            
                                            <h4 class="font-bold text-amber-800 text-lg">{{ $proje->baslik }}</h4>
                                            
                                            <div class="flex items-center mt-2">
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800" style="white-space: nowrap;">
                                                    Revizyon Bekliyor
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 w-full sm:w-auto">
                                            {{-- Sadeleştirilmiş Buton --}}
                                            <a href="{{ route('proje.workspace.show', $proje) }}" 
                                               class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 transition-all">
                                                Projeye Git
                                            </a>
                                        </div>
                                    </div>
                                    
                                    {{-- Revizyon Nedeni (Referans koddaki gibi) --}}
                                    @php $revisionLog = $proje->logs->first(); @endphp
                                    @if (!empty($proje->yonetici_notu))
                                        <div class="mt-4 pt-3 border-t border-yellow-200">
                                            <p class="text-sm font-semibold text-gray-700">Revizyon Nedeni:</p>
                                            <p class="text-sm text-gray-600 mt-1 italic">"{{ $proje->yonetici_notu }}"</p>
                                            @if ($revisionLog && $revisionLog->user)
                                                <p class="text-xs text-gray-500 mt-2 text-right font-medium">
                                                    <strong>{{ $revisionLog->user->name }}</strong> tarafından ({{ $revisionLog->created_at->format('d.m.Y H:i') }})
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            
                            {{-- 2. NORMAL AKTIF PROJE TASARIMI (Pastel Indigo/Mavi) --}}
                            @else
                                <div class="bg-blue-50/70 p-5 rounded-xl border border-blue-200 shadow-sm">
                                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                        <div>
                                            {{-- Proje Tipi Etiketi --}}
                                            @if($proje->musteriSikayeti)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 text-rose-800 mb-1.5">
                                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 3.001-1.742 3.001H4.42c-1.53 0-2.493-1.667-1.743-3.001l5.58-9.92zM10 5a1 1 0 011 1v3a1 1 0 11-2 0V6a1 1 0 011-1zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                    </svg>
                                                    Müşteri Şikayeti
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 mb-1.5">
                                                    IAA Önerisi
                                                </span>
                                            @endif
                                            
                                            <h4 class="font-bold text-indigo-800 text-lg">{{ $proje->baslik }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">Çalışmalar devam ediyor.</p>
                                        </div>
                                        <div class="flex-shrink-0 w-full sm:w-auto">
                                            {{-- Sadeleştirilmiş Buton --}}
                                            <a href="{{ route('proje.workspace.show', $proje) }}" 
                                               class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition-all">
                                                Projeye Git
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <p class="text-center text-gray-500 py-8 font-medium">Bu takımın şu anda üzerinde çalıştığı bir proje bulunmuyor.</p>
                        @endforelse
                    </div>
                </div>

                {{-- YENİ KART: TAMAMLANAN PROJELER --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tamamlanan Projeler ({{ $tamamlananProjeler->count() }})
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($tamamlananProjeler as $proje)
                            <div class="bg-green-50/70 p-5 rounded-xl border border-green-200 flex justify-between items-center gap-4">
                                <div>
                                    <h4 class="font-bold text-green-800 text-lg">{{ $proje->baslik }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">Onay Tarihi: {{ \Carbon\Carbon::parse($proje->onaylanma_tarihi)->format('d.m.Y') }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-2xl font-black text-green-600">{{ number_format($proje->puan, 0) }}</p>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">PUAN</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-8 font-medium">Bu takım henüz bir proje tamamlamadı.</p>
                        @endforelse
                    </div>
                </div>

                {{-- YENİ KART: YÖNETİCİ ONAYI BEKLEYEN PROJELER --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Onay Bekleyen Tamamlanmış Projeler ({{ $yoneticiOnayiBekleyenProjeler->count() }})
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($yoneticiOnayiBekleyenProjeler as $proje)
                            <div class="bg-amber-50/70 p-5 rounded-xl border border-amber-200 flex justify-between items-center gap-4">
                                <div>
                                    <a href="{{ route('proje.workspace.show', $proje) }}" class="font-bold text-amber-800 text-lg hover:underline" title="Projeyi İncele">
                                        {{ $proje->baslik }}
                                    </a>
                                    @if (!empty($proje->yonetici_notu))
                                        <div class="mt-4 pt-3 border-t border-amber-200">
                                            <p class="text-sm font-semibold text-gray-700">Revizyon Nedeni:</p>
                                            <p class="text-sm text-gray-600 mt-1 italic">"{{ $proje->yonetici_notu }}"</p>
                                        </div>
                                    @endif
                                    <p class="text-sm text-gray-600 mt-1">
                                        Onaya Gönderilme: {{ $proje->updated_at->format('d.m.Y') }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-2xl font-black text-amber-600">{{ number_format($proje->puan, 0) }}</p>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">PUAN</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-8 font-medium">Bu takımın yönetici onayı bekleyen bir projesi bulunmuyor.</p>
                        @endforelse
                    </div>
                </div>

                {{-- YENİ KART: ONAY BEKLEYEN TALEPLER --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Onay Bekleyen Proje Talepleri ({{ $bekleyenTalepler->count() }})
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($bekleyenTalepler->isEmpty())
                            <p class="text-center text-gray-500 py-6">Yönetici onayında bekleyen bir proje talebi bulunmuyor.</p>
                        @else
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proje Başlığı</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Puan</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Talep Tarihi</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($bekleyenTalepler as $talep)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('iaa.show', $talep->iaa_id) }}" class="text-gray-900 hover:text-indigo-600 hover:underline">{{ $talep->baslik }}</a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-indigo-600">{{ number_format($talep->puan, 0) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($talep->talep_tarihi)->format('d.m.Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Onay Bekliyor</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- YENİ KART: TAKIM ÜYELERİ & DAVETLER --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-2.356M17 20H7m10 0v-2c0-1.657-1.343-3-3-3H7m10 0-3 3m0 0l-3-3m3 3V6a3 3 0 00-3-3H7a3 3 0 00-3 3v11m0 0h4m-4 0V6a3 3 0 013-3h4a3 3 0 013 3v3"></path></svg>
                            Takım Üyeleri & Davetler
                        </h3>
                    </div>
                    @if (Auth::id() === $takim->lider_user_id)
                        <form action="{{ route('takimlar.davetGonder', $takim) }}" method="POST" class="p-6 bg-gray-50 flex flex-col sm:flex-row items-center gap-3 border-b border-gray-200">
                            @csrf
                            <select name="user_id" class="w-full flex-grow border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Takıma davet etmek için bir kullanıcı seçin...</option>
                                @foreach ($potansiyelUyeler as $uye) 
                                    <option value="{{ $uye->id }}">{{ $uye->name }} ({{ $uye->email }})</option> 
                                @endforeach
                            </select>
                            <button type="submit" class="w-full sm:w-auto flex-shrink-0 bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-indigo-700 transition-colors">Davet Gönder</button>
                        </form>
                    @endif
                    <ul class="divide-y divide-gray-100">
                        <li class="px-6 py-3 bg-gray-50/70"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Mevcut Üyeler ({{ $takim->uyeler->count() }})</h4></li>
                        @forelse ($takim->uyeler as $uye)
                            <li class="p-4 sm:p-6 hover:bg-gray-50/70 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-green-400 to-blue-500 flex items-center justify-center">
                                            <span class="text-sm font-bold text-white">{{ Str::substr($uye->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-semibold text-gray-800">{{ $uye->name }}
                                            @if ($uye->id === $takim->lider_user_id) 
                                                <span class="ms-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Lider</span> 
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $uye->bolum->ad ?? 'Bölüm Atanmamış' }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $uye->pivot->created_at->format('d.m.Y') }} tarihinde {{ $uye->pivot->katilma_sekli }}</p>
                                    </div>
                                </div>
                                @if (Auth::id() === $takim->lider_user_id && $uye->id !== Auth::id())
                                    <form action="{{ route('takimlar.uyeCikar', ['takim' => $takim, 'user' => $uye]) }}" method="POST" onsubmit="return confirm('Bu üyeyi takımdan çıkarmak istediğinizden emin misiniz?');">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold transition-colors">Çıkar</button>
                                    </form>
                                @endif
                            </li>
                        @empty
                            <li class="p-6 text-sm text-gray-500">Takımda henüz hiç üye yok.</li>
                        @endforelse
                        
                        @if (Auth::id() === $takim->lider_user_id && $gonderilenDavetler->isNotEmpty())
                            <li class="px-6 py-3 bg-gray-50/70 border-t border-gray-100"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Gönderilen Davetler ({{ $gonderilenDavetler->count() }})</h4></li>
                            @foreach ($gonderilenDavetler as $davet)
                                <li class="p-4 sm:p-6 hover:bg-gray-50/70 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-sm font-bold text-gray-600">{{ Str::substr($davet->davetEdilen->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-semibold text-gray-800">{{ $davet->davetEdilen->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $davet->davetEdilen->bolum->ad ?? 'Bölüm Atanmamış' }}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('takimlar.davetiIptalEt', $davet) }}" method="POST" onsubmit="return confirm('Bu daveti iptal etmek istediğinizden emin misiniz?');">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-500 hover:text-red-700 text-sm font-semibold transition-colors">İptal Et</button>
                                    </form>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- === SAĞ SÜTUN (KÜNYE KARTI - YUMUŞATILMIŞ) ================ --}}
            {{-- ========================================================== --}}
            <div class="lg:col-span-1">
                {{-- Künye Kartı --}}
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100">
                    <div class="p-6 bg-gradient-to-r from-indigo-600 to-blue-600">
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
                    <div class="p-6 text-center bg-gradient-to-br from-indigo-50 to-blue-50/70 border-b border-gray-100">
                        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-indigo-600 to-blue-600 text-white font-bold text-xl mb-3 shadow-lg">
                            {{ Str::substr($takim->lider->name, 0, 1) }}
                        </div>
                        <p class="text-xs text-indigo-700 uppercase font-bold tracking-widest mb-1">Takım Lideri</p>
                        <h2 class="text-xl font-bold text-gray-900 truncate">{{ $takim->lider->name }}</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white border border-gray-200 p-4 rounded-xl text-center hover:shadow-md transition-shadow">
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-2">Üye Sayısı</p>
                                <p class="text-3xl font-black text-gray-800">{{ $takim->uyeler->count() }}</p>
                            </div>
                            <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl text-center shadow-lg hover:shadow-xl transition-shadow">
                                <p class="text-xs text-amber-800 uppercase font-bold tracking-wider mb-2">Takım Puanı</p>
                                <p class="text-3xl font-black text-amber-700">{{ number_format($takim->toplam_puan, 0) }}</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-4">
                            @php
                                $detaylar = [
                                    ['baslik' => 'Amaç', 'deger' => $takim->amac],
                                    ['baslik' => 'Vizyon', 'deger' => $takim->vizyon],
                                    ['baslik' => 'Misyon', 'deger' => $takim->misyon],
                                ];
                            @endphp
                            @foreach($detaylar as $detay)
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ $detay['baslik'] }}</h4>
                                    <p class="text-gray-700 text-sm leading-relaxed font-medium">{{ $detay['deger'] ?? 'Belirtilmemiş' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if(Auth::id() === $takim->lider_user_id)
                        <div class="p-6 bg-gray-50/50 border-t border-gray-100">
                            <a href="{{ route('takimlar.edit', $takim) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z"></path></svg>
                                Takım Bilgilerini Düzenle
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>