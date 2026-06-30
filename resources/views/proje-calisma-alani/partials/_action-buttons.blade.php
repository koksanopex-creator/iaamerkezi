{{-- 
    DOSYA: resources/views/proje-calisma-alani/partials/_action-buttons.blade.php 
    AÇIKLAMA: Bölüm Yöneticisi ve Superadmin için gelismis aksiyon butonları.
--}}

@php
    $user = auth()->user();
    
    // Varsayılan değerleri FALSE yapalım (Misafirler için güvenlik)
    $isSuperAdmin = false;
    $isBolumYoneticisi = false;
    $sorumluMu = false;
    $isDirektor = false;
    $isBolumunDirektoru = false;

    // Sadece EĞER kullanıcı giriş yapmışsa yetki kontrolü yap
    if ($user) {
        $isSuperAdmin = $user->hasRole('Superadmin');
        $isBolumYoneticisi = $user->hasRole('Bölüm Kalite Yöneticisi');
        $isDirektor = $user->hasRole('Direktör');
        
        // ---------------------------------------------------------
        // 1. BÖLÜM YÖNETİCİSİ SORUMLULUK KONTROLÜ
        // ---------------------------------------------------------
        if ($isBolumYoneticisi) {
            // Şikayet kaynaklı proje mi?
            // NOT: Blade içinde ilişki yüklenmemiş olabilir, garantiye alalım.
            $sikayetKategoriId = null;
            if ($iaa->musteriSikayeti) {
                $sikayetKategoriId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
            } else {
                // İlişki yüklenmemişse ham veri tabanından çekelim (Garanti Yöntem)
                $tempSikayet = \App\Models\MusteriSikayeti::where('iaa_id', $iaa->id)->first();
                if ($tempSikayet) {
                    $sikayetKategoriId = $tempSikayet->sikayet_kategorisi_id;
                }
            }

            if ($sikayetKategoriId) {
                // Kullanıcının yönettiği kategoriler arasında var mı?
                $sorumluMu = $user->yonettigiSikayetKategorileri->contains('id', $sikayetKategoriId);
            }
            
            // YENİ: Eğer müdahale yetkisi (isQualityManagerInterventionPower) aktifse, bu kullanıcı sorumlu sayılmalıdır (Bypass/Intervention)
            if ($isQualityManagerInterventionPower ?? false) {
                $sorumluMu = true;
            }
        }

        // ---------------------------------------------------------
        // 2. DİREKTÖR YETKİ KONTROLÜ
        // ---------------------------------------------------------
        if ($isDirektor) {
            $projeBolumId = $iaa->bolum_id;

            // Eğer normal proje değil, şikayet projesi ise bölüm ID'yi şikayetten bul
            if (!$projeBolumId) {
                // Yukarıdaki mantığın aynısı: Şikayet -> Kategori -> Bölüm
                $tempSikayet = $iaa->musteriSikayeti ?? \App\Models\MusteriSikayeti::where('iaa_id', $iaa->id)->first();
                
                if ($tempSikayet) {
                    $tempKategori = $tempSikayet->sikayetKategori ?? \App\Models\SikayetKategori::find($tempSikayet->sikayet_kategorisi_id);
                    if ($tempKategori) {
                        $projeBolumId = $tempKategori->bolum_id;
                    }
                }
            }

            if ($projeBolumId) {
                // Direktörün yönettiği bölümler arasında bu projenin bölümü var mı?
                $isBolumunDirektoru = $user->yonetilenBolumler->contains('id', $projeBolumId);
            }
        }
    }
@endphp

{{-- Alpine.js Context --}}
<div x-data="{ 
    actionModalOpen: false, 
    actionType: '', 
    actionUrl: '', 
    modalTitle: '', 
    modalDesc: '',
    openModal(type, url, title, desc) {
        this.actionType = type;
        this.actionUrl = url;
        this.modalTitle = title;
        this.modalDesc = desc;
        this.actionModalOpen = true;
    }
}">

    {{-- ================================================================= --}}
    {{-- 0. SENARYO: PROJE LİDERİ / SAHİBİ (GERİ ÇEKME) --}}
    {{-- ================================================================= --}}
    @php
        $isTeamLeader = $iaa->projeEkibi()->where('user_id', auth()->id())->where('iaa_user.rol', 'Lider')->exists();
        $isLeader = ($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || $isTeamLeader;
        $isOwner = $iaa->gonderen_user_id == auth()->id();
        $isInterventionQM = ($isQualityManagerInterventionPower ?? false);
        
        // Superadmin ise sadece projenin sahibi veya lideri ise "Geri Çekme" (Sarı) butonunu görebilir.
        // Diğer durumlarda (Serkan Tölek gibi Müdahale Yetkili QM ise) butonu görebilir.
        $canRecall = ($isLeader || $isOwner || $isInterventionQM);
        
        if ($isSuperAdmin && !$isLeader && !$isOwner) {
            $canRecall = false;
        }
        
        // Onay aşamalarında geri çekilebilir
        $recallableStates = ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor'];
        
        // Eğer Saf İAA ise ilk onay "Yönetici Onayı Bekliyor" aşamasıdır ve geri çekilebilir
        if (!$iaa->musteriSikayeti) {
            $recallableStates[] = 'Yönetici Onayı Bekliyor';
        }

        if ($isSuperAdmin) {
            $recallableStates[] = 'Yönetici Onayı Bekliyor';
        }
    @endphp

    @if($canRecall && in_array($iaa->durum, $recallableStates))
        <div class="mt-6 bg-yellow-50 p-5 rounded-xl border border-yellow-200 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm mb-6">
            <div class="flex items-center gap-4">
                <div class="bg-yellow-100 p-3 rounded-full flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.333 4zM4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z"/></svg>
                </div>
                <div>
                    <p class="text-base font-bold text-yellow-900">Onay Süreci Bekleniyor</p>
                    <p class="text-sm text-yellow-700">
                        Proje şu an onay aşamasında. @if(!$isSuperAdmin) Henüz incelenmeden geri çekip düzenleyebilirsiniz. @endif
                    </p>
                </div>
            </div>
            
            <form action="{{ route('proje.recall', $iaa->id) }}" method="POST">
                @csrf
                <button type="submit" class="whitespace-nowrap inline-flex items-center px-4 py-2 bg-white border-2 border-yellow-300 rounded-lg font-semibold text-sm text-yellow-700 hover:bg-yellow-100 hover:border-yellow-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    Gönderimi Geri Çek
                </button>
            </form>
        </div>
    @endif

    {{-- ================================================================= --}}
    {{-- 1. SENARYO: BÖLÜM YÖNETİCİSİ EKRANI (SADECE ŞİKAYETLERDE) --}}
    {{-- ================================================================= --}}
    @if( (($isBolumYoneticisi && $sorumluMu) || $isSuperAdmin) && $iaa->musteriSikayeti )
        
        {{-- A) KARAR AŞAMASI: Proje Önüne Geldi --}}
        @if($iaa->durum == 'Bölüm Onayı Bekliyor')
            <div class="mt-8 bg-white p-6 rounded-xl shadow-lg border-t-4 border-purple-500 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-purple-50 rounded-full opacity-50 blur-xl"></div>
                <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 relative z-10">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    Bölüm Yöneticisi İşlemleri
                </h4>
                
                <div class="flex flex-wrap gap-3 relative z-10">
                    <form action="{{ route('admin.iaa-yonetim.bolumOnayiVer', $iaa) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-green-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Bölüm Kalite Yöneticisi Onayı Ver
                        </button>
                    </form>

                    <button type="button" 
                            @click="openModal('revision', '{{ route('admin.iaa-yonetim.bolumRevizyon', $iaa) }}', 'Revizyon Talebi', 'Lütfen takıma iletmek istediğiniz revizyon notlarını giriniz. Bu bildirim takıma iletilecektir.')"
                            class="inline-flex items-center px-5 py-2.5 bg-yellow-500 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Revizyon İste
                    </button>

                    <button type="button" 
                            @click="openModal('reject', '{{ route('admin.iaa-yonetim.bolumReddet', $iaa) }}', 'Projeyi Reddet', 'Projeyi reddetme gerekçenizi belirtiniz. Bu işlem projeyi durduracaktır.')"
                            class="inline-flex items-center px-5 py-2.5 bg-red-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reddet
                    </button>
                </div>
            </div>
        @endif

        {{-- B) GERİ ALMA AŞAMASI --}}
        @php
            $geriAlinabilirDurumlar = ['Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Revize Ediliyor', 'Tamamlanması Reddedildi'];
        @endphp

        @if(in_array($iaa->durum, $geriAlinabilirDurumlar) && (($isBolumYoneticisi && $sorumluMu) || $isSuperAdmin))
            <div class="mt-6 bg-orange-50 p-5 rounded-xl border border-orange-200 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                @php
                    $bolumLog = \App\Models\IaaLog::where('iaa_id', $iaa->id)
                        ->whereIn('eylem', [
                            'Bölüm Onayı Verildi', 
                            'Bölüm Onaylandı (Direktör Onayına Sevk)', 
                            'Bölüm Onaylandı (İadeli - Direktör Onayına Sevk)',
                            'Revizyon Talep Edildi (Bölüm)',
                            'Proje Reddedildi (Bölüm)'
                        ])
                        ->with('user')
                        ->latest()
                        ->first();
                    $qmName = $bolumLog && $bolumLog->user ? $bolumLog->user->name : 'Bölüm Kalite Yöneticisi';
                @endphp
                <div class="flex items-center gap-4">
                    <div class="bg-orange-100 p-3 rounded-full flex-shrink-0">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-base font-bold text-orange-900">
                            @if($isSuperAdmin)
                                {{ $qmName }} Kararını Geri Al
                            @else
                                Karar Verildi
                            @endif
                        </p>
                        <p class="text-sm text-orange-700">
                            @if($isSuperAdmin)
                                Bölüm kalite yöneticisi {{ $qmName }} tarafından verilen kararı (Onay, Red veya Revizyon) geri alabilirsiniz.
                            @else
                                Verdiğiniz kararı (Onay, Red veya Revizyon) üst yönetici müdahale etmeden geri alabilirsiniz.
                            @endif
                        </p>
                    </div>
                </div>
                
                <form action="{{ route('admin.iaa-yonetim.bolumOnayiGeriAl', $iaa) }}" method="POST">
                    @csrf
                    <button type="submit" class="whitespace-nowrap inline-flex items-center px-4 py-2 bg-white border-2 border-orange-200 rounded-lg font-semibold text-sm text-orange-700 hover:bg-orange-100 hover:border-orange-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        İşlemi Geri Al
                    </button>
                </form>
            </div>
        @endif

    @endif

    {{-- ================================================================= --}}
    {{-- 1.5 SENARYO: DİREKTÖR EKRANI (YENİ) --}}
    {{-- ================================================================= --}}
    @if( ($isDirektor && $isBolumunDirektoru) || $isSuperAdmin )

        {{-- A) KARAR AŞAMASI: Direktör Onayı Bekliyor --}}
        @if($iaa->durum == 'Direktör Onayı Bekliyor')
            <div class="mt-8 bg-white p-6 rounded-xl shadow-lg border-t-4 border-purple-500 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-purple-50 rounded-full opacity-50 blur-xl"></div>
                <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 relative z-10">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    Direktör İşlemleri
                </h4>
                
                <div class="flex flex-wrap gap-3 relative z-10">
                    <form action="{{ route('admin.iaa-yonetim.direktorOnayiVer', $iaa) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-green-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Direktör Onayı Ver
                        </button>
                    </form>

                    <button type="button" 
                            @click="openModal('director_revision', '{{ route('admin.iaa-yonetim.direktorRevizyon', $iaa) }}', 'Revizyon İste (Direktör)', 'Takıma iletmek istediğiniz revizyon notlarını giriniz.')"
                            class="inline-flex items-center px-5 py-2.5 bg-yellow-500 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Revizyon İste
                    </button>

                    <button type="button" 
                            @click="openModal('director_reject', '{{ route('admin.iaa-yonetim.direktorReddet', $iaa) }}', 'Projeyi Reddet (Direktör)', 'Projeyi reddetme gerekçenizi belirtiniz.')"
                            class="inline-flex items-center px-5 py-2.5 bg-red-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reddet
                    </button>
                </div>
            </div>
        @endif

    @endif

    {{-- ================================================================= --}}
    {{-- 2. SENARYO: SUPERADMIN EKRANI --}}
    {{-- ================================================================= --}}
    @if($isSuperAdmin)

        @php
            // 1. Grup: İşlem Yapılabilir Durumlar (Butonlar Görünür)
            // Yönetici Onayı Bekliyor = Normal Akış
            // Bölüm Onayı Bekliyor = Müdahale
            // Direktör Onayı Bekliyor = Müdahale
            $aksiyonAlinabilirDurumlar = ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor'];

            // 2. Grup: Karar Verilmiş Durumlar (Sadece Geri Al Butonu Görünür)
            $kararVerilmisDurumlar = ['Revize Ediliyor', 'Tamamlanması Reddedildi', 'Tamamlandı'];
        @endphp

        {{-- DURUM A: AKSİYON BEKLENİYOR --}}
        @if(in_array($iaa->durum, $aksiyonAlinabilirDurumlar))
            
            <div class="mt-8 bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-600 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 blur-xl"></div>
                <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 relative z-10">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    Süper Yönetici İşlemleri 
                    @if($iaa->durum == 'Bölüm Onayı Bekliyor' || $iaa->durum == 'Direktör Onayı Bekliyor')
                        <span class="text-xs font-normal text-blue-500 ml-2 bg-blue-50 px-2 py-1 rounded-full border border-blue-100">(Manuel Müdahale Modu)</span> 
                    @endif
                </h4>

                <div class="flex flex-wrap gap-3 relative z-10">
                    {{-- ONAYLA --}}
                    <form action="{{ route('admin.iaa.approveCompleted', $iaa) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Onayla ve Puan Dağıt
                        </button>
                    </form>

                    {{-- REVİZYON --}}
                    <button type="button" 
                            @click="openModal('admin_revision', '{{ route('admin.iaa.requestRevision', $iaa) }}', 'Yönetici Revizyon Talebi', 'Takıma iletilecek revizyon notlarını giriniz.')"
                            class="inline-flex items-center px-5 py-2.5 bg-amber-500 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Revizyon İste
                    </button>

                    {{-- REDDET --}}
                    <button type="button" 
                            @click="openModal('admin_reject', '{{ route('admin.iaa.rejectCompleted', $iaa) }}', 'Projeyi Tamamen Reddet', 'Bu projeyi nihai olarak reddetmek üzeresiniz.')"
                            class="inline-flex items-center px-5 py-2.5 bg-red-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Reddet
                    </button>
                </div>
            </div>

        {{-- DURUM B: KARAR VERİLDİ (BUTONLAR YOK, SADECE GERİ AL VAR) --}}
        @elseif(in_array($iaa->durum, $kararVerilmisDurumlar))
            
            <div class="mt-8 bg-blue-50 p-6 rounded-xl shadow-inner border border-blue-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-white p-3 rounded-full shadow-sm">
                        @if($iaa->durum == 'Tamamlandı')
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($iaa->durum == 'Revize Ediliyor')
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        @else
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-base font-bold text-blue-900">Karar Verildi: <span class="text-blue-700">{{ $iaa->durum }}</span></p>
                        <p class="text-sm text-blue-600">
                            Bu proje için bir karar verilmiş. Değişiklik yapmak için önce işlemi geri almalısınız.
                        </p>
                    </div>
                </div>

                <form action="{{ route('admin.iaa-yonetim.geriAl', $iaa) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="whitespace-nowrap inline-flex items-center px-5 py-2.5 bg-white border border-blue-300 rounded-xl font-semibold text-sm text-blue-700 hover:bg-blue-50 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        Son İşlemi Geri Al
                    </button>
                </form>
            </div>

        @endif

    @endif


    {{-- ================================================================= --}}
    {{-- ORTAK MODAL (Tüm İşlemler İçin Tek Modal) --}}
    {{-- ================================================================= --}}
    <div x-show="actionModalOpen" 
         style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="actionModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" 
                 @click="actionModalOpen = false" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="actionModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                
                <form :action="actionUrl" method="POST">
                    @csrf
                    
                    <div class="bg-gradient-to-r from-gray-50 to-white px-4 py-5 sm:px-6 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title" x-text="modalTitle"></h3>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-5 sm:p-6">
                        <p class="text-sm text-gray-500 mb-4" x-text="modalDesc"></p>
                        
                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700 mb-2">Açıklama / Not</label>
                            <div class="relative rounded-md shadow-sm">
                                <textarea name="not" rows="4" class="form-textarea block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Lütfen detaylı bir açıklama giriniz..."></textarea>
                                <textarea name="rejection_reason" rows="3" class="hidden"></textarea>
                                <textarea name="revision_reason" rows="3" class="hidden"></textarea>
                                
                                <script>
                                    document.querySelector('textarea[name="not"]').addEventListener('input', function(e) {
                                        document.querySelector('textarea[name="rejection_reason"]').value = e.target.value;
                                        document.querySelector('textarea[name="revision_reason"]').value = e.target.value;
                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-0 sm:w-auto sm:text-sm transition-colors">
                            Gönder
                        </button>
                        <button type="button" @click="actionModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                            İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>