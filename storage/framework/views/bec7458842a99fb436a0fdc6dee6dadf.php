<?php $__env->startPush('pageTitle'); ?>
    Müşteri Saha Temsilcileri | 
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
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tight uppercase">
                    <?php echo e(__('Müşteri Saha Temsilcileri Yönetimi')); ?>

                </h2>
                <p class="text-xs font-medium text-slate-500 italic mt-1 uppercase tracking-widest">Temsilcilerin sorumlu olduğu saha bölgelerini (bölümlerini) belirleyin</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" 
                    x-data="" x-on:click.prevent="$dispatch('open-modal', 'temsilci-ekle-modal')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    YENİ TEMSİLCİ EKLE
                </button>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12" x-data="{ selectedUserId: <?php echo e($temsilciler->first() ? $temsilciler->first()->id : 'null'); ?>, searchTerm: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <?php if(session('success')): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                     class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm flex items-start gap-3">
                    <div class="bg-green-100 p-2 rounded-full text-green-600 shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-green-800">Başarılı!</h4>
                        <p class="text-sm text-green-700 mt-1"><?php echo e(session('success')); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                     class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm flex items-start gap-3">
                    <div class="bg-red-100 p-2 rounded-full text-red-600 shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-red-800">Hata!</h4>
                        <p class="text-sm text-red-700 mt-1"><?php echo e(session('error')); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex flex-col md:flex-row gap-6 h-[700px]">
                
                
                <div class="w-full md:w-1/3 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col overflow-hidden">
                    
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" x-model="searchTerm" placeholder="Temsilci Ara..." 
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                        </div>
                    </div>

                    
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        <?php if($temsilciler->isEmpty()): ?>
                            <div class="p-6 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Sistemde henüz "Müşteri Saha Temsilcisi" rolünde bir kullanıcı bulunmuyor.
                            </div>
                        <?php else: ?>
                            <ul class="divide-y divide-gray-100">
                                <?php $__currentLoopData = $temsilciler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $temsilci): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li x-show="searchTerm === '' || '<?php echo e(strtolower($temsilci->name)); ?>'.includes(searchTerm.toLowerCase())">
                                        <button @click="selectedUserId = <?php echo e($temsilci->id); ?>" 
                                                class="w-full text-left px-6 py-4 focus:outline-none transition-colors duration-150"
                                                :class="{ 'bg-indigo-50 border-l-4 border-indigo-600': selectedUserId === <?php echo e($temsilci->id); ?>, 'hover:bg-gray-50 border-l-4 border-transparent': selectedUserId !== <?php echo e($temsilci->id); ?> }">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border border-indigo-200">
                                                        <?php echo e(strtoupper(substr($temsilci->name, 0, 1))); ?>

                                                    </div>
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <p class="text-sm font-bold text-gray-900 truncate"><?php echo e($temsilci->name); ?></p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800">
                                                            <?php echo e($temsilci->musteriSahaTemsilcisiOlduguBolumler->count()); ?> Bölüm
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="ml-2">
                                                    <svg class="h-5 w-5 text-gray-400" :class="{ 'text-indigo-500': selectedUserId === <?php echo e($temsilci->id); ?> }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </button>
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
                        <p class="text-lg">Yetkilerini düzenlemek için soldan bir temsilci seçin.</p>
                    </div>

                    
                    <?php $__currentLoopData = $temsilciler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $temsilci): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div x-show="selectedUserId === <?php echo e($temsilci->id); ?>" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-x-4"
                             x-transition:enter-end="opacity-100 translate-x-0"
                             class="flex flex-col h-full">
                            
                            
                            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900"><?php echo e($temsilci->name); ?></h3>
                                    <p class="text-sm text-gray-500">Sorumlu olduğu saha bölümlerini belirleyin</p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Müşteri Saha Temsilcisi
                                    </span>
                                </div>
                            </div>

                            
                            <form id="update-form-<?php echo e($temsilci->id); ?>" action="<?php echo e(route('admin.musteri-saha-temsilcileri.update', $temsilci->id)); ?>" method="POST" class="flex flex-col flex-1 min-h-0">
                                <?php echo csrf_field(); ?>
                                <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <?php $__currentLoopData = $bolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="relative flex items-start p-4 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors duration-200 group"
                                                   :class="{ 'ring-2 ring-indigo-500 border-transparent bg-indigo-50': $el.querySelector('input').checked }">
                                                <div class="flex items-center h-5">
                                                    <input type="checkbox" 
                                                           name="bolumler[]" 
                                                           value="<?php echo e($bolum->id); ?>" 
                                                           <?php echo e($temsilci->musteriSahaTemsilcisiOlduguBolumler->contains($bolum->id) ? 'checked' : ''); ?>

                                                           class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded"
                                                           onclick="this.closest('label').classList.toggle('ring-2'); this.closest('label').classList.toggle('ring-indigo-500'); this.closest('label').classList.toggle('bg-indigo-50');">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <span class="font-medium text-gray-900 group-hover:text-indigo-700"><?php echo e($bolum->ad); ?></span>
                                                </div>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <?php if($bolumler->isEmpty()): ?>
                                        <div class="text-center text-gray-500 mt-10">
                                            Sistemde aktif bölüm bulunmamaktadır veya sizin yetkili olduğunuz bir bölüm yoktur.
                                        </div>
                                    <?php endif; ?>
                                </div>

                                
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                                    <p class="text-xs text-gray-500 italic">
                                        * Seçili bölümlerdeki şikayetler, bu personelin "Read-Only" ekranına düşecektir.
                                    </p>
                                    <?php if($bolumler->isNotEmpty()): ?>
                                    <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        Değişiklikleri Kaydet
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </div>
            </div>
        </div>
    </div>

    
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'temsilci-ekle-modal','maxWidth' => 'md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'temsilci-ekle-modal','maxWidth' => 'md']); ?>
        <form method="POST" action="<?php echo e(route('admin.musteri-saha-temsilcileri.store')); ?>" class="p-6">
            <?php echo csrf_field(); ?>
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Sisteme Yeni Müşteri Saha Temsilcisi Ekle
            </h2>
            <p class="text-sm text-gray-600 mb-6">Lütfen "Müşteri Saha Temsilcisi" yapmak istediğiniz personeli seçin.</p>
            
            <div class="mb-4">
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Personel Seçimi</label>
                
                <select name="user_id" id="user_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Seçiniz...</option>
                    <?php $__currentLoopData = \App\Models\User::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if (! ($u->hasRole('Müşteri Saha Temsilcisi'))): ?>
                            <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?> (<?php echo e($u->email); ?>)</option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div class="mt-6 flex justify-end">
                <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['xOn:click' => '$dispatch(\'close\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:click' => '$dispatch(\'close\')']); ?>
                    İptal
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['class' => 'ml-3 bg-indigo-600 hover:bg-indigo-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ml-3 bg-indigo-600 hover:bg-indigo-700']); ?>
                    Ekle
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
            </div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>

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
<?php endif; ?>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/musteri_saha_temsilcileri/index.blade.php ENDPATH**/ ?>