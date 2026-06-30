@push('pageTitle')
    Takım Davetlerim | 
@endpush

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

            @if($davetler->isEmpty() && $projeDavetleri->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
                    <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Bekleyen Davet Yok</h3>
                    <p class="text-gray-500">Şu an için sizi takımına veya projesine davet eden kimse bulunmuyor.</p>
                </div>
            @else
                <div class="space-y-12">
                    {{-- TAKIM DAVETLERİ BÖLÜMÜ --}}
                    @if($davetler->isNotEmpty())
                        <div class="space-y-6">
                            <h3 class="text-lg font-black text-gray-900 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-sm">
                                    {{ $davetler->count() }}
                                </span>
                                Gelen Takım Davetleri
                            </h3>
                            <div class="space-y-6">
                                @foreach ($davetler as $davet)
                                    @php
                                        $lider = $davet->takim->lider;
                                        $takim = $davet->takim;
                                        
                                        // Liderin Başarı İstatistikleri
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

                                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300 relative overflow-hidden">
                                        <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-3 border-b border-indigo-100 flex justify-between items-center">
                                            <div class="text-sm text-indigo-800 font-medium flex items-center gap-2">
                                                <span class="flex h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                                Sürekli Takım Daveti
                                            </div>
                                            <span class="text-xs text-gray-400 font-medium">{{ $davet->created_at->diffForHumans() }}</span>
                                        </div>

                                        <div class="p-6">
                                            <div class="flex flex-col xl:flex-row gap-8">
                                                
                                                {{-- SOL: Lider Profili --}}
                                                <div class="xl:w-1/3 border-b xl:border-b-0 xl:border-r border-gray-100 pb-6 xl:pb-0 xl:pr-6">
                                                    <div class="flex items-start gap-5">
                                                        <div class="relative flex-shrink-0">
                                                            <a href="{{ route('profile.show', $lider->id) }}" target="_blank" class="group block">
                                                                @if($lider->profile_photo_path)
                                                                    <img class="h-16 w-16 rounded-2xl object-cover border-4 border-indigo-50 group-hover:border-indigo-100 transition-colors" src="{{ asset('storage/' . $lider->profile_photo_path) }}" alt="{{ $lider->name }}">
                                                                @else
                                                                    <div class="h-16 w-16 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-xl font-bold border-4 border-indigo-50 group-hover:border-indigo-100 transition-colors">
                                                                        {{ substr($lider->name, 0, 1) }}
                                                                    </div>
                                                                @endif
                                                            </a>
                                                        </div>

                                                        <div>
                                                            <h4 class="text-gray-500 text-xs font-medium mb-1">Davet Eden:</h4>
                                                            <a href="{{ route('profile.show', $lider->id) }}" target="_blank" class="text-lg font-bold text-gray-900 hover:text-indigo-600 transition-colors">
                                                                {{ $lider->name }}
                                                            </a>
                                                            <p class="text-xs text-gray-500 font-medium mt-1 uppercase">{{ $lider->bolum->ad ?? 'Bölüm Yok' }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ORTA: Takım Detayları --}}
                                                <div class="flex-1">
                                                    <div class="mb-4">
                                                        <p class="text-xs text-gray-500 mb-1">Sizi şu takıma davet ediyor:</p>
                                                        <h4 class="text-xl font-black text-indigo-700">{{ $takim->ad }}</h4>
                                                    </div>
                                                    <div class="flex flex-wrap gap-4 text-sm">
                                                        <div class="flex items-center gap-1.5 text-gray-600">
                                                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                            {{ $takim->uyeler->count() }} Üye
                                                        </div>
                                                        <div class="flex items-center gap-1.5 text-gray-600">
                                                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ $takim->atananProjeler()->where('durum', 'Tamamlandı')->count() }} Başarılı Proje
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- SAĞ: Karar Butonları --}}
                                                <div class="xl:w-48 flex flex-col justify-center gap-2 border-t xl:border-t-0 xl:border-l border-gray-100 pt-6 xl:pt-0 xl:pl-6">
                                                    <form action="{{ route('takimlar.davetiKabulEt', $davet) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold shadow-md hover:bg-indigo-700 transition-all">
                                                            Kabul Et
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('takimlar.davetiReddet', $davet) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-white text-red-600 border-2 border-red-50 rounded-xl font-bold hover:bg-red-50 transition-colors">
                                                            Reddet
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- PROJE DAVETLERİ (SQUAD) BÖLÜMÜ --}}
                    @if($projeDavetleri->isNotEmpty())
                        <div class="space-y-6">
                            <h3 class="text-lg font-black text-gray-900 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white text-sm">
                                    {{ $projeDavetleri->count() }}
                                </span>
                                Proje (Squad) Davetleri
                            </h3>
                            <div class="space-y-6">
                                @foreach ($projeDavetleri as $proje)
                                    @php
                                        $lider = $proje->atananTakim->lider ?? $proje->gonderen;
                                    @endphp

                                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300 relative overflow-hidden">
                                        <div class="bg-gradient-to-r from-emerald-50/50 to-white px-6 py-3 border-b border-emerald-100 flex justify-between items-center">
                                            <div class="text-sm text-emerald-800 font-medium flex items-center gap-2">
                                                <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Özel Proje Görevi
                                            </div>
                                            <span class="text-xs text-gray-400 font-medium">{{ $proje->created_at->diffForHumans() }}</span>
                                        </div>

                                        <div class="p-6">
                                            <div class="flex flex-col xl:flex-row gap-8">
                                                
                                                {{-- SOL: Proje Lideri Profili --}}
                                                <div class="xl:w-1/3 border-b xl:border-b-0 xl:border-r border-gray-100 pb-6 xl:pb-0 xl:pr-6">
                                                    <div class="flex items-start gap-5">
                                                        <div class="relative flex-shrink-0">
                                                            <a href="{{ route('profile.show', $lider->id) }}" target="_blank" class="group block">
                                                                @if($lider->profile_photo_path)
                                                                    <img class="h-16 w-16 rounded-2xl object-cover border-4 border-emerald-50 group-hover:border-emerald-100 transition-colors" src="{{ asset('storage/' . $lider->profile_photo_path) }}" alt="{{ $lider->name }}">
                                                                @else
                                                                    <div class="h-16 w-16 rounded-2xl bg-emerald-600 flex items-center justify-center text-white text-xl font-bold border-4 border-emerald-50 group-hover:border-emerald-100 transition-colors">
                                                                        {{ substr($lider->name, 0, 1) }}
                                                                    </div>
                                                                @endif
                                                            </a>
                                                        </div>

                                                        <div>
                                                            <h4 class="text-gray-500 text-xs font-medium mb-1">Proje Lideri:</h4>
                                                            <a href="{{ route('profile.show', $lider->id) }}" target="_blank" class="text-lg font-bold text-gray-900 hover:text-emerald-600 transition-colors">
                                                                {{ $lider->name }}
                                                            </a>
                                                            <p class="text-xs text-gray-500 font-medium mt-1 uppercase">{{ $lider->bolum->ad ?? 'Bölüm Yok' }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ORTA: Proje Detayları --}}
                                                <div class="flex-1">
                                                    <div class="mb-4">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            @if($proje->musteriSikayeti)
                                                                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-black uppercase rounded border border-rose-200">
                                                                    Müşteri Şikayeti
                                                                </span>
                                                            @else
                                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-black uppercase rounded border border-blue-200">
                                                                    Saf İAA
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-gray-500 mb-1">Sizi şu projeye dahil etti:</p>
                                                        <h4 class="text-xl font-black text-emerald-700 leading-tight">{{ $proje->baslik }}</h4>
                                                        @if($proje->atananTakim)
                                                            <p class="text-xs text-gray-400 mt-1 uppercase font-bold">Takım: {{ $proje->atananTakim->ad }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase rounded border border-emerald-100">
                                                            Öncelik: {{ $proje->oncelik ?? 'Normal' }}
                                                        </span>
                                                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase rounded border border-indigo-100">
                                                            Puan: {{ $proje->puan ?? 0 }}
                                                        </span>
                                                    </div>
                                                </div>

                                                {{-- SAĞ: Karar Butonları --}}
                                                <div class="xl:w-48 flex flex-col justify-center gap-2 border-t xl:border-t-0 xl:border-l border-gray-100 pt-6 xl:pt-0 xl:pl-6">
                                                    <form action="{{ route('iaa.davetYanitla', $proje->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="yanit" value="kabul">
                                                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-xl font-bold shadow-md hover:bg-emerald-700 transition-all">
                                                            Kabul Et
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('iaa.davetYanitla', $proje->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="yanit" value="red">
                                                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-white text-rose-600 border-2 border-rose-50 rounded-xl font-bold hover:bg-rose-50 transition-colors">
                                                            Reddet
                                                        </button>
                                                    </form>
                                                    <div class="grid grid-cols-1 gap-1">
                                                        <a href="{{ route('proje.workspace.show', $proje->id) }}" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition-colors text-xs" title="Proje Çalışma Alanı">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                            İncele (Workspace)
                                                        </a>
                                                        @if($proje->musteriSikayeti)
                                                            <a href="{{ route('admin.sikayetler.show', $proje->musteriSikayeti->id) }}" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 rounded-xl font-bold hover:bg-rose-100 transition-colors text-[10px]" title="Şikayet Detay Sayfası">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                                Şikayet Detayı
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>