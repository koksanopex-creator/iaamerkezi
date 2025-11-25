<?php if(isset($stats['lider_takim'])): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        <a href="<?php echo e(route('takimlar.show', $stats['lider_takim'])); ?>" class="group relative bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-violet-600/5 rounded-2xl"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Lideri Olduğum Takım</h3>
                <p class="text-3xl font-bold text-gray-900 mb-4"><?php echo e($stats['lider_takim']->ad); ?></p>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-700 font-medium">Toplam Üye</span>
                    <span class="text-purple-600 text-xs bg-purple-100 px-2 py-1 rounded-md font-medium"><?php echo e($stats['lider_takim']->uyeler_count); ?> üye</span>
                </div>
                <div class="flex items-center mt-6 text-purple-600 font-semibold text-sm group-hover:text-purple-700">
                    <span>Takımı Görüntüle</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route('admin.sikayetler.index')); ?>" class="group relative bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-cyan-600/5 rounded-2xl"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">İşlemde Olan Projeler</h3>
                <p class="text-3xl font-bold text-gray-900 mb-4"><?php echo e($stats['islemde_projeler_count']); ?></p>
                <div class="space-y-2">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['son_islemde_projeler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-700 font-medium truncate flex-1 mr-2"><?php echo e(Str::limit($proje->baslik, 20)); ?></span>
                            <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap"><?php echo e($proje->created_at->format('d.m.Y')); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm italic">İşlemde proje yok.</p>
                    <?php endif; ?>
                </div>
                <div class="flex items-center mt-6 text-blue-600 font-semibold text-sm group-hover:text-blue-700">
                    <span>Şikayet Paneline Git</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </div>
        </a>

        <div class="group relative bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 rounded-2xl p-6 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-green-600/5 to-emerald-600/5 rounded-2xl"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Çözülen Projeler</h3>
                <p class="text-3xl font-bold text-gray-900 mb-4"><?php echo e($stats['cozulen_projeler_count']); ?></p>
                <p class="text-gray-500 text-sm italic">Takımınızın tamamladığı toplam proje sayısı.</p>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="bg-gradient-to-br from-yellow-50 to-orange-100 border border-yellow-200 rounded-2xl shadow-lg overflow-hidden p-8">
        <h3 class="text-xl font-bold text-yellow-900 mb-2">Henüz Bir Takıma Lider Değilsiniz</h3>
        <p class="text-yellow-700">Şu anda bir müşteri şikayeti çözüm takımına lider olarak atanmamışsınız.</p>
    </div>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/dashboard/partials/cozum-lideri.blade.php ENDPATH**/ ?>