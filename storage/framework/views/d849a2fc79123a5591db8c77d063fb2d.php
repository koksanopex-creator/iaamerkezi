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
        <h2 class="font-black text-2xl text-gray-900 tracking-tight">
            Tutanak Sorumlusu Yönetimi
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            
            <div class="bg-indigo-50 border-l-4 border-indigo-600 p-5 mb-8 rounded-r-xl shadow-sm flex items-start gap-4">
                <div class="bg-indigo-100 p-2 rounded-full text-indigo-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-indigo-900">Yetki Kapsamı Hakkında</h4>
                    <p class="text-sm text-indigo-700 mt-1">
                        Burada yetkilendirdiğiniz "Disiplin Sorumluları", 
                        <?php if(Auth::user()->bolum->is_disciplinary_global): ?>
                            <strong class="bg-indigo-200 px-1 rounded">bölümünüz "Global Yetkili" olduğu için TÜM FABRİKAYA</strong>
                        <?php else: ?>
                            <strong class="bg-indigo-200 px-1 rounded">sadece sizin bölümünüzdeki diğer personellere</strong>
                        <?php endif; ?>
                        tutanak tutabilecektir.
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Personel Bilgisi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mevcut Durum</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $personeller; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $personel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center font-bold text-gray-600">
                                            <?php echo e(substr($personel->name, 0, 1)); ?>

                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900"><?php echo e($personel->name); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo e($personel->email); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($personel->can_issue_disciplinary): ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                            Tutanak Sorumlusu
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-500 border border-gray-200">
                                            Standart Personel
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    
                                    <form action="<?php echo e(route('admin.disiplin.sorumlular.update', $personel->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php if($personel->can_issue_disciplinary): ?>
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-bold bg-red-50 hover:bg-red-100 px-4 py-2 rounded-lg transition border border-red-200">
                                                Yetkiyi Al
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 font-bold bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-lg transition border border-indigo-200">
                                                Sorumlu Yap
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/disiplin/sorumlular/index.blade.php ENDPATH**/ ?>