<div x-data="{ showModal: $wire.entangle('showModal') }" 
     x-show="showModal" 
     @keydown.escape.window="showModal = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">

    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        
        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
             @click="showModal = false" aria-hidden="true">
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            
            <div class="flex items-center justify-between pb-4 border-b">
                <div>
                    <h3 class="text-xl font-bold text-gray-900" id="modal-title">
                        Şikayet Yönetim Paneli
                    </h3>
                    <p class="text-sm text-gray-500">Şikayet No: <span class="font-semibold text-indigo-600">#<?php echo e($sikayetId); ?></span> - <?php echo e($musteriAdi); ?></p>
                </div>
                <button @click="showModal = false" class="p-1 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!--[if BLOCK]><![endif]--><?php if($ek_sure_talep_durumu == 'Talep Edildi'): ?>
                <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 3.001-1.742 3.001H4.42c-1.53 0-2.493-1.667-1.743-3.001l5.58-9.92zM10 5a1 1 0 011 1v3a1 1 0 11-2 0V6a1 1 0 011-1zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-yellow-800">Ek Süre Talebi Var!</p>
                            <p class="text-sm text-yellow-700 mt-1">
                                <strong>Gerekçe:</strong> <?php echo e($ek_sure_talep_aciklamasi ?? 'Belirtilmemiş'); ?>

                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <form wire:submit.prevent="save" class="mt-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <label for="atanan_cozum_takimi_id" class="block text-sm font-medium text-gray-700">Çözüm Takımı Ata</label>
                        <select wire:model.defer="atanan_cozum_takimi_id" id="atanan_cozum_takimi_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Takım Seçilmedi --</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $cozumTakimlari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $ad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($ad); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>
                    
                    <div>
                        <label for="musteri_cozum_son_tarihi" class="block text-sm font-medium text-gray-700">Çözüm İçin Son Tarih</label>
                        <input type="datetime-local" wire:model.defer="musteri_cozum_son_tarihi" id="musteri_cozum_son_tarihi" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Müşteri Şikayeti Kurulu')): ?>
                            <!--[if BLOCK]><![endif]--><?php if (! (auth()->user()->hasRole('Superadmin'))): ?>
                                <p class="mt-1 text-xs text-gray-500">Boş bırakırsanız otomatik 72 saat atanır.</p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
                <div class="pt-6 border-t">
                    <h4 class="text-lg font-medium text-gray-900">Çözüm Puanlaması (Sadece Superadmin)</h4>
                    <p class="text-sm text-gray-500">
                        Şikayet "Kapatıldı" durumuna geçtiğinde takıma dağıtılacak puanı belirleyin.
                        <span class="font-semibold text-gray-700">Formül: (Etki + Karmaşıklık) * Öncelik * <?php echo e($cozumPuaniCarpan); ?></span>
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label for="etki_puani" class="block text-sm font-medium text-gray-700">Şikayet Etkisi</label>
                            <select wire:model.defer="etki_puani" id="etki_puani" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Puan Seçilmedi --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $etkiPuanlari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $puan => $ad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($puan); ?>"><?php echo e($ad); ?> (<?php echo e($puan); ?> Puan)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div>
                            <label for="karmasiklik_puani" class="block text-sm font-medium text-gray-700">Çözüm Karmaşıklığı</label>
                            <select wire:model.defer="karmasiklik_puani" id="karmasiklik_puani" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Puan Seçilmedi --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $karmasiklikPuanlari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $puan => $ad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($puan); ?>"><?php echo e($ad); ?> (<?php echo e($puan); ?> Puan)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <div class="flex justify-between items-center mt-8 pt-6 border-t">
                    
                    <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
                    <div>
                        <button type="button" wire:click="removeAtama" 
                                wire:confirm="Atamayı kaldırmak ve durumu 'Yeni'ye döndürmek istediğinizden emin misiniz?"
                                wire:loading.attr="disabled"
                                wire:target="removeAtama"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="inline-flex items-center px-4 py-2 bg-red-50 border border-red-300 rounded-lg font-semibold text-sm text-red-700 hover:bg-red-100 transition">
                            <span wire:loading.remove wire:target="removeAtama">Atamayı Kaldır</span>
                            <span wire:loading wire:target="removeAtama">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-red-700 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                İşleniyor...
                            </span>
                        </button>
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div class="flex gap-3 <?php if (! (auth()->user()->hasRole('Superadmin'))): ?> w-full justify-end <?php endif; ?>">
                        <button type="button" @click="showModal = false" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 transition">
                            İptal
                        </button>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="save"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="inline-flex items-center ml-3 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 transition">
                            <span wire:loading.remove wire:target="save">Güncelle ve Kaydet</span>
                            <span wire:loading wire:target="save">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Kaydediliyor...
                            </span>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/sikayet-triyaj-modal.blade.php ENDPATH**/ ?>