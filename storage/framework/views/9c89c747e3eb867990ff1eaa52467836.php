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
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('İyileştirmeye Açık Alan Yönetimi')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            
            <?php if(session('success')): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                     class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="font-medium text-sm"><?php echo e(session('success')); ?></span>
                    </div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
                </div>
            <?php endif; ?>
            
            
            <?php echo $__env->make('admin.iaa-yonetim.partials.stats-cards', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" x-data="{ activeTab: localStorage.getItem('activeTab') || 'aktif-projeler' }">
                
                
                <div class="sm:hidden p-4 border-b border-gray-200 bg-gray-50">
                    <label for="tabs" class="sr-only">Sekme Seçiniz</label>
                    <select id="tabs" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium text-gray-700" 
                            x-model="activeTab" 
                            @change="switchTab($event.target.value)">
                        
                        <option value="aktif-projeler">🔵 Aktif Projeler (<?php echo e($atanmisOlanlar->count()); ?>)</option>
                        
                        <?php if(auth()->user()->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi'])): ?>
                            <option value="onay-bekleyenler">🟡 Onay Bekleyenler</option>
                        <?php endif; ?>

                        
                        <?php if(!auth()->user()->hasRole('Superadmin') && isset($bolumYoneticisiOnayladiklari) && $bolumYoneticisiOnayladiklari->isNotEmpty()): ?>
                            <option value="onayladiklarim">🔵 Onayladıklarım</option>
                        <?php endif; ?>
                        

                        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
                            <option value="havuz-talepler">🔘 Havuz & Talepler</option>
                        <?php endif; ?>

                        <option value="tamamlananlar">🟢 Tamamlananlar</option>

                        <?php if(auth()->user()->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi'])): ?>
                            <option value="reddedilenler">🔴 Reddedilenler</option>
                        <?php endif; ?>
                    </select>
                </div>

                
                <div class="hidden sm:block border-b border-gray-200">
                    <nav class="flex -mb-px overflow-x-auto" aria-label="Tabs">
                        
                        
                        <button @click="switchTab('aktif-projeler'); activeTab = 'aktif-projeler'" 
                                :class="activeTab === 'aktif-projeler' 
                                    ? 'border-blue-500 text-blue-700 bg-blue-50' 
                                    : 'border-transparent text-blue-600 hover:text-blue-800 hover:bg-blue-50/50'"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Aktif Projeler
                            <?php if($atanmisOlanlar->count() > 0): ?>
                                <span :class="activeTab === 'aktif-projeler' ? 'bg-blue-200 text-blue-800' : 'bg-blue-100 text-blue-600'" 
                                      class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-bold transition-colors">
                                    <?php echo e($atanmisOlanlar->count()); ?>

                                </span>
                            <?php endif; ?>
                        </button>

                        
                        <?php if(auth()->user()->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi'])): ?>
                        <button @click="switchTab('onay-bekleyenler'); activeTab = 'onay-bekleyenler'" 
                                :class="activeTab === 'onay-bekleyenler' 
                                    ? 'border-yellow-500 text-yellow-700 bg-yellow-50' 
                                    : 'border-transparent text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50/50'"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Onay Bekleyenler
                            <?php 
                                $onayToplam = $bolumOnayiBekleyenler->count(); 
                                if(auth()->user()->hasRole('Superadmin')) {
                                    $onayToplam += $onayBekleyenMisafirler->count() + $onayBekleyenKullanicilar->count() + $yoneticiOnayiBekleyenler->count();
                                }
                            ?>
                            <?php if($onayToplam > 0): ?>
                                <span :class="activeTab === 'onay-bekleyenler' ? 'bg-yellow-200 text-yellow-800' : 'bg-yellow-100 text-yellow-600'"
                                      class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-bold transition-colors">
                                    <?php echo e($onayToplam); ?>

                                </span>
                            <?php endif; ?>
                        </button>
                        <?php endif; ?>

                        
                        <?php
                            $onayladiklarimCount = 0;
                            if(auth()->user()->hasRole('Superadmin') && isset($superadminOnayladiklari)) {
                                $onayladiklarimCount = $superadminOnayladiklari->count();
                            } elseif(isset($bolumYoneticisiOnayladiklari)) {
                                $onayladiklarimCount = $bolumYoneticisiOnayladiklari->count();
                            }
                        ?>

                        <?php if($onayladiklarimCount > 0): ?>
                        <button onclick="switchTab('onayladiklarim')" 
                                class="tab-button whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors flex items-center gap-2"
                                data-tab="onayladiklarim">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Onayladıklarım
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full"><?php echo e($onayladiklarimCount); ?></span>
                        </button>
                        <?php endif; ?>
                        

                        
                        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
                        <button @click="switchTab('havuz-talepler'); activeTab = 'havuz-talepler'"
                                :class="activeTab === 'havuz-talepler' 
                                    ? 'border-gray-500 text-gray-700 bg-gray-100' 
                                    : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Havuz & Talepler
                        </button>
                        <?php endif; ?>

                        
                        <button @click="switchTab('tamamlananlar'); activeTab = 'tamamlananlar'"
                                :class="activeTab === 'tamamlananlar' 
                                    ? 'border-green-500 text-green-700 bg-green-50' 
                                    : 'border-transparent text-green-600 hover:text-green-800 hover:bg-green-50/50'"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            Tamamlananlar
                            <?php if($sonTamamlananlar->count() > 0): ?>
                                <span :class="activeTab === 'tamamlananlar' ? 'bg-green-200 text-green-800' : 'bg-green-100 text-green-600'"
                                      class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-bold transition-colors">
                                    <?php echo e($sonTamamlananlar->count()); ?>

                                </span>
                            <?php endif; ?>
                        </button>

                        
                        <?php if(auth()->user()->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi'])): ?>
                        <button @click="switchTab('reddedilenler'); activeTab = 'reddedilenler'"
                                :class="activeTab === 'reddedilenler' 
                                    ? 'border-red-500 text-red-700 bg-red-50' 
                                    : 'border-transparent text-red-600 hover:text-red-800 hover:bg-red-50/50'"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reddedilenler
                            <?php 
                                $redToplam = $tamamlanmasiReddedilenler->count();
                                if(auth()->user()->hasRole('Superadmin')) {
                                    $redToplam += $reddedilenler->count();
                                }
                            ?>
                            <?php if($redToplam > 0): ?>
                                <span :class="activeTab === 'reddedilenler' ? 'bg-red-200 text-red-800' : 'bg-red-100 text-red-600'"
                                      class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-bold transition-colors">
                                    <?php echo e($redToplam); ?>

                                </span>
                            <?php endif; ?>
                        </button>
                        <?php endif; ?>
                    </nav>
                </div>

                
                <div class="p-4 sm:p-6 bg-gray-50 min-h-[400px]">
                    
                    
                    <div id="tab-aktif-projeler" class="tab-content space-y-6">
                        <?php if($atanmisOlanlar->isNotEmpty()): ?>
                            <?php echo $__env->make('admin.iaa-yonetim.partials.atanmis-projeler-table', [
                                'iaas' => $atanmisOlanlar, 
                                'type' => 'atanmis', 
                                'title' => 'Atanmış / Revize Edilen Projeler', 
                                'color' => 'blue' 
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php else: ?>
                             <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-200">
                                Aktif (Atanmış veya Revize edilen) proje bulunmamaktadır.
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <div id="tab-onay-bekleyenler" class="tab-content space-y-6 hidden">
                        
                        
                        <?php if($bolumOnayiBekleyenler->isNotEmpty()): ?>
                            <?php echo $__env->make('admin.iaa-yonetim.partials.bolum-onayi-bekleyenler-table', ['iaas' => $bolumOnayiBekleyenler], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>

                        
                        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
                            <?php if($yoneticiOnayiBekleyenler->isNotEmpty()): ?>
                                <?php echo $__env->make('admin.iaa-yonetim.partials.yonetici-onayi-bekleyenler-table', ['iaas' => $yoneticiOnayiBekleyenler, 'title' => 'Yönetici Onayı Bekleyen Tamamlanmış Projeler', 'color' => 'purple'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endif; ?>
                            <?php if($onayBekleyenMisafirler->isNotEmpty()): ?>
                                <?php echo $__env->make('admin.iaa-yonetim.partials.onay-bekleyen-misafirler-table', ['iaas' => $onayBekleyenMisafirler, 'type' => 'onay', 'title' => 'Misafirlerden Gelen Öneriler', 'color' => 'yellow'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endif; ?>
                            <?php if($onayBekleyenKullanicilar->isNotEmpty()): ?>
                                <?php echo $__env->make('admin.iaa-yonetim.partials.onay-bekleyen-kullanicilar-table', ['iaas' => $onayBekleyenKullanicilar, 'type' => 'onay', 'title' => 'Kayıtlı Kullanıcılardan Gelen Öneriler', 'color' => 'yellow'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endif; ?>
                        <?php endif; ?>

                        
                        <?php if($onayToplam == 0): ?>
                             <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-200">
                                Onay bekleyen herhangi bir proje bulunmamaktadır.
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <div id="tab-onayladiklarim" class="tab-content space-y-6 hidden">
                        <?php if(auth()->user()->hasRole('Superadmin') && isset($superadminOnayladiklari) && $superadminOnayladiklari->isNotEmpty()): ?>
                            
                            <?php echo $__env->make('admin.iaa-yonetim.partials.bolum-onayladiklari-table', [
                                'iaas' => $superadminOnayladiklari
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php elseif(isset($bolumYoneticisiOnayladiklari) && $bolumYoneticisiOnayladiklari->isNotEmpty()): ?>
                            
                            <?php echo $__env->make('admin.iaa-yonetim.partials.bolum-onayladiklari-table', [
                                'iaas' => $bolumYoneticisiOnayladiklari
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php else: ?>
                            <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-200">
                                Henüz onayladığınız ve geri alabileceğiniz bir proje bulunmamaktadır.
                            </div>
                        <?php endif; ?>
                    </div>
                    

                    
                    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
                    <div id="tab-havuz-talepler" class="tab-content space-y-6 hidden">
                        <?php if($talepAlanOneriler->isNotEmpty()): ?>
                            <?php echo $__env->make('admin.iaa-yonetim.partials.talep-alan-oneriler-table', ['iaas' => $talepAlanOneriler, 'title' => 'Talep Alan Öneriler', 'color' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>
                        <?php if($havuzdakiler->isNotEmpty()): ?>
                            <?php echo $__env->make('admin.iaa-yonetim.partials.havuzdaki-oneriler-table', ['iaas' => $havuzdakiler, 'type' => 'havuz', 'title' => 'Havuzdaki Öneriler', 'color' => 'gray'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>
                        <?php if($havuzdakiler->isEmpty() && $talepAlanOneriler->isEmpty()): ?>
                             <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-200">
                                Havuz boş.
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    
                    <div id="tab-tamamlananlar" class="tab-content space-y-6 hidden">
                        <?php if($sonTamamlananlar->isNotEmpty()): ?>
                            <?php echo $__env->make('admin.iaa-yonetim.partials.tamamlanmis-projeler-ozet-table', [
                                'iaas' => $sonTamamlananlar, 
                                'title' => 'Tamamlanan Projeler', 
                                'color' => 'green' 
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php else: ?>
                            <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-200">
                                Henüz tamamlanmış bir proje bulunmamaktadır.
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <?php if(auth()->user()->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi'])): ?>
                    <div id="tab-reddedilenler" class="tab-content space-y-6 hidden">
                        
                        
                        <?php if($reddedilenler->isNotEmpty()): ?>
                            <?php echo $__env->make('admin.iaa-yonetim.partials.reddedilen-oneriler-table', [
                                'iaas' => $reddedilenler, 
                                'type' => 'reddedilmis', 
                                'title' => 'Reddedilen Öneriler', 
                                'color' => 'red'
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>

                        
                        <?php if($tamamlanmasiReddedilenler->isNotEmpty()): ?>
                            <?php echo $__env->make('admin.iaa-yonetim.partials.tamamlanmasi-reddedilen-projeler-table', [
                                'iaas' => $tamamlanmasiReddedilenler, 
                                'title' => 'Tamamlanması Reddedilen Projeler'
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>

                        <?php if($reddedilenler->isEmpty() && $tamamlanmasiReddedilenler->isEmpty()): ?>
                             <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-200">
                                Reddedilen proje bulunmamaktadır.
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    
    <?php echo $__env->make('admin.iaa-yonetim.partials.all-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php if($errors->any() && session('error_modal_id')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'reddet-modal-<?php echo e(session('error_modal_id')); ?>' }));
        });
    </script>
    <?php endif; ?>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            
            const targetContent = document.getElementById('tab-' + tabName);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                localStorage.setItem('activeTab', tabName);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const userHasBolumOnayi = <?php echo json_encode($bolumOnayiBekleyenler->isNotEmpty() && !auth()->user()->hasRole('Superadmin'), 15, 512) ?>;
            let defaultTab = localStorage.getItem('activeTab') || 'aktif-projeler';
            
            if (userHasBolumOnayi) { defaultTab = 'onay-bekleyenler'; }
            
            const dropdown = document.getElementById('tabs');
            if(dropdown) { dropdown.value = defaultTab; }

            if (!document.getElementById('tab-' + defaultTab)) {
                switchTab('aktif-projeler');
            } else {
                switchTab(defaultTab);
            }
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/iaa-yonetim/index.blade.php ENDPATH**/ ?>