<div class="space-y-8 animate-fade-in-up">
    
    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        
        <a href="<?php echo e(route('admin.iaa-yonetim.index')); ?>" onclick="localStorage.setItem('activeTab', 'onay-bekleyenler')" 
           class="group relative bg-gradient-to-br from-purple-600 to-indigo-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            <div class="relative z-10 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <?php if($stats['bolum_onay_sayisi'] > 0): ?>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold animate-pulse"><?php echo e($stats['bolum_onay_sayisi']); ?> Bekleyen</span>
                    <?php endif; ?>
                </div>
                <p class="text-indigo-100 text-sm font-medium">Onayınızı Bekleyen Projeler</p>
                <h3 class="text-4xl font-bold mt-1"><?php echo e($stats['bolum_onay_sayisi']); ?></h3>
                <div class="mt-4 flex items-center text-xs text-indigo-200 font-medium group-hover:text-white transition-colors">
                    İncele ve Onayla <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </div>
            </div>
        </a>

        
        <a href="<?php echo e(route('admin.sikayetler.index')); ?>" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group">
            
            <div class="flex items-center justify-between mb-4">
                
                <div class="p-3 bg-blue-50 group-hover:bg-blue-100 text-blue-600 transition-colors rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                </div>
                
                
                <div class="flex flex-col items-end gap-1">
                    <span class="text-blue-600 bg-blue-50 px-2 py-1 rounded text-xs font-bold border border-blue-100">
                        <?php echo e($stats['toplam_sikayet']); ?> Aktif Dosya
                    </span>
                    <?php if(($stats['bolum_onay_sayisi'] ?? 0) > 0): ?>
                        <span class="bg-purple-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">
                            <?php echo e($stats['bolum_onay_sayisi']); ?> Onay Bekleyen
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="flex flex-col">
                
                <h3 class="text-3xl font-bold text-gray-800">
                    <?php echo e(Auth::user()->yonettigiSikayetKategorileri->count()); ?>

                </h3>
                <span class="text-gray-500 text-sm font-medium">Sorumlu Olduğunuz Alan</span>
            </div>
            
            
            <div class="mt-4 pt-3 border-t border-gray-50">
                <p class="text-gray-400 text-[10px] uppercase font-bold mb-2">Yönetilen Kategoriler</p>
                <div class="flex flex-wrap gap-1.5">
                    <?php $__currentLoopData = Auth::user()->yonettigiSikayetKategorileri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 truncate max-w-[150px]">
                            <?php echo e($kategori->ad); ?>

                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route('admin.sikayetler.index')); ?>" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-yellow-200 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-yellow-50 group-hover:bg-yellow-100 text-yellow-600 transition-colors rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-gray-400 text-xs group-hover:text-yellow-600 transition-colors">Listeyi Aç →</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['islemdeki_sikayet']); ?></h3>
            <p class="text-gray-500 text-sm mt-1">Çözüm bekleyen şikayetler</p>
            <div class="mt-4 w-full bg-gray-100 rounded-full h-1.5">
                <?php 
                    $oran = $stats['toplam_sikayet'] > 0 ? ($stats['islemdeki_sikayet'] / $stats['toplam_sikayet']) * 100 : 0; 
                ?>
                <div class="bg-yellow-400 h-1.5 rounded-full" style="width: <?php echo e($oran); ?>%"></div>
            </div>
        </a>

        <a href="<?php echo e(route('admin.sikayetler.index')); ?>" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-green-200 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-50 group-hover:bg-green-100 text-green-600 transition-colors rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-gray-400 text-xs group-hover:text-green-600 transition-colors">Listeyi Aç →</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['cozulen_sikayet']); ?></h3>
            <p class="text-green-600 text-sm mt-1 font-medium flex items-center">
                <?php if($stats['toplam_sikayet'] > 0): ?>
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    %<?php echo e(round(($stats['cozulen_sikayet'] / $stats['toplam_sikayet']) * 100)); ?> Başarı Oranı
                <?php else: ?>
                    %0 Başarı
                <?php endif; ?>
            </p>
        </a>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                    Onayınızı Bekleyenler
                </h3>
                <a href="<?php echo e(route('admin.iaa-yonetim.index')); ?>" onclick="localStorage.setItem('activeTab', 'onay-bekleyenler')" class="text-xs text-purple-600 hover:underline font-semibold">Tümünü Yönet</a>
            </div>
            <div class="flex-1 overflow-y-auto max-h-80">
                <?php if($stats['onay_bekleyen_liste']->isNotEmpty()): ?>
                    <table class="w-full text-sm text-left">
                        <tbody class="divide-y divide-gray-100">
                            <?php $__currentLoopData = $stats['onay_bekleyen_liste']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-purple-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center font-bold text-xs">
                                                    #<?php echo e($proje->id); ?>

                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900 group-hover:text-purple-700 transition-colors"><?php echo e(Str::limit($proje->baslik, 40)); ?></p>
                                                <p class="text-xs text-gray-500"><?php echo e($proje->atananTakim->ad ?? 'Takım Yok'); ?> • <?php echo e($proje->updated_at->diffForHumans()); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-white border border-purple-200 rounded-lg text-xs font-bold text-purple-700 shadow-sm hover:bg-purple-50 transition-colors">
                                            İncele
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm">Şu an onayınızı bekleyen proje yok.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        Departman Şikayet Akışı <span class="text-xs font-normal text-gray-500 ml-1">(Son 5 Kayıt)</span>
                    </h3>
                    
                    
                    <a href="<?php echo e(route('admin.sikayetler.index')); ?>" class="text-xs text-blue-600 hover:underline font-semibold">
                        Tümünü Gör &rarr;
                    </a>
                </div>
            <div class="flex-1 overflow-y-auto max-h-80">
                <?php if($stats['son_departman_sikayetleri']->isNotEmpty()): ?>
                    <div class="space-y-0">
                        <?php $__currentLoopData = $stats['son_departman_sikayetleri']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>" class="block px-6 py-4 border-b border-gray-50 hover:bg-gray-50 transition-colors group">
                                <div class="flex justify-between items-start mb-1">
                                    
                                    <?php echo $sikayet->musteri_durum_badge; ?>

                                    <span class="text-xs text-gray-400"><?php echo e($sikayet->created_at->format('d.m.Y')); ?></span>
                                </div>
                                <p class="text-sm font-medium text-gray-800 mb-1 group-hover:text-blue-600 transition-colors"><?php echo e(Str::limit($sikayet->musteri_sikayet_konusu, 60)); ?></p>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span>Müşteri: <?php echo e(Str::limit($sikayet->musteri_adi, 15)); ?></span>
                                    <?php if($sikayet->iaaProjesi && in_array($sikayet->iaaProjesi->durum, ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Talep Olarak Kapatıldı'])): ?>
                                        <span class="ml-1 scale-75 origin-left">
                                            <?php echo $sikayet->iaaProjesi->durum_etiketi; ?>

                                        </span>
                                    <?php endif; ?>
                                    <span>•</span>
                                    <span>Takım: <?php echo e($sikayet->cozumTakimi->ad ?? 'Atanmadı'); ?></span>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                        <p class="text-sm">Departmanınızda kayıtlı şikayet bulunmuyor.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
    
    
    <?php if(isset($iadeVerileri)): ?>
        <?php echo $__env->make('dashboard.partials.iadeler-tablosu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    
    <div class="pt-4 border-t border-gray-200">
        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Diğer Araçlar</h4>
        
        <?php echo $__env->make('dashboard.partials.standart-kullanici', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/dashboard/partials/bolum-yoneticisi.blade.php ENDPATH**/ ?>