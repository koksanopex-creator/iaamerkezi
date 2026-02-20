@props(['iaa', 'statusDate' => null])

@php
    use App\Models\IaaLog;

    // ========================================================================
    // 1. BÖLÜM YÖNETİCİSİ VERİLERİ
    // ========================================================================
    $bolumLog = IaaLog::where('iaa_id', $iaa->id)
        ->where(function ($q) {
            $q->where('eylem', 'Bölüm Onayı Verildi')
                ->orWhere('eylem', 'Revizyon Talep Edildi (Bölüm)')
                ->orWhere('eylem', 'Proje Reddedildi (Bölüm)')
                ->orWhere('eylem', 'Bölüm İşlemi Geri Alındı');
        })
        ->with('user')
        ->latest()
        ->first();

    $bolumDurum = [
        'tip' => 'waiting',
        'baslik' => 'Bölüm Onayı Bekleniyor',
        'mesaj' => 'Proje tamamlandığında bölüm yöneticisi inceleyecektir.',
        'tarih' => null,
        'kisi' => 'Bölüm Yöneticisi'
    ];

    if ($bolumLog && $bolumLog->eylem !== 'Bölüm İşlemi Geri Alındı') {
        $bolumDurum['tarih'] = $bolumLog->created_at;
        $bolumDurum['kisi'] = $bolumLog->user->name ?? 'Bölüm Yöneticisi';

        switch ($bolumLog->eylem) {
            case 'Bölüm Onayı Verildi':
                $bolumDurum['tip'] = 'success';
                $bolumDurum['baslik'] = 'Bölüm Onayı Verildi';
                $bolumDurum['mesaj'] = 'Bölüm yöneticisi projeyi uygun buldu ve üst yönetime iletti.';
                break;
            case 'Revizyon Talep Edildi (Bölüm)':
                $bolumDurum['tip'] = 'warning';
                $bolumDurum['baslik'] = 'Revizyon Talebi';
                $bolumDurum['mesaj'] = $bolumLog->aciklama;
                break;
            case 'Proje Reddedildi (Bölüm)':
                $bolumDurum['tip'] = 'danger';
                $bolumDurum['baslik'] = 'Bölüm Tarafından Reddedildi';
                $bolumDurum['mesaj'] = $bolumLog->aciklama;
                break;
        }
    } elseif ($bolumLog) {
        $bolumDurum['kisi'] = $bolumLog->user->name ?? 'Bölüm Yöneticisi';
    }

    // ========================================================================
    // 1.5 DİREKTÖR VERİLERİ (YENİ)
    // ========================================================================

    // 1. Önce Logu Sorgula (Geçmiş veriyi kaybetmemek için)
    $direktorLog = IaaLog::where('iaa_id', $iaa->id)
        ->where(function ($q) {
            $q->where('eylem', 'Direktör Onayı Verildi')
                ->orWhere('eylem', 'Revizyon Talep Edildi (Direktör)')
                ->orWhere('eylem', 'Proje Reddedildi (Direktör)')
                ->orWhere('eylem', 'Direktör İşlemi Geri Alındı');
        })
        ->with('user')
        ->latest()
        ->first();

    // 2. Direktör onayı aktif mi? (Ayar veya Log Varlığı)
    $direktorOnayiAktif = false;
    $globalDirektorSetting = false;

    if ($iaa->musteriSikayeti) {
        $setting = \App\Models\Setting::where('key', 'sikayet_direktor_onayi_aktif')->first();
        if ($setting && $setting->value == '1') {
            $globalDirektorSetting = true;
        }
    }

    // Eğer global ayar açıksa, bölüm/direktör ilişki kontrolü yap
    if ($globalDirektorSetting) {
        $bolum = $iaa->bolum; // İlişki varsa
        if (!$bolum && $iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayetKategori) {
            $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum;
        }

        if ($bolum && $bolum->director_id) {
            $direktorOnayiAktif = true;
        }
    }

    // [KRİTİK DÜZELTME] Eğer geçmişte bir direktör işlemi varsa, ayar kapalı olsa bile kartı göster!
    if ($direktorLog) {
        $direktorOnayiAktif = true;
    }

    $direktorDurum = [
        'tip' => 'waiting',
        'baslik' => 'Direktör Onayı Bekleniyor',
        'mesaj' => 'Bölüm onayından sonra direktör inceleyecektir.',
        'tarih' => null,
        'kisi' => 'Direktör',
        'locked' => true
    ];

    if ($direktorOnayiAktif) {
        // Bölüm onayı verildiyse kilidi aç
        if ($bolumDurum['tip'] == 'success') {
            $direktorDurum['locked'] = false;
        }

        // $direktorLog zaten yukarıda sorgulandı

        if ($direktorLog && $direktorLog->eylem !== 'Direktör İşlemi Geri Alındı') {
            $direktorDurum['locked'] = false; // İşlem yapıldıysa kilt aç
            $direktorDurum['tarih'] = $direktorLog->created_at;
            $direktorDurum['kisi'] = $direktorLog->user->name ?? 'Direktör';

            switch ($direktorLog->eylem) {
                case 'Direktör Onayı Verildi':
                    $direktorDurum['tip'] = 'success';
                    $direktorDurum['baslik'] = 'Direktör Onayı Verildi';
                    $direktorDurum['mesaj'] = 'Direktör projeyi onayladı ve üst yönetime iletti.';
                    break;
                case 'Revizyon Talep Edildi (Direktör)':
                    $direktorDurum['tip'] = 'warning';
                    $direktorDurum['baslik'] = 'Revizyon Talebi (Direktör)';
                    $direktorDurum['mesaj'] = $direktorLog->aciklama;
                    break;
                case 'Proje Reddedildi (Direktör)':
                    $direktorDurum['tip'] = 'danger';
                    $direktorDurum['baslik'] = 'Direktör Tarafından Reddedildi';
                    $direktorDurum['mesaj'] = $direktorLog->aciklama;
                    break;
            }
        }
    }


    // ========================================================================
    // 2. SÜPER YÖNETİCİ VERİLERİ
    // ========================================================================
    $adminLog = IaaLog::where('iaa_id', $iaa->id)
        ->where(function ($q) {
            $q->where('eylem', 'Proje Onaylandı')
                ->orWhere('eylem', 'Revizyon Talep Edildi')
                ->orWhere('eylem', 'Tamamlanmış Projenin Reddi')
                ->orWhere('eylem', 'İşlem Geri Alındı');
        })
        ->with('user')
        ->latest()
        ->first();

    $adminDurum = [
        'tip' => 'waiting',
        'baslik' => 'Yönetici Onayı Bekleniyor',
        'mesaj' => 'Onay süreçleri tamamlandıktan sonra üst yönetim inceleyecektir.',
        'tarih' => null,
        'kisi' => 'Süper Yönetici',
        'locked' => true // Varsayılan olarak kilitli başlar
    ];

    // Kilit Mantığı: 
    if ($direktorOnayiAktif) {
        // Direktör varsa, Direktör onayı beklenir
        if ($direktorDurum['tip'] == 'success') {
            $adminDurum['locked'] = false;
        }
    } else {
        // Direktör yoksa, Bölüm onayı yeterli
        if ($bolumDurum['tip'] == 'success') {
            $adminDurum['locked'] = false;
        }
    }

    // AMMA VE LAKİN: Eğer Admin zaten bir işlem yaptıysa (Onay/Red/Revize), 
    // Bölüm durumu ne olursa olsun (Yedek Lastik mantığı) kilit AÇIK olmalı ve admin verisi görünmeli.
    if ($adminLog && $adminLog->eylem !== 'İşlem Geri Alındı') {
        $adminDurum['locked'] = false; // Kilidi Zorla Aç
        $adminDurum['tarih'] = $adminLog->created_at;
        $adminDurum['kisi'] = $adminLog->user->name ?? 'Süper Yönetici';

        switch ($adminLog->eylem) {
            case 'Proje Onaylandı':
                $adminDurum['tip'] = 'success';
                $adminDurum['baslik'] = 'Proje Onaylandı';
                $adminDurum['mesaj'] = 'Proje onaylanmış ve puan dağıtımı yapılmıştır.';
                break;
            case 'Revizyon Talep Edildi':
                $adminDurum['tip'] = 'warning';
                $adminDurum['baslik'] = 'Revizyon Talebi';
                $adminDurum['mesaj'] = $adminLog->aciklama;
                break;
            case 'Tamamlanmış Projenin Reddi':
                $adminDurum['tip'] = 'danger';
                $adminDurum['baslik'] = 'Proje Reddedildi';
                $adminDurum['mesaj'] = $adminLog->aciklama;
                break;
        }
    }
    // YENİ EKLEME: Eğer proje "Tamamlandı" ise ama Admin logu yoksa (ve Direktör onayı varsa),
    // bu demek oluyor ki Direktör onayıyla proje bitmiştir.
    elseif ($iaa->durum == 'Tamamlandı' && ($adminLog == null || $adminLog->eylem == 'İşlem Geri Alındı') && $direktorLog && $direktorLog->eylem == 'Direktör Onayı Verildi') {
        $adminDurum['locked'] = false;
        $adminDurum['tip'] = 'success';
        $adminDurum['baslik'] = 'Süreç Tamamlandı';
        $adminDurum['mesaj'] = 'Direktör onayı ile proje nihai sonuca ulaşmış ve puanlar dağıtılmıştır. İlave yönetici onayına gerek kalmamıştır.';
        $adminDurum['kisi'] = 'Otomatik İşlem';
        $adminDurum['tarih'] = $direktorLog->created_at;
    }

    // === GÜVENLİK KONTROLÜ (OVERRIDE) ===
    // Eğer proje geri döndüyse, ilerideki kartları temizle.
    if ($iaa->durum == 'Bölüm Onayı Bekliyor') {
        // Direktör sıfırla
        $direktorDurum['tip'] = 'waiting';
        $direktorDurum['baslik'] = 'Direktör Onayı Bekleniyor';
        $direktorDurum['mesaj'] = 'Bölüm onayından sonra direktör inceleyecektir.';
        $direktorDurum['locked'] = true;

        // Admin sıfırla
        $adminDurum['tip'] = 'waiting';
        $adminDurum['baslik'] = 'Yönetici Onayı Bekleniyor';
        $adminDurum['mesaj'] = 'Onay süreçleri tamamlandıktan sonra üst yönetim inceleyecektir.';
        $adminDurum['locked'] = true;
    } elseif ($iaa->durum == 'Direktör Onayı Bekliyor') {
        // Admin sıfırla
        $adminDurum['tip'] = 'waiting';
        $adminDurum['baslik'] = 'Yönetici Onayı Bekleniyor';
        $adminDurum['mesaj'] = 'Onay süreçleri tamamlandıktan sonra üst yönetim inceleyecektir.';
        $adminDurum['locked'] = true;
    }


    // Renk Paleti
    function getColors($type)
    {
        return match ($type) {
            'success' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'icon_bg' => 'bg-green-100', 'icon_text' => 'text-green-600', 'title' => 'text-green-800'],
            'warning' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'icon_bg' => 'bg-yellow-100', 'icon_text' => 'text-yellow-600', 'title' => 'text-yellow-800'],
            'danger' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'icon_bg' => 'bg-red-100', 'icon_text' => 'text-red-600', 'title' => 'text-red-800'],
            'waiting' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'icon_bg' => 'bg-gray-100', 'icon_text' => 'text-gray-400', 'title' => 'text-gray-700'],
            default => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'icon_bg' => 'bg-gray-100', 'icon_text' => 'text-gray-400', 'title' => 'text-gray-700'],
        };
    }

    $bRenk = getColors($bolumDurum['tip']);
    $dRenk = getColors($direktorDurum['tip']); // Direktör rengi
    $aRenk = getColors($adminDurum['tip']);

    // Müşteri Şikayeti Değişkeni
    $sikayet = $iaa->musteriSikayeti;

    // Grid yapısı: Kart sayısına göre dinamik
    $activeCardCount = 1; // Süper Yönetici her zaman var
    if ($sikayet)
        $activeCardCount++; // Bölüm Yöneticisi
    if ($direktorOnayiAktif)
        $activeCardCount++; // Direktör

    $gridClass = match ($activeCardCount) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 xl:grid-cols-3', // 3 Kart varsa 3'e böl
        default => 'grid-cols-1 md:grid-cols-2'
    };

    // [ÖZELLEŞTİRME] Eğer "Müşteri Şikayeti Değilse" (Saf İAA) -> Bölüm Onayı Yoktur
    // Dolayısıyla Admin Kilidi Doğrudan Açık Olmalı
    if (!$sikayet) {
        $adminDurum['locked'] = false;

        // Mesajı güncelle: Bölüm onayından bahsetmesin
        if ($adminDurum['tip'] == 'waiting') {
            $adminDurum['mesaj'] = 'Tamamlanan proje üst yönetim onayına sunulmuştur.';
        }
    }
@endphp

<div class="mt-10">
    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Onay Durum Paneli
    </h3>

    <div class="grid grid-cols-1 {{ $gridClass }} gap-6">

        {{-- ====================================================================== --}}
        {{-- 1. KART: BÖLÜM YÖNETİCİSİ (SOL) --}}
        {{-- SADECE Müşteri Şikayeti Kaynaklıysa Gösterilir --}}
        {{-- ====================================================================== --}}
        @if($sikayet)
            <div
                class="rounded-xl p-6 border {{ $bRenk['bg'] }} {{ $bRenk['border'] }} shadow-sm relative overflow-hidden group transition-all hover:shadow-md">

                {{-- TARİH ROZETİ (SAĞ ÜST) --}}
                @if($bolumDurum['tarih'] && $bolumDurum['tip'] != 'waiting')
                    <div class="absolute top-0 right-0">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-bl-xl text-[10px] font-bold uppercase tracking-widest bg-white/60 border-l border-b {{ $bRenk['border'] }} {{ $bRenk['title'] }} shadow-sm">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $bolumDurum['tarih']->format('d.m.Y H:i') }}
                        </span>
                    </div>
                @endif

                <div class="flex items-start gap-4 mt-2">
                    {{-- İkon --}}
                    <div
                        class="w-12 h-12 rounded-full {{ $bRenk['icon_bg'] }} flex items-center justify-center flex-shrink-0">
                        @if($bolumDurum['tip'] == 'success')
                            <svg class="w-6 h-6 {{ $bRenk['icon_text'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @elseif($bolumDurum['tip'] == 'warning')
                            <svg class="w-6 h-6 {{ $bRenk['icon_text'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        @elseif($bolumDurum['tip'] == 'danger')
                            <svg class="w-6 h-6 {{ $bRenk['icon_text'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        @else
                            <svg class="w-6 h-6 {{ $bRenk['icon_text'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>

                    <div class="flex-1">
                        <h4 class="text-base font-bold {{ $bRenk['title'] }}">{{ $bolumDurum['baslik'] }}</h4>

                        {{-- Kişi Bilgisi (Rozet Stil) --}}
                        <div class="mt-1 mb-2">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-white border border-gray-200 text-gray-700 shadow-sm">
                                <span
                                    class="w-1.5 h-1.5 rounded-full {{ str_replace('text-', 'bg-', $bRenk['icon_text']) }}"></span>
                                {{ $bolumDurum['kisi'] }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ Str::limit($bolumDurum['mesaj'], 150) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- ====================================================================== --}}
        {{-- 1.5 KART: DİREKTÖR (ORTA) --}}
        {{-- SADECE Ayar Aktifse ve Direktör Varsa Gösterilir --}}
        {{-- ====================================================================== --}}
        @if($direktorOnayiAktif)
            <div
                class="rounded-xl p-6 border {{ $dRenk['bg'] }} {{ $dRenk['border'] }} shadow-sm relative overflow-hidden group transition-all hover:shadow-md {{ $direktorDurum['locked'] ? 'opacity-80 bg-gray-50' : '' }}">

                {{-- SAĞ ÜST İŞLEM ALANI --}}
                <div class="absolute top-0 right-0 flex flex-col items-end z-10">
                    {{-- TARİH ROZETİ --}}
                    @if($direktorDurum['tarih'] && !$direktorDurum['locked'] && $direktorDurum['tip'] != 'waiting')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-bl-xl text-[10px] font-bold uppercase tracking-widest bg-white/60 border-l border-b {{ $dRenk['border'] }} {{ $dRenk['title'] }} shadow-sm">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $direktorDurum['tarih']->format('d.m.Y H:i') }}
                        </span>
                    @endif

                    {{-- DİREKTÖR İŞLEMİ GERİ AL BUTONU --}}
                    @if($direktorDurum['tip'] == 'success' && $iaa->durum == 'Tamamlandı')
                        @if(auth()->user()->hasRole('Superadmin') || auth()->user()->hasRole('Direktör'))
                            <form action="{{ route('admin.iaa-yonetim.direktorOnayiGeriAl', $iaa->id) }}" method="POST"
                                class="mr-2 mt-2"
                                onsubmit="return confirm('Direktör onayını geri çekmek üzeresiniz. Puanlar silinecek ve proje \'Direktör Onayı Bekliyor\' aşamasına dönecek. Emin misiniz?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="text-[10px] text-red-600 hover:text-red-800 font-bold bg-white/80 hover:bg-white px-2 py-1 rounded border border-red-200 shadow-sm transition-colors flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    İşlemi Geri Al
                                </button>
                            </form>
                        @endif
                    @endif
                </div>

                <div class="flex items-start gap-4 mt-2">
                    {{-- İkon --}}
                    <div
                        class="w-12 h-12 rounded-full {{ $dRenk['icon_bg'] }} flex items-center justify-center flex-shrink-0">
                        @if($direktorDurum['locked'])
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        @elseif($direktorDurum['tip'] == 'success')
                            <svg class="w-6 h-6 {{ $dRenk['icon_text'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @elseif($direktorDurum['tip'] == 'warning')
                            <svg class="w-6 h-6 {{ $dRenk['icon_text'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        @elseif($direktorDurum['tip'] == 'danger')
                            <svg class="w-6 h-6 {{ $dRenk['icon_text'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        @else
                            <svg class="w-6 h-6 {{ $dRenk['icon_text'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>

                    <div class="flex-1">
                        <h4 class="text-base font-bold {{ $dRenk['title'] }}">{{ $direktorDurum['baslik'] }}</h4>

                        {{-- Kişi Bilgisi (Rozet Stil) --}}
                        <div class="mt-1 mb-2">
                            @if(!$direktorDurum['locked'])
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-white border border-gray-200 text-gray-700 shadow-sm">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ str_replace('text-', 'bg-', $dRenk['icon_text']) }}"></span>
                                    {{ $direktorDurum['kisi'] }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 border border-gray-200 text-gray-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Direktör
                                </span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ Str::limit($direktorDurum['mesaj'], 150) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- ====================================================================== --}}
        {{-- 2. KART: SÜPER YÖNETİCİ (SAĞ) --}}
        {{-- ====================================================================== --}}
        {{-- Kilitli ise biraz silik görünür --}}
        <div
            class="rounded-xl p-6 border {{ $aRenk['bg'] }} {{ $aRenk['border'] }} shadow-sm relative overflow-hidden group transition-all hover:shadow-md {{ $adminDurum['locked'] ? 'opacity-80 bg-gray-50' : '' }}">

            {{-- SAĞ ÜST İŞLEM ALANI --}}
            <div class="absolute top-0 right-0 flex flex-col items-end z-10">
                {{-- TARİH ROZETİ --}}
                @if($adminDurum['tarih'] && !$adminDurum['locked'] && $adminDurum['tip'] != 'waiting')
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-bl-xl text-[10px] font-bold uppercase tracking-widest bg-white/60 border-l border-b {{ $aRenk['border'] }} {{ $aRenk['title'] }} shadow-sm mb-2">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $adminDurum['tarih']->format('d.m.Y H:i') }}
                    </span>
                @endif

                {{-- ROZET VE BUTON GRUBU --}}
                @if($adminDurum['tip'] == 'success')
                    <div class="flex items-center mr-2 mb-2">
                        {{-- GERİ AL BUTONU --}}
                        @if(auth()->user()->hasRole('Superadmin'))
                            <form action="{{ route('admin.iaa-yonetim.geriAl', $iaa->id) }}" method="POST" class="mr-2"
                                onsubmit="return confirm('Proje onayını geri almak üzeresiniz. Puanlar geri alınacak ve proje \'Yönetici Onayı Bekliyor\' durumuna dönecek. Emin misiniz?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="text-[10px] text-red-600 hover:text-red-800 font-bold bg-white/80 hover:bg-white px-2 py-1 rounded border border-red-200 shadow-sm transition-colors flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    İşlemi Geri Al
                                </button>
                            </form>
                        @endif

                        <span
                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest bg-green-100 text-green-800 border border-green-200 shadow-sm">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Kapatıldı
                        </span>
                    </div>
                @endif
            </div>

            <div class="flex items-start gap-4 mt-2">
                {{-- İkon --}}
                <div
                    class="w-12 h-12 rounded-full {{ $aRenk['icon_bg'] }} flex items-center justify-center flex-shrink-0">
                    @if($adminDurum['locked'])
                        {{-- Kilit İkonu --}}
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    @elseif($adminDurum['tip'] == 'success')
                        <svg class="w-6 h-6 {{ $aRenk['icon_text'] }}" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif($adminDurum['tip'] == 'warning')
                        <svg class="w-6 h-6 {{ $aRenk['icon_text'] }}" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    @elseif($adminDurum['tip'] == 'danger')
                        <svg class="w-6 h-6 {{ $aRenk['icon_text'] }}" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    @else
                        <svg class="w-6 h-6 {{ $aRenk['icon_text'] }}" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </div>

                <div class="flex-1">
                    <h4 class="text-base font-bold {{ $aRenk['title'] }}">{{ $adminDurum['baslik'] }}</h4>

                    {{-- Kişi Bilgisi (Pill Style) --}}
                    <div class="mt-1 mb-2">
                        @if(!$adminDurum['locked'])
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-white border border-gray-200 text-gray-700 shadow-sm">
                                <span
                                    class="w-1.5 h-1.5 rounded-full {{ str_replace('text-', 'bg-', $aRenk['icon_text']) }}"></span>
                                {{ $adminDurum['kisi'] }}
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 border border-gray-200 text-gray-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                Süper Yönetici
                            </span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ Str::limit($adminDurum['mesaj'], 150) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    {{-- ====================================================================== --}}
    {{-- 3. KART: Müşteri Geri Bildirimi (SAĞ) --}}
    {{-- ====================================================================== --}}
    @if($sikayet && $sikayet->musteri_feedback)
        @php
            // Duruma göre renk ve ikon belirleme mantığı
            $feedbackColor = match ($sikayet->musteri_feedback) {
                'Onaylandı' => 'green',
                'Reddedildi' => 'red',
                'Revizyon İstendi' => 'yellow',
                default => 'gray'
            };

            // Tarih formatı (Veritabanındaki güncellenme tarihini kullanıyoruz)
            $islemTarihi = $sikayet->updated_at->format('d.m.Y H:i');
        @endphp

        <div
            class="mt-5 p-5 rounded-xl shadow-sm border-l-4 bg-{{ $feedbackColor }}-50 border-{{ $feedbackColor }}-500 transition-all hover:shadow-md">
            <div class="flex items-start gap-4">

                {{-- 1. İKON ALANI --}}
                <div class="flex-shrink-0">
                    <div
                        class="w-10 h-10 flex justify-center items-center rounded-full bg-white shadow-sm text-{{ $feedbackColor }}-600">
                        @if($sikayet->musteri_feedback == 'Onaylandı')
                            {{-- Onay İkonu --}}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @elseif($sikayet->musteri_feedback == 'Reddedildi')
                            {{-- Red İkonu --}}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        @elseif($sikayet->musteri_feedback == 'Revizyon İstendi')
                            {{-- Revizyon İkonu --}}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        @endif
                    </div>
                </div>

                {{-- 2. İÇERİK ALANI --}}
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-lg font-bold text-{{ $feedbackColor }}-800">
                                Müşteri Kararı: {{ $sikayet->musteri_feedback }}
                            </h4>
                            @if($sikayet->musteri_feedback_note)
                                <p class="text-sm text-{{ $feedbackColor }}-700 mt-1 italic">
                                    "{{ $sikayet->musteri_feedback_note }}"
                                </p>
                            @else
                                <p class="text-sm text-{{ $feedbackColor }}-600/70 mt-1 italic">
                                    (Ek açıklama girilmedi)
                                </p>
                            @endif
                           </div>

                            {{-- Tarih ve Saat Rozeti --}}
                            <div
                                class="flex items-center gap-1.5 text-xs font-medium text-{{ $feedbackColor }}-700 bg-white px-3 py-1 rounded-full shadow-sm border border-{{ $feedbackColor }}-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $islemTarihi }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @endif
</div>