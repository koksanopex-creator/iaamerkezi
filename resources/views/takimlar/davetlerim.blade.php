<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 00-2-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                {{ __('Gelen Takım Davetleri') }}
            </h2>
            <a href="{{ route('takimlar.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150 group">
                <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Tüm Takımlara Dön
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-8 bg-gradient-to-r from-emerald-500 to-green-500 text-white p-4 rounded-2xl shadow-lg flex items-center gap-3 animate-fade-in-down">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if($davetler->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
                    <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Bekleyen Davet Yok</h3>
                    <p class="text-gray-500">Şu an için sizi takımına davet eden kimse bulunmuyor.</p>
                </div>
            @else
                <div class="space-y-8">
                    @foreach ($davetler as $davet)
                        @php
                            $lider = $davet->takim->lider;
                            $takim = $davet->takim;
                            
                            // Liderin Başarı İstatistikleri (HATA DÜZELTME: iaas.durum)
                            $liderTamamlananIs = $lider->gorevliOlduguProjeler()
                                ->where('iaas.durum', 'Tamamlandı')
                                ->count();
                            
                            // Takımın Aktif Projeleri
                            $takimAktifProjeler = $takim->atananProjeler()
                                ->whereIn('durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor'])
                                ->latest('updated_at')
                                ->take(3)
                                ->get();
                        @endphp

                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300 relative">
                            <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-3 border-b border-indigo-100 flex justify-between items-center rounded-t-2xl">
                                <div class="text-sm text-indigo-800 font-medium flex items-center gap-2">
                                    <span class="flex h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                    Yeni Davet
                                </div>
                                <span class="text-xs text-gray-400 font-medium">{{ $davet->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="p-6">
                                <div class="flex flex-col xl:flex-row gap-8">
                                    
                                    {{-- SOL: Lider Profili ve Takım Bilgisi --}}
                                    <div class="xl:w-1/3 border-b xl:border-b-0 xl:border-r border-gray-100 pb-6 xl:pb-0 xl:pr-6">
                                        <div class="flex items-start gap-5">
                                            {{-- Lider Avatar --}}
                                            <div class="relative flex-shrink-0">
                                                <a href="{{ route('profile.show', $lider->id) }}" target="_blank" class="group block">
                                                    @if($lider->profile_photo_path)
                                                        <img class="h-20 w-20 rounded-2xl object-cover border-4 border-indigo-50 group-hover:border-indigo-100 transition-colors" src="{{ asset('storage/' . $lider->profile_photo_path) }}" alt="{{ $lider->name }}">
                                                    @else
                                                        <div class="h-20 w-20 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-2xl font-bold border-4 border-indigo-50 group-hover:border-indigo-100 transition-colors">
                                                            {{ substr($lider->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-[10px] px-2 py-0.5 rounded-full uppercase font-bold tracking-wider shadow-sm">
                                                        LİDER
                                                    </div>
                                                </a>
                                            </div>

                                            <div>
                                                <h4 class="text-gray-500 text-sm font-medium mb-1">Davet Eden:</h4>
                                                <a href="{{ route('profile.show', $lider->id) }}" target="_blank" class="text-xl font-bold text-gray-900 hover:text-indigo-600 transition-colors flex items-center gap-2">
                                                    {{ $lider->name }}
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                                <p class="text-sm text-gray-500 font-medium mt-1">
                                                    {{ $lider->bolum->ad ?? 'Bölüm Yok' }}
                                                </p>

                                                {{-- Lider Skor Kartı --}}
                                                <div class="flex items-center gap-2 mt-3">
                                                    <div class="bg-yellow-50 text-yellow-700 px-3 py-1 rounded-lg text-sm font-bold border border-yellow-100 flex items-center gap-1" title="Liderin Toplam Puanı">
                                                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        {{ number_format($lider->toplam_puan) }} Puan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ORTA: Takım Detayları ve Üyeler --}}
                                    <div class="flex-1">
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-500 mb-1">Sizi şu takıma davet ediyor:</p>
                                            <a href="{{ route('takimlar.show', $takim->id) }}" target="_blank" class="text-2xl font-black text-indigo-700 hover:text-indigo-900 transition-colors flex items-center gap-2">
                                                {{ $takim->ad }}
                                                <svg class="w-5 h-5 text-gray-400 hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            </a>
                                        </div>

                                        <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                                            <div class="flex justify-between items-center mb-2">
                                                <h4 class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Takımın Durumu</h4>
                                                
                                                {{-- ============================================= --}}
                                                {{-- [YENİ] TAKIM ÜYELERİ TOOLTIP (HOVER İLE ÇIKAR) --}}
                                                {{-- ============================================= --}}
                                                <div class="relative group">
                                                    <div class="cursor-help flex items-center gap-1 text-sm text-gray-700 font-medium hover:text-indigo-600 transition-colors">
                                                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                        {{ $takim->uyeler->count() }} Üye
                                                    </div>
                                                    
                                                    {{-- Tooltip --}}
                                                    <div class="absolute bottom-full right-0 mb-2 w-48 bg-gray-900 text-white text-xs rounded-lg py-2 px-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 pointer-events-none shadow-xl">
                                                        <div class="font-bold border-b border-gray-700 mb-1 pb-1 text-gray-300">Takım Üyeleri</div>
                                                        <ul class="max-h-32 overflow-y-auto custom-scrollbar space-y-1">
                                                            @foreach($takim->uyeler as $uye)
                                                                <li class="flex items-center gap-2">
                                                                    <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full"></span>
                                                                    {{ $uye->name }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                        <div class="absolute top-full right-4 -mt-1 border-4 border-transparent border-t-gray-900"></div>
                                                    </div>
                                                </div>
                                                {{-- ============================================= --}}
                                            </div>

                                            <div class="flex items-center gap-1.5 text-sm text-gray-700 font-medium mb-3">
                                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $takim->atananProjeler()->where('durum', 'Tamamlandı')->count() }} Başarılı Proje
                                            </div>

                                            @if($takimAktifProjeler->count() > 0)
                                                <div class="pt-3 border-t border-indigo-100">
                                                    <p class="text-xs text-indigo-500 font-semibold mb-1">Şu an üzerinde çalışılan projeler:</p>
                                                    <ul class="space-y-1">
                                                        @foreach($takimAktifProjeler as $proje)
                                                            <li class="flex items-center gap-2 text-xs text-gray-600">
                                                                <span class="w-1.5 h-1.5 bg-blue-400 rounded-full flex-shrink-0"></span>
                                                                <span class="truncate" title="{{ $proje->baslik }}">{{ $proje->baslik }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- SAĞ: Karar Butonları --}}
                                    <div class="xl:w-48 flex flex-col justify-center gap-3 border-t xl:border-t-0 xl:border-l border-gray-100 pt-6 xl:pt-0 xl:pl-6">
                                        <form action="{{ route('takimlar.davetiKabulEt', $davet) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full group/btn relative flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold shadow-md hover:from-indigo-700 hover:to-purple-700 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                                <svg class="w-5 h-5 transition-transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Kabul Et
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('takimlar.davetiReddet', $davet) }}" method="POST">
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