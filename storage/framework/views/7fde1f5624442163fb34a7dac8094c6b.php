<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
    <a href="<?php echo e(route('iaa.havuz')); ?>" class="group relative bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/5 to-teal-600/5 rounded-2xl"></div>
        <div class="relative">
            <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Havuzdaki Öneriler</h3>
            <p class="text-3xl font-bold text-gray-900 mb-4"><?php echo e($stats['havuz_oneri_sayisi']); ?></p>
            <div class="space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $stats['son_havuz_onerileri']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-700 font-medium truncate flex-1 mr-2"><?php echo e(Str::limit($iaa->baslik, 20)); ?></span>
                        <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap"><?php echo e($iaa->created_at->format('d.m.Y')); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-sm italic">Havuzda öneri yok.</p>
                <?php endif; ?>
            </div>
            <div class="flex items-center mt-6 text-emerald-600 font-semibold text-sm group-hover:text-emerald-700">
                <span>Havuzu İncele</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </div>
    </a>

    <a href="<?php echo e(route('takimlar.index')); ?>" class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-indigo-600/5 rounded-2xl"></div>
        <div class="relative">
            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Takımlarım</h3>
            <p class="text-3xl font-bold text-gray-900 mb-4"><?php echo e($stats['takimlarim_sayisi']); ?></p>
            <div class="space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $stats['son_takimlarim']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-700 font-medium"><?php echo e($takim->ad); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-sm italic">Henüz bir takıma üye değilsiniz.</p>
                <?php endif; ?>
            </div>
            <div class="flex items-center mt-6 text-blue-600 font-semibold text-sm group-hover:text-blue-700">
                <span>Takımlarımı Yönet</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </div>
    </a>

    <a href="<?php echo e(route('takimlar.index')); ?>" class="group relative bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-violet-600/5 rounded-2xl"></div>
        <div class="relative">
            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Katılıma Açık Takımlar</h3>
            <p class="text-3xl font-bold text-gray-900 mb-4"><?php echo e($stats['acik_takim_sayisi']); ?></p>
            <div class="space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $stats['son_acik_takimlar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-700 font-medium"><?php echo e($takim->ad); ?></span>
                        <span class="text-purple-600 text-xs bg-purple-100 px-2 py-1 rounded-md font-medium"><?php echo e($takim->uyeler_count); ?> üye</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-sm italic">Katılıma açık takım yok.</p>
                <?php endif; ?>
            </div>
            <div class="flex items-center mt-6 text-purple-600 font-semibold text-sm group-hover:text-purple-700">
                <span>Takımlara Göz At</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </div>
    </a>

    <?php if(isset($stats['iaa_projelerim_count'])): ?>
        <a href="<?php echo e(route('iaa.takimProjeleri')); ?>" class="group relative bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-600/5 to-orange-600/5 rounded-2xl"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Devam Eden İAA Projelerim</h3>
                <p class="text-3xl font-bold text-gray-900 mb-4"><?php echo e($stats['iaa_projelerim_count']); ?></p>
                <div class="space-y-2">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['son_iaa_projelerim']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-700 font-medium truncate flex-1 mr-2"><?php echo e(Str::limit($proje->baslik, 20)); ?></span>
                            <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap"><?php echo e($proje->onaylanma_tarihi->format('d.m.Y')); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm italic">Devam eden İAA projeniz yok.</p>
                    <?php endif; ?>
                </div>
                <div class="flex items-center mt-6 text-yellow-600 font-semibold text-sm group-hover:text-yellow-700">
                    <span>Projelerime Git</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </div>
        </a>
    <?php endif; ?>

    <?php if(isset($stats['sikayet_projelerim_count'])): ?>
        <a href="<?php echo e(route('sikayet-gorevlerim.index')); ?>" class="group relative bg-gradient-to-br from-red-50 to-pink-50 border border-red-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-red-600/5 to-pink-600/5 rounded-2xl"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Devam Eden Şikayet Projelerim</h3>
                <p class="text-3xl font-bold text-gray-900 mb-4"><?php echo e($stats['sikayet_projelerim_count']); ?></p>
                <div class="space-y-2">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['son_sikayet_projelerim']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-700 font-medium truncate flex-1 mr-2"><?php echo e(Str::limit($proje->baslik, 20)); ?></span>
                            <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap"><?php echo e($proje->onaylanma_tarihi->format('d.m.Y')); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm italic">Devam eden şikayet projeniz yok.</p>
                    <?php endif; ?>
                </div>
                <div class="flex items-center mt-6 text-red-600 font-semibold text-sm group-hover:text-red-700">
                    <span>Projelerime Git</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </div>
        </a>
    <?php endif; ?>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/dashboard/partials/standart-kullanici.blade.php ENDPATH**/ ?>