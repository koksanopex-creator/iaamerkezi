<?php $isEmbedded = $embedded ?? false; $isSidebar = $sidebar ?? false; ?>

<div class="<?php echo e($isEmbedded ? '' : 'pt-10'); ?>" 
     x-data="{ 
        showTooltip: null, 
        activeUserLogs: [], 
        tooltipX: 0, 
        tooltipY: 0,
        updateTooltip(logs, type, id, event) {
            this.activeUserLogs = logs;
            this.showTooltip = type + '-' + id;
            let rect = event.currentTarget.getBoundingClientRect();
            this.tooltipX = (type === 'online') ? (rect.right + 10) : (rect.left - 270);
            this.tooltipY = rect.top;
        }
     }"
     @mousemove.window="if (showTooltip && !$event.target.closest('.tooltip-trigger')) showTooltip = null"
     @scroll.window="showTooltip = null"
     @click.window="showTooltip = null">
    <div class="<?php echo e($isEmbedded ? '' : 'bg-white rounded-3xl border border-indigo-100 shadow-[0_10px_40px_rgba(79,70,229,0.05)]'); ?> relative">
        
        
        <div class="flex <?php echo e($isSidebar ? 'flex-col' : 'flex-col sm:flex-row sm:items-center'); ?> justify-between gap-4 <?php echo e($isSidebar ? 'p-3' : 'px-6 py-4'); ?> border-b border-gray-100 <?php echo e($isEmbedded ? 'bg-gray-50/50' : 'bg-gradient-to-r from-green-50/50 to-emerald-50/50'); ?>">
            <div class="flex items-center gap-3">
                <div class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </div>
                <h3 class="font-black text-gray-900 tracking-tight flex items-center gap-2 text-sm lg:text-base uppercase">
                    Online Kullanıcılar & Son Aktiviteler
                </h3>
            </div>
            
            <div class="flex items-center <?php echo e($isSidebar ? 'justify-between w-full' : 'gap-2'); ?>">
                <a href="<?php echo e(route('logs.login.index')); ?>"
                   class="<?php echo e($isSidebar ? 'text-[8px] px-1.5 py-1' : 'text-[10px] px-3 py-1.5'); ?> font-black text-emerald-600 hover:text-emerald-800 uppercase tracking-widest bg-white/80 backdrop-blur-sm rounded-xl border border-green-100 shadow-sm transition-all hover:shadow-md">
                    &larr; <?php echo e($isSidebar ? 'Kayıtlar' : 'Tüm Giriş Kayıtları'); ?>

                </a>
                <span class="<?php echo e($isSidebar ? 'text-[8px] px-1.5 py-1' : 'text-xs px-4 py-1.5'); ?> font-black text-emerald-700 bg-white rounded-full border border-green-200 shadow-sm">
                    <?php echo e($onlineKullanicilar->count()); ?> Aktif
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            
            
            <div class="flex flex-col">
                <div class="bg-gray-50/50 px-4 py-2 border-b border-gray-100">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Şu An Online Olanlar</span>
                </div>
                <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                    <table class="w-full text-sm text-left">
                        <tbody class="divide-y divide-gray-50">
                            <?php $__empty_1 = true; $__currentLoopData = $onlineKullanicilar->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="bg-white hover:bg-indigo-50/30 transition-colors group relative">
                                    <td class="px-4 py-3 font-medium text-gray-900 flex items-center gap-3 cursor-default">
                                        <div class="flex items-center gap-3 tooltip-trigger"
                                            @mouseenter="updateTooltip(<?php echo e(json_encode($user->loginActivities->take(10)->map(fn($l) => ['date' => $l->created_at->format('d.m H:i'), 'ip' => $l->ip_address])->toArray())); ?>, 'online', <?php echo e($user->id); ?>, $event)">
                                        
                                        <div class="relative flex-shrink-0">
                                            <?php if($user->profile_photo_path): ?>
                                                <img src="<?php echo e(asset('storage/' . $user->profile_photo_path)); ?>"
                                                     class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-gray-100">
                                            <?php else: ?>
                                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center text-xs font-black border-2 border-white shadow-sm">
                                                    <?php echo e(substr($user->name, 0, 1)); ?>

                                                </div>
                                            <?php endif; ?>
                                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full shadow-sm"></span>
                                        </div>
                                        
                                        <div class="flex flex-col min-w-0 relative">
                                            <a href="<?php echo e(route('profile.show', $user->id)); ?>" class="text-sm font-bold text-gray-800 hover:text-indigo-600 transition-colors truncate">
                                                <?php echo e($user->name); ?>

                                            </a>
                                            <span class="text-[10px] text-gray-400 font-bold uppercase truncate">
                                                <?php echo e(!$user->is_personnel ? 'Müşteri Temsilcisi' : ($user->unvan ?? ($user->getRoleNames()->first() ?? 'Personel'))); ?>

                                            </span>

                                            
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-[11px] text-gray-500 font-bold bg-gray-50/30">
                                        <?php echo e(!$user->is_personnel ? ($user->firma_adlari ?: '-') : ($user->bolum->ad ?? '-')); ?>

                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black bg-green-100 text-green-700 border border-green-200">
                                            AKTİF
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="px-4 py-10 text-center text-gray-400 italic text-xs">Aktif kullanıcı bulunmuyor.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div class="flex flex-col">
                <div class="bg-gray-50/50 px-4 py-2 border-b border-gray-100">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Son Görülme (Geçmiş)</span>
                </div>
                <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                    <table class="w-full text-sm text-left">
                        <tbody class="divide-y divide-gray-50">
                            <?php $__currentLoopData = $sonAktifKullanicilar->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="bg-white hover:bg-indigo-50/30 transition-colors group relative">
                                    <td class="px-4 py-3 font-medium text-gray-900 flex items-center gap-3 cursor-default">
                                        <div class="flex items-center gap-3 tooltip-trigger"
                                            @mouseenter="updateTooltip(<?php echo e(json_encode($user->loginActivities->take(10)->map(fn($l) => ['date' => $l->created_at->format('d.m H:i'), 'ip' => $l->ip_address])->toArray())); ?>, 'last', <?php echo e($user->id); ?>, $event)">
                                        
                                        <div class="relative flex-shrink-0 grayscale group-hover:grayscale-0 transition-all duration-300">
                                            <?php if($user->profile_photo_path): ?>
                                                <img src="<?php echo e(asset('storage/' . $user->profile_photo_path)); ?>"
                                                     class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-gray-100">
                                            <?php else: ?>
                                                <div class="w-9 h-9 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center text-xs font-black border-2 border-white shadow-sm">
                                                    <?php echo e(substr($user->name, 0, 1)); ?>

                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="flex flex-col min-w-0 relative">
                                            <a href="<?php echo e(route('profile.show', $user->id)); ?>" class="text-sm font-bold text-gray-700 group-hover:text-indigo-600 transition-colors truncate">
                                                <?php echo e($user->name); ?>

                                            </a>
                                            <span class="text-[10px] text-gray-400 font-bold uppercase truncate">
                                                <?php echo e(!$user->is_personnel ? ($user->firma_adlari ?: 'Müşteri Temsilcisi') : ($user->bolum->ad ?? '-')); ?>

                                            </span>

                                            
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-[11px] font-mono font-bold text-gray-400">
                                            <?php echo e($user->last_seen_at ? $user->last_seen_at->diffForHumans() : '-'); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    
    <div x-show="showTooltip"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed z-[9999] w-64 bg-white rounded-2xl shadow-2xl border border-indigo-100 p-4 pointer-events-none"
         :style="`left: ${tooltipX}px; top: ${tooltipY}px;`"
         x-cloak>
        <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pb-2 border-b border-gray-50">Son Giriş Hareketleri</h5>
        <ul class="space-y-2">
            <template x-for="log in activeUserLogs" :key="log.date">
                <li class="flex justify-between items-center text-[10px]">
                    <span class="font-bold text-gray-700" x-text="log.date"></span>
                    <span class="text-gray-400 font-mono" x-text="log.ip"></span>
                </li>
            </template>
            <template x-if="activeUserLogs.length === 0">
                <li class="text-[10px] text-gray-400 italic text-center py-2">Kayıt bulunamadı.</li>
            </template>
        </ul>
    </div>
</div>


<?php /**PATH /var/www/kys_koksan/iaa/resources/views/dashboard/partials/_users-activity.blade.php ENDPATH**/ ?>