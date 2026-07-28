<div class="space-y-8">
    
    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-8 shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 -mr-16 -mt-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        
        <div class="relative flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-white">
                <h3 class="text-2xl font-black mb-2 flex items-center gap-3">
                    <svg class="w-8 h-8 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Sistem Veri Kalibrasyonu
                </h3>
                <div class="flex items-center gap-4 text-indigo-100 opacity-90 text-xs font-bold mb-4">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Son Kalibrasyon: <?php echo e($lastCalibrationDate ? \Carbon\Carbon::parse($lastCalibrationDate)->format('d.m.Y H:i') : 'Henüz yapılmadı'); ?>

                    </span>
                    <a href="<?php echo e(route('admin.health.logs', ['type' => 'veri'])); ?>" class="flex items-center gap-1 text-white hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Tüm Kayıtları Gör
                    </a>
                </div>
                <p class="text-indigo-100 opacity-90 max-w-xl text-sm">
                    Veritabanı kayıtları ile işlem logları arasındaki tutarsızlıkları analiz edin ve sistemi senkronize hale getirin.
                </p>
            </div>

            <div class="flex flex-wrap justify-center gap-4">
                <button wire:click="scan" wire:loading.attr="disabled" class="px-6 py-3 bg-white text-indigo-700 font-black rounded-2xl shadow-lg hover:bg-slate-50 transition-all flex items-center gap-3 disabled:opacity-50">
                    <div wire:loading wire:target="scan">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                    <svg wire:loading.remove wire:target="scan" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Taramayı Başlat
                </button>

                <!--[if BLOCK]><![endif]--><?php if(count($inconsistencies) > 0): ?>
                    <button wire:click="calibrate" wire:loading.attr="disabled" class="px-6 py-3 bg-emerald-500 text-white font-black rounded-2xl shadow-lg hover:bg-emerald-600 transition-all flex items-center gap-3 disabled:opacity-50">
                        <div wire:loading wire:target="calibrate">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <svg wire:loading.remove wire:target="calibrate" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Onayla ve Kalibre Et
                    </button>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        <!--[if BLOCK]><![endif]--><?php if(count($inconsistencies) > 0): ?>
            <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4 animate-fade-in">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20">
                    <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mb-1">Tarih Hataları</p>
                    <p class="text-2xl font-black text-white"><?php echo e($scanResults['dates']); ?> <span class="text-xs font-normal opacity-60">Kayıt</span></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20">
                    <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mb-1">Durum Uyuşmazlığı</p>
                    <p class="text-2xl font-black text-white"><?php echo e($scanResults['statuses']); ?> <span class="text-xs font-normal opacity-60">Kayıt</span></p>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20">
                    <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mb-1">Bölüm Sapmaları</p>
                    <p class="text-2xl font-black text-white"><?php echo e($scanResults['departments']); ?> <span class="text-xs font-normal opacity-60">Kayıt</span></p>
                </div>
                <div class="bg-amber-400/20 backdrop-blur-md rounded-2xl p-4 border border-amber-400/30">
                    <p class="text-[10px] font-bold text-amber-200 uppercase tracking-widest mb-1">Hatalı Puanlar</p>
                    <p class="text-2xl font-black text-white"><?php echo e($scanResults['points']); ?> <span class="text-xs font-normal opacity-60">Kayıt</span></p>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 animate-bounce-short">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-bold text-emerald-800"><?php echo e(session('message')); ?></span>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php if(session()->has('info')): ?>
        <div class="p-6 rounded-3xl bg-indigo-50 border border-indigo-100 flex flex-col items-center gap-3 text-center animate-fade-in">
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h4 class="text-lg font-black text-indigo-900">Sistem Sağlıklı!</h4>
            <p class="text-sm text-indigo-700 max-w-md"><?php echo e(session('info')); ?></p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if(count($inconsistencies) > 0): ?>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden animate-fade-in">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                <h5 class="font-black text-slate-800 uppercase tracking-widest text-sm">Tutarsızlık Listesi</h5>
                <span class="text-xs font-bold text-slate-400">Toplam <?php echo e(count($inconsistencies)); ?> uyuşmazlık bulundu.</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kayıt Bilgisi</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tür</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Eski Değer</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Yeni (Gerçek) Değer</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $inconsistencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-800"><?php echo e($item['name']); ?></span>
                                        <span class="text-[10px] text-slate-400">ID: #<?php echo e($item['id']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-600 border border-indigo-100 uppercase">
                                        <?php echo e($item['type']); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-medium text-rose-500 line-through opacity-60"><?php echo e($item['old_value']); ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-emerald-600"><?php echo e($item['new_value']); ?></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="calibrateSingle(<?php echo e($loop->index); ?>)" wire:loading.attr="disabled" class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-all flex items-center gap-1 group/btn" title="Sadece Bu Kaydı Kalibre Et">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <span class="text-[10px] font-black hidden group-hover/btn:block uppercase">Onayla</span>
                                        </button>
                                        <a href="<?php echo e($item['url']); ?>" target="_blank" class="p-2 bg-slate-50 text-slate-400 rounded-lg hover:text-indigo-600 hover:bg-indigo-50 transition-all inline-block" title="İlgili Kayda Git">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif(!$isScanning && !session()->has('info')): ?>
        <div class="text-center py-24 bg-white rounded-3xl border-2 border-dashed border-slate-100">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h6 class="text-lg font-bold text-slate-600 mb-2">Taramaya Hazır</h6>
            <p class="text-sm text-slate-400 max-w-sm mx-auto">Sistem verilerini analiz etmek ve tutarsızlıkları listelemek için yukarıdaki butona tıklayın.</p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if(count($history) > 0): ?>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h5 class="font-black text-slate-800 uppercase tracking-widest text-sm">Kalibrasyon Geçmişi</h5>
            </div>
            <div class="overflow-x-auto max-h-[400px] overflow-y-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 sticky top-0 z-10">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">Tarih</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">Yönetici</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">Tür / Model</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">Eski -> Yeni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-4 text-xs font-bold text-slate-500">
                                    <?php echo e($log->created_at->format('d.m.Y H:i')); ?>

                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center text-[10px] font-bold text-indigo-600">
                                            <?php echo e(mb_substr($log->causer->name ?? '?', 0, 1)); ?>

                                        </div>
                                        <span class="text-xs font-bold text-slate-700"><?php echo e($log->causer->name ?? 'Bilinmeyen'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black uppercase text-indigo-400"><?php echo e($log->type); ?></span>
                                        <span class="text-xs font-bold text-slate-600"><?php echo e($log->model_type); ?> #<?php echo e($log->model_id); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-[10px] font-medium">
                                        <span class="text-rose-400 line-through"><?php echo e($log->old_value); ?></span>
                                        <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        <span class="text-emerald-600 font-black"><?php echo e($log->new_value); ?></span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/data-calibration.blade.php ENDPATH**/ ?>