<div class="w-full">
    <div class="bg-gradient-to-br from-white to-gray-50 p-6 rounded-xl shadow-lg border border-gray-200">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Proje Künyesi</h3>
        </div>
        
        <dl class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                <dt class="text-xs font-medium text-gray-500 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Takım
                </dt>
                <dd class="text-sm font-bold text-gray-900"><?php echo e($takim->ad); ?></dd>
            </div>
            
            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                <dt class="text-xs font-medium text-gray-500 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Takım Lideri
                </dt>
                <dd class="text-sm font-bold text-gray-900"><?php echo e($takim->lider->name); ?></dd>
            </div>
            
            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                <dt class="text-xs font-medium text-gray-500 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Başlangıç
                </dt>
                <dd class="text-sm font-bold text-gray-900"><?php echo e(\Carbon\Carbon::parse($assignment->start_date)->format('d.m.Y')); ?></dd>
            </div>
            
            <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-lg p-3 shadow-sm border-2 border-red-200">
                <dt class="text-xs font-medium text-red-600 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Bitiş Tarihi
                </dt>
                <dd class="text-sm font-bold text-red-700"><?php echo e(\Carbon\Carbon::parse($assignment->due_date)->format('d.m.Y')); ?></dd>
            </div>
        </dl>

        <div class="mt-4 text-center">
            <?php
                $durumRenk = match($iaa->durum) {
                    'Tamamlandı' => 'bg-green-100 text-green-800',
                    'Yönetici Onayı Bekliyor' => 'bg-blue-100 text-blue-800',
                    'Revize Ediliyor' => 'bg-yellow-100 text-yellow-800',
                    'Tamamlanması Reddedildi' => 'bg-red-100 text-red-800',
                    'Atandı' => 'bg-indigo-100 text-indigo-800',
                    default => 'bg-gray-100 text-gray-800',
                };
            ?>
            <span class="inline-block px-4 py-2 rounded-full font-semibold shadow-sm text-sm <?php echo e($durumRenk); ?>">
                Mevcut Proje Durumu: <strong><?php echo e($iaa->durum); ?></strong>
            </span>
        </div>

        
        <?php if($iaa->durum == 'Revize Ediliyor' || $iaa->durum == 'Tamamlanması Reddedildi' || $iaa->durum == 'Tamamlandı'): ?>
            <?php
                // === 1. LOG VERİSİNİ ÇEK ===
                $logKaydi = null;
                $yapanKisi = 'Yönetici';
                $yapanUnvan = 'Yönetici';
                $islemTarihi = $statusDate ?? now();

                // Sadece Red veya Revize durumunda loga bakmaya gerek var
                if ($iaa->durum == 'Revize Ediliyor' || $iaa->durum == 'Tamamlanması Reddedildi') {
                    $aranacakKelime = ($iaa->durum == 'Revize Ediliyor') ? 'Revizyon' : 'Red';
                    
                    $logKaydi = \App\Models\IaaLog::where('iaa_id', $iaa->id)
                        ->where('eylem', 'like', '%' . $aranacakKelime . '%')
                        ->with('user')
                        ->latest()
                        ->first();

                    if ($logKaydi && $logKaydi->user) {
                        $yapanKisi = $logKaydi->user->name;
                        $islemTarihi = $logKaydi->created_at;
                        
                        if ($logKaydi->user->hasRole('Superadmin')) {
                            $yapanUnvan = 'Süper Yönetici';
                        } elseif ($logKaydi->user->hasRole('Bölüm Kalite Yöneticisi')) {
                            $yapanUnvan = 'Bölüm Yöneticisi';
                        }
                    }
                }

                // === 2. KUTU AYARLARINI YAP ===
                $kutuAyar = match($iaa->durum) {
                    'Tamamlandı' => [
                        'baslik' => 'Proje Onaylandı',
                        'renk' => 'green',
                        'ikon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                        'mesaj' => 'Proje yönetici tarafından onaylanarak başarıyla tamamlandı.'
                    ],
                    'Tamamlanması Reddedildi' => [
                        'baslik' => $yapanUnvan . ' (' . $yapanKisi . ') Reddedildi', // Başlık Dinamik Oldu
                        'renk' => 'red',
                        'ikon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />',
                        'mesaj' => $iaa->yonetici_notu
                    ],
                    'Revize Ediliyor' => [
                        'baslik' => $yapanUnvan . ' Revizyon Talebi', // Başlık Dinamik Oldu
                        'renk' => 'yellow',
                        'ikon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />',
                        'mesaj' => $iaa->yonetici_notu
                    ],
                    default => [
                        'renk' => 'gray', 'ikon' => '', 'baslik' => 'Bilinmeyen Durum', 'mesaj' => ''
                    ]
                };
            ?>

            <div class="mt-6 p-4 bg-<?php echo e($kutuAyar['renk']); ?>-50 border-l-4 border-<?php echo e($kutuAyar['renk']); ?>-400 rounded-r-lg shadow-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-<?php echo e($kutuAyar['renk']); ?>-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <?php echo $kutuAyar['ikon']; ?>

                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-sm font-bold text-<?php echo e($kutuAyar['renk']); ?>-800 flex items-center gap-2">
                                    <?php echo e($kutuAyar['baslik']); ?>

                                    <?php if(isset($yapanKisi) && ($iaa->durum == 'Revize Ediliyor' || $iaa->durum == 'Tamamlanması Reddedildi')): ?>
                                        <span class="text-xs font-normal bg-white/50 px-2 py-0.5 rounded text-<?php echo e($kutuAyar['renk']); ?>-700 border border-<?php echo e($kutuAyar['renk']); ?>-200">
                                            <?php echo e($yapanKisi); ?>

                                        </span>
                                    <?php endif; ?>
                                </h4>
                                <div class="mt-2 text-sm text-<?php echo e($kutuAyar['renk']); ?>-700">
                                    <p class="whitespace-pre-wrap font-medium">"<?php echo e($kutuAyar['mesaj']); ?>"</p>
                                </div>
                            </div>
                            <?php if(!empty($islemTarihi)): ?>
                            <div class="ml-4 flex-shrink-0 text-xs text-<?php echo e($kutuAyar['renk']); ?>-600 font-medium flex items-center space-x-1.5 whitespace-nowrap">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h18"></path></svg>
                                <span><?php echo e(\Carbon\Carbon::parse($islemTarihi)->format('d.m.Y H:i')); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-sm font-semibold text-gray-700">İlerleme Durumu</p>
                        <span class="text-2xl font-bold text-blue-600"><?php echo e(round($progressPercentage)); ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-500 shadow-sm" style="width: <?php echo e($progressPercentage); ?>%"></div>
                    </div>
                </div>
                <div class="flex items-center justify-center md:justify-end gap-6">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gray-900"><?php echo e($completedStepsCount); ?></p>
                        <p class="text-xs text-gray-600">Tamamlanan</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gray-400"><?php echo e($totalStepsCount - $completedStepsCount); ?></p>
                        <p class="text-xs text-gray-600">Kalan</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-blue-600"><?php echo e($totalStepsCount); ?></p>
                        <p class="text-xs text-gray-600">Toplam Adım</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_project-header.blade.php ENDPATH**/ ?>