<?php $__env->startPush('pageTitle'); ?>
    Bölüm Kalite Yöneticisi Atamaları | 
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
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bölüm Kalite Yöneticisi Atamaları
            </h2>
            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                Toplam <?php echo e($yoneticiler->count()); ?> Yönetici
            </span>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            
            <?php if(session('success')): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                     class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-green-800 font-medium"><?php echo e(session('success')); ?></span>
                    </div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
                </div>
            <?php endif; ?>

            
            
            <div x-data="{ 
                    selectedUserId: <?php echo e($yoneticiler->first()->id ?? 'null'); ?>, 
                    search: '' 
                 }" 
                 class="flex flex-col md:flex-row gap-6 h-[calc(100vh-200px)] min-h-[600px]">

                
                <div class="w-full md:w-1/3 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col overflow-hidden">
                    
                    <div class="p-4 border-b border-gray-100 bg-gray-50">
                        <div class="relative">
                            <input x-model="search" 
                                   type="text" 
                                   placeholder="Yönetici ara..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition duration-150">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        <?php if($yoneticiler->isEmpty()): ?>
                            <div class="p-6 text-center text-gray-500">
                                <p>Henüz atanmış bir yönetici yok.</p>
                                <a href="<?php echo e(route('admin.users.index')); ?>" class="text-indigo-600 hover:underline text-sm mt-2 block">Kullanıcı Ekle</a>
                            </div>
                        <?php else: ?>
                            <ul class="divide-y divide-gray-100">
                                <?php $__currentLoopData = $yoneticiler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yonetici): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li x-show="search === '' || '<?php echo e(strtolower($yonetici->name)); ?>'.includes(search.toLowerCase())"
                                        @click="selectedUserId = <?php echo e($yonetici->id); ?>"
                                        :class="selectedUserId === <?php echo e($yonetici->id); ?> ? 'bg-indigo-50 border-l-4 border-indigo-600' : 'hover:bg-gray-50 border-l-4 border-transparent'"
                                        class="cursor-pointer transition-all duration-150 ease-in-out group">
                                        <div class="p-4 flex items-center">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm group-hover:bg-indigo-200 transition">
                                                    <?php echo e(strtoupper(substr($yonetici->name, 0, 1))); ?><?php echo e(strtoupper(substr(explode(' ', $yonetici->name)[1] ?? '', 0, 1))); ?>

                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate" 
                                                   :class="selectedUserId === <?php echo e($yonetici->id); ?> ? 'text-indigo-700' : ''">
                                                    <?php echo e($yonetici->name); ?>

                                                </p>
                                                <p class="text-xs text-gray-500 truncate"><?php echo e($yonetici->email); ?></p>
                                            </div>
                                            <div x-show="selectedUserId === <?php echo e($yonetici->id); ?>">
                                                <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="w-full md:w-2/3 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col overflow-hidden relative">
                    
                    
                    <div x-show="selectedUserId === null" x-cloak class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                        <svg class="h-16 w-16 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-lg">Yetkilerini düzenlemek için soldan bir yönetici seçin.</p>
                    </div>

                    
                    <?php $__currentLoopData = $yoneticiler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yonetici): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div x-show="selectedUserId === <?php echo e($yonetici->id); ?>" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-x-4"
                             x-transition:enter-end="opacity-100 translate-x-0"
                             class="flex flex-col h-full">
                            
                            
                            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900"><?php echo e($yonetici->name); ?></h3>
                                    <p class="text-sm text-gray-500">Sorumlu olduğu kalite kategorilerini belirleyin</p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Bölüm Kalite Yöneticisi
                                    </span>
                                    
                                    <label class="inline-flex items-center cursor-pointer group">
                                        <div class="mr-3 text-xs font-bold text-gray-600 group-hover:text-indigo-600 transition-colors uppercase tracking-wider">Müdahale Yetkisi</div>
                                        <div class="relative">
                                            <input type="hidden" form="update-form-<?php echo e($yonetici->id); ?>" name="can_intervene_quality" value="0">
                                            <input type="checkbox" form="update-form-<?php echo e($yonetici->id); ?>" name="can_intervene_quality" value="1" <?php echo e($yonetici->can_intervene_quality ? 'checked' : ''); ?> class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            
                            <form id="update-form-<?php echo e($yonetici->id); ?>" action="<?php echo e(route('admin.kalite-yoneticileri.update', $yonetici->id)); ?>" method="POST" class="flex flex-col flex-1 min-h-0">
                                <?php echo csrf_field(); ?>
                                <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="relative flex items-start p-4 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors duration-200 group"
                                                   :class="{ 'ring-2 ring-indigo-500 border-transparent bg-indigo-50': $el.querySelector('input').checked }">
                                                <div class="flex items-center h-5">
                                                    <input type="checkbox" 
                                                           name="kategoriler[]" 
                                                           value="<?php echo e($kategori->id); ?>" 
                                                           <?php echo e($yonetici->yonettigiSikayetKategorileri->contains($kategori->id) ? 'checked' : ''); ?>

                                                           class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded"
                                                           onclick="this.closest('label').classList.toggle('ring-2'); this.closest('label').classList.toggle('ring-indigo-500'); this.closest('label').classList.toggle('bg-indigo-50');">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <span class="font-medium text-gray-900 group-hover:text-indigo-700"><?php echo e($kategori->ad); ?></span>
                                                    <?php if($kategori->varsayilanTakim): ?>
                                                        <p class="text-xs text-gray-500 mt-1">Takım: <?php echo e($kategori->varsayilanTakim->ad); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>

                                
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                                    <p class="text-xs text-gray-500 italic">
                                        * Seçili kategorilerden gelen projeler, bu kullanıcının onayına düşecektir.
                                    </p>
                                    <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        Değişiklikleri Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </div>
            </div>
        </div>
    </div>

    <style>
        /* Alpine.js yüklenene kadar gizle */
        [x-cloak] { display: none !important; }

        /* İnce Scrollbar Tasarımı */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c7c7c7;
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/bolum_kalite_yoneticileri/index.blade.php ENDPATH**/ ?>