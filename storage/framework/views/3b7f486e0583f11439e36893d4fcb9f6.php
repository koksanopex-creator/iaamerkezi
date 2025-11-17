<div>
    

     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Devam Eden Şikayet Görevlerim')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="text-2xl">
                        Takımınıza Atanan Aktif Şikayet Projeleri
                    </div>
                    <div class="mt-2 text-gray-500">
                        Burada, üyesi olduğunuz çözüm takımlarına atanmış ve durumu "İşlemde" olan projeleri görebilirsiniz.
                    </div>
                </div>

                <div class="bg-gray-50 bg-opacity-25">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $projeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-6 border-t border-gray-200 hover:bg-gray-100 transition duration-150">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                    <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                        <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>"><?php echo e($proje->baslik); ?></a>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo e($proje->onaylanma_tarihi->format('d.m.Y')); ?>

                                </div>
                            </div>

                            <div class="ml-12 mt-2">
                                <div class="text-sm text-gray-500">
                                    <!--[if BLOCK]><![endif]--><?php if($proje->musteriSikayeti): ?>
                                        <strong>Müşteri:</strong> <?php echo e($proje->musteriSikayeti->musteri_adi); ?>

                                    <?php else: ?>
                                        <strong>Müşteri:</strong> (Belirtilmemiş)
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                
                                <div class="mt-3 flex items-center gap-3">
                                    <!--[if BLOCK]><![endif]--><?php if($proje->musteriSikayeti): ?>
                                    
                                    <a href="<?php echo e(route('admin.sikayetler.show', $proje->musteriSikayeti->id)); ?>"
                                       class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Şikayet Detayı
                                    </a>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>"
                                       class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        Projeye Git
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-6 text-center text-gray-500">
                            Takımınıza atanmış ve devam eden bir şikayet projesi bulunmamaktadır.
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <!--[if BLOCK]><![endif]--><?php if($projeler->hasPages()): ?>
                    <div class="p-6 bg-gray-50 border-t border-gray-200">
                        <?php echo e($projeler->links()); ?>

                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/sikayet-gorevlerim.blade.php ENDPATH**/ ?>