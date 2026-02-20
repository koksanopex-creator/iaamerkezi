<div>
    <x-slot name="header">
        <div class="bg-white border-b border-gray-200 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-6">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                            {{ __('Aktif Görevlerim') }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Üzerinizdeki devam eden projeleri yönetin ve takip edin</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-2 text-sm font-semibold text-indigo-700 bg-indigo-50 rounded-lg border-2 border-indigo-200">
                            <span class="text-lg font-bold">{{ $projeler->total() }}</span> Proje
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <style>
        .project-card {
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }
        
        .project-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #e5e7eb;
        }
    </style>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            {{-- Filtre ve Bilgi Çubuğu --}}
            <div class="bg-white rounded-lg border-2 border-gray-200 p-4">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span><strong class="text-gray-900">{{ $projeler->count() }}</strong> proje gösteriliyor</span>
                    </div>
                </div>
            </div>
            
            {{-- Liste --}}
            @forelse ($projeler as $proje)
                <div class="project-card bg-white rounded-lg border-2 border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            
                            {{-- Sol Taraf: Durum İkonu --}}
                            <div class="flex-shrink-0">
                                @if($proje->durum == 'Revize Ediliyor')
                                    <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600 border-2 border-orange-200">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    </div>
                                @elseif(Str::contains($proje->durum, 'Onay'))
                                    <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 border-2 border-purple-200">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                @else
                                    <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 border-2 border-blue-200">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Orta Taraf: Bilgiler --}}
                            <div class="flex-1 min-w-0">
                                {{-- Rozetler --}}
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="text-xs font-semibold px-3 py-1.5 rounded-md {{ 
                                        $proje->durum == 'Revize Ediliyor' ? 'bg-orange-100 text-orange-700 border border-orange-300' : 
                                        (Str::contains($proje->durum, 'Onay') ? 'bg-purple-100 text-purple-700 border border-purple-300' : 'bg-blue-100 text-blue-700 border border-blue-300') 
                                    }}">
                                        {{ $proje->durum }}
                                    </span>
                                    <span class="text-xs font-medium text-gray-600 px-3 py-1.5 bg-gray-100 rounded-md border border-gray-300">
                                        {{ $proje->musteriSikayeti->sikayetKategori->ad ?? 'Genel Kategori' }}
                                    </span>
                                    <span class="text-xs text-gray-500 ml-auto font-mono">#{{ $proje->musteriSikayeti->id ?? '-' }}</span>
                                </div>

                                {{-- Başlık --}}
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-indigo-700 transition-colors">
                                    <a href="{{ route('proje.workspace.show', $proje->id) }}">
                                        {{ $proje->baslik }}
                                    </a>
                                </h3>

                                {{-- ========================================================= --}}
                                {{-- [YENİ YERLEŞİM] ADIM SORUMLULUĞU UYARISI BURAYA GELDİ --}}
                                {{-- ========================================================= --}}
                                @php
                                    $aktifAdim = $proje->aktifAdim;
                                    $benimSorumlulugumda = false;
                                    
                                    // Adım varsa ve sorumlular listesi yüklüyse kontrol et
                                    if($aktifAdim && $aktifAdim->sorumlular) {
                                        // Pivot tablodaki 'iaa_id' ile projenin 'id'sini eşleştir
                                        // Ve kullanıcı listesinde ben var mıyım?
                                        $benimSorumlulugumda = $aktifAdim->sorumlular
                                            ->where('pivot.iaa_id', $proje->id) 
                                            ->contains('id', auth()->id());
                                    }
                                @endphp

                                @if($benimSorumlulugumda)
                                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-3 rounded-r-md">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 pt-0.5">
                                                <svg class="h-5 w-5 text-red-500 animate-pulse" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-bold text-red-700">Sıra Sizde!</p>
                                                <p class="text-xs text-red-600 mt-0.5">
                                                    Bu projede <strong>"{{ $aktifAdim->adim_tanimi ?? $aktifAdim->name ?? 'Aktif Adım' }}"</strong> adımını tamamlamanız bekleniyor.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{-- ========================================================= --}}

                                {{-- Alt Bilgiler (Tarih ve İşlemi Yapan) --}}
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mt-2">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>
                                            <span class="text-gray-500">Son Güncelleme:</span> <strong class="text-gray-900">{{ $proje->updated_at->diffForHumans() }}</strong>
                                        </span>
                                    </div>
                                    
                                    @if($proje->logs->isNotEmpty())
                                        <div class="hidden sm:flex items-center gap-1.5 pl-4 border-l-2 border-gray-200">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            <span class="truncate max-w-[200px]">
                                                <span class="text-gray-500">İşlemi Yapan:</span> <strong class="text-gray-900">{{ $proje->logs->first()->user->name ?? 'Sistem' }}</strong>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Sağ Taraf: Butonlar --}}
                            <div class="flex flex-row md:flex-col gap-2 justify-center md:justify-start border-t md:border-t-0 md:border-l-2 border-gray-200 pt-4 md:pt-0 md:pl-6 mt-4 md:mt-0">
                                <a href="{{ route('proje.workspace.show', $proje->id) }}" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors whitespace-nowrap shadow-sm">
                                    Çalışma Alanı
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                                
                                @if($proje->musteriSikayeti)
                                    <a href="{{ route('admin.sikayetler.show', $proje->musteriSikayeti->id) }}" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2.5 bg-white text-gray-700 text-sm font-semibold rounded-lg border-2 border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition-colors whitespace-nowrap">
                                        Şikayet Detayı
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 bg-white rounded-lg border-2 border-gray-200">
                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-4 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aktif Görev Bulunamadı</h3>
                    <p class="text-gray-600 text-center max-w-md">Şu anda üzerinize atanmış veya dahil olduğunuz devam eden bir şikayet projesi bulunmuyor.</p>
                </div>
            @endforelse

            @if ($projeler->hasPages())
                <div class="pt-4">
                    {{ $projeler->links() }}
                </div>
            @endif
        </div>
    </div>
</div>