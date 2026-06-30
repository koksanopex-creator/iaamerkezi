@push('pageTitle')
    Katılma İsteklerim | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                {{ __('Gelen Katılma İstekleri') }}
            </h2>
            
            <a href="{{ route('takimlar.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150 group">
                <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Takımlara Dön
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if($istekler->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Her Şey Sakin</h3>
                    <p class="text-gray-500">Takımlarınıza katılmak için bekleyen yeni bir istek bulunmuyor.</p>
                </div>
            @else
                <div class="space-y-8">
                    @foreach($istekler as $istek)
                        @php
                            $user = $istek->davetEden;
                            // === PERFORMANS VE İŞ YÜKÜ VERİLERİ ===
                            // 1. Devam Eden Projeler (HATA DÜZELTİLDİ: 'iaas.durum' kullanıldı)
                            $aktifGorevler = $user->gorevliOlduguProjeler()
                                ->whereIn('iaas.durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Çalışılıyor'])
                                ->with('musteriSikayeti') 
                                ->latest('iaas.updated_at')
                                ->take(3)
                                ->get();

                            $aktifGorevSayisi = $user->gorevliOlduguProjeler()
                                ->whereIn('iaas.durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Çalışılıyor'])
                                ->count();

                            // 2. Tamamlanan Başarılı Projeler
                            $tamamlananSayisi = $user->gorevliOlduguProjeler()
                                ->where('iaas.durum', 'Tamamlandı')
                                ->count();
                        @endphp

                        {{-- DİKKAT: 'overflow-hidden' kaldırıldı ki Tooltip dışarı taşabilsin --}}
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300 relative">
                            
                            <div class="bg-gradient-to-r from-orange-50 to-white px-6 py-3 border-b border-orange-100 flex justify-between items-center rounded-t-2xl">
                                <div class="text-sm text-orange-800 font-medium flex items-center gap-2">
                                    <span class="flex h-2 w-2 rounded-full bg-orange-500 animate-pulse"></span>
                                    Bu istek <span class="font-bold">"{{ $istek->takim->ad }}"</span> takımı için gönderildi.
                                </div>
                                <span class="text-xs text-gray-400 font-medium">{{ $istek->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="p-6">
                                <div class="flex flex-col xl:flex-row gap-8">
                                    
                                    {{-- SOL TARAF: Personel Kimlik Kartı --}}
                                    <div class="xl:w-1/3 border-b xl:border-b-0 xl:border-r border-gray-100 pb-6 xl:pb-0 xl:pr-6">
                                        <div class="flex items-start gap-5">
                                            {{-- Avatar --}}
                                            <div class="relative flex-shrink-0">
                                                <a href="{{ route('profile.show', $user->id) }}" target="_blank">
                                                    @if($user->profile_photo_path)
                                                        <img class="h-20 w-20 rounded-2xl object-cover border-2 border-gray-100 shadow-sm" src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}">
                                                    @else
                                                        <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-sm">
                                                            {{ substr($user->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    
                                                    @if($user->isOnline())
                                                        <span class="absolute -bottom-1 -right-1 h-5 w-5 bg-green-500 border-4 border-white rounded-full" title="Şu an çevrimiçi"></span>
                                                    @endif
                                                </a>
                                            </div>

                                            <div>
                                                <a href="{{ route('profile.show', $user->id) }}" target="_blank" class="text-xl font-bold text-gray-900 hover:text-indigo-600 transition-colors flex items-center gap-2">
                                                    {{ $user->name }}
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                                <p class="text-sm text-gray-500 font-medium mt-1">
                                                    {{ $user->bolum->ad ?? 'Bölüm Yok' }} 
                                                    @if($user->unvan) <span class="mx-1 text-gray-300">|</span> {{ $user->unvan }} @endif
                                                </p>
                                                
                                                {{-- Skor Kartı --}}
                                                <div class="flex items-center gap-2 mt-3">
                                                    <div class="bg-yellow-50 text-yellow-700 px-3 py-1 rounded-lg text-sm font-bold border border-yellow-100 flex items-center gap-1">
                                                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        {{ number_format($user->toplam_puan) }} Puan
                                                    </div>
                                                    <div class="bg-green-50 text-green-700 px-3 py-1 rounded-lg text-sm font-bold border border-green-100 flex items-center gap-1">
                                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        {{ $tamamlananSayisi }} Bitmiş İş
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ORTA: Mevcut İş Yükü ve Takım Durumu --}}
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                                Mevcut İşler ({{ $aktifGorevSayisi }})
                                            </h4>

                                            {{-- ============================================= --}}
                                            {{-- [YENİ] TAKIM ÜYELERİ TOOLTIP (HOVER İLE ÇIKAR) --}}
                                            {{-- ============================================= --}}
                                            <div class="relative group">
                                                <div class="cursor-help flex items-center gap-1.5 px-2 py-1 bg-indigo-50 text-indigo-600 rounded text-xs font-bold border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                    {{ $istek->takim->uyeler->count() }} Üye
                                                </div>
                                                
                                                {{-- Tooltip Kutusu --}}
                                                <div class="absolute bottom-full right-0 mb-2 w-48 bg-gray-900 text-white text-xs rounded-lg py-2 px-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 pointer-events-none shadow-xl">
                                                    <div class="font-bold border-b border-gray-700 mb-1 pb-1 text-gray-300">Takım Üyeleri</div>
                                                    <ul class="max-h-32 overflow-y-auto custom-scrollbar space-y-1">
                                                        @foreach($istek->takim->uyeler as $uye)
                                                            <li class="flex items-center gap-2">
                                                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                                                                {{ $uye->name }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    {{-- Ok --}}
                                                    <div class="absolute top-full right-4 -mt-1 border-4 border-transparent border-t-gray-900"></div>
                                                </div>
                                            </div>
                                            {{-- ============================================= --}}
                                        </div>

                                        @if($aktifGorevler->count() > 0)
                                            <div class="space-y-3">
                                                @foreach($aktifGorevler as $gorev)
                                                    <div class="bg-slate-50 rounded-lg p-3 border border-slate-100 hover:border-indigo-200 transition-colors group/item">
                                                        <div class="flex justify-between items-start">
                                                            <div>
                                                                <div class="flex items-center gap-2">
                                                                    @if($gorev->tur == 'sikayet' || $gorev->musteriSikayeti)
                                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600 border border-red-200">ŞİKAYET</span>
                                                                    @else
                                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-600 border border-blue-200">İAA</span>
                                                                    @endif
                                                                    <a href="{{ route('proje.workspace.show', $gorev->id) }}" target="_blank" class="text-sm font-semibold text-gray-800 hover:text-indigo-600 line-clamp-1">
                                                                        {{ $gorev->baslik }}
                                                                    </a>
                                                                </div>
                                                                <div class="text-xs text-gray-500 mt-1 pl-1">
                                                                    Durum: <span class="text-indigo-600 font-medium">{{ $gorev->durum }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if($aktifGorevSayisi > 3)
                                                    <div class="text-center">
                                                        <span class="text-xs font-medium text-gray-400 bg-gray-50 px-3 py-1 rounded-full border border-gray-100">
                                                            +{{ $aktifGorevSayisi - 3 }} diğer görev daha var
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="bg-green-50 border border-green-100 rounded-lg p-4 text-center">
                                                <p class="text-sm text-green-700 font-medium flex items-center justify-center gap-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Şu an üzerinde aktif bir iş yükü yok.
                                                </p>
                                                <p class="text-xs text-green-600 mt-1">Takımınıza hemen katkı sağlayabilir!</p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- SAĞ: Karar Butonları --}}
                                    <div class="xl:w-48 flex flex-col justify-center gap-3 border-t xl:border-t-0 xl:border-l border-gray-100 pt-6 xl:pt-0 xl:pl-6">
                                        <form action="{{ route('takimlar.istekKabulEt', $istek->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full group/btn relative flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold shadow-md hover:from-emerald-600 hover:to-green-700 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                                <svg class="w-5 h-5 transition-transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Kabul Et
                                            </button>
                                        </form>

                                        <form action="{{ route('takimlar.istekReddet', $istek) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white text-red-600 border-2 border-red-100 rounded-xl font-bold hover:bg-red-50 hover:border-red-200 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Reddet
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>