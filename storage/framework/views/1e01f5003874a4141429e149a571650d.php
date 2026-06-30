<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('pageTitle'); ?> Mail Bildirim Logları | <?php $__env->stopPush(); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Mail Bildirim Logları
                </h1>
                <p class="text-sm text-slate-500 mt-1">Gönderilemeyen mail bildirimlerinin takibi ve yeniden gönderim</p>
            </div>
        </div>

        
        <?php $__currentLoopData = ['success' => 'emerald', 'error' => 'rose', 'info' => 'indigo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(session($type)): ?>
                <div class="mb-4 p-3 rounded-lg bg-<?php echo e($color); ?>-50 border border-<?php echo e($color); ?>-200 text-<?php echo e($color); ?>-700 text-sm font-medium flex items-center gap-2">
                    <?php if($type === 'success'): ?>
                        <svg class="w-5 h-5 text-<?php echo e($color); ?>-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <?php elseif($type === 'error'): ?>
                        <svg class="w-5 h-5 text-<?php echo e($color); ?>-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <?php else: ?>
                        <svg class="w-5 h-5 text-<?php echo e($color); ?>-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <?php endif; ?>
                    <?php echo e(session($type)); ?>

                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Toplam Log</p>
                        <p class="text-2xl font-black text-slate-800 mt-1"><?php echo e($stats['toplam']); ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-rose-200 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-rose-400 uppercase tracking-wider">Çözülmedi</p>
                        <p class="text-2xl font-black text-rose-600 mt-1"><?php echo e($stats['cozulmedi']); ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-emerald-200 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Çözüldü</p>
                        <p class="text-2xl font-black text-emerald-600 mt-1"><?php echo e($stats['cozuldu']); ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6 shadow-sm">
            <form method="GET" action="<?php echo e(route('admin.mail-logs.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Başlangıç Tarihi</label>
                    <input type="date" name="tarih_baslangic" value="<?php echo e(request('tarih_baslangic')); ?>"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Bitiş Tarihi</label>
                    <input type="date" name="tarih_bitis" value="<?php echo e(request('tarih_bitis')); ?>"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Durum</label>
                    <select name="durum" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tümü</option>
                        <option value="cozulmedi" <?php echo e(request('durum') == 'cozulmedi' ? 'selected' : ''); ?>>Çözülmedi</option>
                        <option value="cozuldu" <?php echo e(request('durum') == 'cozuldu' ? 'selected' : ''); ?>>Çözüldü</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Arama</label>
                    <input type="text" name="arama" value="<?php echo e(request('arama')); ?>" placeholder="İşlem veya hata ara..."
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-colors">
                        Filtrele
                    </button>
                    <a href="<?php echo e(route('admin.mail-logs.index')); ?>" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg hover:bg-slate-200 transition-colors" title="Filtreleri Temizle">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </a>
                </div>
            </form>
        </div>

        
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <?php if($logs->isEmpty()): ?>
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-emerald-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-bold text-slate-700">Hata Kaydı Yok</h3>
                    <p class="text-sm text-slate-500 mt-1">Tüm mail bildirimleri başarıyla gönderilmiş görünüyor.</p>
                </div>
            <?php else: ?>
                
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Tarih</th>
                                <th class="px-4 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-wider">İşlem</th>
                                <th class="px-4 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Alıcılar</th>
                                <th class="px-4 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Hata</th>
                                <th class="px-4 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Bölüm</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-slate-500 uppercase tracking-wider">Durum</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-slate-500 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-slate-50/50 transition-colors <?php echo e($log->isResolved() ? 'opacity-60' : ''); ?>">
                                    
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-xs font-bold text-slate-700"><?php echo e($log->created_at->format('d.m.Y')); ?></div>
                                        <div class="text-[10px] text-slate-400"><?php echo e($log->created_at->format('H:i')); ?></div>
                                    </td>

                                    
                                    <td class="px-4 py-3">
                                        <div class="text-xs font-bold text-slate-800 max-w-[200px] truncate" title="<?php echo e($log->source_action); ?>"><?php echo e($log->source_action); ?></div>
                                        <?php if($log->source_page): ?>
                                            <div class="text-[10px] text-slate-400 max-w-[200px] truncate" title="<?php echo e($log->source_page); ?>"><?php echo e(parse_url($log->source_page, PHP_URL_PATH)); ?></div>
                                        <?php endif; ?>
                                    </td>

                                    
                                    <td class="px-4 py-3">
                                        <div class="text-xs text-slate-600 max-w-[180px]">
                                            <?php if($log->recipients && is_array($log->recipients)): ?>
                                                <?php $__currentLoopData = array_slice($log->recipients, 0, 2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="truncate" title="<?php echo e($recipient); ?>"><?php echo e($recipient); ?></div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(count($log->recipients) > 2): ?>
                                                    <div class="text-indigo-500 font-bold text-[10px]">+<?php echo e(count($log->recipients) - 2); ?> kişi daha</div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-slate-400 italic">Bilinmiyor</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    
                                    <td class="px-4 py-3">
                                        <div class="text-xs text-rose-600 max-w-[220px] truncate cursor-help" title="<?php echo e($log->error_message); ?>">
                                            <?php echo e(Str::limit($log->error_message, 80)); ?>

                                        </div>
                                    </td>

                                    
                                    <td class="px-4 py-3">
                                        <span class="text-xs text-slate-500"><?php echo e($log->bolum->ad ?? '—'); ?></span>
                                    </td>

                                    
                                    <td class="px-4 py-3 text-center">
                                        <?php if($log->isResolved()): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700" title="Çözüldü: <?php echo e($log->resolved_at->format('d.m.Y H:i')); ?><?php echo e($log->resolver ? ' — ' . $log->resolver->name : ''); ?>">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                ÇÖZÜLDÜ
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-600 animate-pulse">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                                BAŞARISIZ
                                            </span>
                                            <?php if($log->retry_count > 0): ?>
                                                <div class="text-[9px] text-slate-400 mt-0.5"><?php echo e($log->retry_count); ?>x denenmiş</div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>

                                    
                                    <td class="px-4 py-3 text-center">
                                        <?php if(!$log->isResolved()): ?>
                                            <div class="flex flex-col gap-1 items-center">
                                                <?php if($log->notification_class && $log->notification_data): ?>
                                                    <form method="POST" action="<?php echo e(route('admin.mail-logs.retry', $log->id)); ?>" class="inline" onsubmit="return confirm('Bu bildirimi tekrar göndermeyi denemek istediğinize emin misiniz?')">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-indigo-600 text-white hover:bg-indigo-700 transition-colors shadow-sm">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                            Tekrar Dene
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <form method="POST" action="<?php echo e(route('admin.mail-logs.mark-resolved', $log->id)); ?>" class="inline" onsubmit="return confirm('Bu kaydı çözüldü olarak işaretlemek istediğinize emin misiniz?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-600 hover:bg-emerald-100 hover:text-emerald-700 transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Çözüldü İşaretle
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-[10px] text-slate-400">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                
                <div class="lg:hidden divide-y divide-slate-100">
                    <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-4 <?php echo e($log->isResolved() ? 'opacity-60' : ''); ?>">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div>
                                    <div class="text-xs font-black text-slate-800"><?php echo e($log->source_action); ?></div>
                                    <div class="text-[10px] text-slate-400 mt-0.5"><?php echo e($log->created_at->format('d.m.Y H:i')); ?></div>
                                </div>
                                <?php if($log->isResolved()): ?>
                                    <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-700">✓ ÇÖZÜLDÜ</span>
                                <?php else: ?>
                                    <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-100 text-rose-600 animate-pulse">✕ BAŞARISIZ</span>
                                <?php endif; ?>
                            </div>

                            <div class="text-[11px] text-rose-600 mb-2 line-clamp-2"><?php echo e($log->error_message); ?></div>

                            <?php if($log->recipients && is_array($log->recipients)): ?>
                                <div class="text-[10px] text-slate-500 mb-2">
                                    <span class="font-bold">Alıcılar:</span> <?php echo e(implode(', ', array_slice($log->recipients, 0, 3))); ?>

                                    <?php if(count($log->recipients) > 3): ?> <span class="text-indigo-500 font-bold">+<?php echo e(count($log->recipients) - 3); ?></span> <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if(!$log->isResolved()): ?>
                                <div class="flex gap-2 mt-2">
                                    <?php if($log->notification_class && $log->notification_data): ?>
                                        <form method="POST" action="<?php echo e(route('admin.mail-logs.retry', $log->id)); ?>" class="inline" onsubmit="return confirm('Tekrar göndermek istediğinize emin misiniz?')">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                                                🔄 Tekrar Dene
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo e(route('admin.mail-logs.mark-resolved', $log->id)); ?>" class="inline" onsubmit="return confirm('Çözüldü olarak işaretlemek istediğinize emin misiniz?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-600 hover:bg-emerald-100 transition-colors">
                                            ✓ Çözüldü
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                    <?php echo e($logs->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/mail-logs/index.blade.php ENDPATH**/ ?>