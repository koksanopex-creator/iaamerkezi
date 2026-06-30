<div x-show="activeTab === 'performans'" class="space-y-8 animate-fade-in-up">
    
    
    <div class="space-y-6">
        <h3 class="text-lg font-bold text-gray-800">İAA Çözme Performansı (Tüm Zamanlar)</h3>
        
        
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <?php if(empty($aylikPerformans)): ?>
                 <div class="flex flex-col items-center justify-center h-[350px] text-gray-400">
                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <p class="text-sm font-medium">Henüz tamamlanan İAA projesi bulunmamaktadır.</p>
                 </div>
            <?php else: ?>
                <div id="performanceChart"></div>
            <?php endif; ?>
        </div>

        
        <?php
            $canViewComplaintStats = auth()->id() == $user->id || auth()->user()->hasRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Müşteri Şikayeti Çözüm Lideri', 'Müşteri Şikayeti Kurulu', 'Bölüm Kalite Yöneticisi']);
        ?>

        <?php if($canViewComplaintStats): ?>
            <div class="space-y-4 mt-6">
                <h3 class="text-lg font-bold text-gray-800">Müşteri Şikayeti Çözme Performansı (Tüm Zamanlar)</h3>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                    <?php if(empty($sikayetPerformans)): ?>
                        <div class="flex flex-col items-center justify-center h-[300px] text-gray-400">
                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm font-medium">Henüz bir müşteri şikayeti çözümünde yer almadınız.</p>
                        </div>
                    <?php else: ?>
                        <div id="complaintChart"></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200" x-data="{ limit: 5, total: <?php echo e($sonAktiviteler->count()); ?> }">
                <h4 class="text-sm font-bold text-gray-600 uppercase mb-4">Son Aktiviteler</h4>
                <ul class="space-y-4">
                    <?php $__currentLoopData = $sonAktiviteler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-start space-x-3 text-sm" x-show="<?php echo e($loop->index); ?> < limit" x-transition>
                            <div class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-indigo-500"></div>
                            <div>
                                <span class="font-semibold text-gray-800"><?php echo e($log->eylem); ?></span>
                                <p class="text-gray-500 text-xs text-pretty">
                                    <?php echo e($log->aciklama); ?>

                                    <?php if($log->iaa): ?>
                                        <br>
                                        <span class="text-indigo-600 font-bold opacity-80 mt-0.5 inline-block">
                                            Proje: <?php echo e(Str::limit($log->iaa->baslik, 50)); ?>

                                        </span>
                                    <?php endif; ?>
                                </p>
                                <span class="text-xs text-gray-400 block mt-1"><?php echo e($log->created_at->diffForHumans()); ?></span>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                
                <?php if($sonAktiviteler->count() > 5): ?>
                <div class="mt-4 pt-4 border-t border-gray-50 flex justify-center">
                    <button @click="limit = (limit === 5 ? total : 5)" 
                            class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors">
                        <span x-text="limit === 5 ? 'Daha Fazla Göster' : 'Daha Az Göster'"></span>
                        <svg class="w-3 h-3 transform transition-transform" :class="limit !== 5 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>
                <?php endif; ?>
            </div>

            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h4 class="text-sm font-bold text-gray-600 uppercase mb-4">En Son Katıldığı Proje</h4>
                <?php if($sonProje): ?>
                    <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-100">
                        <p class="font-bold text-indigo-900"><?php echo e($sonProje->baslik); ?></p>
                        <p class="text-sm text-indigo-700 mt-1">Durum: <?php echo e($sonProje->durum); ?></p>
                        <p class="text-xs text-indigo-500 mt-2">Son İşlem: <?php echo e($sonProje->updated_at->format('d.m.Y H:i')); ?></p>
                        <a href="<?php echo e(route('proje.workspace.show', $sonProje->id)); ?>" class="mt-3 inline-block text-xs font-bold text-indigo-600 hover:underline">Projeye Git &rarr;</a>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">Henüz bir projede yer almadı.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <hr class="border-gray-200 my-8">

    
    
    
    <div>
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
            <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span>
            Bağlı Olduğu Takımlar
        </h3>

        <?php if(isset($takimlar) && $takimlar->count() > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php $__currentLoopData = $takimlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow group flex flex-col justify-between">
                        
                        
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 group-hover:bg-indigo-100 transition-colors flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm line-clamp-1" title="<?php echo e($takim->ad); ?>"><?php echo e($takim->ad); ?></h4>
                                <span class="text-xs text-gray-500 block">
                                    <?php echo e($takim->lider_user_id == $user->id ? 'Takım Lideri' : 'Takım Üyesi'); ?>

                                </span>
                            </div>
                        </div>

                        
                        <?php if(auth()->check() && auth()->id() != $user->id): ?>
                            <?php
                                // 1. Zaten üye miyim?
                                $isMember = auth()->user()->takimlar->contains($takim->id);
                                
                                // 2. Bekleyen isteğim var mı?
                                $pendingRequest = \App\Models\TakimDavetiyesi::where('takim_id', $takim->id)
                                    ->where('davet_eden_user_id', auth()->id())
                                    ->where('type', 'istek')
                                    ->where('durum', 'bekliyor')
                                    ->first();
                            ?>

                            <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
                                <?php if($isMember): ?>
                                    
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-100 cursor-default">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Üyesiniz
                                    </span>
                                <?php elseif($pendingRequest): ?>
                                    
                                    <form action="<?php echo e(route('takimlar.istegiGeriCek', $pendingRequest->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-100 hover:bg-yellow-100 transition-colors w-full justify-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            İsteği Geri Çek
                                        </button>
                                    </form>
                                <?php else: ?>
                                    
                                    <form action="<?php echo e(route('takimlar.katilmaIstegi', $takim->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors shadow-sm">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                            Katılma İsteği
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                <p class="text-sm text-gray-500">Bu kullanıcı henüz herhangi bir takıma üye değil.</p>
            </div>
        <?php endif; ?>
    </div>

    
    <div x-data="{ limit: 5, total: <?php echo e($kullaniciProjeleri->count()); ?> }">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
            <span class="w-1.5 h-6 bg-green-500 rounded-full"></span>
            Tüm Projeler ve Öneriler
            <span class="bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs font-bold border border-gray-200">
                <?php echo e($kullaniciProjeleri->count()); ?>

            </span>
        </h3>

        <?php if(isset($kullaniciProjeleri) && $kullaniciProjeleri->count() > 0): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rolü</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $kullaniciProjeleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 transition-colors" x-show="<?php echo e($loop->index); ?> < limit" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        
                                        <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="text-sm font-bold text-indigo-700 hover:text-indigo-900 hover:underline cursor-pointer">
                                            <?php echo e($proje->baslik); ?>

                                        </a>
                                        
                                        <?php if($proje->musteriSikayeti): ?>
                                            <span class="text-[10px] text-red-600 bg-red-50 px-2 py-0.5 rounded w-fit mt-1 border border-red-100">Müşteri Şikayeti</span>
                                        <?php else: ?>
                                            <span class="text-[10px] text-green-600 bg-green-50 px-2 py-0.5 rounded w-fit mt-1 border border-green-100">İyileştirme Önerisi</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php
                                        $roller = [];

                                        // 1. Öneri Sahibi (projeyi gönderen kişi)
                                        if ($proje->gonderen_user_id == $user->id) {
                                            $roller[] = ['text' => 'Öneri Sahibi', 'class' => 'text-purple-700 bg-purple-50 border-purple-200'];
                                        }

                                        // 2. Takım Lideri (atanan takımın lideri)
                                        if ($proje->atananTakim && $proje->atananTakim->lider_user_id == $user->id) {
                                            $roller[] = ['text' => 'Takım Lideri', 'class' => 'text-indigo-700 bg-indigo-50 border-indigo-200'];
                                        }

                                        // 3. Squad Üyesi (projeEkibi pivot - Müşteri Şikayeti projeleri)
                                        if ($proje->projeEkibi && $proje->projeEkibi->contains('id', $user->id)) {
                                            $pivotRol = $proje->projeEkibi->firstWhere('id', $user->id)?->pivot?->rol;
                                            if ($pivotRol === 'Lider') {
                                                $roller[] = ['text' => 'Squad Lideri', 'class' => 'text-orange-700 bg-orange-50 border-orange-200'];
                                            } else {
                                                $roller[] = ['text' => 'Squad Üyesi', 'class' => 'text-teal-700 bg-teal-50 border-teal-200'];
                                            }
                                        }

                                        // 4. Takım Üyesi fallback (sadece hiçbir rol bulunamadıysa)
                                        if (empty($roller)) {
                                            $roller[] = ['text' => 'Takım Üyesi', 'class' => 'text-gray-600 bg-gray-50 border-gray-200'];
                                        }
                                    ?>

                                    <div class="flex flex-col gap-1">
                                        <?php $__currentLoopData = $roller; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border <?php echo e($rol['class']); ?> w-fit">
                                                <?php echo e($rol['text']); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo $proje->durum_etiketi; ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                    <?php echo e($proje->updated_at->format('d.m.Y')); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            
            <?php if($kullaniciProjeleri->count() > 5): ?>
                <div class="mt-6 flex justify-center">
                    <button @click="limit = (limit === 5 ? total : 5)" 
                            class="inline-flex items-center px-6 py-2.5 bg-white border border-gray-300 rounded-xl font-bold text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105 active:scale-95 group">
                        <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-indigo-500 transition-colors transform transition-transform" :class="limit !== 5 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                        </svg>
                        <span x-text="limit === 5 ? 'Daha Fazla Göster' : 'Daha Az Göster'"></span>
                        <span x-if="limit === 5" class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-500 rounded-lg text-xs" x-text="'(' + (total - 5) + ' kaldı)'"></span>
                    </button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                <p class="text-sm text-gray-500">Kayıtlı proje bulunamadı.</p>
            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/profile/partials/show/tab-performance.blade.php ENDPATH**/ ?>