


<?php
    $gosterilecekUyariVarMi = false;
    if(isset($stats['son_iaa_projelerim']) && count($stats['son_iaa_projelerim']) > 0) {
        foreach($stats['son_iaa_projelerim'] as $proje) {
            if($proje->talep_admin_notu || $proje->talep_kalite_notu || $proje->durum == 'talep_onayi_bekliyor_kalite') {
                $gosterilecekUyariVarMi = true;
                break;
            }
        }
    }
?>

<?php if($gosterilecekUyariVarMi): ?>
    
    <div class="space-y-4 mb-8">
        <?php $__currentLoopData = $stats['son_iaa_projelerim']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            
            
            <?php if($proje->talep_admin_notu || $proje->talep_kalite_notu || $proje->durum == 'talep_onayi_bekliyor_kalite'): ?>
                
                <div class="rounded-xl border-l-4 shadow-md bg-white p-4 animate-fade-in-down relative overflow-hidden
                    <?php echo e($proje->talep_admin_notu ? 'border-red-500' : ($proje->talep_kalite_notu ? 'border-orange-500' : 'border-purple-500')); ?>">
                    
                    
                    <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 rounded-full opacity-10
                        <?php echo e($proje->talep_admin_notu ? 'bg-red-500' : ($proje->talep_kalite_notu ? 'bg-orange-500' : 'bg-purple-500')); ?>">
                    </div>

                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <h4 class="font-bold text-gray-800 flex flex-wrap items-center gap-2">
                                <?php if($proje->talep_admin_notu): ?>
                                    <span class="text-white bg-red-600 px-2 py-0.5 rounded text-[10px] font-black tracking-wider shadow-sm flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        REDDEDİLDİ
                                    </span>
                                <?php elseif($proje->talep_kalite_notu): ?>
                                    <span class="text-white bg-orange-500 px-2 py-0.5 rounded text-[10px] font-black tracking-wider shadow-sm">DÜZELTME/NOT</span>
                                <?php else: ?>
                                    <span class="text-white bg-purple-500 px-2 py-0.5 rounded text-[10px] font-black tracking-wider shadow-sm animate-pulse">ONAY BEKLİYOR</span>
                                <?php endif; ?>
                                
                                <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="hover:text-indigo-600 hover:underline transition-colors text-lg">
                                    <?php echo e($proje->baslik); ?>

                                </a>
                            </h4>
                            <p class="text-xs text-gray-500 mt-1">Bu proje ile ilgili süreç notları bulunmaktadır.</p>
                        </div>
                        <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="flex-shrink-0 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm">
                            Projeye Git &rarr;
                        </a>
                    </div>

                    
                    <div class="mt-3 space-y-2 relative z-10">
                        
                        
                        <?php if($proje->talep_admin_notu): ?>
                            <div class="text-sm bg-red-50 p-3 rounded-lg border border-red-100 text-red-900 shadow-sm">
                                <span class="font-bold block text-[10px] uppercase text-red-600 mb-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    Yönetim Red Gerekçesi:
                                </span>
                                <?php echo e($proje->talep_admin_notu); ?>

                            </div>
                        <?php endif; ?>

                        
                        <?php if($proje->talep_kalite_notu): ?>
                            <div class="text-sm bg-orange-50 p-3 rounded-lg border border-orange-100 text-orange-900 shadow-sm">
                                <span class="font-bold block text-[10px] uppercase text-orange-600 mb-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                    Kalite Yöneticisi Notu:
                                </span>
                                <?php echo e($proje->talep_kalite_notu); ?>

                            </div>
                        <?php endif; ?>
                        
                        
                        <?php if($proje->talep_gerekcesi && ($proje->talep_admin_notu || $proje->talep_kalite_notu)): ?>
                             <div class="text-xs text-gray-500 mt-2 pl-2 border-l-2 border-gray-300">
                                <span class="font-semibold">Sizin Talebiniz:</span> "<?php echo e(\Illuminate\Support\Str::limit($proje->talep_gerekcesi, 100)); ?>"
                             </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/dashboard/partials/_alerts.blade.php ENDPATH**/ ?>