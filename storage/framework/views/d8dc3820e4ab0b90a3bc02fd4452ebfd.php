<?php if(isset($isComplaintProject) && $isComplaintProject && $iaa->musteriSikayeti): ?>
    <?php
        // Yetki Kontrolü: Sadece Lider ve Superadmin düzenleyebilir
        $isLeader = ($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id);
        $isSuperAdmin = auth()->user()->hasRole('Superadmin');

        $canEditDetails = $isLeader || $isSuperAdmin;

        // Kısıtlama: Onayda veya Tamamlandıysa Lider DÜZENLEYEMEZ (Superadmin hariç)
        $kilitliDurumlar = [
            'Bölüm Onayı Bekliyor',
            'Yönetici Onayı Bekliyor',
            'Tamamlandı',
            'Talep Olarak Kapatıldı',
            'hatali_bildirim_olarak_kapatildi'
        ];
        if (!$isSuperAdmin && in_array($iaa->durum, $kilitliDurumlar)) {
            $canEditDetails = false;
        }

        // TAMAMEN KAPALI DURUMLAR (Superadmin dahil düzenleyemesin isteniyorsa - Opsiyonel ama talep bu yönde)
        if (in_array($iaa->durum, ['Tamamlandı', 'Talep Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])) {
            $canEditDetails = false;
        }

        // Veri Hazırlığı
        $technicalDetails = $iaa->musteriSikayeti->teknikDetaylar->map(function ($detay) {
            return [
                'lot_no' => $detay->lot_no,
                'machine_id' => $detay->machine_id,
                'genel_hammadde_id' => $detay->genel_hammadde_id,
                'urun_versiyonu_id' => $detay->urun_versiyonu_id
            ];
        })->toArray();

        // Eğer veritabanı boşsa (eski veri yoksa) en az 1 boş satır ekle
        if (empty($technicalDetails)) {
            $technicalDetails = [
                [
                    'lot_no' => $iaa->musteriSikayeti->lot_no, // Varsa eski sütundan al
                    'machine_id' => $iaa->musteriSikayeti->machine_id,
                    'genel_hammadde_id' => $iaa->musteriSikayeti->genel_hammadde_id,
                    'urun_versiyonu_id' => $iaa->musteriSikayeti->urun_versiyonu_id
                ]
            ];
        }

        // Eğer validation hatası varsa old() verilerini kullan
        if (old('lot_no')) {
            $technicalDetails = [];
            foreach (old('lot_no') as $key => $value) {
                $technicalDetails[] = [
                    'lot_no' => old('lot_no.' . $key),
                    'machine_id' => old('machine_id.' . $key),
                    'genel_hammadde_id' => old('genel_hammadde_id.' . $key),
                    'urun_versiyonu_id' => old('urun_versiyonu_id.' . $key),
                ];
            }
        }
    ?>

    <div class="w-full mt-6">
        <div class="bg-gradient-to-br from-white to-gray-50 p-6 rounded-xl shadow-lg border border-gray-200">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Şikayet Teknik Detayları</h3>
                        <p class="text-xs text-gray-500">Lot, Makine, Hammadde ve Versiyon bilgileri</p>
                    </div>
                </div>

                <?php if($canEditDetails): ?>
                    <div
                        class="text-xs text-indigo-600 font-medium bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Düzenleme Modu Aktif
                    </div>
                <?php endif; ?>
            </div>

            <?php if($canEditDetails): ?>
                
                <form action="<?php echo e(route('proje.update-complaint-details', $iaa->id)); ?>" method="POST"
                    x-data="{ details: <?php echo e(json_encode($technicalDetails)); ?> }">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="space-y-4">
                        <template x-for="(detail, index) in details" :key="index">
                            <div class="relative bg-white p-4 rounded-lg border border-gray-200 shadow-sm group">

                                
                                <button type="button" @click="details.splice(index, 1)" x-show="details.length > 1"
                                    class="absolute top-2 right-2 text-gray-300 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>

                                <span
                                    class="absolute -top-2 -left-2 bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-indigo-200"
                                    x-text="'#' + (index + 1)"></span>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    
                                    <div>
                                        <label :for="'lot_no_' + index"
                                            class="block text-xs font-bold text-gray-500 uppercase mb-1">Lot Numarası</label>
                                        <input type="text" name="lot_no[]" :id="'lot_no_' + index" x-model="detail.lot_no"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium transition-colors"
                                            placeholder="Lot No Giriniz">
                                    </div>

                                    
                                    <div>
                                        <label :for="'machine_id_' + index"
                                            class="block text-xs font-bold text-gray-500 uppercase mb-1">Makine Bilgisi</label>
                                        <select name="machine_id[]" :id="'machine_id_' + index" x-model="detail.machine_id"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium transition-colors">
                                            <option value="">Seçiniz</option>
                                            <?php $__currentLoopData = $machines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $machine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($machine->id); ?>"><?php echo e($machine->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    
                                    <div>
                                        <label :for="'genel_hammadde_id_' + index"
                                            class="block text-xs font-bold text-gray-500 uppercase mb-1">Kullanılan
                                            Hammadde</label>
                                        <select name="genel_hammadde_id[]" :id="'genel_hammadde_id_' + index"
                                            x-model="detail.genel_hammadde_id"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium transition-colors">
                                            <option value="">Seçiniz</option>
                                            <?php $__currentLoopData = $hammaddeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hammadde): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($hammadde->id); ?>"><?php echo e($hammadde->ad); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    
                                    <div>
                                        <label :for="'urun_versiyonu_id_' + index"
                                            class="block text-xs font-bold text-gray-500 uppercase mb-1">Ürün Versiyonu</label>
                                        <select name="urun_versiyonu_id[]" :id="'urun_versiyonu_id_' + index"
                                            x-model="detail.urun_versiyonu_id"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium transition-colors">
                                            <option value="">Seçiniz</option>
                                            <?php $__currentLoopData = $versiyonlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $versiyon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($versiyon->id); ?>"><?php echo e($versiyon->ad); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        <button type="button"
                            @click="details.push({lot_no: '', machine_id: '', genel_hammadde_id: '', urun_versiyonu_id: ''})"
                            class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-xs font-bold transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Yeni Satır Ekle
                        </button>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Detayları Güncelle
                        </button>
                    </div>
                </form>
            <?php else: ?>
                
                <?php if($iaa->musteriSikayeti->teknikDetaylar->isNotEmpty()): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $iaa->musteriSikayeti->teknikDetaylar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative bg-white p-3 rounded-lg border border-gray-100">
                                <span class="absolute top-1 right-2 text-[10px] font-bold text-gray-300">#<?php echo e($loop->iteration); ?></span>
                                
                                <div>
                                    <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Lot Numarası</dt>
                                    <dd class="text-sm font-bold text-gray-900">
                                        <?php echo e($detay->lot_no ?? '-'); ?>

                                    </dd>
                                </div>

                                
                                <div>
                                    <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Makine Bilgisi</dt>
                                    <dd class="text-sm font-bold text-gray-900">
                                        <?php echo e($detay->machine->name ?? '-'); ?>

                                    </dd>
                                </div>

                                
                                <div>
                                    <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Kullanılan Hammadde</dt>
                                    <dd class="text-sm font-bold text-gray-900">
                                        <?php echo e($detay->genelHammadde->ad ?? '-'); ?>

                                    </dd>
                                </div>

                                
                                <div>
                                    <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Ürün Versiyonu</dt>
                                    <dd class="text-sm font-bold text-gray-900">
                                        <?php echo e($detay->urunVersiyonu->ad ?? '-'); ?>

                                    </dd>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                            <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Lot Numarası</dt>
                            <dd class="text-sm font-bold text-gray-900">
                                <?php echo e($iaa->musteriSikayeti->lot_no ?? '-'); ?>

                            </dd>
                        </div>

                        
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                            <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Makine Bilgisi</dt>
                            <dd class="text-sm font-bold text-gray-900">
                                <?php echo e($iaa->musteriSikayeti->machine->name ?? '-'); ?>

                            </dd>
                        </div>

                        
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                            <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Kullanılan Hammadde</dt>
                            <dd class="text-sm font-bold text-gray-900">
                                <?php echo e($iaa->musteriSikayeti->genelHammadde->ad ?? '-'); ?>

                            </dd>
                        </div>

                        
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                            <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Ürün Versiyonu</dt>
                            <dd class="text-sm font-bold text-gray-900">
                                <?php echo e($iaa->musteriSikayeti->urunVersiyonu->ad ?? '-'); ?>

                            </dd>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_technical-details.blade.php ENDPATH**/ ?>