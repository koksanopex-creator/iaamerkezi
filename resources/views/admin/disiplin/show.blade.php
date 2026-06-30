<x-app-layout>
    @push('pageTitle', $case->user->name . ' - Disiplin Dosyası | ')
    <x-slot name="header">
        <div class="relative bg-white border border-slate-200 rounded-[2rem] p-6 lg:p-8 shadow-2xl shadow-slate-200/50">
            {{-- Arka Plan Dekoru (Clipped Container) --}}
            <div class="absolute inset-0 rounded-[2rem] overflow-hidden pointer-events-none">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-slate-50 rounded-full opacity-50 blur-3xl"></div>
            </div>
            
            <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                {{-- SOL TARAF: PERSONEL KARTI --}}
                <div class="flex items-center gap-6">
                    <a href="javascript:history.back()"
                        class="group flex items-center justify-center w-12 h-12 bg-slate-50 text-slate-400 hover:text-indigo-600 hover:bg-white hover:shadow-md transition-all rounded-2xl border border-slate-100"
                        title="Geri Dön">
                        <svg class="w-6 h-6 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    
                                    @php
                                        $previousCases = $case->user->disiplinDosyalari()
                                            ->where('id', '!=', $case->id)
                                            ->latest()
                                            ->get();
                                        $prevCount = $previousCases->count();
                                    @endphp

                    <div x-data="{ showHistory: false }" class="flex items-center gap-5">
                        {{-- AVATAR VE İSİM GRUBU (Tetikleyici) --}}
                        <div @click="showHistory = !showHistory" class="relative group cursor-pointer flex items-center gap-6">
                            <div class="flex flex-col items-center">
                                <div class="relative">
                                    <div class="w-16 h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-[1.5rem] flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-indigo-100 group-hover:scale-105 transition-transform">
                                        {{ substr($case->user->name, 0, 1) }}
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 border-4 border-white rounded-full"></div>
                                    {{-- Hover İpucu --}}
                                    <div class="absolute -top-2 -left-2 bg-indigo-600 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity uppercase tracking-tighter">SİCİL</div>
                                </div>
                                <div class="mt-2 text-[8px] font-bold text-slate-400 uppercase tracking-tighter text-center">
                                    Önceki Kayıt: 
                                    <span class="font-black {{ $prevCount > 3 ? 'text-red-600' : ($prevCount > 0 ? 'text-amber-500' : 'text-slate-900') }}">
                                        {{ $prevCount }} Adet
                                    </span>
                                </div>
                            </div>

                            <div>
                                <h1 class="text-2xl lg:text-3xl font-black text-slate-900 tracking-tight leading-none mb-1 group-hover:text-indigo-600 transition-colors">
                                    {{ $case->user->name }}
                                </h1>
                                
                                {{-- İŞE GİRİŞ TARİHİ VE SÜRESİ --}}
                                <div class="text-[11px] font-bold text-slate-400 mb-3 flex flex-wrap items-center gap-x-3 gap-y-1">
                                    @if($case->user->hire_date)
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            İşe Giriş: <span class="text-slate-600 font-black uppercase tracking-tight">{{ $case->user->hire_date->format('d.m.Y') }}</span>
                                        </div>
                                        @php
                                            $diff = $case->user->hire_date->diff(now());
                                            $parts = [];
                                            if($diff->y > 0) $parts[] = $diff->y . ' Yıl';
                                            if($diff->m > 0) $parts[] = $diff->m . ' Ay';
                                            if($diff->d > 0) $parts[] = $diff->d . ' Gün';
                                            $duration = implode(', ', $parts);
                                        @endphp
                                        @if($duration)
                                            <div class="flex items-center gap-1.5 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-100">
                                                <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">KIDEM:</span>
                                                <span class="text-slate-600 font-black uppercase tracking-tight">{{ $duration }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="flex items-center gap-1.5 text-rose-500/70 font-black italic uppercase tracking-tight">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            işe giriş tarihi bilgisi bulunamadı
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-col gap-2">
                                    <span class="w-fit px-3 py-1 bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest rounded-lg border border-slate-200">
                                        {{ $case->user->bolum->ad ?? '-' }}
                                    </span>

                                    {{-- PERFORMANS PUANI --}}
                                    <span class="w-fit px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-emerald-100 flex items-center gap-1.5 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Mevcut Puan: {{ number_format($case->user->toplam_puan, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            {{-- GEÇMİŞ DROPDOWN (İçeride durması hizalama için daha iyi) --}}
                            <div x-show="showHistory" 
                                @click.away="showHistory = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                class="absolute left-0 top-full mt-4 w-[320px] md:w-[450px] bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-slate-100 z-[100] overflow-hidden no-print cursor-default"
                                @click.stop>
                                <div class="p-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-1">DİSİPLİN GEÇMİŞİ</span>
                                        <h3 class="text-sm font-black text-slate-800">{{ $case->user->name }}</h3>
                                    </div>
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded-full border border-indigo-100 italic">
                                        {{ $prevCount > 0 ? $prevCount . ' Önceki Kayıt' : 'Temiz Sicil' }}
                                    </span>
                                </div>

                                <div class="max-h-[350px] overflow-y-auto custom-scrollbar bg-white">
                                    @if($prevCount > 0)
                                        @foreach($previousCases as $prev)
                                            <a href="{{ route('admin.disiplin.show', $prev->id) }}" class="flex items-center gap-4 p-5 hover:bg-indigo-50/50 transition-all border-b border-slate-50 last:border-0 group/item">
                                                <div class="w-12 h-12 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col items-center justify-center group-hover/item:border-indigo-200 group-hover/item:bg-white transition-all shadow-sm">
                                                    <span class="text-[9px] font-black text-slate-300 uppercase leading-none mb-1">DOSYA</span>
                                                    <span class="text-xs font-black text-slate-600 group-hover/item:text-indigo-600">#{{ $prev->id }}</span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-black text-slate-800 truncate mb-1 uppercase tracking-tight">
                                                        {{ $prev->behavior->category->ad ?? 'Genel İhlal' }}
                                                    </p>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[10px] font-bold text-slate-400">
                                                            📅 {{ $prev->olay_tarihi->format('d.m.Y') }}
                                                        </span>
                                                        @if($prev->final_karar)
                                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                            <div class="flex items-center gap-1">
                                                                <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                                <span class="text-[10px] font-black text-rose-600 uppercase tracking-tighter italic">
                                                                    {{ Str::limit($prev->final_karar, 30) }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                            <span class="text-[10px] font-bold text-amber-500 uppercase tracking-tighter">
                                                                ⏳ {{ $prev->durum }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <svg class="w-5 h-5 text-slate-200 group-hover/item:text-indigo-400 group-hover/item:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="p-10 text-center">
                                            <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 mx-auto mb-4 border border-emerald-100 shadow-inner">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <p class="text-sm font-black text-slate-800 mb-1 italic">Tertemiz Bir Sicil!</p>
                                            <p class="text-xs text-slate-400 font-medium px-6">Bu personelin sistemde kayıtlı başka bir disiplin dosyası bulunmamaktadır.</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4 bg-indigo-600 text-center relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent animate-pulse"></div>
                                    <p class="relative text-[10px] font-black text-white uppercase tracking-[0.2em]">
                                        SİSTEMDE TOPLAM {{ $prevCount }} KAYIT BULUNMAKTADIR
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ORTA TARAF: İHLAL ÖZETİ --}}
                <div class="flex-1 lg:max-w-2xl">
                    <div class="bg-indigo-50/50 border border-indigo-100/50 rounded-[2rem] p-6 relative group hover:bg-indigo-50 transition-all shadow-sm">
                        <div class="absolute -left-1 top-1/2 -translate-y-1/2 w-1.5 h-16 bg-indigo-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity shadow-[0_0_10px_rgba(99,102,241,0.5)]"></div>
                        
                        {{-- Teknik Detay Yatay Yerleşim --}}
                        <div class="grid grid-cols-3 gap-6 mb-5">
                            <div class="space-y-1.5">
                                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.15em] block">KATEGORİ</span>
                                <span class="text-[11px] font-black text-slate-800 uppercase tracking-tight leading-tight block">{{ $case->behavior->category->ad ?? 'Genel İhlal' }}</span>
                            </div>
                            <div class="space-y-1.5">
                                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.15em] block">ETKİ VE ŞİDDET</span>
                                <span class="text-[11px] font-black text-slate-800 uppercase tracking-tight leading-tight block">{{ $case->impact->tanim ?? '-' }}</span>
                            </div>
                            <div class="space-y-1.5">
                                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.15em] block">KAPSAM</span>
                                <span class="text-[11px] font-black text-slate-800 uppercase tracking-tight leading-tight block">{{ $case->scope->tanim ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Madde Tanımı --}}
                        <div class="pt-4 border-t border-indigo-100/60">
                            <p class="text-slate-600 text-xs md:text-sm font-semibold leading-relaxed italic">
                                <span class="not-italic font-black text-indigo-600 uppercase text-[10px] tracking-wider mr-1.5">ÖZET:</span>
                                "{{ $case->behavior->tanim }}" maddesinden dolayı açılan disiplin süreci.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- SAĞ TARAF: AKSİYONLAR VE ID --}}
                <div class="flex flex-col sm:flex-row lg:flex-col items-center lg:items-end gap-6">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        @php
                            $isSuper = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']);
                            $canEdit = ($case->durum == 'Kurulda' 
                                ? Auth::user()->hasRole('Superadmin') 
                                : ($isSuper || (Auth::id() == $case->reporter_id && !$case->yonetici_degerlendirme_notu) || Auth::user()->can('disiplin.tutanak.duzenle')));
                            
                            $personelSlug = \Str::slug($case->user->name, '_');
                            $kararTarihFmt = $case->karar_tarihi ? $case->karar_tarihi->format('d_m_Y') : ($case->created_at ? $case->created_at->format('d_m_Y') : now()->format('d_m_Y'));
                            $pdfFileName = 'disiplin_karari_' . $personelSlug . '_' . $kararTarihFmt . '_' . $case->id . '.pdf';
                        @endphp
                        
                        @if($canEdit && $case->durum != 'Karar Verildi' && $case->durum != 'İptal')
                            <a href="{{ route('admin.disiplin.edit', $case->id) }}"
                                class="flex-1 lg:flex-none text-center bg-indigo-600 text-white px-6 py-3 rounded-xl text-xs font-black hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all uppercase tracking-wider">Düzenle</a>
                        @endif

                        <a href="javascript:void(0)" onclick="openPrintModal('print', '{{ route('admin.disiplin.print', $case->id) }}')"
                            id="btn-print"
                            class="p-3 bg-white text-slate-500 hover:text-indigo-600 rounded-xl border border-slate-200 hover:border-indigo-200 hover:shadow-md transition-all shadow-sm"
                            title="Yazdır">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        </a>
                        <a href="javascript:void(0)" onclick="openPrintModal('pdf', '{{ route('admin.disiplin.download-pdf', $case->id) }}', '{{ $pdfFileName }}')"
                            id="btn-download-pdf"
                            class="p-3 bg-slate-900 text-white hover:bg-slate-800 rounded-xl shadow-lg shadow-slate-200 transition-all flex items-center justify-center"
                            title="PDF İndir">
                            <span id="pdf-btn-icon" class="flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </span>
                            <span id="pdf-btn-spinner" class="hidden">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </a>
                    </div>
                    
                    <div class="text-right hidden lg:block">
                        <span class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-1">REFERANS DOSYA NO</span>
                        <div class="flex items-center justify-end gap-2">
                            <span class="w-2 h-2 rounded-full bg-slate-200 animate-pulse"></span>
                            <span class="text-xl font-black text-slate-300 italic">#{{ $case->id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-[1600px] mx-auto sm:px-6 lg:px-8">

            {{-- DURUM ÇUBUĞU --}}
            @php
                $durumRenk = match ($case->durum) {
                    'Savunma Bekleniyor' => 'yellow',
                    'Yönetici Değerlendirmesi' => 'blue',
                    'Kurulda' => 'purple',
                    'Karar Verildi' => 'green',
                    'İptal' => 'red',
                    default => 'gray'
                };
                $isSavunmaKabul = ($case->final_karar == 'Savunma Kabul Edildi (Ceza Yok)');
                $durumMetni = match ($case->durum) {
                    'Savunma Bekleniyor' => (Auth::id() == $case->user_id) ? 'Savunmanızı girmeniz bekleniyor.' : 'Personelden savunma bekleniyor.',
                    'Yönetici Değerlendirmesi' => 'Savunma girildi, yönetici onayı bekleniyor.',
                    'Karar Verildi' => $isSavunmaKabul 
                        ? 'Savunma haklı bulundu ve Dosya Kapatıldı (Ceza Uygulanmadı).' 
                        : 'Dosya Kapatıldı. Verilen Ceza: ' . ($case->manual_penalty_name ?? ($case->final_karar ?? 'Ceza Onaylandı.')) . ' (-' . $case->hesaplanan_puan . ' Puan)',
                    default => 'İşlem bekleniyor.'
                };
            @endphp
            {{-- ========================================================== --}}
            {{-- İTİRAZ DURUM BANNERLARI (MODERN TASARIM) --}}
            {{-- ========================================================== --}}
            @if($case->durum === 'İtiraz Edildi')
                {{-- ✦ İTİRAZ EDİLDİ BANNER — Premium Glassmorphism --}}
                <div class="relative mb-8 rounded-[2rem] overflow-hidden group">
                    {{-- Animated Gradient Background --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-violet-600 to-indigo-700"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent"></div>
                    <div class="absolute -right-20 -top-20 w-72 h-72 bg-white/5 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-1000"></div>
                    <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-violet-500/20 rounded-full blur-3xl"></div>

                    <div class="relative flex flex-col md:flex-row items-center gap-6 md:gap-8 p-6 md:p-8">
                        {{-- İkon --}}
                        <div class="flex-shrink-0 relative">
                            <div class="w-20 h-20 bg-white/10 backdrop-blur-xl border-2 border-white/20 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-indigo-900/30 group-hover:scale-105 transition-transform duration-500">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            </div>
                            <div class="absolute -top-1.5 -right-1.5 w-6 h-6 bg-amber-400 rounded-full flex items-center justify-center shadow-lg animate-bounce">
                                <svg class="w-3.5 h-3.5 text-amber-900" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            </div>
                        </div>

                        {{-- İçerik --}}
                        <div class="flex-1 text-center md:text-left space-y-3">
                            <div class="flex flex-col md:flex-row items-center md:items-center gap-2 md:gap-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 backdrop-blur-sm text-white/90 text-[10px] font-black uppercase tracking-[0.2em] rounded-full border border-white/20">
                                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span>
                                    Süreç Devam Ediyor
                                </span>
                                <h4 class="text-xl md:text-2xl font-black text-white tracking-tight uppercase">Dosya İtiraz Aşamasında</h4>
                            </div>
                            <p class="text-indigo-100/80 font-medium text-sm leading-relaxed max-w-xl">Bu karara resmi itiraz yapılmıştır. Dosya <span class="text-white font-black">2. tur değerlendirme</span> için Kurul gündemine alınmıştır.</p>
                        </div>

                        {{-- İtiraz Tarihi --}}
                        <div class="flex-shrink-0">
                            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl px-6 py-4 text-center shadow-inner">
                                <span class="block text-[9px] font-black text-indigo-200 uppercase tracking-[0.2em] mb-1.5">İtiraz Tarihi</span>
                                <span class="block text-lg font-black text-white tracking-tight">{{ $case->appeals()->latest()->first()?->created_at->format('d.m.Y') ?? '-' }}</span>
                                <span class="block text-[10px] font-bold text-indigo-200/60 mt-0.5">{{ $case->appeals()->latest()->first()?->created_at->format('H:i') ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($case->is_appeal_window_open)
                {{-- ✦ İTİRAZ HAKKI AKTİF BANNER — Premium Gradient Card --}}
                @php
                    $deadline = $case->appeal_deadline;
                    $diff = now()->diff($deadline);
                    $daysLeft = ceil(now()->diffInHours($deadline) / 24);
                    if ($daysLeft < 1 && now()->diffInHours($deadline) > 0) $daysLeft = 1;
                    $isUrgent = $daysLeft <= 1;
                    $totalDays = 3;
                    $progressPercent = max(0, min(100, (($totalDays - $daysLeft) / $totalDays) * 100));
                @endphp
                <div class="relative mb-6 rounded-3xl overflow-hidden group border-2 {{ $isUrgent ? 'border-rose-400 bg-rose-50' : 'border-amber-400 bg-amber-50' }} animate-[pulse_3s_infinite]">
                    {{-- Decorative Patterns --}}
                    <div class="absolute inset-0 opacity-40">
                        <div class="absolute -right-12 -top-12 w-48 h-48 {{ $isUrgent ? 'bg-rose-200' : 'bg-amber-200' }} rounded-full blur-3xl"></div>
                        <div class="absolute -left-6 -bottom-6 w-32 h-32 {{ $isUrgent ? 'bg-rose-100' : 'bg-amber-100' }} rounded-full blur-2xl"></div>
                    </div>

                    <div class="relative flex flex-col lg:flex-row items-center gap-4 lg:gap-6 p-4 md:p-6">
                        {{-- Sayaç Bölümü --}}
                        <div class="flex items-center gap-4 flex-shrink-0">
                            <div class="relative">
                                {{-- Dairesel Sayaç --}}
                                <div class="w-20 h-20 md:w-24 md:h-24 relative">
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="42" fill="none" stroke="{{ $isUrgent ? 'rgba(225,29,72,0.1)' : 'rgba(217,119,6,0.1)' }}" stroke-width="6"/>
                                        <circle cx="50" cy="50" r="42" fill="none" stroke="{{ $isUrgent ? '#e11d48' : '#d97706' }}" stroke-width="6" stroke-linecap="round"
                                            stroke-dasharray="{{ 2 * 3.14159 * 42 }}" 
                                            stroke-dashoffset="{{ 2 * 3.14159 * 42 * (1 - ($daysLeft / $totalDays)) }}" 
                                            class="transition-all duration-1000"/>
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-3xl md:text-4xl font-black {{ $isUrgent ? 'text-rose-600 animate-pulse' : 'text-amber-600' }} leading-none">{{ (int)$daysLeft }}</span>
                                        <span class="text-[7px] md:text-[8px] font-black {{ $isUrgent ? 'text-rose-400' : 'text-amber-400' }} uppercase tracking-[0.2em] mt-0.5">Gün</span>
                                    </div>
                                </div>
                                @if($isUrgent)
                                    <div class="absolute -top-1 -right-1 px-1.5 py-0.5 bg-rose-600 rounded-full shadow-lg shadow-rose-200">
                                        <span class="text-[7px] font-black text-white uppercase tracking-wider animate-pulse">ACİL</span>
                                    </div>
                                @endif
                            </div>
                            <div class="hidden lg:block w-px h-16 {{ $isUrgent ? 'bg-rose-200' : 'bg-amber-200' }} rounded-full"></div>
                        </div>

                        {{-- İçerik --}}
                        <div class="flex-1 text-center lg:text-left">
                            <div class="space-y-1">
                                <h4 class="text-lg md:text-xl font-black {{ $isUrgent ? 'text-rose-900' : 'text-amber-900' }} tracking-tight uppercase leading-tight">
                                    {{ Auth::id() == $case->user_id ? 'İtiraz Hakkınız Aktif' : 'Personelin İtiraz Hakkı Aktif' }}
                                </h4>
                                <p class="{{ $isUrgent ? 'text-rose-700/70' : 'text-amber-700/70' }} font-semibold text-xs md:text-sm max-w-lg">
                                    {{ Auth::id() == $case->user_id ? 'Verilen karara itiraz etmek için aşağıdaki butonu kullanabilirsiniz.' : 'Personel bu süre zarfında karara itiraz edebilir.' }}
                                </p>
                            </div>
                            
                            {{-- Bilgi Kapsülleri --}}
                            <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2 mt-3">
                                <div class="flex items-center gap-1.5 {{ $isUrgent ? 'bg-rose-100/50 text-rose-700' : 'bg-amber-100/50 text-amber-700' }} px-3 py-1.5 rounded-lg border {{ $isUrgent ? 'border-rose-200' : 'border-amber-200' }} text-[11px] font-black">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="uppercase tracking-wider">Son:</span>
                                    <span class="font-black">{{ $deadline->format('d.m.Y') }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 {{ $isUrgent ? 'bg-rose-100/50 text-rose-700' : 'bg-amber-100/50 text-amber-700' }} px-3 py-1.5 rounded-lg border {{ $isUrgent ? 'border-rose-200' : 'border-amber-200' }} text-[11px] font-black">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="uppercase tracking-wider">Saat:</span>
                                    <span class="font-black">23:59</span>
                                </div>
                            </div>
                        </div>

                        {{-- Aksiyon Butonu --}}
                        @php
                            $user = Auth::user();
                            $canAppeal = ($case->user_id == $user->id) 
                                || ($user->hasRole('Bölüm Lideri') && $user->bolum_id == $case->user->bolum_id)
                                || ($user->hasRole('Bölüm Lider Yardımcısı') && $user->bolum_id == $case->user->bolum_id && $user->can('disiplin.itiraz.vekaleten'))
                                || $user->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı']);
                        @endphp
                        
                        @if($canAppeal)
                            <div class="flex-shrink-0 w-full lg:w-auto">
                                <button onclick="openAppealModal()" class="w-full lg:w-auto relative {{ $isUrgent ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-rose-200' : 'bg-amber-600 hover:bg-amber-700 text-white shadow-amber-200' }} px-8 py-3.5 rounded-2xl font-black shadow-xl transition-all hover:-translate-y-1 active:translate-y-0 flex items-center justify-center gap-3 text-sm md:text-base overflow-hidden">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span class="uppercase tracking-widest">{{ Auth::id() == $case->user_id ? 'İtiraz Et' : 'Personel Adına İtiraz Et' }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Alt Progress Bar --}}
                    <div class="relative h-1.5 bg-black/10">
                        <div class="absolute inset-y-0 left-0 bg-white/40 rounded-r-full transition-all duration-1000" style="width: {{ 100 - $progressPercent }}%"></div>
                    </div>
                </div>

                {{-- İTİRAZ MODALI --}}
                <div id="appealModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeAppealModal()"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
                            <form action="{{ route('disiplin.appeal.submit', $case->id) }}" method="POST">
                                @csrf
                                <div class="bg-white px-8 pt-8 pb-6">
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </div>
                                            <div>
                                                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight italic" id="modal-title">Karara İtiraz Formu</h3>
                                                <p class="text-slate-500 font-medium">Lütfen itiraz gerekçenizi detaylıca açıklayınız.</p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="closeAppealModal()" class="text-slate-400 hover:text-slate-600 p-2 hover:bg-slate-100 rounded-full transition-all">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    <div class="space-y-4">
                                        @if(Auth::id() != $case->user_id)
                                            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl">
                                                <p class="text-sm text-amber-700 font-bold">👤 Vekâleten İtiraz</p>
                                                <p class="text-xs text-amber-600 mt-1 font-medium">Bu itirazı <span class="font-black">{{ $case->user->name }}</span> adına gerçekleştiriyorsunuz. İşlem log kaydına vekâlet bilgisi ile işlenecektir.</p>
                                            </div>
                                        @endif
                                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-xl">
                                            <p class="text-sm text-blue-700 font-bold">⚠️ Önemli Hatırlatma:</p>
                                            <p class="text-xs text-blue-600 mt-1 font-medium">İtiraz hakkı bu dosya için sadece 1 defaya mahsustur. İtirazınız kurul tarafından tekrar değerlendirilecek ve verilecek olan 2. karar kesin olacaktır.</p>
                                        </div>

                                        <div>
                                            <label for="reason" class="block text-sm font-black text-slate-700 uppercase tracking-wider mb-2">İtiraz Gerekçesi</label>
                                            <textarea name="reason" id="reason" rows="6" required minlength="10"
                                                class="w-full rounded-2xl border-slate-200 focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 text-slate-700 font-medium placeholder-slate-400 transition-all"
                                                placeholder="Kararın hangi noktalarına neden itiraz ettiğinizi belirtiniz..."></textarea>
                                            <p class="text-[10px] text-slate-400 mt-2 font-bold uppercase">En az 10 karakter girilmelidir.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3 border-t border-slate-200">
                                    <button type="submit" class="w-full sm:w-auto bg-amber-600 hover:bg-amber-700 text-white px-8 py-3 rounded-xl font-black shadow-lg shadow-amber-200 transition-all flex items-center justify-center gap-2">
                                        İtirazı Gönder
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                                    </button>
                                    <button type="button" onclick="closeAppealModal()" class="w-full sm:w-auto bg-white hover:bg-slate-100 text-slate-700 px-8 py-3 rounded-xl font-bold border border-slate-200 transition-all">
                                        Vazgeç
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    function openAppealModal() {
                        document.getElementById('appealModal').classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    }
                    function closeAppealModal() {
                        document.getElementById('appealModal').classList.add('hidden');
                        document.body.style.overflow = 'auto';
                    }
                </script>
            @endif

            <div
                class="bg-{{ $durumRenk }}-50 border-l-4 border-{{ $durumRenk }}-500 p-4 mb-6 rounded-r shadow-sm flex justify-between items-center transition-all duration-500">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white rounded-full text-{{ $durumRenk }}-600 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-{{ $durumRenk }}-800 text-lg">
                            Dosya Durumu: {{ $case->durum == 'Karar Verildi' ? 'Dosya Kapatıldı' : $case->durum }}
                        </p>
                        @if($case->durum == 'Karar Verildi')
                            @php
                                $isAdmin = Auth::user()->hasRole(['Superadmin', 'Yonetim', 'Yönetim', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']);
                            @endphp

                            <div class="mt-2 flex flex-col gap-1.5">
                                @if($isAdmin)
                                    {{-- Yönetici Görünümü: Tüm detaylar --}}
                                    <div class="flex items-center gap-2 bg-white/50 backdrop-blur-sm rounded-lg px-3 py-1 border border-gray-100 shadow-sm w-fit">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest border-r border-gray-200 pr-2">SİSTEM ÖNERİSİ</span>
                                        <span class="text-xs font-bold text-gray-500 uppercase">{{ $case->sistem_oneri_ceza }}</span>
                                    </div>
                                    
                                    @if($case->manual_penalty_name)
                                        <div class="flex items-center gap-2 bg-rose-50/50 backdrop-blur-sm rounded-lg px-3 py-1 border border-rose-100 shadow-sm w-fit">
                                            <span class="text-[9px] font-black text-rose-400 uppercase tracking-widest border-r border-rose-200 pr-2">MANUEL SEÇİLEN</span>
                                            <span class="text-xs font-black text-rose-700 uppercase">{{ $case->manual_penalty_name }} <span class="ml-1 opacity-70">(-{{ $case->hesaplanan_puan }} Puan)</span></span>
                                        </div>
                                    @endif
                                @else
                                    {{-- Personel Görünümü: Sadece verilen ceza (manuel olduğu gizlenerek) --}}
                                    <div class="flex items-center gap-2 bg-rose-50/50 backdrop-blur-sm rounded-lg px-3 py-1 border border-rose-100 shadow-sm w-fit">
                                        <span class="text-[9px] font-black text-rose-400 uppercase tracking-widest border-r border-rose-200 pr-2">VERİLEN CEZA</span>
                                        <span class="text-xs font-black text-rose-700 uppercase">
                                            {{ $case->manual_penalty_name ?? $case->sistem_oneri_ceza }} 
                                            @if(!$isSavunmaKabul)
                                                <span class="ml-1 opacity-70">(-{{ $case->hesaplanan_puan }} Puan)</span>
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-xs {{ !$isSavunmaKabul && $case->durum == 'Karar Verildi' ? 'text-red-600' : 'text-'.$durumRenk.'-600' }} font-bold {{ (Auth::id() == $case->user_id && $case->durum == 'Savunma Bekleniyor') ? '' : 'uppercase' }} tracking-tight">{{ $durumMetni }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center gap-4 flex-shrink-0">
                    @if($case->durum == 'Savunma Bekleniyor' && Auth::id() == $case->user_id)
                        <button onclick="const el = document.getElementById('defense_section'); if(el) { el.scrollIntoView({behavior: 'smooth'}); } else { window.location.href='{{ route('admin.disiplin.show', $case->id) }}?tab=detay#defense_section'; }" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-amber-200 transition-all flex items-center gap-2 border border-amber-500 hover:scale-[1.02] active:scale-95 group">
                            Savunma Yazmak İçin Tıklayın
                            <svg class="w-5 h-5 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        </button>
                    @endif

                    @if($case->oylama_aktif && $case->durum != 'Karar Verildi' && Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']))
                        @php
                            $currentRound = ($case->rediscussion_count ?? 0) + 1;
                            $hasVoted = $case->oylar()->where('user_id', Auth::id())->where('round', $currentRound)->exists();
                        @endphp
                        @if(!$hasVoted)
                            <button onclick="const el = document.getElementById('voting_section'); if(el) { el.scrollIntoView({behavior: 'smooth', block: 'center'}); } else { window.location.href='{{ route('admin.disiplin.show', $case->id) }}?tab=kurul#voting_section'; }" class="relative bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 transition-all flex items-center gap-2 border border-indigo-500 hover:scale-105">
                                <span class="absolute -top-1.5 -right-1.5 flex h-3.5 w-3.5">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-rose-500"></span>
                                </span>
                                <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                                OY KULLAN
                            </button>
                        @endif
                    @endif

                    @if($case->durum == 'Yönetici Değerlendirmesi' && (Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']) || Auth::user()->can('disiplin.degerlendirme.kullan')))
                        <button onclick="document.getElementById('manager_evaluation_section').scrollIntoView({behavior: 'smooth'})" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-200 transition-all flex items-center gap-2 border border-blue-500 hover:scale-[1.02] active:scale-95 group">
                            Değerlendirme Yapmak İçin Tıklayın
                            <svg class="w-5 h-5 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        </button>
                    @endif

                    @if(($case->rediscussion_count > 0 || $case->durum == 'Karar Verildi') && Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']))
                        @php
                            $isClosed = ($case->durum == 'Karar Verildi');
                            $bannerColor = $isClosed ? 'emerald' : 'amber';
                        @endphp
                        <div class="flex-shrink-0 text-right {{ !$isClosed ? 'animate-pulse' : '' }}" style="animation-duration: 3s;">
                            <div class="bg-white/80 backdrop-blur-sm rounded-xl px-3 py-2 border border-{{ $bannerColor }}-200 shadow-md shadow-{{ $bannerColor }}-100/40">
                                <div class="flex items-center justify-end gap-1.5 mb-0.5">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-{{ $bannerColor }}-500 {{ !$isClosed ? 'animate-ping' : '' }}" style="animation-duration: 2s;"></span>
                                    <span class="text-[9px] font-black text-{{ $bannerColor }}-800 uppercase tracking-widest">
                                        {{ $isClosed ? 'Görüşme Tamamlandı' : 'Görüşülme Durumu' }}
                                    </span>
                                </div>
                                <div class="text-base font-black text-{{ $bannerColor }}-700">
                                    {{ $case->rediscussion_count + 1 }}. {{ $isClosed ? 'Tur Kararı' : 'Kez' }}
                                </div>
                                
                                @if($isClosed)
                                    <div class="mt-1 pt-1 border-t border-emerald-100">
                                        <div class="text-[8px] font-bold text-gray-400 uppercase tracking-wider text-right">Karar Tarihi</div>
                                        <div class="text-[11px] font-black text-emerald-700 flex items-center justify-end gap-1">
                                            <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            {{ ($case->oylama_bitti_at ?? $case->updated_at)->format('d.m.Y H:i') }}
                                        </div>
                                    </div>
                                @elseif($case->toplanti_tarihi)
                                    <div class="mt-1 pt-1 border-t border-amber-100">
                                        <div class="text-[8px] font-bold text-gray-400 uppercase tracking-wider">Planlanan Toplantı</div>
                                        <div class="text-[11px] font-black text-blue-700 flex items-center justify-end gap-1">
                                            <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ \Carbon\Carbon::parse($case->toplanti_tarihi)->format('d.m.Y H:i') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            
            {{-- OYLAMA AKTİF UYARISI (Global Banner) --}}
            @php
                $activeTab = request()->get('tab', session('tab', 'detay'));
            @endphp
            @if($case->oylama_aktif && $case->durum != 'Karar Verildi' && $activeTab !== 'kurul')
                @php
                    $isAuthorizedToVote = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) 
                        || (Auth::user()->hasRole('Hukuk Yöneticisi') && Auth::user()->can('disiplin.kurul.portal.gor'));
                @endphp
                @if($isAuthorizedToVote)
                    <div class="relative overflow-hidden bg-white border-2 border-indigo-500 rounded-2xl p-4 mb-6 shadow-xl animate-in zoom-in duration-300">
                        <div class="absolute inset-0 bg-indigo-50/50 animate-pulse"></div>
                        <div class="relative flex flex-col md:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-indigo-900 font-black text-lg flex items-center gap-2">
                                        KARAR OYLAMASI DEVAM EDİYOR
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-bounce">AKTİF</span>
                                    </h4>
                                    <p class="text-indigo-700/80 text-sm font-medium">Bu dosya için Disiplin Kurulu karar oylaması başlatılmıştır. Görüşlerinizi bildirmek için odaya giriş yapınız.</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.disiplin.show', $case->id) }}?tab=kurul" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg shadow-indigo-200 flex items-center justify-center gap-2 group">
                                Oylama Odasına Git
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @endif
            @endif

            {{-- PERSONEL İÇİN KURUL BİLGİLENDİRMESİ --}}
            @if($case->durum == 'Kurulda' && !Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']))
                @php
                    $latestAppeal = $case->is_appealed ? $case->appeals()->latest()->first() : null;
                    $meetings = $case->toplantilar()->orderBy('baslangic_tarihi', 'asc')->get();
                @endphp
                <div class="mt-4 bg-orange-50/50 border border-orange-200 rounded-3xl p-4 mb-4 shadow-xl shadow-orange-100/20 relative overflow-hidden animate-in fade-in slide-in-from-bottom-2 duration-500">
                    {{-- Arka Plan Dekoru --}}
                    <div class="absolute -right-16 -bottom-16 w-32 h-32 bg-orange-100 rounded-full opacity-50 blur-2xl"></div>
                    
                    <div class="relative flex flex-col md:flex-row items-center gap-5">
                        <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600 shadow-sm border border-orange-200/50">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </div>
                        
                        <div class="flex-1 text-center md:text-left">
                            @if($case->is_appealed)
                                <div class="flex items-center justify-center md:justify-start gap-3 mb-1">
                                    <h4 class="text-sm font-black text-orange-800 tracking-tight italic uppercase">İTİRAZ SONRASI KURUL DEĞERLENDİRMESİ</h4>
                                </div>
                                <p class="text-orange-700/70 font-medium text-[11px]">İtirazınız üzerine dosyanız 2. tur değerlendirme için tekrar Kurul'a sevk edilmiştir.</p>
                                @if($case->toplanti_tarihi)
                                    <div class="mt-2 flex items-center justify-center md:justify-start gap-2">
                                        <span class="text-[10px] font-black text-orange-600 uppercase tracking-tight">Güncel Toplantı:</span>
                                        <span class="px-2 py-0.5 bg-orange-600 text-white text-[9px] font-bold rounded-lg shadow-sm">📅 {{ $case->toplanti_tarihi->format('d.m.Y') }}</span>
                                    </div>
                                @endif
                            @else
                                <div class="flex items-center justify-center md:justify-start gap-3 mb-1">
                                    <h4 class="text-sm font-black text-orange-800 tracking-tight italic uppercase">Disiplin Kurulu Değerlendirmesi</h4>
                                </div>
                                <p class="text-orange-700/70 font-medium text-[11px]">Dosyanız Disiplin Kurulu'na sevk edilmiştir. Kurul üyeleri tarafından incelenmektedir.</p>
                                @if($case->toplanti_tarihi)
                                    <div class="mt-2 flex items-center justify-center md:justify-start gap-2">
                                        <span class="text-[10px] font-black text-orange-600 uppercase tracking-tight">Planlanan Toplantı:</span>
                                        <span class="px-2 py-0.5 bg-orange-600 text-white text-[9px] font-bold rounded-lg shadow-sm">📅 {{ $case->toplanti_tarihi->format('d.m.Y') }}</span>
                                    </div>
                                @endif
                            @endif

                            {{-- Geçmiş Toplantılar --}}
                            @if($meetings->count() > 1)
                                <div class="mt-3 flex flex-wrap justify-center md:justify-start gap-2">
                                    @foreach($meetings as $index => $meeting)
                                        <div class="flex items-center gap-1 px-2 py-0.5 bg-white/60 border border-orange-100 rounded text-[9px] font-bold text-orange-800">
                                            <span class="opacity-50">{{ $index + 1 }}. Tur:</span>
                                            {{ $meeting->baslangic_tarihi->format('d.m.Y') }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if($case->is_appealed && $latestAppeal)
                            <div class="flex-shrink-0 w-full md:w-64 bg-white/60 backdrop-blur-sm rounded-xl p-3 border border-orange-100 text-left">
                                <div class="flex items-center justify-between mb-1.5 border-b border-orange-100 pb-1">
                                    <span class="text-[9px] font-black text-orange-400 uppercase tracking-widest">İtirazınız</span>
                                    <span class="text-[9px] font-bold text-orange-400">{{ $latestAppeal->created_at->format('d.m.Y') }}</span>
                                </div>
                                <p class="text-orange-800 italic font-medium text-[11px] line-clamp-2" title="{{ $latestAppeal->reason }}">"{{ $latestAppeal->reason }}"</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- SEKME NAVİGASYONU (Sadece Kurul Süreci Varsa) --}}
            @php
                $hasMeeting = $case->toplantilar()->exists();
            @endphp
            @if((Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) || Auth::user()->can('disiplin.kurul.portal.gor')) && ( $case->durum === 'Kurulda' || ($case->durum === 'Karar Verildi' && $hasMeeting) ))
                @php
                    $activeTab = request()->get('tab', session('tab', 'detay'));
                @endphp
                <div class="flex gap-1 mb-6 bg-slate-100 p-1 rounded-xl w-fit no-print">
                    <a href="{{ route('admin.disiplin.show', $case->id) }}?tab=detay"
                        class="px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 flex items-center gap-2 {{ $activeTab === 'detay' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500 hover:text-slate-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Dosya Detayı
                    </a>
                    <a href="{{ route('admin.disiplin.show', $case->id) }}?tab=kurul"
                        class="px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 flex items-center gap-2 {{ $activeTab === 'kurul' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 shadow-lg shadow-indigo-200/60 text-white' : 'text-slate-500 hover:text-slate-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        🏛️ Disiplin Kurulu Odası
                    </a>
                </div>
            @endif

            {{-- İÇERİK ALANI (Aktif Sekmeye Göre) --}}
            @php $activeTab = request()->get('tab', session('tab', 'detay')); @endphp
            
            @if($activeTab === 'detay')
                {{-- DOSYA DETAYLARI --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-in fade-in slide-in-from-bottom-2 duration-500">
                    {{-- SOL KOLON (MD:COL-SPAN-2) --}}
                    <div class="md:col-span-2 space-y-6">
                        @include('admin.disiplin.partials.case-details')
                        @include('admin.disiplin.partials.defense-section')
                        @include('admin.disiplin.partials.manager-actions')
                        @include('admin.disiplin.partials.comments')
                    </div>

                    {{-- SAĞ KOLON (SIDEBAR) --}}
                    <div class="space-y-6">
                        @include('admin.disiplin.partials.sidebar')
                    </div>
                </div>
            @elseif($activeTab === 'kurul' && (Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) || Auth::user()->can('disiplin.kurul.portal.gor')) && ($case->durum === 'Kurulda' || ($case->durum === 'Karar Verildi' && $hasMeeting)))
                {{-- DİSİPLİN KURULU ODASI --}}
                <div class="animate-in fade-in slide-in-from-bottom-2 duration-500">
                    <div class="w-full">
                        <livewire:admin.disiplin.disiplin-oylama-paneli :case="$case" :key="'voting-tab-'.$case->id" />
                    </div>
                </div>
            @else
                {{-- Geçersiz sekme veya yetkisiz erişim durumunda varsayılan detay (TÜM BİLGİLERİ GÖSTER) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-6">
                        @include('admin.disiplin.partials.case-details')
                        @include('admin.disiplin.partials.defense-section')
                        @include('admin.disiplin.partials.manager-actions')
                        @include('admin.disiplin.partials.comments')
                    </div>
                    <div class="space-y-6">
                         @include('admin.disiplin.partials.sidebar')
                    </div>
                </div>
            @endif



            {{-- İŞLEM GEÇMİŞİ (SAYFA ALTI) --}}
        </div>
        {{-- İŞLEM GEÇMİŞİ (TAM GENİŞLİK) --}}
        <div class="max-w-full mx-auto sm:px-6 lg:px-12">
            @include('admin.disiplin.partials.logs')
        </div>
    </div>

    {{-- Scripts now in layouts/app --}}
    
    {{-- PDF Yükleme Bildirimi (Premium Glassmorphic Toast) --}}
    <div id="pdf-toast" class="fixed bottom-5 right-5 z-[9999] hidden animate-in fade-in slide-in-from-bottom-5 duration-300">
        <div class="bg-slate-900/95 backdrop-blur-md text-white rounded-2xl px-6 py-4 shadow-2xl border border-slate-800 flex items-center gap-4">
            <svg class="animate-spin h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <div>
                <p class="text-xs font-black tracking-wider uppercase text-indigo-400">Dosya Oluşturuluyor</p>
                <p class="text-sm font-black tracking-tight text-white mt-0.5">PDF Dosyanız Hazırlanıyor</p>
                <p class="text-[11px] text-slate-400 font-medium">Bu işlem birkaç saniye sürebilir, lütfen bekleyiniz...</p>
            </div>
        </div>
    </div>

    {{-- Print/PDF Modal --}}
    @php
        $puan = $case->hesaplanan_puan;
        $penaltyScale = \App\Models\DisciplinaryPenaltyScale::where('min_puan', '<=', $puan)->where('max_puan', '>=', $puan)->first();
        $hasTemplate = $penaltyScale && !empty($penaltyScale->karar_metni);
        $hasManualText = !empty($case->yonetici_notu);
    @endphp

    <div id="print-modal" class="fixed inset-0 z-[9999] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closePrintModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Çıktı İçin Karar Metni Seçimi
                            </h3>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 mb-4">Lütfen belgeye yazdırılacak olan karar metnini seçiniz:</p>
                                
                                <div class="space-y-3">
                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors @if(!$hasTemplate) opacity-50 @endif">
                                        <div class="flex items-center h-5">
                                            <input type="radio" name="print_text_type" value="sablon" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" onchange="updateTextarea()"
                                                @if($hasTemplate) checked @else disabled @endif>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">Şablon Metni (Varsayılan)</span>
                                            <p class="text-gray-500">Ayarlarda tanımlanmış dinamik değişkenli standart şablon metni.</p>
                                            @if(!$hasTemplate) <p class="text-red-500 text-xs mt-1">Bu ceza skalası için şablon bulunamadı.</p> @endif
                                        </div>
                                    </label>

                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors @if(!$hasManualText) opacity-50 @endif">
                                        <div class="flex items-center h-5">
                                            <input type="radio" name="print_text_type" value="manuel" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" onchange="updateTextarea()"
                                                @if(!$hasTemplate && $hasManualText) checked @elseif(!$hasManualText) disabled @endif>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">Manuel Gerekçe (Kurul)</span>
                                            <p class="text-gray-500">Kurul ekranında yazılan "Nihai Karar Gerekçesi" metni.</p>
                                            @if(!$hasManualText) <p class="text-red-500 text-xs mt-1">Nihai karar gerekçesi girilmemiş.</p> @endif
                                        </div>
                                    </label>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Karar Metnini Düzenle (İsteğe Bağlı):</label>
                                    <textarea id="custom_karar_metni" rows="6" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-3" style="border:1px solid #ccc;"></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Bu metni belgeye yazdırmadan önce dilediğiniz gibi düzenleyebilirsiniz. (Not: Metni **kalın** yapmak için iki yıldız arasına alabilirsiniz)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="executePrint()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Devam Et
                    </button>
                    <button type="button" onclick="closePrintModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        İptal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-disabled-pdf {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
        }
        .btn-disabled-pdf:hover {
            transform: none !important;
            box-shadow: none !important;
            background-color: inherit !important;
            color: inherit !important;
        }
    </style>

    <script>
        let isPdfDownloading = false;
        let currentPrintType = '';
        let currentPrintUrl = '';
        let currentPrintFilename = '';

        const rawSablon = {!! json_encode($rawSablon ?? '') !!};
        const rawManuel = {!! json_encode($rawManuel ?? '') !!};

        function updateTextarea() {
            const selectedType = document.querySelector('input[name="print_text_type"]:checked');
            const textarea = document.getElementById('custom_karar_metni');
            if (selectedType && selectedType.value === 'sablon') {
                textarea.value = rawSablon;
            } else if (selectedType && selectedType.value === 'manuel') {
                textarea.value = rawManuel;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateTextarea();
        });

        function openPrintModal(type, url, filename = null) {
            currentPrintType = type;
            currentPrintUrl = url;
            currentPrintFilename = filename;
            updateTextarea();
            document.getElementById('print-modal').classList.remove('hidden');
        }

        function closePrintModal() {
            document.getElementById('print-modal').classList.add('hidden');
        }

        function executePrint() {
            const selectedType = document.querySelector('input[name="print_text_type"]:checked');
            if (!selectedType) {
                alert('Lütfen bir metin türü seçiniz.');
                return;
            }
            
            const metinTuru = selectedType.value;
            const customText = document.getElementById('custom_karar_metni').value;
            
            let finalUrl = currentPrintUrl + (currentPrintUrl.includes('?') ? '&' : '?') + 'metin_turu=' + metinTuru;
            
            if (customText.trim() !== '') {
                finalUrl += '&custom_karar_metni=' + encodeURIComponent(customText);
            }
            
            closePrintModal();
            
            if (currentPrintType === 'print') {
                window.open(finalUrl, '_blank');
            } else if (currentPrintType === 'pdf') {
                downloadPDF(null, finalUrl, currentPrintFilename);
            }
        }

        function downloadPDF(event, url, filename) {
            if (event) event.preventDefault();
            if (isPdfDownloading) return false;

            const btnPdf = document.getElementById('btn-download-pdf');
            const btnPrint = document.getElementById('btn-print');
            const pdfIcon = document.getElementById('pdf-btn-icon');
            const pdfSpinner = document.getElementById('pdf-btn-spinner');
            const toast = document.getElementById('pdf-toast');

            // Set state
            isPdfDownloading = true;

            // Apply disabled styling & cursor
            btnPdf.classList.add('btn-disabled-pdf');
            if (btnPrint) {
                btnPrint.classList.add('btn-disabled-pdf');
                btnPrint.setAttribute('title', 'PDF Hazırlanırken yazdırılamaz');
            }
            btnPdf.setAttribute('title', 'PDF Hazırlanıyor...');

            // Show spinner on button
            if (pdfIcon) pdfIcon.classList.add('hidden');
            if (pdfSpinner) pdfSpinner.classList.remove('hidden');

            // Show toast
            if (toast) toast.classList.remove('hidden');

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('PDF generation failed');
                    return response.blob();
                })
                .then(blob => {
                    const blobUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = blobUrl;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(blobUrl);
                })
                .catch(error => {
                    console.error(error);
                    alert('PDF dosyası oluşturulurken bir hata meydana geldi. Lütfen tekrar deneyiniz.');
                })
                .finally(() => {
                    // Reset state
                    isPdfDownloading = false;

                    // Remove disabled styling
                    btnPdf.classList.remove('btn-disabled-pdf');
                    if (btnPrint) {
                        btnPrint.classList.remove('btn-disabled-pdf');
                        btnPrint.setAttribute('title', 'Yazdır');
                    }
                    btnPdf.setAttribute('title', 'PDF İndir');

                    // Hide spinner
                    if (pdfIcon) pdfIcon.classList.remove('hidden');
                    if (pdfSpinner) pdfSpinner.classList.add('hidden');

                    // Hide toast
                    if (toast) toast.classList.add('hidden');
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const btnPrint = document.getElementById('btn-print');
            if (btnPrint) {
                btnPrint.addEventListener('click', function(e) {
                    if (isPdfDownloading) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });
            }
        });
    </script>
</x-app-layout>