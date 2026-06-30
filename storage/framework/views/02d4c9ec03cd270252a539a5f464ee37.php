<?php
    // Yetki kontrolü: Şikayet sekmesini kimler görebilir?
    $sikayetGormeYetkisi = $user->hasRole([
        'Superadmin',
        'Müşteri Şikayeti Kurulu',
        'Müşteri Şikayeti Çözüm Lideri',
        'Bölüm Kalite Yöneticisi'
    ]);
?>

<?php $__env->startPush('pageTitle'); ?>
    <?php echo e($user->name); ?> | 
<?php $__env->stopPush(); ?>

<?php
    $isSuperadmin = auth()->check() && auth()->user()->hasRole('Superadmin');
    $isTrashed = method_exists($user, 'trashed') && $user->trashed();
?>

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
    <?php if($isTrashed && !$isSuperadmin): ?>
        
        <div class="max-w-4xl mx-auto py-16 px-4">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                
                <div class="bg-gradient-to-r from-red-500 to-rose-600 pt-12 pb-24 px-8 relative overflow-hidden text-center">
                    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    <svg class="w-16 h-16 text-white/80 mx-auto relative z-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <h2 class="text-3xl font-black text-white tracking-tight relative z-10">Personel Sistemden Ayrılmıştır</h2>
                    <?php if($user->termination_date): ?>
                        <p class="text-white/80 text-sm font-semibold mt-2 relative z-10">Ayrılma Tarihi: <?php echo e(\Carbon\Carbon::parse($user->termination_date)->format('d.m.Y')); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="px-8 sm:px-12 pb-12 bg-white relative">
                    
                    
                    <div class="-mt-16 flex flex-col items-center mb-8 relative z-20">
                        <div class="w-32 h-32 bg-white rounded-full p-2 shadow-xl border border-gray-100">
                            <?php if($user->profile_photo_path): ?>
                                <img src="<?php echo e(asset('storage/' . $user->profile_photo_path)); ?>" alt="<?php echo e($user->name); ?>" class="w-full h-full rounded-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-4xl font-bold text-gray-500 uppercase">
                                    <?php echo e(substr($user->name, 0, 1)); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                        <h3 class="mt-4 text-3xl font-bold text-gray-900"><?php echo e($user->name); ?></h3>
                        <p class="text-gray-500 font-medium uppercase tracking-widest text-sm mt-1"><?php echo e($user->unvan ?? 'Eski Personel'); ?></p>
                    </div>

                    <p class="text-center text-lg text-gray-600 mb-10 leading-relaxed max-w-2xl mx-auto">
                        Aradığınız personelin sistemimizle ilişiği kesilmiş ve hesabı <span class="font-bold text-red-600">pasif</span> duruma getirilmiştir.
                    </p>

                    
                    <?php if($user->is_personnel): ?>
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 mb-10">
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-6 text-center">Önceki Organizasyon Bilgileri</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            <div class="flex flex-col items-center text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-10 h-10 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Bağlı Olduğu Bölüm</span>
                                <span class="text-sm font-bold text-gray-800 mt-1"><?php echo e($user->bolum->ad ?? 'Belirtilmedi'); ?></span>
                            </div>

                            
                            <div class="flex flex-col items-center text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-10 h-10 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Bölüm Lideri</span>
                                <?php if(isset($bolumManager) && $bolumManager): ?>
                                    <a href="<?php echo e(route('profile.show', $bolumManager->id)); ?>" class="mt-1 flex items-center gap-2 group">
                                        <?php if($bolumManager->profile_photo_path): ?>
                                            <img src="<?php echo e(asset('storage/' . $bolumManager->profile_photo_path)); ?>" class="w-6 h-6 rounded-full object-cover">
                                        <?php else: ?>
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-500"><?php echo e(substr($bolumManager->name, 0, 1)); ?></div>
                                        <?php endif; ?>
                                        <span class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors"><?php echo e($bolumManager->name); ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-sm font-bold text-gray-800 mt-1">-</span>
                                <?php endif; ?>
                            </div>

                            
                            <div class="flex flex-col items-center text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-10 h-10 bg-purple-50 text-purple-500 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Direktör</span>
                                <?php if(isset($director) && $director): ?>
                                    <a href="<?php echo e(route('profile.show', $director->id)); ?>" class="mt-1 flex items-center gap-2 group">
                                        <?php if($director->profile_photo_path): ?>
                                            <img src="<?php echo e(asset('storage/' . $director->profile_photo_path)); ?>" class="w-6 h-6 rounded-full object-cover">
                                        <?php else: ?>
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-500"><?php echo e(substr($director->name, 0, 1)); ?></div>
                                        <?php endif; ?>
                                        <span class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors"><?php echo e($director->name); ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-sm font-bold text-gray-800 mt-1">-</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    
                    <div class="bg-orange-50/50 rounded-xl p-5 border border-orange-100 flex items-start gap-4 max-w-2xl mx-auto">
                        <div class="bg-orange-100 p-2 rounded-lg text-orange-600 shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">Devredilen Sorumluluklar</h4>
                            <p class="text-sm text-gray-600">
                                Bu personelin daha önceden dahil olduğu <strong>Çözüm Takımları</strong>, projeler ve şikayet onay süreçleri sistem tarafından korunmaktadır. Bekleyen görevler ve iletişim talepleri için doğrudan yukarida belirtilen yöneticilerle veya takımın yeni lideriyle iletişime geçebilirsiniz.
                            </p>
                        </div>
                    </div>
                    
                    
                    <div class="flex flex-col sm:flex-row justify-center gap-4 mt-10">
                        <button onclick="window.history.back()" class="px-8 py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl shadow-sm hover:bg-gray-50 transition-all">
                            Önceki Sayfaya Dön
                        </button>
                        <a href="<?php echo e(route('dashboard')); ?>" class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 transition-all">
                            Ana Sayfaya Git
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        
        <?php echo $__env->make('profile.partials.show.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-12">

            
            <?php echo $__env->make('profile.partials.show.stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div x-data="{ activeTab: '<?php echo e(session('active_tab', request('tab', (isset($isCustomerRep) && $isCustomerRep ? 'sikayetler' : 'performans')))); ?>' }"
                class="bg-white rounded-2xl shadow-xl overflow-hidden min-h-[600px]">

                
                <?php echo $__env->make('profile.partials.show.tabs-nav', ['activeTasksCount' => $activeTasks->count()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                
                <div class="p-6 bg-gray-50 min-h-[500px]">

                    <?php if(!isset($isCustomerRep) || !$isCustomerRep): ?>
                        <?php echo $__env->make('profile.partials.show.tab-performance', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>

                    <?php if($canViewActiveTasks && (!isset($isCustomerRep) || !$isCustomerRep)): ?>
                        <?php echo $__env->make('profile.partials.show.tab-aktif-gorevler', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>

                    <?php if($sikayetGormeYetkisi || (isset($isCustomerRep) && $isCustomerRep)): ?>
                        <?php echo $__env->make('profile.partials.show.tab-complaints', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>

                    <?php if(isset($isCustomerRep) && $isCustomerRep): ?>
                        <?php echo $__env->make('profile.partials.show.tab-colleagues', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>

                    <?php echo $__env->make('profile.partials.show.tab-comments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
                    <?php echo $__env->make('profile.partials.show.tab-security', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>

                    
                    <?php if(!isset($isCustomerRep) || !$isCustomerRep): ?>
                        <?php echo $__env->make('profile.partials.show.tab-disciplinary', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <?php echo $__env->make('profile.partials.show.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/profile/show.blade.php ENDPATH**/ ?>