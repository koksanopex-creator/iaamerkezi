<?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'onayla-modal-'.e($iaa->id).'','focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'onayla-modal-'.e($iaa->id).'','focusable' => true]); ?>
    
    <div x-data="approvalForm({
        risk: '<?php echo e($iaa->risk); ?>',
        kazanc_miktar: '<?php echo e($iaa->kazanc_miktar); ?>',
        kazanc_birim: '<?php echo e($iaa->kazanc_birim); ?>',
        butce_miktar: '<?php echo e($iaa->butce_miktar); ?>',
        butce_birim: '<?php echo e($iaa->butce_birim); ?>',
        yil_baz: '<?php echo e($iaa->yil_baz); ?>',
        oneren_kazanc_miktar: '<?php echo e($iaa->oneren_kazanc_miktar); ?>',
        oneren_kazanc_birim: '<?php echo e($iaa->oneren_kazanc_birim); ?>',
        oneren_butce_miktar: '<?php echo e($iaa->oneren_butce_miktar); ?>',
        oneren_butce_birim: '<?php echo e($iaa->oneren_butce_birim); ?>'
    })">
        <form method="post" action="<?php echo e(route('admin.iaa-yonetim.onayla', $iaa)); ?>" class="p-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('patch'); ?>

            
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Öneriyi Onayla ve Puanla</h2>
                    <p class="mt-1 text-sm text-gray-600">"<?php echo e(Str::limit($iaa->baslik, 30)); ?>" önerisini puanlayın.</p>
                </div>

                
                <template x-if="hasProposerValues">
                    <button type="button" @click="useProposerValues()" 
                            title="Öneriyi gönderen kullanıcının girdiği tahmini finansal değerleri forma otomatik olarak uygular."
                            class="flex-shrink-0 ml-4 inline-flex items-center space-x-2 bg-blue-100 text-blue-800 font-semibold text-xs py-1 px-3 rounded-full hover:bg-blue-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                        </svg>
                        <span>Öneren Verilerini Kullan</span>
                    </button>
                </template>
            </div>
            

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div>
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'risk_'.e($iaa->id).'','value' => 'Risk (1-5 Arası)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'risk_'.e($iaa->id).'','value' => 'Risk (1-5 Arası)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <select name="risk" x-model="risk" id="risk_<?php echo e($iaa->id); ?>" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="1">1 (Çok Düşük)</option>
                        <option value="2">2 (Düşük)</option>
                        <option value="3">3 (Orta)</option>
                        <option value="4">4 (Yüksek)</option>
                        <option value="5">5 (Çok Yüksek)</option>
                    </select>
                </div>
                <div></div>

                
                <div>
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'kazanc_miktar_'.e($iaa->id).'','value' => 'Tahmini Yıllık Kazanç']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'kazanc_miktar_'.e($iaa->id).'','value' => 'Tahmini Yıllık Kazanç']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['type' => 'number','step' => '0.01','name' => 'kazanc_miktar','xModel' => 'kazanc_miktar','id' => 'kazanc_miktar_'.e($iaa->id).'','class' => 'mt-1 block w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','step' => '0.01','name' => 'kazanc_miktar','x-model' => 'kazanc_miktar','id' => 'kazanc_miktar_'.e($iaa->id).'','class' => 'mt-1 block w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
                </div>
                <div>
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'kazanc_birim_'.e($iaa->id).'','value' => 'Birim']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'kazanc_birim_'.e($iaa->id).'','value' => 'Birim']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <select name="kazanc_birim" x-model="kazanc_birim" id="kazanc_birim_<?php echo e($iaa->id); ?>" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                         <?php $__currentLoopData = $paraBirimleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $birim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($birim); ?>"><?php echo e($birim); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div>
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'butce_miktar_'.e($iaa->id).'','value' => 'Tahmini Bütçe (Tek Seferlik)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'butce_miktar_'.e($iaa->id).'','value' => 'Tahmini Bütçe (Tek Seferlik)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['type' => 'number','step' => '0.01','name' => 'butce_miktar','xModel' => 'butce_miktar','id' => 'butce_miktar_'.e($iaa->id).'','class' => 'mt-1 block w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','step' => '0.01','name' => 'butce_miktar','x-model' => 'butce_miktar','id' => 'butce_miktar_'.e($iaa->id).'','class' => 'mt-1 block w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
                </div>
                 <div>
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'butce_birim_'.e($iaa->id).'','value' => 'Birim']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'butce_birim_'.e($iaa->id).'','value' => 'Birim']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <select name="butce_birim" x-model="butce_birim" id="butce_birim_<?php echo e($iaa->id); ?>" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <?php $__currentLoopData = $paraBirimleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $birim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($birim); ?>"><?php echo e($birim); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="col-span-2">
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'yil_baz_input_'.e($iaa->id).'','value' => 'Puanlama Süresi (Yıl)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'yil_baz_input_'.e($iaa->id).'','value' => 'Puanlama Süresi (Yıl)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <div class="mt-1 flex items-center gap-3">
                        
                        <div class="grid grid-cols-4 gap-2 flex-grow">
                            <?php $__currentLoopData = [1 => '1 Yıl', 3 => '3 Yıl', 5 => '5 Yıl', 10 => '10 Yıl']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yil => $etiket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" 
                                    @click="yil_baz = <?php echo e($yil); ?>" 
                                    class="text-center px-2 py-2 rounded-lg border-2 text-xs font-semibold transition-all focus:outline-none"
                                    :class="yil_baz == <?php echo e($yil); ?> 
                                        ? 'bg-indigo-600 border-indigo-600 text-white shadow-md' 
                                        : 'bg-white border-gray-200 text-gray-600 hover:border-indigo-300'">
                                <?php echo e($etiket); ?>

                            </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        
                        <div class="w-24">
                            <input type="number" min="1" max="50" x-model.number="yil_baz" id="yil_baz_input_<?php echo e($iaa->id); ?>"
                                   class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs py-2 text-center" 
                                   placeholder="Diğer" />
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Seçtiğiniz süre boyunca elde edilecek toplam kazanç esas alınarak puan hesaplanır.</p>
                    <input type="hidden" name="yil_baz" :value="yil_baz">
                </div>
            </div>

            
            <div class="mt-5 rounded-xl overflow-hidden border border-indigo-200">
                
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span class="text-sm font-semibold text-white">
                            Hesaplanan Öneri Puanı
                            <template x-if="parseFloat(kazanc_miktar) > 0 && parseFloat(butce_miktar) > 0">
                                <span class="text-indigo-200 text-xs font-normal" x-text="'(' + yil_baz + ' yıl baz alındı)'" ></span>
                            </template>
                        </span>
                    </div>
                    <div class="text-right">
                        <template x-if="dynamicPuan !== null">
                            <span class="text-2xl font-extrabold text-white" x-text="dynamicPuan"></span>
                        </template>
                        <template x-if="dynamicPuan === null">
                            <span class="text-sm text-indigo-200 font-medium">Tüm alanları doldurun</span>
                        </template>
                    </div>
                </div>

                
                <template x-if="parseFloat(kazanc_miktar) > 0 && parseFloat(butce_miktar) > 0">
                    <div>
                        
                        <div class="bg-white px-4 pt-3 pb-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Kümülatif Kazanç Projeksiyonu</p>
                            <div class="grid grid-cols-3 gap-2">
                                
                                <div class="rounded-lg p-2 text-center transition-all"
                                     :class="[
                                        roi1 >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200',
                                        yil_baz == 1 ? 'ring-2 ring-indigo-500 ring-offset-1' : ''
                                     ]">
                                    <p class="text-[10px] font-semibold uppercase" :class="roi1 >= 0 ? 'text-green-600' : 'text-red-600'">1 Yıl</p>
                                    <p class="text-sm font-extrabold" :class="roi1 >= 0 ? 'text-green-800' : 'text-red-700'"
                                       x-text="(roi1 >= 0 ? '+' : '') + roi1.toLocaleString('tr-TR') + ' ' + kazanc_birim"></p>
                                    <p class="text-[9px] mt-0.5" :class="roi1 >= 0 ? 'text-green-500' : 'text-red-400'"
                                       x-text="roi1 >= 0 ? 'Kârda ✓' : 'Zararda'"></p>
                                    <p class="text-[8px] text-gray-400 mt-1 border-t border-dashed border-gray-200 pt-0.5">(1 × Yıllık Kazanç - Bütçe)</p>
                                </div>
                                
                                <div class="rounded-lg p-2 text-center transition-all"
                                     :class="[
                                        roi5 >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200',
                                        yil_baz == 5 ? 'ring-2 ring-indigo-500 ring-offset-1' : ''
                                     ]">
                                    <p class="text-[10px] font-semibold uppercase" :class="roi5 >= 0 ? 'text-green-600' : 'text-red-600'">5 Yıl</p>
                                    <p class="text-sm font-extrabold" :class="roi5 >= 0 ? 'text-green-800' : 'text-red-700'"
                                       x-text="(roi5 >= 0 ? '+' : '') + roi5.toLocaleString('tr-TR') + ' ' + kazanc_birim"></p>
                                    <p class="text-[9px] mt-0.5" :class="roi5 >= 0 ? 'text-green-500' : 'text-red-400'"
                                       x-text="roi5 >= 0 ? 'Kârda ✓' : 'Zararda'"></p>
                                    <p class="text-[8px] text-gray-400 mt-1 border-t border-dashed border-gray-200 pt-0.5">(5 × Yıllık Kazanç - Bütçe)</p>
                                </div>
                                
                                <div class="rounded-lg p-2 text-center transition-all"
                                     :class="[
                                        roi10 >= 0 ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border border-red-200',
                                        yil_baz == 10 ? 'ring-2 ring-indigo-500 ring-offset-1' : ''
                                     ]">
                                    <p class="text-[10px] font-semibold uppercase" :class="roi10 >= 0 ? 'text-emerald-600' : 'text-red-600'">10 Yıl</p>
                                    <p class="text-sm font-extrabold" :class="roi10 >= 0 ? 'text-emerald-800' : 'text-red-700'"
                                       x-text="(roi10 >= 0 ? '+' : '') + roi10.toLocaleString('tr-TR') + ' ' + kazanc_birim"></p>
                                    <p class="text-[9px] mt-0.5" :class="roi10 >= 0 ? 'text-emerald-500' : 'text-red-400'"
                                       x-text="roi10 >= 0 ? 'Kârda ✓' : 'Zararda'"></p>
                                    <p class="text-[8px] text-gray-400 mt-1 border-t border-dashed border-gray-200 pt-0.5">(10 × Yıllık Kazanç - Bütçe)</p>
                                </div>
                            </div>
                        </div>

                        
                        <div class="bg-amber-50 border-t border-amber-100 px-4 py-2 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-amber-700">
                                <span class="font-semibold">Tahmini amortisman:</span>
                                <span x-text="amortisman"></span>
                            </p>
                        </div>
                    </div>
                </template>

                
                <div class="bg-indigo-50 px-4 py-2 border-t border-indigo-100">
                    <p class="text-xs text-indigo-600">
                        Puan formülü: <span class="font-mono font-semibold">round((Risk × Yıllık Kazanç × N) ÷ Bütçe)</span>
                        — <span x-text="'N = ' + yil_baz + ' yıl (seçtiğiniz ufuk)'"></span>.
                        Bütçe tek seferlik yatırım, kazanç yıllıktır.
                    </p>
                </div>
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
<?php $component->withAttributes(['x-on:click' => '$dispatch(\'close\')']); ?>İptal <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                <button type="submit" class="ms-3 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">Onayla ve Puanla</button>
            </div>
        </form>
    </div>
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





<?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'puan-duzenle-modal-'.e($iaa->id).'','focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'puan-duzenle-modal-'.e($iaa->id).'','focusable' => true]); ?>
    <div x-data="approvalForm({
        risk: '<?php echo e(old('risk', $iaa->risk)); ?>',
        kazanc_miktar: '<?php echo e(old('kazanc_miktar', $iaa->kazanc_miktar)); ?>',
        kazanc_birim: '<?php echo e(old('kazanc_birim', $iaa->kazanc_birim)); ?>',
        butce_miktar: '<?php echo e(old('butce_miktar', $iaa->butce_miktar)); ?>',
        butce_birim: '<?php echo e(old('butce_birim', $iaa->butce_birim)); ?>',
        yil_baz: '<?php echo e(old('yil_baz', $iaa->yil_baz)); ?>',
        oneren_kazanc_miktar: '<?php echo e($iaa->oneren_kazanc_miktar); ?>',
        oneren_kazanc_birim: '<?php echo e($iaa->oneren_kazanc_birim); ?>',
        oneren_butce_miktar: '<?php echo e($iaa->oneren_butce_miktar); ?>',
        oneren_butce_birim: '<?php echo e($iaa->oneren_butce_birim); ?>'
    })">
        <form method="post" action="<?php echo e(route('admin.iaa-yonetim.updateScore', $iaa)); ?>" class="p-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>

            <h2 class="text-lg font-medium text-gray-900">
                "<?php echo e(Str::limit($iaa->baslik, 30)); ?>" Puanını Düzenle
            </h2>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div>
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'risk_edit_'.e($iaa->id).'','value' => 'Risk (1-5 Arası)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'risk_edit_'.e($iaa->id).'','value' => 'Risk (1-5 Arası)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <select name="risk" x-model="risk" id="risk_edit_<?php echo e($iaa->id); ?>" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="1">1 (Çok Düşük)</option>
                        <option value="2">2 (Düşük)</option>
                        <option value="3">3 (Orta)</option>
                        <option value="4">4 (Yüksek)</option>
                        <option value="5">5 (Çok Yüksek)</option>
                    </select>
                </div>
                <div></div>

                
                <div>
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'kazanc_miktar_edit_'.e($iaa->id).'','value' => 'Tahmini Yıllık Kazanç']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'kazanc_miktar_edit_'.e($iaa->id).'','value' => 'Tahmini Yıllık Kazanç']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['type' => 'number','step' => '0.01','name' => 'kazanc_miktar','xModel' => 'kazanc_miktar','id' => 'kazanc_miktar_edit_'.e($iaa->id).'','class' => 'mt-1 block w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','step' => '0.01','name' => 'kazanc_miktar','x-model' => 'kazanc_miktar','id' => 'kazanc_miktar_edit_'.e($iaa->id).'','class' => 'mt-1 block w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
                </div>
                <div>
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'kazanc_birim_edit_'.e($iaa->id).'','value' => 'Birim']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'kazanc_birim_edit_'.e($iaa->id).'','value' => 'Birim']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <select name="kazanc_birim" x-model="kazanc_birim" id="kazanc_birim_edit_<?php echo e($iaa->id); ?>" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <?php $__currentLoopData = $paraBirimleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $birim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($birim); ?>"><?php echo e($birim); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div>
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'butce_miktar_edit_'.e($iaa->id).'','value' => 'Tahmini Bütçe (Tek Seferlik)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'butce_miktar_edit_'.e($iaa->id).'','value' => 'Tahmini Bütçe (Tek Seferlik)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['type' => 'number','step' => '0.01','name' => 'butce_miktar','xModel' => 'butce_miktar','id' => 'butce_miktar_edit_'.e($iaa->id).'','class' => 'mt-1 block w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','step' => '0.01','name' => 'butce_miktar','x-model' => 'butce_miktar','id' => 'butce_miktar_edit_'.e($iaa->id).'','class' => 'mt-1 block w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
                </div>
                <div>
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'butce_birim_edit_'.e($iaa->id).'','value' => 'Birim']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'butce_birim_edit_'.e($iaa->id).'','value' => 'Birim']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <select name="butce_birim" x-model="butce_birim" id="butce_birim_edit_<?php echo e($iaa->id); ?>" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                         <?php $__currentLoopData = $paraBirimleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $birim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($birim); ?>"><?php echo e($birim); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="col-span-2">
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'yil_baz_edit_input_'.e($iaa->id).'','value' => 'Puanlama Süresi (Yıl)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'yil_baz_edit_input_'.e($iaa->id).'','value' => 'Puanlama Süresi (Yıl)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <div class="mt-1 flex items-center gap-3">
                        
                        <div class="grid grid-cols-4 gap-2 flex-grow">
                            <?php $__currentLoopData = [1 => '1 Yıl', 3 => '3 Yıl', 5 => '5 Yıl', 10 => '10 Yıl']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yil => $etiket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" 
                                    @click="yil_baz = <?php echo e($yil); ?>" 
                                    class="text-center px-2 py-2 rounded-lg border-2 text-xs font-semibold transition-all focus:outline-none"
                                    :class="yil_baz == <?php echo e($yil); ?> 
                                        ? 'bg-indigo-600 border-indigo-600 text-white shadow-md' 
                                        : 'bg-white border-gray-200 text-gray-600 hover:border-indigo-300'">
                                <?php echo e($etiket); ?>

                            </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        
                        <div class="w-24">
                            <input type="number" min="1" max="50" x-model.number="yil_baz" id="yil_baz_edit_input_<?php echo e($iaa->id); ?>"
                                   class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs py-2 text-center" 
                                   placeholder="Diğer" />
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Seçtiğiniz süre boyunca elde edilecek toplam kazanç esas alınarak puan hesaplanır.</p>
                    <input type="hidden" name="yil_baz" :value="yil_baz">
                </div>
            </div>

            
            <div class="mt-5 rounded-xl overflow-hidden border border-indigo-200">
                
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span class="text-sm font-semibold text-white">
                            Hesaplanan Öneri Puanı
                            <template x-if="parseFloat(kazanc_miktar) > 0 && parseFloat(butce_miktar) > 0">
                                <span class="text-indigo-200 text-xs font-normal" x-text="'(' + yil_baz + ' yıl baz alındı)'" ></span>
                            </template>
                        </span>
                    </div>
                    <div class="text-right">
                        <template x-if="dynamicPuan !== null">
                            <span class="text-2xl font-extrabold text-white" x-text="dynamicPuan"></span>
                        </template>
                        <template x-if="dynamicPuan === null">
                            <span class="text-sm text-indigo-200 font-medium">Tüm alanları doldurun</span>
                        </template>
                    </div>
                </div>

                
                <template x-if="parseFloat(kazanc_miktar) > 0 && parseFloat(butce_miktar) > 0">
                    <div>
                        
                        <div class="bg-white px-4 pt-3 pb-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Kümülatif Kazanç Projeksiyonu</p>
                            <div class="grid grid-cols-3 gap-2">
                                
                                <div class="rounded-lg p-2 text-center transition-all"
                                     :class="[
                                        roi1 >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200',
                                        yil_baz == 1 ? 'ring-2 ring-indigo-500 ring-offset-1' : ''
                                     ]">
                                    <p class="text-[10px] font-semibold uppercase" :class="roi1 >= 0 ? 'text-green-600' : 'text-red-600'">1 Yıl</p>
                                    <p class="text-sm font-extrabold" :class="roi1 >= 0 ? 'text-green-800' : 'text-red-700'"
                                       x-text="(roi1 >= 0 ? '+' : '') + roi1.toLocaleString('tr-TR') + ' ' + kazanc_birim"></p>
                                    <p class="text-[9px] mt-0.5" :class="roi1 >= 0 ? 'text-green-500' : 'text-red-400'"
                                       x-text="roi1 >= 0 ? 'Kârda ✓' : 'Zararda'"></p>
                                    <p class="text-[8px] text-gray-400 mt-1 border-t border-dashed border-gray-200 pt-0.5">(1 × Yıllık Kazanç - Bütçe)</p>
                                </div>
                                
                                <div class="rounded-lg p-2 text-center transition-all"
                                     :class="[
                                        roi5 >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200',
                                        yil_baz == 5 ? 'ring-2 ring-indigo-500 ring-offset-1' : ''
                                     ]">
                                    <p class="text-[10px] font-semibold uppercase" :class="roi5 >= 0 ? 'text-green-600' : 'text-red-600'">5 Yıl</p>
                                    <p class="text-sm font-extrabold" :class="roi5 >= 0 ? 'text-green-800' : 'text-red-700'"
                                       x-text="(roi5 >= 0 ? '+' : '') + roi5.toLocaleString('tr-TR') + ' ' + kazanc_birim"></p>
                                    <p class="text-[9px] mt-0.5" :class="roi5 >= 0 ? 'text-green-500' : 'text-red-400'"
                                       x-text="roi5 >= 0 ? 'Kârda ✓' : 'Zararda'"></p>
                                    <p class="text-[8px] text-gray-400 mt-1 border-t border-dashed border-gray-200 pt-0.5">(5 × Yıllık Kazanç - Bütçe)</p>
                                </div>
                                
                                <div class="rounded-lg p-2 text-center transition-all"
                                     :class="[
                                        roi10 >= 0 ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border border-red-200',
                                        yil_baz == 10 ? 'ring-2 ring-indigo-500 ring-offset-1' : ''
                                     ]">
                                    <p class="text-[10px] font-semibold uppercase" :class="roi10 >= 0 ? 'text-emerald-600' : 'text-red-600'">10 Yıl</p>
                                    <p class="text-sm font-extrabold" :class="roi10 >= 0 ? 'text-emerald-800' : 'text-red-700'"
                                       x-text="(roi10 >= 0 ? '+' : '') + roi10.toLocaleString('tr-TR') + ' ' + kazanc_birim"></p>
                                    <p class="text-[9px] mt-0.5" :class="roi10 >= 0 ? 'text-emerald-500' : 'text-red-400'"
                                       x-text="roi10 >= 0 ? 'Kârda ✓' : 'Zararda'"></p>
                                    <p class="text-[8px] text-gray-400 mt-1 border-t border-dashed border-gray-200 pt-0.5">(10 × Yıllık Kazanç - Bütçe)</p>
                                </div>
                            </div>
                        </div>

                        
                        <div class="bg-amber-50 border-t border-amber-100 px-4 py-2 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-amber-700">
                                <span class="font-semibold">Tahmini amortisman:</span>
                                <span x-text="amortisman"></span>
                            </p>
                        </div>
                    </div>
                </template>

                
                <div class="bg-indigo-50 px-4 py-2 border-t border-indigo-100">
                    <p class="text-xs text-indigo-600">
                        Puan formülü: <span class="font-mono font-semibold">round((Risk × Yıllık Kazanç × N) ÷ Bütçe)</span>
                        — <span x-text="'N = ' + yil_baz + ' yıl (seçtiğiniz ufuk)'"></span>.
                        Bütçe tek seferlik yatırım, kazanç yıllıktır.
                    </p>
                </div>
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
<?php $component->withAttributes(['x-on:click' => '$dispatch(\'close\')']); ?>İptal <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['class' => 'ml-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ml-3']); ?>Puanı Güncelle <?php echo $__env->renderComponent(); ?>
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
    </div>
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


<?php if (! $__env->hasRenderedOnce('14a40773-c3c1-496a-a924-d3633d51c2fd')): $__env->markAsRenderedOnce('14a40773-c3c1-496a-a924-d3633d51c2fd'); ?>
    <?php $__env->startPush('scripts'); ?>
    <script>
        function approvalForm(iaaData) {
            return {
                risk: iaaData.risk || 3,
                kazanc_miktar: iaaData.kazanc_miktar || '',
                kazanc_birim: iaaData.kazanc_birim || '<?php echo e($paraBirimleri[0] ?? 'TL'); ?>',
                butce_miktar: iaaData.butce_miktar || '',
                butce_birim: iaaData.butce_birim || '<?php echo e($paraBirimleri[0] ?? 'TL'); ?>',
                yil_baz: parseInt(iaaData.yil_baz) || 5,
                
                // Orijinal verileri sakla
                originalData: iaaData,

                // Dinamik puan hesapla: round((risk × yıllık_kazanç × yil_baz) / bütçe)
                get dynamicPuan() {
                    const r = parseFloat(this.risk) || 0;
                    const k = parseFloat(this.kazanc_miktar) || 0;
                    const b = parseFloat(this.butce_miktar) || 0;
                    const y = parseFloat(this.yil_baz) || 5;
                    if (r > 0 && k > 0 && b > 0) {
                        return Math.round((r * k * y) / b);
                    }
                    return null;
                },

                // ROI Hesaplamaları
                // Bütçe tek seferlik yatırım, kazanç yıllık gelir
                // Net Getiri (n yıl) = (n × yıllık_kazanç) - bütçe
                get roi1() {
                    const k = parseFloat(this.kazanc_miktar) || 0;
                    const b = parseFloat(this.butce_miktar) || 0;
                    return Math.round((1 * k) - b);
                },
                get roi5() {
                    const k = parseFloat(this.kazanc_miktar) || 0;
                    const b = parseFloat(this.butce_miktar) || 0;
                    return Math.round((5 * k) - b);
                },
                get roi10() {
                    const k = parseFloat(this.kazanc_miktar) || 0;
                    const b = parseFloat(this.butce_miktar) || 0;
                    return Math.round((10 * k) - b);
                },

                // Amortisman: Kaç yılda kendini amorti eder?
                // Formül: ceil(bütçe / yıllık_kazanç)
                get amortisman() {
                    const k = parseFloat(this.kazanc_miktar) || 0;
                    const b = parseFloat(this.butce_miktar) || 0;
                    if (k <= 0) return 'Hesaplanamadı (kazanç girilmedi)';
                    if (b <= 0) return 'Hesaplanamadı (bütçe girilmedi)';
                    const yil = Math.ceil(b / k);
                    if (yil === 1) return 'İlk yılda kendini amorti eder ✓';
                    if (yil <= 3) return `Yaklaşık ${yil} yılda kendini amorti eder`;
                    if (yil <= 7) return `Yaklaşık ${yil} yılda kendini amorti eder (Uzun vadeli proje)`;
                    return `${yil} yıl+ (Çok uzun vadeli yatırım)`;
                },

                get hasProposerValues() {
                    const kazanc = parseFloat(this.originalData.oneren_kazanc_miktar) || 0;
                    const butce = parseFloat(this.originalData.oneren_butce_miktar) || 0;
                    return kazanc > 0 || butce > 0;
                },
                useProposerValues() {
                    this.kazanc_miktar = this.originalData.oneren_kazanc_miktar;
                    this.kazanc_birim = this.originalData.oneren_kazanc_birim;
                    this.butce_miktar = this.originalData.oneren_butce_miktar;
                    this.butce_birim = this.originalData.oneren_butce_birim;
                }
            }
        }
    </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/iaa-yonetim/partials/onayla-modal.blade.php ENDPATH**/ ?>