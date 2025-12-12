@php
    // Sadece "Savunma Bekleniyor" durumundaki ve kullanıcının sorumlu olduğu (Kendi dosyası)
    $bekleyenSavunmalar = \App\Models\DisciplinaryCase::where('user_id', Auth::id())
        ->where('durum', 'Savunma Bekleniyor')
        ->with(['behavior.category']) // İlişkiyi çekelim
        ->get();
@endphp

@if($bekleyenSavunmalar->isNotEmpty())
    <div class="mb-8 space-y-4">
        @foreach($bekleyenSavunmalar as $dosya)
            {{-- MODERN UYARI KARTI --}}
            <div class="relative bg-white rounded-xl shadow-lg border border-red-200 overflow-hidden group">
                
                {{-- Sol Kenar Çizgisi ve Arka Plan Efekti --}}
                <div class="absolute top-0 bottom-0 left-0 w-2 bg-gradient-to-b from-red-500 to-red-700"></div>
                <div class="absolute inset-0 bg-red-50 opacity-30 pointer-events-none"></div>

                <div class="p-6 relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    
                    {{-- SOL TARAF: İKON VE BİLGİ --}}
                    <div class="flex items-start gap-5 w-full">
                        {{-- Animasyonlu İkon --}}
                        <div class="flex-shrink-0 relative">
                            <span class="absolute inset-0 bg-red-400 rounded-full opacity-20 animate-ping"></span>
                            <div class="relative bg-gradient-to-br from-red-500 to-red-600 w-12 h-12 rounded-full flex items-center justify-center text-white shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>

                        <div class="flex-1">
                            <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                                SAVUNMA BEKLENİYOR
                                <span class="bg-red-100 text-red-700 text-[10px] px-2 py-0.5 rounded uppercase tracking-wider font-bold border border-red-200">Acil İşlem</span>
                            </h3>
                            
                            <div class="mt-2 text-sm text-gray-600">
                                <p class="font-medium text-gray-800">
                                    {{ $dosya->behavior->category->ad ?? 'Genel Disiplin' }}
                                </p>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    {{ Str::limit($dosya->behavior->tanim ?? 'İhlal tanımı bulunamadı.', 60) }}
                                </p>
                            </div>

                            <div class="mt-3 flex items-center gap-4 text-xs font-medium text-gray-500">
                                <div class="flex items-center gap-1 bg-white px-2 py-1 rounded border border-gray-200 shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Olay: {{ $dosya->olay_tarihi->format('d.m.Y') }}
                                </div>
                                <div class="flex items-center gap-1 text-red-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Bekleme Süresi: {{ $dosya->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SAĞ TARAF: BUTON --}}
                    @php
                        $hedefRota = Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı']) 
                            ? 'admin.disiplin.show' 
                            : 'disiplin.show';
                    @endphp

                    <div class="w-full md:w-auto flex-shrink-0">
                        <a href="{{ route($hedefRota, $dosya->id) }}" class="group/btn relative w-full md:w-auto inline-flex items-center justify-center gap-2 bg-gray-900 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                            {{-- Buton Hover Efekti --}}
                            <span class="absolute top-0 left-0 w-full h-full bg-white/20 transform -skew-x-12 -translate-x-full group-hover/btn:animate-shine"></span>
                            
                            <svg class="w-5 h-5 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            <span>Savunma Ver</span>
                        </a>
                        <p class="text-[10px] text-center mt-2 text-gray-400">Dosya #{{ $dosya->id }}</p>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
@endif