<?php if(isset($bekleyenTakimIstekleri) && $bekleyenTakimIstekleri > 0): ?>
    <div class="mb-8 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-2xl shadow-xl overflow-hidden animate-pulse">
        <div class="p-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4 text-white">
                <div class="p-3 bg-white/20 rounded-full backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold">Yeni Katılma İstekleri Var!</h3>
                    <p class="text-yellow-100 font-medium">
                        Lideri olduğunuz takımlara katılmak isteyen <span class="bg-white/20 px-2 py-0.5 rounded text-white font-bold"><?php echo e($bekleyenTakimIstekleri); ?> kişi</span> onayınızı bekliyor.
                    </p>
                </div>
            </div>
            
            <a href="<?php echo e(route('takimlar.isteklerim')); ?>" class="w-full md:w-auto px-6 py-3 bg-white text-orange-600 font-bold rounded-xl shadow-lg hover:bg-orange-50 hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 whitespace-nowrap">
                <span>İstekleri Yönet</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </a>
        </div>
    </div>
<?php endif; ?>


    <?php if(isset($banaGelenDavetler) && $banaGelenDavetler > 0): ?>
        <div class="mb-4 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl shadow-xl overflow-hidden animate-pulse">
            <div class="p-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4 text-white">
                    <div class="p-3 bg-white/20 rounded-full backdrop-blur-sm">
                        
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Yeni Takım Davetiniz Var!</h3>
                        <p class="text-blue-100 font-medium">
                            Sizi takımlarına katmak isteyen <span class="bg-white/20 px-2 py-0.5 rounded text-white font-bold"><?php echo e($banaGelenDavetler); ?> takım</span> yanıtınızı bekliyor.
                        </p>
                    </div>
                </div>
                
                <a href="<?php echo e(route('takimlar.davetlerim')); ?>" class="w-full md:w-auto px-6 py-3 bg-white text-blue-600 font-bold rounded-xl shadow-lg hover:bg-blue-50 hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 whitespace-nowrap">
                    <span>Davetleri İncele</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>
        </div>
    <?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/dashboard/partials/waiting-requests.blade.php ENDPATH**/ ?>