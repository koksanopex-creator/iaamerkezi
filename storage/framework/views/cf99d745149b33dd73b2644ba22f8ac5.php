<?php $__env->startPush('pageTitle'); ?>
    Direktör Atamaları | 
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
        <div class="flex justify-between items-center" x-data="{ showAddModal: false }">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Direktör Bölüm Atamaları
            </h2>
            <div class="flex items-center gap-4">
                <button @click="$dispatch('open-add-director-modal')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Yeni Direktör Ekle
                </button>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Toplam <?php echo e($direktorler->count()); ?> Direktör
                </span>
            </div>

            
            <div x-data="{ open: false }" 
                 x-show="open" 
                 @open-add-director-modal.window="open = true"
                 x-cloak
                 class="fixed inset-0 z-[60] overflow-y-auto" 
                 aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form action="<?php echo e(route('admin.direktorler.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Yeni Direktör Oluştur</h3>
                                        <div class="mt-4 space-y-4">
                                            <div>
                                                <label for="name" class="block text-sm font-medium text-gray-700">Ad Soyad</label>
                                                <input type="text" name="name" id="name" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                            <div>
                                                <label for="email" class="block text-sm font-medium text-gray-700">E-posta</label>
                                                <input type="email" name="email" id="email" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label for="password" class="block text-sm font-medium text-gray-700">Şifre</label>
                                                    <input type="password" name="password" id="password" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                </div>
                                                <div>
                                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Şifre Tekrar</label>
                                                    <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Oluştur</button>
                                <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">İptal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
                    selectedUserId: <?php echo e($direktorler->first()->id ?? 'null'); ?>, 
                    search: '' 
                 }" 
                 class="flex flex-col md:flex-row gap-6 h-[calc(100vh-200px)] min-h-[600px]">

                
                <div class="w-full md:w-1/3 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col overflow-hidden">
                    
                    <div class="p-4 border-b border-gray-100 bg-gray-50">
                        <div class="relative">
                            <input x-model="search" 
                                   type="text" 
                                   placeholder="Direktör ara..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition duration-150">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        <?php if($direktorler->isEmpty()): ?>
                            <div class="p-6 text-center text-gray-500">
                                <p>Henüz 'Direktör' rolüne sahip bir kullanıcı yok.</p>
                                <a href="<?php echo e(route('admin.users.index')); ?>" class="text-indigo-600 hover:underline text-sm mt-2 block">Kullanıcı Ekle</a>
                            </div>
                        <?php else: ?>
                            <ul class="divide-y divide-gray-100">
                                <?php $__currentLoopData = $direktorler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $direktor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li x-show="search === '' || '<?php echo e(strtolower($direktor->name)); ?>'.includes(search.toLowerCase())"
                                        @click="selectedUserId = <?php echo e($direktor->id); ?>"
                                        :class="selectedUserId === <?php echo e($direktor->id); ?> ? 'bg-indigo-50 border-l-4 border-indigo-600' : 'hover:bg-gray-50 border-l-4 border-transparent'"
                                        class="cursor-pointer transition-all duration-150 ease-in-out group">
                                        <div class="p-4 flex items-center">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm group-hover:bg-indigo-200 transition">
                                                    <?php echo e(strtoupper(substr($direktor->name, 0, 1))); ?><?php echo e(strtoupper(substr(explode(' ', $direktor->name)[1] ?? '', 0, 1))); ?>

                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate" 
                                                   :class="selectedUserId === <?php echo e($direktor->id); ?> ? 'text-indigo-700' : ''">
                                                    <?php echo e($direktor->name); ?>

                                                </p>
                                                <p class="text-xs text-gray-500 truncate"><?php echo e($direktor->email); ?></p>
                                            </div>
                                            <div x-show="selectedUserId === <?php echo e($direktor->id); ?>">
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
                        <p class="text-lg">Bölüm atamalarını düzenlemek için soldan bir direktör seçin.</p>
                    </div>

                    
                    <?php $__currentLoopData = $direktorler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $direktor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div x-show="selectedUserId === <?php echo e($direktor->id); ?>" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-x-4"
                             x-transition:enter-end="opacity-100 translate-x-0"
                             class="flex flex-col h-full">
                            
                            
                            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900"><?php echo e($direktor->name); ?></h3>
                                    <p class="text-sm text-gray-500">Sorumlu olduğu bölümleri belirleyin</p>
                                </div>
                                <div class="text-right">
                                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Direktör
                                    </span>
                                </div>
                            </div>

                            
                            <form action="<?php echo e(route('admin.direktorler.update', $direktor->id)); ?>" method="POST" class="flex flex-col flex-1 min-h-0">
                                <?php echo csrf_field(); ?>
                                <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b pb-2">Tüm Bölümler</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <?php $__currentLoopData = $bolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="relative flex items-start p-4 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors duration-200 group"
                                                   :class="{ 'ring-2 ring-indigo-500 border-transparent bg-indigo-50': $el.querySelector('input').checked }">
                                                <div class="flex items-center h-5">
                                                    <input type="checkbox" 
                                                           name="bolumler[]" 
                                                           value="<?php echo e($bolum->id); ?>" 
                                                           <?php echo e($direktor->yonetilenBolumler->contains($bolum->id) ? 'checked' : ''); ?>

                                                           class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded"
                                                           onclick="this.closest('label').classList.toggle('ring-2'); this.closest('label').classList.toggle('ring-indigo-500'); this.closest('label').classList.toggle('bg-indigo-50');">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <span class="font-medium text-gray-900 group-hover:text-indigo-700"><?php echo e($bolum->ad); ?></span>
                                                    <?php if($bolum->director && $bolum->director_id != $direktor->id): ?>
                                                        <p class="text-[10px] text-red-500 mt-1 italic">
                                                            Şu anki: <?php echo e($bolum->director->name); ?>

                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>

                                
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                                    <p class="text-xs text-gray-500 italic">
                                        * Atanan bölümlerden gelen müşteri şikayetleri bu direktörün onayına düşecektir.
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
        [x-cloak] { display: none !important; }

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
<?php endif; ?>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/direktor_atamalari/index.blade.php ENDPATH**/ ?>