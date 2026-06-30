@props(['iaa', 'statusDate' => null])

@php
    use App\Models\IaaLog;

    // 1. BÖLÜM ONAYI MANTIĞI
    $bolumDurum = [
        'tip' => 'waiting',
        'baslik' => $iaa->musteriSikayeti ? 'Bölüm Kalite Yöneticisi Onayı Bekleniyor' : 'Süreç Onayı Bekleniyor',
        'mesaj' => $iaa->musteriSikayeti ? 'Proje tamamlandığında bölüm kalite yöneticisi inceleyecektir.' : 'Proje tamamlandığında yönetim inceleyecektir.',
        'tarih' => null,
        'kisi' => $iaa->musteriSikayeti ? 'Bölüm Kalite Yöneticisi' : 'Süper Yönetici',
        'locked' => false
    ];

    $bolumLog = IaaLog::where('iaa_id', $iaa->id)
        ->whereIn('eylem', ['Bölüm Onayı Verildi', 'Bölüm Onaylandı (Direktör Onayına Sevk)', 'Bölüm Onaylandı (İadeli - Direktör Onayına Sevk)', 'Proje Onaylandı'])
        ->with('user')
        ->latest()
        ->first();

    // FALLBACK: Eğer proje durumu Direktör veya Yönetici onayı aşamasındaysa, log olmasa bile bu aşama geçilmiştir.
    $bolumGecildi = in_array($iaa->durum, ['Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Tamamlandı', 'Talep Olarak Kapatıldı', 'Tamamlanması Reddedildi']);

    if ($bolumLog || $bolumGecildi) {
        $bolumDurum['tip'] = 'success';
        $bolumDurum['baslik'] = $iaa->musteriSikayeti ? 'Bölüm Kalite Yöneticisi Onayladı' : 'Süreç Onaylandı';
        $bolumDurum['mesaj'] = 'Bölüm yöneticisi tarafından projenin tamamlanması onaylandı.';
        $bolumDurum['tarih'] = $bolumLog ? $bolumLog->created_at : $iaa->updated_at;
        $bolumDurum['kisi'] = $bolumLog && $bolumLog->user ? $bolumLog->user->name : ($iaa->musteriSikayeti->sikayetKategori->bolum->qualityManager->name ?? 'Bölüm Yöneticisi');
    } else {
        // Revizyon veya Red durumlarını kontrol et (Sadece log varsa)
        $bolumHataLog = IaaLog::where('iaa_id', $iaa->id)
            ->whereIn('eylem', ['Revizyon Talep Edildi (Bölüm)', 'Proje Reddedildi (Bölüm)'])
            ->with('user')
            ->latest()
            ->first();
            
        if ($bolumHataLog) {
            $bolumDurum['tip'] = $bolumHataLog->eylem == 'Proje Reddedildi (Bölüm)' ? 'danger' : 'warning';
            $bolumDurum['baslik'] = $bolumHataLog->eylem == 'Proje Reddedildi (Bölüm)' ? 'Bölüm Tarafından Reddedildi' : 'Revizyon Talebi';
            $bolumDurum['mesaj'] = $bolumHataLog->aciklama;
            $bolumDurum['tarih'] = $bolumHataLog->created_at;
            $bolumDurum['kisi'] = $bolumHataLog->user->name ?? 'Bölüm Yöneticisi';
        }
    }

    // 2. DİREKTÖR ONAYI MANTIĞI
    $direktorOnayiAktif = false;
    $globalDirektorSetting = false;

    if ($iaa->musteriSikayeti) {
        $setting = \App\Models\Setting::where('key', 'sikayet_direktor_onayi_aktif')->first();
        if ($setting && $setting->value == '1') {
            $globalDirektorSetting = true;
        }
    }

    if ($globalDirektorSetting && $iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum && $iaa->musteriSikayeti->sikayetKategori->bolum->director_id) {
        $direktorOnayiAktif = true;
    }

    $direktorLog = IaaLog::where('iaa_id', $iaa->id)
        ->whereIn('eylem', ['Direktör Onayı Verildi', 'Revizyon Talep Edildi (Direktör)', 'Proje Reddedildi (Direktör)'])
        ->with('user')
        ->latest()
        ->first();

    if ($direktorLog) {
        $direktorOnayiAktif = true;
    }

    $direktorDurum = [
        'tip' => 'waiting',
        'baslik' => 'Direktör Onayı Bekleniyor',
        'mesaj' => 'Bölüm onayından sonra direktör inceleyecektir.',
        'tarih' => null,
        'kisi' => 'Direktör',
        'locked' => $bolumDurum['tip'] !== 'success'
    ];

    // FALLBACK: Eğer proje durumu Yönetici onayı veya Tamamlandı aşamasındaysa, log olmasa bile bu aşama geçilmiştir.
    $direktorGecildi = in_array($iaa->durum, ['Yönetici Onayı Bekliyor', 'Tamamlandı', 'Talep Olarak Kapatıldı']);

    if ($direktorLog || $direktorGecildi) {
        $direktorDurum['locked'] = false;
        if (($direktorLog && $direktorLog->eylem == 'Direktör Onayı Verildi') || $direktorGecildi) {
            $direktorDurum['tip'] = 'success';
            $direktorDurum['baslik'] = 'Direktör Onayladı';
            $direktorDurum['mesaj'] = 'Bölüm direktörü tarafından proje sonucu onaylandı.';
            $direktorDurum['tarih'] = $direktorLog ? $direktorLog->created_at : $iaa->updated_at;
            $direktorDurum['kisi'] = $direktorLog && $direktorLog->user ? $direktorLog->user->name : ($iaa->musteriSikayeti->sikayetKategori->bolum->director->name ?? 'Direktör');
        } else if ($direktorLog) {
            $direktorDurum['tip'] = $direktorLog->eylem == 'Proje Reddedildi (Direktör)' ? 'danger' : 'warning';
            $direktorDurum['baslik'] = $direktorLog->eylem == 'Proje Reddedildi (Direktör)' ? 'Direktör Tarafından Reddedildi' : 'Revizyon Talebi (Direktör)';
            $direktorDurum['mesaj'] = $direktorLog->aciklama;
            $direktorDurum['tarih'] = $direktorLog->created_at;
            $direktorDurum['kisi'] = $direktorLog->user->name ?? 'Direktör';
        }
    }

    // 3. FİNAL (YÖNETİCİ/SÜPERADMİN) ONAYI MANTIĞI
    $adminLog = IaaLog::where('iaa_id', $iaa->id)
        ->whereIn('eylem', ['Proje Onaylandı', 'Revizyon Talep Edildi', 'Tamamlanmış Projenin Reddi'])
        ->with('user')
        ->latest()
        ->first();

    $adminDurum = [
        'tip' => 'waiting',
        'baslik' => 'Final Onay Bekleniyor',
        'mesaj' => 'Onay süreçleri tamamlandıktan sonra üst yönetim inceleyecektir.',
        'tarih' => null,
        'kisi' => 'Süper Yönetici',
        'locked' => $direktorOnayiAktif ? ($direktorDurum['tip'] !== 'success') : ($bolumDurum['tip'] !== 'success')
    ];

    if ($adminLog || in_array($iaa->durum, ['Tamamlandı', 'Talep Olarak Kapatıldı'])) {
        $adminDurum['locked'] = false;
        if (($adminLog && $adminLog->eylem == 'Proje Onaylandı') || in_array($iaa->durum, ['Tamamlandı', 'Talep Olarak Kapatıldı'])) {
            $adminDurum['tip'] = 'success';
            $adminDurum['baslik'] = 'Proje Tamamlandı';
            $adminDurum['mesaj'] = 'Proje tüm onay süreçlerini başarıyla tamamlayarak kapatıldı.';
            $adminDurum['tarih'] = $adminLog ? $adminLog->created_at : $iaa->onaylanma_tarihi;
            $adminDurum['kisi'] = $adminLog && $adminLog->user ? $adminLog->user->name : 'Yönetim';
        } else if ($adminLog) {
            $adminDurum['tip'] = $adminLog->eylem == 'Tamamlanmış Projenin Reddi' ? 'danger' : 'warning';
            $adminDurum['baslik'] = $adminLog->eylem == 'Tamamlanmış Projenin Reddi' ? 'Proje Reddedildi' : 'Revizyon Talebi';
            $adminDurum['mesaj'] = $adminLog->aciklama;
            $adminDurum['tarih'] = $adminLog->created_at;
            $adminDurum['kisi'] = $adminLog->user->name ?? 'Süper Yönetici';
        }
    }

    // Renk Paleti Fonksiyonu
    if (!function_exists('getFinalStatusColors')) {
        function getFinalStatusColors($type)
        {
            return match ($type) {
                'success' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'icon_bg' => 'bg-green-100', 'icon_text' => 'text-green-600', 'title' => 'text-green-800'],
                'warning' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'icon_bg' => 'bg-yellow-100', 'icon_text' => 'text-yellow-600', 'title' => 'text-yellow-800'],
                'danger' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'icon_bg' => 'bg-red-100', 'icon_text' => 'text-red-600', 'title' => 'text-red-800'],
                'waiting' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'icon_bg' => 'bg-gray-100', 'icon_text' => 'text-gray-400', 'title' => 'text-gray-700'],
                default => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'icon_bg' => 'bg-gray-100', 'icon_text' => 'text-gray-400', 'title' => 'text-gray-700'],
            };
        }
    }

    $bRenk = getFinalStatusColors($bolumDurum['tip']);
    $dRenk = getFinalStatusColors($direktorDurum['tip']);
    $aRenk = getFinalStatusColors($adminDurum['tip']);

    $activeCardCount = 1; // Süper Yönetici her zaman var
    if ($iaa->musteriSikayeti) $activeCardCount++; // Bölüm Yöneticisi
    if ($direktorOnayiAktif) $activeCardCount++; // Direktör

    $gridClass = match ($activeCardCount) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 xl:grid-cols-3',
        default => 'grid-cols-1 md:grid-cols-2'
    };

    $sikayet = $iaa->musteriSikayeti;
@endphp

<div id="onay-durum-paneli" class="mt-8 mb-8 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md relative">
    
    {{-- BAŞLIK ALANI --}}
    <div class="bg-gradient-to-r from-indigo-50 via-white to-white px-6 py-5 border-b border-indigo-100 flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                Onay Durum Paneli
            </h3>
            <p class="text-xs text-indigo-600 mt-1 pl-11">Projenin aşamalarının yöneticiler tarafından onay durumu.</p>
        </div>
    </div>
    
    <div class="p-6 md:p-8">

    {{-- PROJE ONAYA GÖNDERİLME BİLGİSİ (LOGDAN ÇEKİLİR) --}}
    @php
        $submissionLog = IaaLog::where('iaa_id', $iaa->id)
            ->whereIn('eylem', ['Bölüm Onayına Gönderildi', 'Bölüm Onayına Gönderildi (İadeli)', 'Yönetici Onayına Gönderildi'])
            ->with('user')
            ->latest()
            ->first();
    @endphp

    @if($submissionLog && !in_array($iaa->durum, ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Çalışılıyor']))
        <div class="mb-6 p-4 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-between animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <div>
                    <p class="text-sm text-indigo-900 font-semibold text-wrap">
                        Proje, <span class="text-indigo-700 underline decoration-indigo-300 underline-offset-4">{{ $submissionLog->user->name ?? 'Lider' }}</span> tarafından 
                        <span class="text-indigo-700">{{ $submissionLog->created_at->format('d.m.Y') }}</span> günü 
                        saat <span class="text-indigo-700">{{ $submissionLog->created_at->format('H:i') }}</span>'de onay sürecine gönderilmiştir.
                    </p>
                </div>
            </div>
            <div class="hidden md:block shrink-0 px-3 py-1 bg-white/50 rounded-full border border-indigo-200 text-[10px] font-bold text-indigo-500 uppercase tracking-tighter">
                {{ $iaa->durum == 'Tamamlandı' ? 'Süreç Tamamlandı' : 'Süreç Başlatıldı' }}
            </div>
        </div>
    @endif

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

                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            {{ Str::limit($direktorDurum['mesaj'], 150) }}
                        </p>

                        {{-- DİREKTÖR İŞLEMİ GERİ AL BUTONU (YENİ KONUM) --}}
                        @if($direktorDurum['tip'] == 'success' && $iaa->durum == 'Tamamlandı')
                            @if(auth()->check() && (auth()->user()->hasRole('Superadmin') || auth()->user()->hasRole('Direktör')))
                                <form action="{{ route('admin.iaa-yonetim.direktorOnayiGeriAl', $iaa->id) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Direktör onayını geri çekmek üzeresiniz. Puanlar silinecek ve proje \'Direktör Onayı Bekliyor\' aşamasına dönecek. Emin misiniz?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="text-[10px] text-red-600 hover:text-red-800 font-bold bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg border border-red-200 shadow-sm transition-all flex items-center gap-1.5 active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        İşlemi Geri Al
                                    </button>
                                </form>
                            @endif
                        @endif
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
                        class="inline-flex items-center px-3 py-1 rounded-bl-xl text-[10px] font-bold uppercase tracking-widest bg-white/60 border-l border-b {{ $aRenk['border'] }} {{ $aRenk['title'] }} shadow-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $adminDurum['tarih']->format('d.m.Y H:i') }}
                    </span>
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

                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        {{ Str::limit($adminDurum['mesaj'], 150) }}
                    </p>

                    {{-- ALT AKSİYON ALANI (YENİ KONUM) --}}
                    @if($adminDurum['tip'] == 'success')
                        <div class="flex items-center gap-3">
                            {{-- GERİ AL BUTONU --}}
                            @if(auth()->check() && auth()->user()->hasRole('Superadmin'))
                                <form action="{{ route('admin.iaa-yonetim.geriAl', $iaa->id) }}" method="POST"
                                    onsubmit="return confirm('Proje onayını geri almak üzeresiniz. Puanlar geri alınacak ve proje \'Yönetici Onayı Bekliyor\' durumuna dönecek. Emin misiniz?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="text-[10px] text-red-600 hover:text-red-800 font-bold bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg border border-red-200 shadow-sm transition-all flex items-center gap-1.5 active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        İşlemi Geri Al
                                    </button>
                                </form>
                            @endif

                            {{-- DURUM ROZETİ --}}
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest bg-green-100 text-green-800 border border-green-200 shadow-sm">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Kapatıldı
                            </span>
                        </div>
                    @endif
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
    
    </div> {{-- End of p-6 md:p-8 --}}
</div>