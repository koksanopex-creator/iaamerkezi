<x-app-layout>
    @push('pageTitle', 'Disiplin Dosya Detayı | ')
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <a href="javascript:history.back()"
                    class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100"
                    title="Geri Dön">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                Disiplin Dosyası #{{ $case->id }} — ({{ $case->user->name }} - {{ $case->user->bolum->ad ?? '-' }})
            </h2>
            <div class="flex gap-2">
                {{-- Kurul Odası sekme üzerinden erişiliyor --}}
                {{-- Düzenle Butonu (Kurallara Uygun Kısıtlama) --}}
                @php
                    $isSuper = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']);
                    $canEdit = ($case->durum == 'Kurulda') 
                        ? Auth::user()->hasRole('Superadmin') 
                        : ($isSuper || (Auth::id() == $case->reporter_id && !$case->yonetici_degerlendirme_notu) || Auth::user()->can('disiplin.tutanak.duzenle'));
                @endphp
                @if($canEdit && $case->durum != 'Karar Verildi' && $case->durum != 'İptal')
                    <a href="{{ route('admin.disiplin.edit', $case->id) }}"
                        class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-100 border border-indigo-200">Düzenle</a>
                @endif
                <a href="{{ route('admin.disiplin.print', $case->id) }}" target="_blank"
                    class="bg-white text-slate-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-50 border border-slate-200 shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Yazdır
                </a>
                <a href="{{ route('admin.disiplin.download-pdf', $case->id) }}"
                    class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-800 border border-slate-900 shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF İndir
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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
                    'Savunma Bekleniyor' => 'Personelden savunma bekleniyor.',
                    'Yönetici Değerlendirmesi' => 'Savunma girildi, yönetici onayı bekleniyor.',
                    'Karar Verildi' => $isSavunmaKabul 
                        ? 'Savunma haklı bulundu ve Kabul Edildi (Ceza Uygulanmadı).' 
                        : 'Ceza Onaylandı: ' . ($case->final_karar ?? 'Dosya kapatıldı.') . ' (-' . $case->hesaplanan_puan . ' Puan)',
                    default => 'İşlem bekleniyor.'
                };
            @endphp
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
                        <p class="font-bold text-{{ $durumRenk }}-800 text-lg">Dosya Durumu: {{ $case->durum }}</p>
                        <p class="text-xs {{ !$isSavunmaKabul && $case->durum == 'Karar Verildi' ? 'text-red-600' : 'text-'.$durumRenk.'-600' }} font-bold uppercase tracking-tight">{{ $durumMetni }}</p>
                    </div>
                </div>
                @if($case->durum == 'Yönetici Değerlendirmesi' && (Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']) || Auth::user()->can('disiplin.degerlendirme.kullan')))
                    <button onclick="document.getElementById('manager_evaluation_section').scrollIntoView({behavior: 'smooth'})" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-200 transition-all flex items-center gap-2 border border-blue-500 hover:scale-[1.02] active:scale-95 group">
                        Değerlendirme Yapmak İçin Tıklayın
                        <svg class="w-5 h-5 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                @endif
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
                <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6 text-center mb-6">
                    <div class="inline-block p-3 bg-gray-200 rounded-full mb-3">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Disiplin Kurulu Değerlendirmesi</h3>
                    <p class="text-gray-600 mt-2">Dosyanız Disiplin Kurulu'na sevk edilmiştir. Kurul üyeleri tarafından
                        incelenmektedir.</p>
                    @if($case->toplanti_tarihi)
                        <p class="mt-3 text-sm font-bold text-indigo-600 bg-indigo-50 inline-block px-3 py-1 rounded">📅
                            Planlanan Toplantı: {{ $case->toplanti_tarihi->format('d.m.Y H:i') }}</p>
                    @endif
                </div>
            @endif

            {{-- SEKME NAVİGASYONU (Sadece Kurul Süreci Varsa) --}}
            @php
                $hasMeeting = \App\Models\DisiplinKuruluToplanti::where('disciplinary_case_id', $case->id)->exists();
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
            @elseif($activeTab === 'kurul' && (Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) || Auth::user()->can('disiplin.kurul.portal.gor')))
                {{-- DİSİPLİN KURULU ODASI --}}
                <div class="animate-in fade-in slide-in-from-bottom-2 duration-500">
                    <div class="w-full">
                        @include('admin.disiplin.partials.council-room')
                    </div>
                </div>
            @else
                {{-- Geçersiz sekme veya yetkisiz erişim durumunda varsayılan detay --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-6">
                        @include('admin.disiplin.partials.case-details')
                    </div>
                    <div class="space-y-6">
                         @include('admin.disiplin.partials.sidebar')
                    </div>
                </div>
            @endif



        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush
</x-app-layout>