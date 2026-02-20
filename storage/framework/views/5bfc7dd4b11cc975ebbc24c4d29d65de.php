<div class="w-full">
    <div class="bg-gradient-to-br from-white to-gray-50 p-6 rounded-xl shadow-lg border border-gray-200">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Proje Künyesi</h3>
        </div>

        
            <div class="bg-blue-50 rounded-lg p-4 shadow-sm border border-blue-100 col-span-1 md:col-span-4 mt-2 mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <div class="p-1.5 bg-blue-100 rounded-md">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-blue-600/80">Uygulanan Akış</dt>
                        <dd class="text-sm font-bold text-blue-900 leading-tight">
                            <?php echo e($workflow->name ?? 'Standart Akış'); ?>

                        </dd>
                    </div>
                </div>
                
                
                <?php if(isset($steps) && $steps->count() > 0): ?>
                    <div class="flex flex-wrap items-center gap-y-3">
                        <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $isCompleted = in_array($step->id, $completedStepIds);
                                
                                // Gizlilik verisini çekme
                                $pUpdate = isset($progressUpdates) ? ($progressUpdates[$step->id] ?? null) : null;
                                $isHidden = $pUpdate ? $pUpdate->is_hidden_from_customer : false;
                                
                                // Personel kontrolü
                                $isPersonnel = auth()->check() && auth()->user()->is_personnel;
                            ?>

                            <div class="flex items-center group">
                                
                                
                                <?php if($isCompleted): ?>
                                    
                                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 flex items-center justify-center mr-1.5 border border-green-200" title="Tamamlandı">
                                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                <?php else: ?>
                                    
                                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-white flex items-center justify-center mr-1.5 border-2 border-gray-300" title="Bekliyor">
                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
                                    </div>
                                <?php endif; ?>

                                
                                
                                <a href="#step-<?php echo e($step->id); ?>" class="text-sm font-medium transition-colors hover:underline decoration-blue-400 decoration-2 underline-offset-2 cursor-pointer
                                    <?php echo e($isCompleted ? 'text-gray-900' : 'text-gray-500'); ?>">
                                    <?php echo e($loop->iteration); ?>. <?php echo e($step->name); ?>

                                </a>

                                
                                <?php if($isHidden && $isPersonnel): ?>
                                     <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600 border border-red-200 select-none" title="Bu adım müşteriye GİZLENMİŞTİR">
                                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                        GİZLİ
                                     </span>
                                <?php endif; ?>

                                
                                <?php if(!$loop->last): ?>
                                    <svg class="w-4 h-4 text-blue-300 mx-2 sm:mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
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
                    <dd class="text-sm font-bold text-gray-900">
                        <a href="<?php echo e(route('profile.show', $takim->lider->id)); ?>" class="hover:text-indigo-600 hover:underline transition-colors flex items-center gap-1">
                            <?php echo e($takim->lider->name); ?>

                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </dd>
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
            
            
            <?php echo $iaa->durum_etiketi; ?>

            
            <?php if(isset($statusDate)): ?>
                <div class="mt-2 text-xs text-gray-500 font-medium">
                    <?php echo e(\Carbon\Carbon::parse($statusDate)->format('d.m.Y H:i')); ?>

                </div>
            <?php endif; ?>
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

    
    <?php if($iaa->musteriSikayeti && $iaa->musteriSikayeti->iadeler->isNotEmpty()): ?>
        <?php $iade = $iaa->musteriSikayeti->iadeler->first(); ?>
        <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-3 flex items-start gap-3 animate-pulse-slow">
            <div class="p-2 bg-red-100 rounded-full shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-red-900">İADE ALINMIŞTIR</h4>
                <p class="text-xs text-red-700 mt-0.5">
                    Bu şikayet kapsamında <strong><?php echo e($iaa->musteriSikayeti->customer->name ?? 'Müşteri'); ?></strong> firmasından 
                    <strong><?php echo e($iade->miktar); ?> <?php echo e($iade->birim); ?> <?php echo e($iade->urun_turu); ?></strong> ürün, 
                    <strong><?php echo e($iade->iade_sebebi); ?></strong> sebebiyle iade alınmıştır.
                </p>
            </div>
        </div>
    <?php endif; ?>

</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_project-header.blade.php ENDPATH**/ ?>