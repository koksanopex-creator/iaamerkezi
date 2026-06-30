<div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-2">
        <div class="px-6 py-4 bg-indigo-50/50 border-b border-indigo-100/50 flex flex-col lg:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-600 rounded-xl shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-gray-800 text-base font-bold leading-tight">İAA LİDERLİK TABLOSU</h3>
                    <p class="text-indigo-600 text-[10px] uppercase tracking-[0.1em] font-bold opacity-80">En Yüksek Puanlı 5 Personel</p>
                </div>
            </div>

            
            <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-2 bg-white p-1.5 rounded-xl border border-indigo-100 shadow-sm">
                <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" 
                    class="bg-transparent border-none text-[10px] text-gray-700 focus:ring-0 p-0 w-24 placeholder-gray-400">
                <span class="text-gray-300 text-xs">-</span>
                <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" 
                    class="bg-transparent border-none text-[10px] text-gray-700 focus:ring-0 p-0 w-24 placeholder-gray-400">
                <button type="submit" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 p-1 rounded-lg transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                <div class="h-4 w-px bg-gray-100 mx-1"></div>
                <?php
                    $thisWeekStart = \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d');
                    $thisMonthStart = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                ?>
                <a href="<?php echo e(route('dashboard', ['start_date' => $thisWeekStart])); ?>" class="text-[9px] px-2 py-1 rounded-lg <?php echo e(request('start_date') == $thisWeekStart ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-600'); ?> font-bold transition-all">HAFTA</a>
                <a href="<?php echo e(route('dashboard', ['start_date' => $thisMonthStart])); ?>" class="text-[9px] px-2 py-1 rounded-lg <?php echo e(request('start_date') == $thisMonthStart ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-600'); ?> font-bold transition-all">AY</a>
            </form>

            <a href="<?php echo e(route('puan-durumu')); ?>" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-xl text-white text-xs font-bold transition-all shadow-md hover:shadow-lg">
                TÜM LİSTEYİ GÖR
            </a>
        </div>


        
        <div class="overflow-x-auto">
            <?php if(isset($isSidebar) && $isSidebar): ?>
                
                <div class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $topPerformers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $performer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $rank = $index + 1;
                            $badge = match($rank) {
                                1 => '🥇',
                                2 => '🥈',
                                3 => '🥉',
                                default => '#' . $rank
                            };
                            $rankColor = match($rank) {
                                1 => 'text-amber-500',
                                2 => 'text-slate-400',
                                3 => 'text-orange-400',
                                default => 'text-gray-400'
                            };
                        ?>
                        <div class="px-4 py-3 hover:bg-indigo-50/30 transition-colors flex items-center justify-between gap-2">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-sm font-bold <?php echo e($rankColor); ?> w-6"><?php echo e($badge); ?></span>
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border border-white shadow-sm overflow-hidden flex-shrink-0">
                                    <?php if($performer->profile_photo_path): ?>
                                        <img src="<?php echo e(asset('storage/' . $performer->profile_photo_path)); ?>" alt="<?php echo e($performer->name); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <span class="text-[10px]"><?php echo e(strtoupper(substr($performer->name, 0, 1))); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-800 truncate"><?php echo e($performer->name); ?></p>
                                    <p class="text-[9px] text-gray-400 font-medium uppercase truncate"><?php echo e($performer->bolum->ad ?? 'Bölüm Yok'); ?></p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs font-black text-indigo-700"><?php echo e(number_format($performer->period_puan ?? $performer->toplam_puan, 0)); ?></p>
                                <p class="text-[8px] font-bold text-indigo-300 uppercase tracking-tighter">PUAN</p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-6 py-8 text-center">
                            <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">Veri yok</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Sıralama</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Personel</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Departman & Rol</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 text-right">Toplam Puan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php $__empty_1 = true; $__currentLoopData = $topPerformers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $performer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $rank = $index + 1;
                                $badge = match($rank) {
                                    1 => '🥇',
                                    2 => '🥈',
                                    3 => '🥉',
                                    default => '#' . $rank
                                };
                                $rankColor = match($rank) {
                                    1 => 'text-amber-500',
                                    2 => 'text-slate-400',
                                    3 => 'text-orange-400',
                                    default => 'text-gray-400'
                                };
                            ?>
                            <tr class="hover:bg-indigo-50/30 transition-colors group/row">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-xl font-bold <?php echo e($rankColor); ?>"><?php echo e($badge); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border-2 border-white shadow-sm overflow-hidden">
                                            <?php if($performer->profile_photo_path): ?>
                                                <img src="<?php echo e(asset('storage/' . $performer->profile_photo_path)); ?>" alt="<?php echo e($performer->name); ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <?php echo e(strtoupper(substr($performer->name, 0, 1))); ?>

                                            <?php endif; ?>
                                        </div>
                                        <a href="<?php echo e(route('profile.show', $performer->id)); ?>" class="text-sm font-bold text-gray-800 hover:text-indigo-600 transition-colors">
                                            <?php echo e($performer->name); ?>

                                            <?php if($performer->trashed()): ?>
                                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black bg-red-100 text-red-600 border border-red-200 uppercase tracking-tighter">
                                                    PASİF
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-semibold text-gray-600"><?php echo e($performer->bolum->ad ?? 'Bölüm Yok'); ?></span>
                                        <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider"><?php echo e($performer->getRoleNames()->first() ?? 'Personel'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="<?php echo e(route('profile.puanlar', ['user' => $performer->id, 'start_date' => request('start_date'), 'end_date' => request('end_date')])); ?>" class="inline-flex flex-col items-end group/points">
                                        <span class="text-lg font-black text-indigo-700 group-hover/row:scale-110 transition-transform origin-right"><?php echo e(number_format($performer->period_puan ?? $performer->toplam_puan, 0)); ?></span>
                                        <span class="text-[9px] font-bold text-indigo-300 uppercase tracking-tighter">Puan Detayı</span>
                                    </a>
                                </td>

                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">Henüz puanlama verisi bulunmuyor</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/dashboard/partials/top5-performers.blade.php ENDPATH**/ ?>