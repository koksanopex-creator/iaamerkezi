<?php
    $isAuthorizedToSeeVotingBanner = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) 
                        || (Auth::user()->hasRole('Hukuk Yöneticisi') && Auth::user()->can('disiplin.kurul.portal.gor'));
?>

<?php if($isAuthorizedToSeeVotingBanner && isset($activeVotingCases) && $activeVotingCases->isNotEmpty()): ?>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        <div class="flex items-center justify-between mb-4 px-2">
            <h3 class="text-sm font-black text-indigo-900/50 uppercase tracking-widest flex items-center gap-2">
                <span class="flex h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></span>
                Aktif Disiplin Oylamaları (<?php echo e($activeVotingCases->count()); ?>)
            </h3>
            <?php if($activeVotingCases->count() > 3): ?>
                <span class="text-[10px] font-bold text-indigo-400 animate-pulse">Tümünü görmek için sağa kaydırın &rarr;</span>
            <?php endif; ?>
        </div>
        
        
        <div class="flex overflow-x-auto pb-4 gap-4 snap-x scroll-smooth no-scrollbar">
            <?php $__currentLoopData = $activeVotingCases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $votingCase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex-shrink-0 w-full sm:w-[calc(50%-8px)] lg:w-[calc(33.333%-11px)] snap-start group relative overflow-hidden bg-white border border-indigo-100 rounded-2xl p-4 shadow-sm hover:shadow-xl hover:shadow-indigo-500/10 hover:border-indigo-300 transition-all duration-300">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="relative flex flex-col h-full">
                        
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 transform group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="text-indigo-950 font-black text-xs uppercase truncate">Disiplin Oylaması</h4>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-black bg-red-100 text-red-600 animate-pulse">AKTİF</span>
                                </div>
                                <p class="text-indigo-600 font-bold text-[11px] truncate">
                                    Dosya #<?php echo e($votingCase->id); ?> — <?php echo e($votingCase->user->name ?? 'Bilinmeyen'); ?>

                                </p>
                            </div>
                        </div>

                        
                        <div class="flex-1 mb-4">
                            <p class="text-slate-500 text-[11px] leading-relaxed line-clamp-2 italic">
                                "<?php echo e($votingCase->behavior->tanim ?? 'İçerik belirtilmedi.'); ?>"
                            </p>
                        </div>

                        
                        <a href="<?php echo e(route('admin.disiplin.show', $votingCase->id)); ?>?tab=kurul" class="w-full bg-slate-50 hover:bg-indigo-600 group-hover:bg-indigo-600 text-indigo-900 group-hover:text-white px-4 py-2.5 rounded-xl text-[11px] font-black transition-all border border-slate-100 group-hover:border-indigo-500 flex items-center justify-center gap-2 shadow-sm">
                            OYLAMA ODASINA GİT
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
<?php endif; ?>


<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/dashboard/partials/disciplinary-voting-alert.blade.php ENDPATH**/ ?>