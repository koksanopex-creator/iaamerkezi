<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        
        <div class="lg:col-span-2 space-y-6">
            
            
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 bg-gradient-to-br from-amber-50 to-white border-b border-amber-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-amber-500 rounded-2xl text-white shadow-lg shadow-amber-200">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-800 tracking-tight">Puan Kalibrasyonu</h3>
                                <div class="flex items-center gap-3 text-slate-500 text-[10px] font-bold mt-1">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Son Kalibrasyon: <?php echo e($lastCalibrationDate ? \Carbon\Carbon::parse($lastCalibrationDate)->format('d.m.Y H:i') : 'Henüz yapılmadı'); ?>

                                    </span>
                                    <span class="text-slate-200">|</span>
                                    <a href="<?php echo e(route('admin.health.logs', ['type' => 'puan'])); ?>" class="flex items-center gap-1 text-indigo-600 hover:underline">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        Günlüğü Görüntüle
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button wire:click="scan" wire:loading.attr="disabled"
                                class="px-6 py-3 bg-white border-2 border-amber-500 text-amber-600 font-black rounded-2xl hover:bg-amber-50 transition-all flex items-center gap-2 disabled:opacity-50">
                                <span wire:loading.remove wire:target="scan">Taramayı Başlat</span>
                                <span wire:loading wire:target="scan" class="flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Taranıyor...
                                </span>
                            </button>
                            
                            <!--[if BLOCK]><![endif]--><?php if(!empty($inconsistencies)): ?>
                                <button wire:click="calibrate" wire:loading.attr="disabled"
                                    onclick="confirm('Tüm uyumsuz puanlar güncellenecektir. Emin misiniz?') || event.stopImmediatePropagation()"
                                    class="px-6 py-3 bg-amber-600 text-white font-black rounded-2xl shadow-lg shadow-amber-200 hover:bg-amber-700 transition-all flex items-center gap-2">
                                    <span wire:loading.remove wire:target="calibrate">Hepsini Düzelt</span>
                                    <span wire:loading wire:target="calibrate" class="flex items-center gap-2">
                                        <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Düzeltiliyor...
                                    </span>
                                </button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>

                <div class="p-0">
                    <!--[if BLOCK]><![endif]--><?php if($scanning): ?>
                        <div class="p-20 text-center">
                            <div class="inline-block p-4 bg-amber-50 rounded-full mb-4 animate-bounce">
                                <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <h4 class="text-lg font-bold text-slate-700">Veritabanı Analiz Ediliyor...</h4>
                            <p class="text-sm text-slate-400">Bu işlem kayıt sayısına göre birkaç saniye sürebilir.</p>
                        </div>
                    <?php elseif($lastScanResults !== null): ?>
                        <!--[if BLOCK]><![endif]--><?php if(count($inconsistencies) > 0): ?>
                            <div class="p-6 bg-rose-50 border-b border-rose-100 flex items-center gap-3">
                                <div class="w-10 h-10 bg-rose-500 rounded-full flex items-center justify-center text-white shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-black text-rose-800 uppercase tracking-wider text-xs">Uyumsuzluk Tespit Edildi</h4>
                                    <p class="text-sm text-rose-700 font-medium">Toplam <b><?php echo e(count($inconsistencies)); ?></b> kayıt gerçek puanıyla uyuşmuyor.</p>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            <th class="px-6 py-4">Tür / İsim</th>
                                            <th class="px-6 py-4">Mevcut DB</th>
                                            <th class="px-6 py-4">Hesaplanan</th>
                                            <th class="px-6 py-4 text-center">Fark</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $inconsistencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex flex-col">
                                                        <span class="text-[10px] font-black uppercase <?php echo e($item['type'] === 'user' ? 'text-indigo-500' : 'text-emerald-500'); ?>">
                                                            <?php echo e($item['type'] === 'user' ? 'PERSONEL' : 'TAKIM'); ?>

                                                        </span>
                                                        <!--[if BLOCK]><![endif]--><?php if($item['type'] === 'user'): ?>
                                                            <a href="<?php echo e(route('profile.show', $item['id'])); ?>" target="_blank" class="text-sm font-bold text-slate-700 hover:text-indigo-600 hover:underline">
                                                                <?php echo e($item['name']); ?>

                                                            </a>
                                                        <?php else: ?>
                                                            <a href="<?php echo e(route('takim-puanlari', $item['id'])); ?>" target="_blank" class="text-sm font-bold text-slate-700 hover:text-indigo-600 hover:underline">
                                                                <?php echo e($item['name']); ?>

                                                            </a>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        
                                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2">
                                                            <!--[if BLOCK]><![endif]--><?php if($item['type'] === 'user'): ?>
                                                                <div class="flex items-center gap-1 bg-indigo-50 px-2 py-0.5 rounded text-[9px] font-bold text-indigo-600 border border-indigo-100" title="Tamamlanan Proje Puanları">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                    Projeler: <?php echo e($item['details']['projeler_puan']); ?> P (<?php echo e($item['details']['projeler']); ?>)
                                                                </div>
                                                                <div class="flex items-center gap-1 bg-emerald-50 px-2 py-0.5 rounded text-[9px] font-bold text-emerald-600 border border-emerald-100" title="Şikayet Giriş Puanları">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                                    Şikayet: <?php echo e($item['details']['sikayet_giris_puan']); ?> P
                                                                </div>
                                                                <div class="flex items-center gap-1 bg-amber-50 px-2 py-0.5 rounded text-[9px] font-bold text-amber-600 border border-amber-100" title="Öneri Puanları">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                                    Öneri: <?php echo e($item['details']['oneriler_puan']); ?> P
                                                                </div>
                                                                <!--[if BLOCK]><![endif]--><?php if($item['details']['cezalar_puan'] > 0): ?>
                                                                    <div class="flex items-center gap-1 bg-rose-50 px-2 py-0.5 rounded text-[9px] font-bold text-rose-600 border border-rose-100" title="Disiplin Cezaları">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                        Ceza: -<?php echo e($item['details']['cezalar_puan']); ?> P
                                                                    </div>
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                                <!--[if BLOCK]><![endif]--><?php if(!empty($item['details']['proje_listesi'])): ?>
                                                                    <div class="w-full mt-2 pt-2 border-t border-slate-50">
                                                                        <div class="flex flex-col gap-1">
                                                                            <p class="text-[8px] font-black uppercase text-slate-400">Son Projeler:</p>
                                                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $item['details']['proje_listesi']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pId => $pName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <a href="<?php echo e(route('proje.workspace.show', $pId)); ?>" target="_blank" class="flex items-center gap-2 group/item">
                                                                                    <span class="text-[9px] font-black text-slate-300">#<?php echo e($pId); ?></span>
                                                                                    <span class="text-[9px] font-bold text-slate-500 truncate max-w-[250px] group-hover/item:text-indigo-600 transition-colors hover:underline"><?php echo e($pName); ?></span>
                                                                                </a>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                                <!--[if BLOCK]><![endif]--><?php if(!empty($item['details']['sikayet_listesi'])): ?>
                                                                    <div class="w-full mt-2 pt-2 border-t border-slate-50">
                                                                        <div class="flex flex-col gap-1">
                                                                            <p class="text-[8px] font-black uppercase text-slate-400">Son Şikayetler:</p>
                                                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $item['details']['sikayet_listesi']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sId => $sName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <a href="<?php echo e(route('iaa.sikayetler.show', $sId)); ?>" target="_blank" class="flex items-center gap-2 group/item">
                                                                                    <span class="text-[9px] font-black text-slate-300">#<?php echo e($sId); ?></span>
                                                                                    <span class="text-[9px] font-bold text-slate-500 truncate max-w-[250px] group-hover/item:text-emerald-600 transition-colors hover:underline"><?php echo e($sName); ?></span>
                                                                                </a>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                                <!--[if BLOCK]><![endif]--><?php if(!empty($item['details']['oneri_listesi'])): ?>
                                                                    <div class="w-full mt-2 pt-2 border-t border-slate-50">
                                                                        <div class="flex flex-col gap-1">
                                                                            <p class="text-[8px] font-black uppercase text-slate-400">Son Öneriler:</p>
                                                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $item['details']['oneri_listesi']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oId => $oName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <a href="<?php echo e(route('proje.workspace.show', $oId)); ?>" target="_blank" class="flex items-center gap-2 group/item">
                                                                                    <span class="text-[9px] font-black text-slate-300">#<?php echo e($oId); ?></span>
                                                                                    <span class="text-[9px] font-bold text-slate-500 truncate max-w-[250px] group-hover/item:text-amber-600 transition-colors hover:underline"><?php echo e($oName); ?></span>
                                                                                </a>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                            <?php else: ?>
                                                                <span class="text-[9px] text-slate-400 font-bold">Projeler: <?php echo e($item['details']['projeler']); ?></span>
                                                                <span class="text-slate-200">|</span>
                                                                <span class="text-[9px] text-slate-400 font-bold">Şikayetler: <?php echo e($item['details']['sikayetler']); ?></span>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <!--[if BLOCK]><![endif]--><?php if($item['type'] === 'user'): ?>
                                                        <a href="<?php echo e(route('profile.puanlar', $item['id'])); ?>" target="_blank" class="text-sm font-bold text-slate-400 hover:text-indigo-600 hover:underline">
                                                            <?php echo e($item['current']); ?> P
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?php echo e(route('takim-puanlari', $item['id'])); ?>" target="_blank" class="text-sm font-bold text-slate-400 hover:text-indigo-600 hover:underline">
                                                            <?php echo e($item['current']); ?> P
                                                        </a>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="text-sm font-black text-slate-800"><?php echo e($item['calculated']); ?> P</span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="px-2 py-1 rounded-lg text-[10px] font-black <?php echo e($item['diff'] > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>">
                                                        <?php echo e($item['diff'] > 0 ? '+' : ''); ?><?php echo e($item['diff']); ?>

                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="p-20 text-center">
                                <div class="inline-block p-4 bg-emerald-50 rounded-full mb-4">
                                    <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <h4 class="text-lg font-bold text-slate-700">Harika! Veriler Tertemiz.</h4>
                                <p class="text-sm text-slate-400">Tüm puanlar veritabanı kayıtlarıyla birebir örtüşüyor.</p>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php else: ?>
                        <div class="p-20 text-center">
                            <div class="inline-block p-4 bg-slate-50 rounded-full mb-4">
                                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h4 class="text-lg font-bold text-slate-600">Henüz Tarama Yapılmadı</h4>
                            <p class="text-sm text-slate-400">Verileri doğrulamak için "Taramayı Başlat" butonuna tıklayın.</p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>

        
        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                    <h5 class="font-black text-slate-800 uppercase tracking-widest text-xs">Kalibrasyon Geçmişi</h5>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="divide-y divide-slate-50 max-h-[600px] overflow-y-auto custom-scrollbar">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $recentLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-black text-indigo-500 uppercase"><?php echo e($log->model_type); ?> #<?php echo e($log->model_id); ?></span>
                                <span class="text-[9px] font-bold text-slate-400"><?php echo e($log->created_at->diffForHumans()); ?></span>
                            </div>
                            <p class="text-xs font-bold text-slate-700 mb-2"><?php echo e($log->description); ?></p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded font-bold line-through"><?php echo e($log->old_value); ?> P</span>
                                    <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                    <span class="text-[10px] px-1.5 py-0.5 bg-emerald-100 text-emerald-700 rounded font-black"><?php echo e($log->new_value); ?> P</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <div class="w-5 h-5 bg-slate-100 rounded-full flex items-center justify-center text-[8px] font-black text-slate-500" title="<?php echo e($log->causer->name ?? 'Sistem'); ?>">
                                        <?php echo e(mb_substr($log->causer->name ?? 'S', 0, 1)); ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-10 text-center text-slate-400 italic text-xs">
                            Henüz işlem yapılmamış.
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>

            
            <div class="bg-indigo-900 rounded-3xl p-6 text-white relative overflow-hidden shadow-xl shadow-indigo-100">
                <div class="absolute right-0 top-0 -mr-8 -mt-8 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <h6 class="text-sm font-black uppercase tracking-widest mb-3">Neden Kalibrasyon?</h6>
                <p class="text-xs text-indigo-200 leading-relaxed font-medium">
                    Puanlar, projeler tamamlandığında veya şikayetler çözüldüğünde otomatik hesaplanır. 
                    Ancak veritabanında manuel bir değişiklik (silme/güncelleme) yapıldığında, kullanıcıların toplam puanları bu değişikliklerden haberdar olmayabilir.
                    <br><br>
                    Bu panel, verileri baştan hesaplayarak veritabanı toplamlarını gerçek kayıtlarla eşitler.
                </p>
            </div>
        </div>

    </div>
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/score-calibration.blade.php ENDPATH**/ ?>