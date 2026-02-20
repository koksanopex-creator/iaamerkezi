<?php
    // 1. Yetki ve Veri Hazırlığı
    $isLeader = ($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || auth()->user()->hasRole('Superadmin');
    
    // GÜVENLİ ERİŞİM: musteriSikayeti null ise hata vermemesi için
    $sikayet = $iaa->musteriSikayeti; 
    
    // Şikayet varsa bölüm ID'sini al, yoksa 0 (Tanımları çekmek için)
    $bolumId = $sikayet ? ($sikayet->sikayetKategori->bolum_id ?? 0) : 0;
    
    // Tanımları Çek (Sadece Şikayet Varsa Anlamlıdır)
    $tanimlar = collect();
    $urunGruplari = collect();
    $iadeSebepleri = collect();
    $birimler = collect();

    if ($sikayet) {
        $tanimlar = \App\Models\IadeTanimi::where('bolum_id', $bolumId)->where('aktif', true)->get()->groupBy('tip');
        $urunGruplari = $tanimlar['urun_grubu'] ?? collect();
        $iadeSebepleri = $tanimlar['iade_sebebi'] ?? collect();
        $birimler = $tanimlar['birim'] ?? collect();
    }

    // Mevcut İade Var mı? (Revizyon için)
    $mevcutIade = ($sikayet && $sikayet->iadeler) ? $sikayet->iadeler->first() : null;
    
    // Varsayılan: Eğer iade varsa toggle açık (true), yoksa kapalı (false)
    $iadeVarMi = $mevcutIade ? 'true' : 'false';
?>


<?php if($progressPercentage == 100 && in_array($iaa->durum, ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Çalışılıyor'])): ?>

    
    <?php if(!$isLeader): ?>
        <div class="mt-8 mb-8 p-6 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-4 animate-fade-in-up">
            <div class="p-3 bg-blue-100 rounded-full shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h4 class="text-lg font-bold text-blue-900">Süreç Liderde</h4>
                <p class="text-sm text-blue-700">
                    Tüm adımlar tamamlandı. Proje Lideri (<?php echo e($iaa->atananTakim->lider->name ?? 'Lider'); ?>) kapanış işlemini gerçekleştirecektir.
                </p>
            </div>
        </div>
    
    <?php else: ?>
        
        
        <div class="mt-8 mb-8 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            
            
            <div class="p-5 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100 flex items-center gap-3">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900">Proje Kapanış İşlemleri</h4>
                    <p class="text-xs text-gray-500">Projeyi tamamlamak için aşağıdaki adımları takip edin.</p>
                </div>
            </div>

            <div class="p-6">

                
                <?php if($iaa->musteriSikayeti): ?>
                    
                    <div x-data="{ iadeVar: <?php echo e($iadeVarMi); ?> }">
                        
                        
                        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6 transition-colors hover:border-indigo-200">
                            <div>
                                <span class="block text-sm font-bold text-gray-900">Bu proje kapsamında ürün iadesi alındı mı?</span>
                                <span class="text-xs text-gray-500" x-text="iadeVar ? 'Evet, iade detaylarını giriyorum.' : 'Hayır, iadesiz kapatılacak.'"></span>
                            </div>
                            
                            <button type="button" 
                                    @click="iadeVar = !iadeVar"
                                    :class="iadeVar ? 'bg-red-500' : 'bg-gray-300'"
                                    class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                <span aria-hidden="true" 
                                      :class="iadeVar ? 'translate-x-5' : 'translate-x-0'"
                                      class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                                </span>
                            </button>
                        </div>

                        
                        <div x-show="!iadeVar" class="bg-green-50 border border-green-200 rounded-xl p-5 text-center space-y-4">
                            <div class="inline-flex p-3 bg-green-100 rounded-full text-green-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-green-800">İadesiz Tamamlama</h5>
                                <p class="text-xs text-green-600 mt-1">Herhangi bir iade girişi yapılmadan proje onaya sunulacaktır.</p>
                            </div>
                            
                            <form action="<?php echo e(route('proje.completeWithoutReturn', $iaa->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-sm hover:shadow-md">
                                    İadesiz Kapat ve Gönder
                                </button>
                            </form>

                            <?php if($mevcutIade): ?>
                                <div class="mt-4 pt-4 border-t border-green-200">
                                    <p class="text-xs text-red-500 mb-2 font-bold">Uyarı: Sistemde kayıtlı eski bir iade bilgisi var.</p>
                                    <form action="<?php echo e(route('proje.deleteReturnInfo', $iaa->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-xs text-red-600 underline hover:text-red-800">Mevcut İade Kaydını Sil ve Devam Et</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>

                        
                        <div x-show="iadeVar" x-transition class="bg-white border border-red-100 rounded-xl p-5 shadow-sm">
                            <form action="<?php echo e(route('proje.completeWithReturn', $iaa->id)); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">İade Tarihi</label>
                                            <input type="date" name="iade_tarihi" value="<?php echo e(($mevcutIade && $mevcutIade->iade_tarihi) ? $mevcutIade->iade_tarihi->format('Y-m-d') : date('Y-m-d')); ?>" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Ürün Grubu</label>
                                            <select name="urun_turu" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" required>
                                                <option value="">Seçiniz...</option>
                                                <?php $__currentLoopData = $urunGruplari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($item->deger); ?>" <?php echo e(($mevcutIade && $mevcutIade->urun_turu == $item->deger) ? 'selected' : ''); ?>><?php echo e($item->deger); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">İade Sebebi</label>
                                            <select name="iade_sebebi" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" required>
                                                <option value="">Seçiniz...</option>
                                                <?php $__currentLoopData = $iadeSebepleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($item->deger); ?>" <?php echo e(($mevcutIade && $mevcutIade->iade_sebebi == $item->deger) ? 'selected' : ''); ?>><?php echo e($item->deger); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Toplam Parti</label>
                                                <input type="number" step="0.01" name="toplam_parti_miktari" value="<?php echo e($mevcutIade->toplam_parti_miktari ?? ''); ?>" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="Toplam">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">İade Miktarı</label>
                                                <input type="number" step="0.01" name="miktar" value="<?php echo e($mevcutIade->miktar ?? ''); ?>" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="İade" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Birim</label>
                                            <select name="birim" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" required>
                                                <option value="">Seçiniz...</option>
                                                <?php $__currentLoopData = $birimler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($item->deger); ?>" <?php echo e(($mevcutIade && $mevcutIade->birim == $item->deger) ? 'selected' : ''); ?>><?php echo e($item->deger); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Dosya (Opsiyonel)</label>
                                            <input type="file" name="dosya" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Açıklama</label>
                                    <textarea name="aciklama" rows="2" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="Eklemek istediğiniz notlar..."><?php echo e($mevcutIade->aciklama ?? ''); ?></textarea>
                                </div>

                                <button type="submit" class="w-full py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition shadow-lg hover:shadow-red-500/30 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <?php echo e($mevcutIade ? 'Güncelle ve Onaya Gönder' : 'Kaydet ve Onaya Gönder'); ?>

                                </button>
                            </form>
                        </div>
                    </div>

                
                <?php else: ?>
                    
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-blue-900 text-lg">Projeyi Tamamla</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    Tüm adımlar tamamlandı. Projeyi sonlandırıp <strong>Yönetici Onayına</strong> göndermek için aşağıdaki butona tıklayın.
                                </p>
                                <p class="text-xs text-blue-500 mt-2 italic">(Not: Proje Onaylandıktan Sonra Düzenleme Yapamazsınız.)</p>
                            </div>
                        </div>

                        
                        <form action="<?php echo e(route('proje.completeWithoutReturn', $iaa->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center gap-2 whitespace-nowrap">
                                <span>Tamamla ve Onaya Gönder</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </form>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    <?php endif; ?>

<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_project-completion.blade.php ENDPATH**/ ?>