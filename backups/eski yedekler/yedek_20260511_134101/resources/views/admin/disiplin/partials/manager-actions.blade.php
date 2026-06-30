@php
    $cardColor = 'slate'; 
    $cardIcon = 'info-circle';
    $cardTitle = 'Durum Belirsiz';

    if ($case->durum == 'Karar Verildi') {
        if ($case->final_karar == 'Savunma Kabul Edildi (Ceza Yok)') {
            $cardColor = 'emerald';
            $cardIcon = 'check-circle';
            $cardTitle = 'Dosya Kapatıldı - Savunma Kabul Edildi';
        } else {
            $cardColor = 'rose';
            $cardIcon = 'exclamation-circle';
            $cardTitle = 'Dosya Kapatıldı - Ceza Onaylandı';
        }
    } elseif ($case->durum == 'Kurulda') {
        $cardColor = 'amber';
        $cardIcon = 'users';
        $cardTitle = 'Dosya Disiplin Kuruluna Sevk Edildi';
    }
@endphp

{{-- SONUÇ EKRANI --}}
@if(in_array($case->durum, ['Karar Verildi', 'Kurulda']))
    <div class="mt-6 relative">
        <div class="bg-gradient-to-br from-{{ $cardColor }}-50 to-white border-l-[6px] border-{{ $cardColor }}-500 p-8 shadow-lg rounded-r-2xl relative overflow-hidden">
            
            <div class="absolute top-0 right-0 w-64 h-64 bg-{{ $cardColor }}-100 opacity-20 rounded-full -mr-32 -mt-32 blur-2xl"></div>
            
            <div class="relative flex flex-col md:flex-row justify-between items-start gap-6">
                <div class="flex-1 w-full">
                    
                    {{-- Başlık ve İkon --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-{{ $cardColor }}-500 to-{{ $cardColor }}-700 rounded-2xl flex items-center justify-center shadow-lg transform -rotate-2">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($cardIcon == 'check-circle') <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/> 
                                @elseif($cardIcon == 'exclamation-circle') <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                @else <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                @endif
                            </svg>
                        </div>
                        
                        <div>
                            <h3 class="text-2xl font-black text-{{ $cardColor }}-900 leading-tight">{{ $cardTitle }}</h3>
                            
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                {{-- İşlem Tarihi --}}
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-white text-{{ $cardColor }}-700 border border-{{ $cardColor }}-200 shadow-sm">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    İşlem: {{ $case->karar_tarihi ? $case->karar_tarihi->format('d.m.Y H:i') : 'Tarih Yok' }}
                                </span>

                                {{-- YENİ EKLENEN: Toplantı Tarihi --}}
                                @if($case->toplanti_tarihi)
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 shadow-sm">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        Toplantı: {{ $case->toplanti_tarihi->format('d.m.Y H:i') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        {{-- Nihai Karar --}}
                        @if($case->final_karar)
                            <div class="bg-white/60 backdrop-blur-sm rounded-xl p-4 border border-{{ $cardColor }}-200">
                                <span class="block text-[10px] font-bold text-{{ $cardColor }}-400 uppercase tracking-wider mb-1">Nihai Karar</span>
                                <p class="text-lg font-bold text-gray-800">{{ $case->final_karar }}</p>
                            </div>
                        @endif
                        
                        {{-- Açıklama --}}
                        @php
                            $isDisciplinedPerson = Auth::id() == $case->user_id;
                            $hasAuthorizedRole = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) || Auth::user()->can('disiplin.degerlendirme.gor');
                            $canSeeManagerNotes = !$isDisciplinedPerson || $hasAuthorizedRole;
                        @endphp

                        @if($case->yonetici_notu)
                            @php
                                $yoneticiNotu = $case->yonetici_notu;
                                $islemYapanMatch = [];
                                if (preg_match('/\((İşlemi Yapan: .*?)\)\s*$/', $yoneticiNotu, $islemYapanMatch)) {
                                    $islemYapanText = $islemYapanMatch[1];
                                    $yoneticiNotu = trim(str_replace($islemYapanMatch[0], '', $yoneticiNotu));
                                }
                            @endphp
                            
                            @if($canSeeManagerNotes)
                                <div class="bg-white/60 backdrop-blur-sm rounded-xl p-5 border border-{{ $cardColor }}-200 relative group flex flex-col justify-between">
                                    <div>
                                        <span class="block text-[10px] font-bold text-{{ $cardColor }}-400 uppercase tracking-wider mb-2">Yönetici / Kurul Açıklaması</span>
                                        <p class="text-sm text-gray-700 italic leading-relaxed">"{{ $yoneticiNotu }}"</p>
                                    </div>
                                    @if(isset($islemYapanText))
                                        <div class="mt-4 pt-3 border-t border-{{ $cardColor }}-100 flex justify-end">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-{{ $cardColor }}-50 text-{{ $cardColor }}-600 rounded-lg text-[11px] font-bold border border-{{ $cardColor }}-100 shadow-sm opacity-80 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                {{ $islemYapanText }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @elseif(isset($islemYapanText))
                                <div class="flex justify-end mt-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-{{ $cardColor }}-50 text-{{ $cardColor }}-600 rounded-lg text-[11px] font-bold border border-{{ $cardColor }}-100 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        {{ $islemYapanText }}
                                    </span>
                                </div>
                            @endif
                        @endif

                        {{-- Karar Dosyaları --}}
                        @if($canSeeManagerNotes && $case->karar_dosyasi && count($case->karar_dosyasi) > 0)
                            <div class="mt-4">
                                <span class="text-[10px] font-bold text-{{ $cardColor }}-400 uppercase tracking-wider block mb-2">Karar / Tutanak Dosyaları</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-2xl">
                                    @foreach($case->karar_dosyasi as $kFile)
                                        @php
                                            $kUrl = \Illuminate\Support\Facades\Storage::url($kFile);
                                            $kExt = strtolower(pathinfo($kFile, PATHINFO_EXTENSION));
                                            $kIsImage = in_array($kExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        @endphp
                                        <a href="{{ $kUrl }}" target="_blank" class="flex items-center gap-3 p-2.5 border border-{{ $cardColor }}-200 rounded-xl bg-white hover:bg-{{ $cardColor }}-50 transition group shadow-sm">
                                            <div class="h-10 w-10 flex-shrink-0 bg-{{ $cardColor }}-50 rounded-lg flex items-center justify-center overflow-hidden border border-{{ $cardColor }}-100">
                                                @if($kIsImage) <img src="{{ $kUrl }}" class="w-full h-full object-cover">
                                                @else <span class="text-[10px] font-black text-{{ $cardColor }}-500 uppercase">{{ $kExt }}</span>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-bold text-gray-800 truncate" title="{{ basename($kFile) }}">{{ basename($kFile) }}</p>
                                                <p class="text-[9px] text-{{ $cardColor }}-500 font-medium group-hover:underline">Görüntüle</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Geri Alma Butonu (Sadece Hukuk ve Admin yetkilileri) --}}
                @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']) || Auth::user()->can('disiplin.degerlendirme.kullan'))
                    <div class="flex-shrink-0 mt-4 md:mt-0">
                        <form id="revoke_form_{{ $case->id }}" action="{{ route('admin.disiplin.decision.revoke', $case->id) }}" method="POST">
                            @csrf
                            <button type="button" onclick="revokeDecision('revoke_form_{{ $case->id }}')" class="bg-white/80 backdrop-blur border border-gray-300 text-gray-500 px-4 py-2 rounded-lg font-bold text-xs hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                Kararı Geri Al
                            </button>
                        </form>
                        <script>
                            function revokeDecision(formId) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'DİKKAT: İşlemi geri almak üzeresiniz!',
                                        html: 'Dosya tekrar yönetici değerlendirmesine dönecek.<br>Varsa düşülen puan iade edilecek.<br><br><b>Onaylıyor musunuz?</b>',
                                        showCancelButton: true,
                                        confirmButtonText: 'Evet, Geri Al',
                                        cancelButtonText: 'İptal',
                                        confirmButtonColor: '#ef4444',
                                        cancelButtonColor: '#6b7280',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            Swal.fire({ title: 'İşleniyor...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                                            document.getElementById(formId).submit();
                                        }
                                    });
                                } else {
                                    if (confirm('DİKKAT: İşlemi geri almak üzeresiniz!\n\nDosya tekrar yönetici değerlendirmesine dönecek.\nVarsa düşülen puan iade edilecek.\n\nOnaylıyor musunuz?')) {
                                        document.getElementById(formId).submit();
                                    }
                                }
                            }
                        </script>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- ========================================================== --}}
{{-- 2. YÖNETİCİ KARAR VERME FORMU (GELİŞMİŞ TASARIM) --}}
{{-- ========================================================== --}}
@if($case->durum == 'Yönetici Değerlendirmesi')
    @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']) || Auth::user()->can('disiplin.degerlendirme.kullan'))        {{-- Ana Form Kartı --}}
        <div id="manager_evaluation_section" x-data="{ 
            selectedFiles: [],
            handleFileSelect(event) {
                this.selectedFiles = Array.from(event.target.files).map(f => f.name);
            }
        }" class="relative bg-gradient-to-br from-indigo-50 to-white border-2 border-indigo-100 rounded-2xl p-8 shadow-xl overflow-hidden scroll-mt-32">
            
            {{-- Dekoratif Arka Plan --}}
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-100 rounded-full opacity-20 -mr-32 -mt-32 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-100 rounded-full opacity-20 -ml-20 -mb-20 blur-3xl pointer-events-none"></div>

            {{-- Başlık --}}
            <div class="flex items-center gap-4 mb-8 relative z-10">
                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-xl flex items-center justify-center shadow-lg transform -rotate-3">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-gray-800 tracking-tight">Yönetici Değerlendirmesi</h3>
                    <p class="text-sm text-gray-500 font-medium">Lütfen savunmayı inceleyip nihai kararınızı verin.</p>
                </div>
            </div>

            <form id="manager_decision_form" method="POST" enctype="multipart/form-data" class="relative z-10">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    {{-- SOL KOLON: Not Alanı (7/12) --}}
                    <div class="lg:col-span-7">
                        <div class="bg-white/80 rounded-xl p-1 shadow-sm border border-indigo-100 h-full">
                            <label class="block text-xs font-bold text-indigo-900 uppercase tracking-wider mb-2 px-2 pt-2">
                                Karar Gerekçesi / Notunuz <span class="text-red-500">*</span>
                            </label>
                            <textarea id="manager_note_area" name="yonetici_notu" rows="8" class="w-full border-0 bg-transparent text-gray-700 text-sm focus:ring-0 resize-none p-3 placeholder-gray-400" placeholder="Kararınızın gerekçesini buraya detaylıca yazınız..."></textarea>
                            <div class="border-t border-gray-100 p-2 flex justify-end">
                                <span class="text-[10px] text-gray-400 italic">Yönetici imzası otomatik eklenecektir.</span>
                            </div>
                        </div>
                    </div>

                    {{-- SAĞ KOLON: Dosya ve Tarih (5/12) --}}
                    <div class="lg:col-span-5 space-y-6">
                        {{-- Dosya Yükleme --}}
                        <div class="bg-white/80 rounded-xl p-4 border border-indigo-100 shadow-sm">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">Karar Dosyaları (Opsiyonel)</label>
                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-indigo-200 border-dashed rounded-lg cursor-pointer hover:bg-indigo-50 hover:border-indigo-400 transition-all group">
                                <div class="flex flex-col items-center justify-center pt-2">
                                    <svg class="w-6 h-6 mb-1 text-indigo-300 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <p class="text-[10px] text-gray-500"><span class="font-bold text-indigo-600" x-text="selectedFiles.length > 0 ? selectedFiles.length + ' dosya seçildi' : 'Dosya seçin'">Dosya seçin</span> veya sürükleyin</p>
                                </div>
                                <input type="file" name="karar_dosyalari[]" class="hidden" multiple @change="handleFileSelect" />
                            </label>
                            
                            {{-- Seçilen Dosya İsimleri --}}
                            <template x-if="selectedFiles.length > 0">
                                <div class="mt-3 space-y-1">
                                    <template x-for="name in selectedFiles">
                                        <div class="text-[10px] text-indigo-600 flex items-center gap-1 bg-indigo-50 px-2 py-1 rounded truncate">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                            <span x-text="name"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Tarih Seçimi --}}
                        <div class="bg-white/80 rounded-xl p-4 border border-indigo-100 shadow-sm">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                                Kurul Toplantı Tarihi 
                                <span class="text-[9px] font-normal text-gray-400 ml-1">(Sadece Sevk İçin)</span>
                            </label>
                            <input type="datetime-local" id="toplanti_tarihi_input" name="toplanti_tarihi" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                        </div>
                    </div>
                </div>

                {{-- ALT BÖLÜM: NİHAİ KARAR BUTONLARI --}}
                <div class="mt-8 pt-6 border-t border-indigo-100">
                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-8 h-1 bg-indigo-500 rounded-full"></span>
                        Nihai Kararı Verin
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        
                        {{-- 1. CEZAYI ONAYLA (Kırmızı) --}}
                        <button type="button" onclick="managerSubmitForm('{{ route('admin.disiplin.penalty.approve', $case->id) }}', 'Ceza onaylanacak ve {{ $case->hesaplanan_puan }} puan düşülecek. Emin misiniz?')" class="group relative overflow-hidden bg-white border-2 border-rose-100 rounded-xl p-4 hover:border-rose-500 hover:shadow-lg transition-all duration-300 text-left">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-rose-50 rounded-bl-full -mr-8 -mt-8 transition-all group-hover:bg-rose-100"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 mb-3 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <h5 class="font-bold text-gray-800 group-hover:text-rose-700">Cezayı Onayla</h5>
                                <p class="text-xs text-gray-500 mt-1">Puan düşülür ve dosya kapatılır.</p>
                            </div>
                        </button>
 
                        {{-- 2. SAVUNMAYI KABUL ET (Yeşil) --}}
                        <button type="button" onclick="managerSubmitForm('{{ route('admin.disiplin.defense.accept', $case->id) }}', 'Dosya cezasız kapatılacak. Emin misiniz?')" class="group relative overflow-hidden bg-white border-2 border-emerald-100 rounded-xl p-4 hover:border-emerald-500 hover:shadow-lg transition-all duration-300 text-left">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-emerald-50 rounded-bl-full -mr-8 -mt-8 transition-all group-hover:bg-emerald-100"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 mb-3 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h5 class="font-bold text-gray-800 group-hover:text-emerald-700">Savunmayı Kabul Et</h5>
                                <p class="text-xs text-gray-500 mt-1">Ceza verilmez, dosya kapatılır.</p>
                            </div>
                        </button>

                        {{-- 3. KURULA SEVK ET (Siyah/Koyu) --}}
                        <button type="button" onclick="managerSubmitForm('{{ route('admin.disiplin.board.send', $case->id) }}', 'Dosya Kurulama sevk edilecek. Emin misiniz?')" class="group relative overflow-hidden bg-white border-2 border-slate-200 rounded-xl p-4 hover:border-slate-800 hover:shadow-lg transition-all duration-300 text-left">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-slate-50 rounded-bl-full -mr-8 -mt-8 transition-all group-hover:bg-slate-200"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-600 mb-3 group-hover:bg-slate-800 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <h5 class="font-bold text-gray-800 group-hover:text-slate-900">Kurula Sevk Et</h5>
                                <p class="text-xs text-gray-500 mt-1">Disiplin Kurulu değerlendirir.</p>
                            </div>
                        </button>
                    </div>
                </div>
            </form>
        </div>

    <script>
        function managerSubmitForm(route, confirmMsg) {
            const noteArea = document.getElementById('manager_note_area');
            const noteValue = noteArea ? noteArea.value.trim() : '';
            const errorDiv = document.getElementById('manager_note_error');

            if (noteValue === '') {
                if (errorDiv) {
                    errorDiv.classList.remove('hidden');
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Eksik Bilgi',
                        text: 'Lütfen bir karar gerekçesi / notu yazınız.',
                        confirmButtonText: 'Tamam'
                    }).then(() => {
                        if(noteArea) noteArea.focus();
                    });
                } else {
                    alert('Lütfen bir karar gerekçesi / notu yazınız.');
                    if(noteArea) noteArea.focus();
                }
                return;
            }

            if (errorDiv) {
                errorDiv.classList.add('hidden');
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'question',
                    title: 'Emin misiniz?',
                    text: confirmMsg,
                    showCancelButton: true,
                    confirmButtonText: 'Evet, Onaylıyorum',
                    cancelButtonText: 'İptal',
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#ef4444',
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitTheForm(route);
                    }
                });
            } else {
                if (confirm(confirmMsg)) {
                    submitTheForm(route);
                }
            }
        }

        function submitTheForm(route) {
            const form = document.getElementById('manager_decision_form');
            if (form) {
                form.action = route;
                // Engelleme butonlarıyla çifte gönderimi önlemek için opsiyonel:
                Swal.fire({
                    title: 'İşleniyor...',
                    text: 'Lütfen bekleyiniz',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            } else {
                console.error('Form bulunamadı!');
            }
        }
    </script>
@else
        {{-- Yetkisiz Kullanıcılar İçin Durum Bilgisi --}}
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-2xl p-8 shadow-md">
            <div class="flex items-center gap-5">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-blue-900">Yönetici Girişi Tamamlandı</h3>
                    <p class="text-blue-700 text-sm mt-1">Savunma sisteme başarıyla kaydedildi. Şu an <b>Hukuk ve Üst Yönetim</b> tarafından incelenmektedir.</p>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-blue-200 flex flex-wrap gap-4 text-xs font-semibold text-blue-600">
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Savunma Kaydedildi</span>
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Bildirim Gönderildi</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-blue-100 bg-blue-600">İnceleme Bekliyor</span>
            </div>
        </div>
    @endif
@endif