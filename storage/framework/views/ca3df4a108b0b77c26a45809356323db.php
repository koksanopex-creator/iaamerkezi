<?php $__env->startPush('pageTitle'); ?>
    Makine Logları | 
<?php $__env->stopPush(); ?>

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
    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- ÜST BİLGİ VE BREADCRUMB -->
            <div class="mb-8 animate-fade-in-down">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-slate-500">
                        <li><a href="<?php echo e(route('dashboard')); ?>" class="hover:text-indigo-600 transition-colors">Dashboard</a></li>
                        <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
                        <li class="font-semibold text-slate-800">Makine İşlem Geçmişi</li>
                    </ol>
                </nav>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Makine İşlem Geçmişi</h2>
                        <p class="text-slate-500 text-sm mt-1 uppercase tracking-wider font-semibold">Operasyonel Kayıtlar ve Sistem İzleme</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 bg-white rounded-xl shadow-sm border border-slate-200">
                            <span class="text-xs text-slate-400 block uppercase font-bold">Toplam Kayıt</span>
                            <span class="text-lg font-bold text-slate-700"><?php echo e($logs->total()); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LOG TABLOSU -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden animate-fade-in-up">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100">
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Tarih / Zaman</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Bölüm</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Kullanıcı</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Makine</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">İşlem</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Değişiklik Detayları</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-700"><?php echo e($log->created_at->format('d.m.Y')); ?></span>
                                            <span class="text-[11px] text-slate-400 font-medium"><?php echo e($log->created_at->format('H:i:s')); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold ring-1 ring-indigo-100">
                                            <?php echo e($log->bolum->ad ?? 'Bilinmeyen'); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs ring-2 ring-white">
                                                <?php echo e(substr($log->user->name ?? '?', 0, 1)); ?>

                                            </div>
                                            <span class="text-sm font-semibold text-slate-600"><?php echo e($log->user->name ?? 'Dış Sistem'); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                                            <span class="text-sm font-bold text-slate-700 uppercase"><?php echo e($log->machine->name ?? ($log->details['deleted_machine_name'] ?? '-')); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        <?php
                                            $actionStyles = [
                                                'Ekleme' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'ring' => 'ring-emerald-200', 'icon' => 'plus'],
                                                'Silme' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'ring' => 'ring-rose-200', 'icon' => 'trash'],
                                                'Güncelleme' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'ring' => 'ring-amber-200', 'icon' => 'refresh'],
                                            ];
                                            $style = $actionStyles[$log->action] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'ring' => 'ring-slate-200', 'icon' => 'dots-horizontal'];
                                        ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-tighter <?php echo e($style['bg']); ?> <?php echo e($style['text']); ?> ring-1 <?php echo e($style['ring']); ?> shadow-sm">
                                            <?php echo e($log->action); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="max-w-sm">
                                            <?php if(is_array($log->details)): ?>
                                                <div class="grid grid-cols-1 gap-1">
                                                    <?php $__currentLoopData = $log->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if(!is_array($value) && $key !== 'deleted_machine_name'): ?>
                                                            <div class="flex items-center gap-2 text-[11px]">
                                                                <span class="text-slate-400 font-bold uppercase tracking-tighter"><?php echo e($key); ?>:</span>
                                                                <span class="text-slate-600 font-semibold italic"><?php echo e(Str::limit($value, 40)); ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-[11px] text-slate-500 italic"><?php echo e($log->details); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <p class="text-slate-400 font-semibold italic">Henüz hiçbir işlem kaydı bulunamadı.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <?php if($logs->hasPages()): ?>
                    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
                        <?php echo e($logs->links()); ?>

                    </div>
                <?php endif; ?>
            </div>

            <p class="mt-4 text-center text-slate-400 text-[10px] font-medium uppercase tracking-[0.2em]">Sistem tarafından otomatik olarak kayıt altına alınmıştır.</p>
        </div>
    </div>

    <style>
        @keyframes fade-in-down {
            0% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down { animation: fade-in-down 0.5s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
    </style>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/machine_logs/index.blade.php ENDPATH**/ ?>