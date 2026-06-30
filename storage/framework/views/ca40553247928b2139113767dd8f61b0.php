<div class="flex flex-wrap border-b border-gray-200">
    <?php if(!isset($isCustomerRep) || !$isCustomerRep): ?>
    <button @click="activeTab = 'performans'"
        :class="activeTab === 'performans' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        Performans & Grafik
    </button>

    <?php if($canViewActiveTasks): ?>
        <button @click="activeTab = 'gorevler'"
            :class="activeTab === 'gorevler' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                </path>
            </svg>
            <?php echo e(auth()->id() == $user->id ? 'Aktif Görevlerim' : 'Kişinin Aktif Görevleri'); ?>

            <?php if(count($activeTasks) > 0): ?>
                <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-bold bg-orange-100 text-orange-600">
                    <?php echo e(count($activeTasks)); ?>

                </span>
            <?php endif; ?>
        </button>
    <?php endif; ?>
    <?php endif; ?>

    <?php if($sikayetGormeYetkisi || (isset($isCustomerRep) && $isCustomerRep)): ?>
        <button @click="activeTab = 'sikayetler'"
            :class="activeTab === 'sikayetler' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center transition-all duration-200 rounded-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Şikayet Geçmişi
        </button>
    <?php endif; ?>

    <button @click="activeTab = 'yorumlar'"
        :class="activeTab === 'yorumlar' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
        </svg>
        Yorumlar & Geri Bildirim
    </button>

    
    <?php if(isset($isCustomerRep) && $isCustomerRep): ?>
        <button @click="activeTab = 'DigerCalisanlar'"
            :class="activeTab === 'DigerCalisanlar' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Şirketteki Diğer Çalışanlar
        </button>
    <?php endif; ?>

    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
    <button @click="activeTab = 'guvenlik'"
        :class="activeTab === 'guvenlik' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
        Güvenlik Logları (Admin)
    </button>
    <?php endif; ?>

    
    <?php
        // Yetki: Dosya sahibi, Bölüm Lideri, Üst Yönetim
        $canViewDiscipline = (Auth::id() == $user->id ||
            Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) ||
            (Auth::user()->hasRole('Bölüm Lideri') && Auth::user()->bolum_id == $user->bolum_id)) && 
            (!isset($isCustomerRep) || !$isCustomerRep); // Müşteri temsilcisi ise disiplin görmesin

        // Sayaç: Yetkisi varsa sayıyı çek, yoksa 0
        $disiplinCount = 0;
        if ($canViewDiscipline) {
            $disiplinCount = \App\Models\DisciplinaryCase::where('user_id', $user->id)->count();
        }
    ?>

    <?php if($canViewDiscipline): ?>
        <button @click="activeTab = 'disiplin'"
            :class="activeTab === 'disiplin' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center transition-all duration-200">

            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>

            Disiplin Geçmişi

            
            <span
                class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-bold <?php echo e($disiplinCount > 0 ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-500'); ?>">
                <?php echo e($disiplinCount); ?>

            </span>
        </button>
    <?php endif; ?>
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/profile/partials/show/tabs-nav.blade.php ENDPATH**/ ?>