
<?php if(auth()->user()->is_personnel && !auth()->user()->hasRole(['Müşteri Temsilcisi', 'Müşteri'])): ?>
    <?php
        $birthdayIsActive = \App\Models\Setting::where('key', 'birthday_is_active')->first()?->value ?? '1';
        $anniversaryIsActive = \App\Models\Setting::where('key', 'anniversary_is_active')->first()?->value ?? '1';
    ?>

    <?php if($birthdayIsActive == '1' || $anniversaryIsActive == '1'): ?>
    <div class="col-span-full mb-8" x-data="{ reminderType: '<?php echo e($birthdayIsActive == '1' ? 'birthday' : 'anniversary'); ?>' }">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            <div class="bg-gray-900 px-6 py-4 flex flex-col <?php echo e(isset($isSidebar) && $isSidebar ? '' : 'md:flex-row'); ?> md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/10 backdrop-blur-md rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white tracking-tight">Hatırlatıcılar & Kutlamalar</h3>
                </div>

                
                <div class="flex bg-white/10 p-1 rounded-xl backdrop-blur-sm">
                    <?php if($birthdayIsActive == '1'): ?>
                    <button type="button" @click="reminderType = 'birthday'" :class="reminderType === 'birthday' ? 'bg-white text-gray-900 shadow-sm' : 'text-white hover:bg-white/10'" class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all duration-300 flex items-center gap-2">
                        🎂 Doğum Günleri
                        <?php if($dogumGunuBugun->isNotEmpty()): ?>
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        <?php endif; ?>
                    </button>
                    <?php endif; ?>
                    
                    <?php if($anniversaryIsActive == '1'): ?>
                    <button type="button" @click="reminderType = 'anniversary'" :class="reminderType === 'anniversary' ? 'bg-white text-gray-900 shadow-sm' : 'text-white hover:bg-white/10'" class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all duration-300 flex items-center gap-2">
                        🎊 İş Yıldönümleri
                        <?php if($yildonumuBugun->isNotEmpty()): ?>
                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        <?php endif; ?>
                    </button>
                    <?php endif; ?>
                </div>

                
                <div>
                    <a :href="reminderType === 'birthday' ? '<?php echo e(route('personel.dogum-gunleri')); ?>' : '<?php echo e(route('personel.yildonumleri')); ?>'" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-[10px] font-black uppercase rounded-xl transition-all border border-white/20 backdrop-blur-sm flex items-center gap-2">
                        Tümünü Gör
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
            </div>

            
            <div x-show="reminderType === 'birthday'" x-transition class="p-6 grid grid-cols-1 <?php echo e(isset($isSidebar) && $isSidebar ? '' : 'md:grid-cols-2 lg:grid-cols-3'); ?> gap-8">
                
                <div class="<?php echo e(isset($isSidebar) && $isSidebar ? 'border-b pb-8 mb-4' : 'border-r pr-0 lg:pr-8'); ?> border-gray-50">
                    <?php
                        $pastRange = \App\Models\Setting::where('key', 'birthday_past_days')->first()?->value ?? 3;
                    ?>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Geçmiş Doğum Günleri (<?php echo e($pastRange); ?> Gün)
                    </h4>
                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $dogumGunuGecmis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-center gap-3">
                                <img src="<?php echo e($u->profile_photo_url); ?>" class="w-8 h-8 rounded-full object-cover grayscale opacity-40">
                                <div>
                                    <a href="<?php echo e(route('profile.show', $u->id)); ?>" class="text-xs font-bold text-gray-500 hover:text-indigo-600 transition-colors"><?php echo e($u->name); ?></a>
                                    <p class="text-[9px] text-gray-400">
                                        <?php
                                            $bday = $u->dogum_tarihi->copy()->year(now()->year);
                                            if($bday->isAfter(now()->startOfDay())) $bday->subYear();
                                        ?>
                                        <?php echo e($bday->translatedFormat('d F')); ?> (<?php echo e((int)now()->startOfDay()->diffInDays($bday->startOfDay())); ?> gün önce)
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-xs text-gray-300 italic">Son günlerde kutlama yok.</p>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="lg:px-4">
                    <h4 class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500 <?php echo e($dogumGunuBugun->isNotEmpty() ? 'animate-pulse' : ''); ?>"></span>
                        Bugün Doğanlar
                    </h4>
                    <div class="space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $dogumGunuBugun; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-center justify-between p-3 bg-rose-50 rounded-xl border border-rose-100 hover:border-rose-300 transition-all group">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <img src="<?php echo e($u->profile_photo_url); ?>" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                                        <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-0.5 shadow-sm text-[10px]">🎂</div>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-bold text-gray-900"><?php echo e($u->name); ?></h5>
                                        <p class="text-[10px] text-gray-500"><?php echo e($u->bolum->ad ?? 'Genel'); ?></p>
                                    </div>
                                </div>
                                <a href="<?php echo e(route('profile.show', $u->id)); ?>?tab=yorumlar&bday_msg=1" class="px-4 py-2 bg-white text-rose-500 text-xs font-bold rounded-lg border border-rose-200 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                    Kutla
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="flex flex-col items-center justify-center py-6 border-2 border-dashed border-rose-50 rounded-xl">
                                <p class="text-sm text-gray-300 italic">Bugün doğum günü yok.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="<?php echo e(isset($isSidebar) && $isSidebar ? 'border-t pt-8 mt-4' : 'border-l pl-0 lg:pl-8'); ?> border-gray-50">
                    <?php
                        $upcomingRange = \App\Models\Setting::where('key', 'birthday_upcoming_days')->first()?->value ?? 7;
                    ?>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Yaklaşan Doğum Günleri (<?php echo e($upcomingRange); ?> Gün)</h4>
                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $dogumGunuYaklasan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <img src="<?php echo e($u->profile_photo_url); ?>" class="w-8 h-8 rounded-full object-cover grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all">
                                    <div>
                                        <a href="<?php echo e(route('profile.show', $u->id)); ?>" class="text-sm font-bold text-gray-600 group-hover:text-indigo-600 transition-colors"><?php echo e($u->name); ?></a>
                                        <p class="text-[10px] text-gray-400">
                                            <?php
                                                $bday = $u->dogum_tarihi->copy()->year(now()->year);
                                                if($bday->isBefore(now()->startOfDay())) $bday->addYear();
                                                $daysLeft = (int)now()->startOfDay()->diffInDays($bday->startOfDay());
                                            ?>
                                            <?php echo e($bday->translatedFormat('d F')); ?> (<?php echo e($daysLeft); ?> gün kaldı)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-xs text-gray-300 italic">Yakın zamanda doğum günü yok.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div x-show="reminderType === 'anniversary'" x-transition class="p-6 grid grid-cols-1 <?php echo e(isset($isSidebar) && $isSidebar ? '' : 'md:grid-cols-2 lg:grid-cols-3'); ?> gap-8" style="display:none;">
                
                <div class="<?php echo e(isset($isSidebar) && $isSidebar ? 'border-b pb-8 mb-4' : 'border-r pr-0 lg:pr-8'); ?> border-gray-50">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Geçmiş Yıldönümleri
                    </h4>
                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $yildonumuGecmis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-center gap-3">
                                <img src="<?php echo e($u->profile_photo_url); ?>" class="w-8 h-8 rounded-full object-cover grayscale opacity-40">
                                <div>
                                    <a href="<?php echo e(route('profile.show', $u->id)); ?>" class="text-xs font-bold text-gray-500 hover:text-blue-600 transition-colors"><?php echo e($u->name); ?></a>
                                    <p class="text-[9px] text-gray-400">
                                        <?php echo e($u->hire_date->copy()->year(now()->year)->translatedFormat('d F')); ?> (<?php echo e($u->anniversary_years); ?>. Yıl)
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-xs text-gray-300 italic">Son günlerde yıldönümü yok.</p>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="lg:px-4">
                    <h4 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500 <?php echo e($yildonumuBugun->isNotEmpty() ? 'animate-pulse' : ''); ?>"></span>
                        Bugün Yılını Dolduranlar
                    </h4>
                    <div class="space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $yildonumuBugun; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl border border-blue-100 hover:border-blue-300 transition-all group">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <img src="<?php echo e($u->profile_photo_url); ?>" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                                        <div class="absolute -bottom-1 -right-1 bg-white rounded-full px-1.5 py-0.5 shadow-sm text-[8px] font-black text-blue-600 border border-blue-100"><?php echo e($u->anniversary_years); ?></div>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-bold text-gray-900"><?php echo e($u->name); ?></h5>
                                        <p class="text-[10px] text-gray-500"><?php echo e($u->anniversary_years); ?>. Gurur Yılı!</p>
                                    </div>
                                </div>
                                <a href="<?php echo e(route('profile.show', $u->id)); ?>?tab=yorumlar&anniv_msg=1&years=<?php echo e($u->anniversary_years); ?>" class="px-4 py-2 bg-white text-blue-500 text-xs font-bold rounded-lg border border-blue-200 hover:bg-blue-500 hover:text-white transition-all shadow-sm">
                                    Tebrik Et
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="flex flex-col items-center justify-center py-6 border-2 border-dashed border-blue-50 rounded-xl">
                                <p class="text-sm text-gray-300 italic">Bugün yıldönümü yok.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="<?php echo e(isset($isSidebar) && $isSidebar ? 'border-t pt-8 mt-4' : 'border-l pl-0 lg:pl-8'); ?> border-gray-50">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Yaklaşan Yıldönümleri</h4>
                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $yildonumuYaklasan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <img src="<?php echo e($u->profile_photo_url); ?>" class="w-8 h-8 rounded-full object-cover grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all">
                                    <div>
                                        <a href="<?php echo e(route('profile.show', $u->id)); ?>" class="text-sm font-bold text-gray-600 group-hover:text-blue-600 transition-colors"><?php echo e($u->name); ?></a>
                                        <p class="text-[10px] text-gray-400">
                                            <?php
                                                $anniv = $u->hire_date->copy()->year(now()->year);
                                                if($anniv->isBefore(now()->startOfDay())) $anniv->addYear();
                                                $daysLeft = (int)now()->startOfDay()->diffInDays($anniv->startOfDay());
                                            ?>
                                            <?php echo e($anniv->translatedFormat('d F')); ?> (<?php echo e($u->anniversary_years); ?>. Yıl - <?php echo e($daysLeft); ?> gün)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-xs text-gray-300 italic">Yakın zamanda yıldönümü yok.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH /var/www/kys_koksan/iaa/resources/views/dashboard/partials/birthdays.blade.php ENDPATH**/ ?>