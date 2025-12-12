@props(['iaa', 'statusDate' => null])

@php
    use App\Models\IaaLog;

    // ========================================================================
    // 1. BÖLÜM YÖNETİCİSİ VERİLERİ
    // ========================================================================
    $bolumLog = IaaLog::where('iaa_id', $iaa->id)
        ->where(function($q) {
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
    // 2. SÜPER YÖNETİCİ VERİLERİ
    // ========================================================================
    $adminLog = IaaLog::where('iaa_id', $iaa->id)
        ->where(function($q) {
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
        'mesaj' => 'Bölüm onayı tamamlandıktan sonra üst yönetim inceleyecektir.',
        'tarih' => null,
        'kisi' => 'Süper Yönetici',
        'locked' => true // Varsayılan olarak kilitli başlar
    ];

    // Kilit Mantığı: Eğer Bölüm Onayı verildiyse kilit açılır.
    if ($bolumDurum['tip'] == 'success') {
        $adminDurum['locked'] = false;
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

    // Renk Paleti
    function getColors($type) {
        return match($type) {
            'success' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'icon_bg' => 'bg-green-100', 'icon_text' => 'text-green-600', 'title' => 'text-green-800'],
            'warning' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'icon_bg' => 'bg-yellow-100', 'icon_text' => 'text-yellow-600', 'title' => 'text-yellow-800'],
            'danger'  => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'icon_bg' => 'bg-red-100', 'icon_text' => 'text-red-600', 'title' => 'text-red-800'],
            'waiting' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'icon_bg' => 'bg-gray-100', 'icon_text' => 'text-gray-400', 'title' => 'text-gray-700'],
            default   => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'icon_bg' => 'bg-gray-100', 'icon_text' => 'text-gray-400', 'title' => 'text-gray-700'],
        };
    }

    $bRenk = getColors($bolumDurum['tip']);
    $aRenk = getColors($adminDurum['tip']);
@endphp

<div class="mt-10">
    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Onay Durum Paneli
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- ====================================================================== --}}
        {{-- 1. KART: BÖLÜM YÖNETİCİSİ (SOL) --}}
        {{-- ====================================================================== --}}
        <div class="rounded-xl p-6 border {{ $bRenk['bg'] }} {{ $bRenk['border'] }} shadow-sm relative overflow-hidden group transition-all hover:shadow-md">
            <div class="flex items-start gap-4">
                {{-- İkon --}}
                <div class="w-12 h-12 rounded-full {{ $bRenk['icon_bg'] }} flex items-center justify-center flex-shrink-0">
                    @if($bolumDurum['tip'] == 'success')
                        <svg class="w-6 h-6 {{ $bRenk['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @elseif($bolumDurum['tip'] == 'warning')
                        <svg class="w-6 h-6 {{ $bRenk['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    @elseif($bolumDurum['tip'] == 'danger')
                        <svg class="w-6 h-6 {{ $bRenk['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    @else
                        <svg class="w-6 h-6 {{ $bRenk['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>

                <div class="flex-1">
                    <h4 class="text-base font-bold {{ $bRenk['title'] }} mb-2">{{ $bolumDurum['baslik'] }}</h4>
                    
                    {{-- Kişi Bilgisi --}}
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-full bg-white/80 border border-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600 shadow-sm">
                            {{ substr($bolumDurum['kisi'], 0, 1) }}
                        </div>
                        <span class="text-xs font-semibold text-gray-700">{{ $bolumDurum['kisi'] }}</span>
                    </div>

                    <p class="text-sm text-gray-600 leading-relaxed mb-3">
                        {{ Str::limit($bolumDurum['mesaj'], 150) }}
                    </p>

                    @if($bolumDurum['tarih'])
                        <div class="pt-3 border-t border-gray-200/60 text-xs text-gray-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $bolumDurum['tarih']->format('d.m.Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- 2. KART: SÜPER YÖNETİCİ (SAĞ) --}}
        {{-- ====================================================================== --}}
        {{-- Kilitli ise biraz silik görünür --}}
        <div class="rounded-xl p-6 border {{ $aRenk['bg'] }} {{ $aRenk['border'] }} shadow-sm relative overflow-hidden group transition-all hover:shadow-md {{ $adminDurum['locked'] ? 'opacity-80 bg-gray-50' : '' }}">
             
             {{-- NİHAİ ONAY MÜHRÜ (Sağ Üst Köşe) --}}
             @if($adminDurum['tip'] == 'success')
                <div class="absolute top-0 right-0">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-bl-xl text-[10px] font-bold uppercase tracking-widest bg-green-100 text-green-800 border-l border-b border-green-200 shadow-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Şikayet Kapatıldı
                    </span>
                </div>
             @endif

             <div class="flex items-start gap-4">
                {{-- İkon (Kilitliyse Kilit, Değilse Duruma Göre) --}}
                <div class="w-12 h-12 rounded-full {{ $aRenk['icon_bg'] }} flex items-center justify-center flex-shrink-0">
                    @if($adminDurum['locked'])
                         {{-- Kilit İkonu --}}
                         <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    @elseif($adminDurum['tip'] == 'success')
                        <svg class="w-6 h-6 {{ $aRenk['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($adminDurum['tip'] == 'warning')
                        <svg class="w-6 h-6 {{ $aRenk['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    @elseif($adminDurum['tip'] == 'danger')
                        <svg class="w-6 h-6 {{ $aRenk['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    @else
                        <svg class="w-6 h-6 {{ $aRenk['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>

                <div class="flex-1">
                    <h4 class="text-base font-bold {{ $aRenk['title'] }} mb-2">{{ $adminDurum['baslik'] }}</h4>
                    
                    {{-- Kişi Bilgisi --}}
                    <div class="flex items-center gap-2 mb-3">
                        @if(!$adminDurum['locked'])
                            <div class="w-6 h-6 rounded-full bg-white/80 border border-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600 shadow-sm">
                                {{ substr($adminDurum['kisi'], 0, 1) }}
                            </div>
                            <span class="text-xs font-semibold text-gray-700">{{ $adminDurum['kisi'] }}</span>
                        @else
                            {{-- Kilitliyse hayalet isim --}}
                            <div class="w-6 h-6 rounded-full bg-gray-100 border border-gray-200"></div>
                            <span class="text-xs font-semibold text-gray-400">Süper Yönetici</span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-600 leading-relaxed mb-3">
                        {{ Str::limit($adminDurum['mesaj'], 150) }}
                    </p>

                    {{-- Tarih (Sadece kilitli değilse ve tarih varsa) --}}
                    @if(!$adminDurum['locked'] && $adminDurum['tarih'])
                        <div class="pt-3 border-t border-gray-200/60 text-xs text-gray-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $adminDurum['tarih']->format('d.m.Y H:i') }}
                        </div>
                    @elseif($adminDurum['locked'])
                         {{-- Kilitli ise boş tarih çizgisi --}}
                         <div class="pt-3 border-t border-gray-200/60 text-xs text-gray-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Sıra Bekleniyor
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- ====================================================================== --}}
        {{-- 3. KART: Müşteri Geri Bildirimi (SAĞ) --}}
        {{-- ====================================================================== --}}
        @if($sikayet->musteri_feedback)
        @php
            // Duruma göre renk ve ikon belirleme mantığı
            $feedbackColor = match($sikayet->musteri_feedback) {
                'Onaylandı' => 'green',
                'Reddedildi' => 'red',
                'Revizyon İstendi' => 'yellow',
                default => 'gray'
            };
            
            // Tarih formatı (Veritabanındaki güncellenme tarihini kullanıyoruz)
            $islemTarihi = $sikayet->updated_at->format('d.m.Y H:i');
        @endphp

        <div class="mt-5 p-5 rounded-xl shadow-sm border-l-4 bg-{{ $feedbackColor }}-50 border-{{ $feedbackColor }}-500 transition-all hover:shadow-md">
            <div class="flex items-start gap-4">
                
                {{-- 1. İKON ALANI --}}
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 flex justify-center items-center rounded-full bg-white shadow-sm text-{{ $feedbackColor }}-600">
                        @if($sikayet->musteri_feedback == 'Onaylandı')
                            {{-- Onay İkonu --}}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif($sikayet->musteri_feedback == 'Reddedildi')
                            {{-- Red İkonu --}}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @elseif($sikayet->musteri_feedback == 'Revizyon İstendi')
                            {{-- Revizyon İkonu --}}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
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
                        <div class="flex items-center gap-1.5 text-xs font-medium text-{{ $feedbackColor }}-700 bg-white px-3 py-1 rounded-full shadow-sm border border-{{ $feedbackColor }}-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $islemTarihi }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>