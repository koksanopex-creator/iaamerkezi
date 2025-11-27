<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    
    
    
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proje Çalışma Alanı: <span class="text-indigo-600"><?php echo e($iaa->baslik); ?></span>
            </h2>
            <a href="<?php echo e(url()->previous()); ?>" class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Geri Dön
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    
    <div class="py-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            
            <?php echo $__env->make('proje-calisma-alani.partials._project-header', [
                'iaa' => $iaa,
                'takim' => $takim,
                'assignment' => $assignment,
                'progressPercentage' => $progressPercentage,
                'completedStepsCount' => $completedStepsCount,
                'totalStepsCount' => $totalStepsCount,
                'statusDate' => $statusDate ?? null
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            
            
            <?php if($iaa->musteriSikayeti && Auth::id() == $iaa->atananTakim->lider_user_id): ?>
                <div class="bg-white rounded-xl shadow-sm border border-indigo-100 p-5 flex items-center justify-between animate-fade-in-up">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Proje Görev Gücü (Squad)</h3>
                            
                            
                            <?php
                                // Aktif: Lider olanlar VEYA durumu 'onaylandi' olanlar
                                $aktifSayisi = $iaa->projeEkibi->filter(function($uye) {
                                    return $uye->pivot->rol == 'Lider' || $uye->pivot->durum == 'onaylandi';
                                })->count();

                                // Bekleyen: Durumu 'bekliyor' olanlar
                                $bekleyenSayisi = $iaa->projeEkibi->where('pivot.durum', 'bekliyor')->count();
                            ?>

                            <div class="flex items-center gap-3 mt-2">
                                
                                <div class="flex -space-x-2 overflow-hidden">
                                    <?php $__currentLoopData = $iaa->projeEkibi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($uye->profile_photo_path): ?>
                                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover <?php echo e($uye->pivot->durum == 'bekliyor' ? 'opacity-50 grayscale' : ''); ?>" 
                                                 src="<?php echo e(asset('storage/'.$uye->profile_photo_path)); ?>" 
                                                 title="<?php echo e($uye->name); ?> (<?php echo e($uye->pivot->durum == 'bekliyor' ? 'Davet Bekleniyor' : $uye->pivot->rol); ?>)">
                                        <?php else: ?>
                                            <div class="inline-flex items-center justify-center h-8 w-8 rounded-full ring-2 ring-white bg-gray-100 text-xs font-bold text-gray-600 <?php echo e($uye->pivot->durum == 'bekliyor' ? 'opacity-50 grayscale' : ''); ?>" 
                                                 title="<?php echo e($uye->name); ?> (<?php echo e($uye->pivot->durum == 'bekliyor' ? 'Davet Bekleniyor' : $uye->pivot->rol); ?>)">
                                                <?php echo e(substr($uye->name, 0, 1)); ?>

                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                                
                                <div class="flex flex-col text-xs border-l pl-3 border-gray-200">
                                    <span class="text-gray-500 font-semibold mb-0.5">Toplam <?php echo e($iaa->projeEkibi->count()); ?> Kişi</span>
                                    
                                    <div class="flex items-center gap-2">
                                        
                                        <span class="inline-flex items-center text-green-700 bg-green-50 px-1.5 py-0.5 rounded font-bold">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>
                                            <?php echo e($aktifSayisi); ?> Aktif
                                        </span>

                                        
                                        <?php if($bekleyenSayisi > 0): ?>
                                            <span class="inline-flex items-center text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded font-bold animate-pulse">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1"></span>
                                                <?php echo e($bekleyenSayisi); ?> Bekliyor
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                  
                    
                    
                    <?php
                        // Kilitlenecek Durumlar
                        $kilitliDurumlar = ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Tamamlandı'];
                        $kilitliMi = in_array($iaa->durum, $kilitliDurumlar);
                    ?>

                    <?php if(!$kilitliMi): ?>
                        <button onclick="Livewire.dispatch('openSquadModal', { iaaId: <?php echo e($iaa->id); ?> })" 
                                class="flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Ekibi Yönet
                        </button>
                    <?php else: ?>
                        <span class="flex items-center px-4 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed" title="Proje onay aşamasında olduğu için ekip kilitlenmiştir.">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Ekip Kilitlendi
                        </span>
                    <?php endif; ?>
                </div>
                
                
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.squad-yonetim-modal', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-3632604134-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            <?php endif; ?>
            

            
            
            
            <?php if($iaa->musteriSikayeti): ?>
                <div x-data="{ open: false }" class="group">
                    
                    
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                        
                        
                        <div class="h-1.5 bg-gradient-to-r from-rose-400 via-pink-500 to-purple-500"></div>
                        
                        
                        <div @click="open = !open" class="p-6 cursor-pointer transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-50/50 hover:to-purple-50/50">
                            <div class="flex items-start justify-between gap-4">
                                
                                
                                <div class="flex items-start gap-4 flex-1 min-w-0">
                                    
                                    
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-rose-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg shadow-rose-500/30 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    
                                    
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                                            Müşteri Şikayeti Detayları
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                                #<?php echo e($iaa->musteriSikayeti->id); ?>

                                            </span>
                                        </h3>
                                        
                                        
                                        <div x-show="!open" class="mt-2 flex flex-wrap items-center gap-3 text-sm">
                                            <div class="flex items-center gap-1.5 text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                <span class="font-medium"><?php echo e($iaa->musteriSikayeti->musteri_adi); ?></span>
                                            </div>
                                            <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                                            <div class="flex items-center gap-1.5 text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span><?php echo e(\Carbon\Carbon::parse($iaa->musteriSikayeti->musteri_sikayet_tarihi)->format('d.m.Y')); ?></span>
                                            </div>
                                            <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-purple-100 text-purple-800">
                                                <?php echo e($iaa->musteriSikayeti->sikayetKategori->ad ?? 'Belirtilmemiş'); ?>

                                            </span>
                                        </div>
                                        
                                        
                                        <p class="mt-2 text-sm" :class="open ? 'text-purple-600 font-medium' : 'text-gray-500'">
                                            <span x-show="!open">Tüm detayları görmek için tıklayın</span>
                                            <span x-show="open" style="display: none;">Gizlemek için tıklayın</span>
                                        </p>
                                    </div>
                                </div>
                                
                                
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center group-hover:from-purple-100 group-hover:to-pink-100 transition-all duration-300">
                                        <svg x-show="!open" class="w-5 h-5 text-gray-600 group-hover:text-purple-600 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                        <svg x-show="open" style="display: none;" class="w-5 h-5 text-purple-600 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             style="display: none;"
                             class="border-t border-gray-100">

                            <div class="p-6 bg-gradient-to-br from-gray-50/50 to-purple-50/30">
                                
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    
                                    
                                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Müşteri Adı</p>
                                                <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($iaa->musteriSikayeti->musteri_adi); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">E-posta</p>
                                                <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($iaa->musteriSikayeti->musteri_iletisim); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Konum Tipi</p>
                                                <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($iaa->musteriSikayeti->konum_tipi); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Şikayet Tarihi</p>
                                                <p class="text-sm font-semibold text-gray-900"><?php echo e(\Carbon\Carbon::parse($iaa->musteriSikayeti->musteri_sikayet_tarihi)->format('d.m.Y')); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class="mb-6">
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-xl shadow-sm border border-purple-100">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        <span class="text-xs font-medium text-gray-500">Kategori:</span>
                                        <span class="text-sm font-bold text-purple-700"><?php echo e($iaa->musteriSikayeti->sikayetKategori->ad ?? 'Belirtilmemiş'); ?></span>
                                    </div>
                                </div>
                                
                                
                                <div class="mb-6 bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Şikayet Konusu</h4>
                                    </div>
                                    <p class="text-base font-semibold text-gray-800 leading-relaxed pl-11"><?php echo e($iaa->musteriSikayeti->musteri_sikayet_konusu); ?></p>
                                </div>
                                
                                
                                <div class="mb-6 bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Detaylı Açıklama</h4>
                                    </div>
                                    <div class="pl-11">
                                        <div class="bg-gradient-to-br from-gray-50 to-blue-50/30 rounded-lg p-4 border border-gray-200">
                                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap"><?php echo e($iaa->musteriSikayeti->musteri_sikayet_detayi); ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <?php if($iaa->musteriSikayeti->dosyalar->isNotEmpty()): ?>
                                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100" x-data="{ previewModal: false, previewUrl: '', previewType: '', previewName: '' }">
                                    <div class="flex items-start gap-3 mb-4">
                                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Eklenen Dosyalar (<?php echo e($iaa->musteriSikayeti->dosyalar->count()); ?>)</h4>
                                    </div>
                                    <div class="pl-11 grid grid-cols-1 md:grid-cols-2 gap-3">
                                        
                                        <?php $__currentLoopData = $iaa->musteriSikayeti->dosyalar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $fileUrl = asset('storage/' . $dosya->dosya_yolu);
                                            $isImage = str_starts_with($dosya->mime_tipi, 'image/');
                                            $isPdf = $dosya->mime_tipi === 'application/pdf';
                                            $extension = pathinfo($dosya->orijinal_adi, PATHINFO_EXTENSION);
                                        ?>
                                        
                                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-100 overflow-hidden hover:shadow-lg transition-all duration-200 group">
                                            
                                            <div class="relative h-32 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                                                <?php if($isImage): ?>
                                                    
                                                    <img src="<?php echo e($fileUrl); ?>" alt="<?php echo e($dosya->orijinal_adi); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                                <?php elseif($isPdf): ?>
                                                    
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <svg class="w-16 h-16 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                <?php else: ?>
                                                    
                                                    <div class="w-full h-full flex flex-col items-center justify-center">
                                                        <svg class="w-12 h-12 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        <span class="text-xs font-bold text-indigo-600 uppercase px-2 py-1 bg-white rounded"><?php echo e(strtoupper($extension)); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                
                                                <div class="absolute top-2 right-2 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                    <?php if($isImage || $isPdf): ?>
                                                    <button @click="previewModal = true; previewUrl = '<?php echo e($fileUrl); ?>'; previewType = '<?php echo e($isImage ? 'image' : 'pdf'); ?>'; previewName = '<?php echo e($dosya->orijinal_adi); ?>'" class="w-8 h-8 bg-white/90 hover:bg-white rounded-lg flex items-center justify-center shadow-lg backdrop-blur-sm transition-colors">
                                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </button>
                                                    <?php endif; ?>
                                                    <a href="<?php echo e($fileUrl); ?>" target="_blank" class="w-8 h-8 bg-white/90 hover:bg-white rounded-lg flex items-center justify-center shadow-lg backdrop-blur-sm transition-colors">
                                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="p-3 bg-white">
                                                <p class="text-sm font-semibold text-gray-900 truncate mb-1" title="<?php echo e($dosya->orijinal_adi); ?>"><?php echo e($dosya->orijinal_adi); ?></p>
                                                <div class="flex items-center justify-between">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        <?php echo e($dosya->mime_tipi); ?>

                                                    </span>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>

                                    
                                    <div 
                                         x-show="previewModal" 
                                         x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="ease-in duration-200"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        class="fixed inset-0 z-50 overflow-y-auto bg-black/90 flex items-center justify-center p-4" 
                                        style="display: none;" 
                                        @keydown.escape.window="previewModal = false"
                                        @click="previewModal = false"
                                    >
                                        <div class="relative w-full max-w-6xl max-h-[90vh]" @click.stop>
                                            
                                            <div class="bg-white/10 backdrop-blur-md rounded-t-xl px-6 py-4 flex items-center justify-between">
                                                <h3 class="text-white font-semibold truncate" x-text="previewName"></h3>
                                                <div class="flex items-center gap-2">
                                                    <a :href="previewUrl" target="_blank" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                    <button @click="previewModal = false" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="bg-white rounded-b-xl overflow-hidden shadow-2xl">
                                                <div class="flex items-center justify-center p-4" style="max-height: calc(90vh - 80px);">
                                                    
                                                    <img x-show="previewType === 'image'" :src="previewUrl" :alt="previewName" class="max-w-full max-h-full object-contain rounded-lg shadow-lg">
                                                    
                                                    
                                                    <iframe x-show="previewType === 'pdf'" :src="previewUrl" class="w-full rounded-lg shadow-lg" style="height: calc(90vh - 120px);" frameborder="0"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            
            


            
            <div class="w-full">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Proje Adımları</h3>
                    
                    <?php if(session('success')): ?>
                        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert"><p><?php echo e(session('success')); ?></p></div>
                    <?php endif; ?>
                    
                    <div class="relative border-l-2 border-gray-200">
                        <?php $currentStepFound = false; ?>
                        
                        <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $isCompleted = in_array($step->id, $completedStepIds);
                                $progressUpdate = $progressUpdates[$step->id] ?? null;
                                $isCurrent = !$isCompleted && !$currentStepFound;
                                
                                if ($isCurrent) {
                                    $currentStepFound = true;
                                    $currentStep = $step;
                                }
                            ?>
                            
                            
                            <?php echo $__env->make('proje-calisma-alani.partials._step-item', [
                                'step' => $step,
                                'isCompleted' => $isCompleted,
                                'isCurrent' => $isCurrent,
                                'progressUpdate' => $progressUpdate,
                                'isTeamMember' => $isTeamMember,
                                'iaa' => $iaa,
                                'assignment' => $assignment,
                                'takim' => $takim
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                        
                        <?php if(!$currentStepFound): ?>
                            <?php echo $__env->make('proje-calisma-alani.partials._project-final-status', [
                                'iaa' => $iaa,
                                'statusDate' => $statusDate ?? null
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            
                            <?php echo $__env->make('proje-calisma-alani.partials._action-buttons', ['iaa' => $iaa], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="w-full" x-data="{ logModalOpen: false }">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Son 5 Proje Geçmişi Kaydı</h3>
                    <div class="border border-gray-200 rounded-lg overflow-hidden mb-4">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eylem</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $sonOnLoglar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?> 
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($log->user->name ?? 'Sistem'); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900"><?php echo e($log->eylem); ?></div>
                                            <div class="text-sm text-gray-500 italic">"<?php echo e($log->aciklama); ?>"</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($log->created_at->format('d.m.Y H:i')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Bu proje için henüz bir log kaydı bulunmuyor.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <?php if($tumProjeLoglari->count() > 5): ?>
                        <div class="text-center">
                            <button 
                                @click="logModalOpen = true" 
                                type="button" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Tüm Logları Gör (<?php echo e($tumProjeLoglari->count()); ?>)
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div 
                    x-show="logModalOpen" 
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4"
                    style="display: none;"
                    @keydown.escape.window="logModalOpen = false"
                >
                    <div 
                        class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-4xl" 
                        @click.outside="logModalOpen = false"
                    >
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Proje Geçmişi - Tüm Loglar (<?php echo e($tumProjeLoglari->count()); ?>)
                            </h3>
                        </div>
                        <div class="p-6 max-h-[70vh] overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eylem</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $__empty_1 = true; $__currentLoopData = $tumProjeLoglari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($log->user->name ?? 'Sistem'); ?></td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900"><?php echo e($log->eylem); ?></div>
                                                <div class="text-sm text-gray-500 italic">"<?php echo e($log->aciklama); ?>"</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($log->created_at->format('d.m.Y H:i')); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Log kaydı bulunamadı.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-right">
                            <button 
                                @click="logModalOpen = false" 
                                type="button" 
                                class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                                Kapat
                            </button>
                        </div>
                    </div>
                </div>
                
            </div>
            

        </div> 
    </div> 

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Controller'dan gelen 'scroll_to_step' verisi varsa çalışır
            <?php if(session('scroll_to_step')): ?>
                setTimeout(() => {
                    // Tamamlanan adımın ID'sini al (Örn: 25)
                    const stepId = "<?php echo e(session('scroll_to_step')); ?>";
                    
                    // Sayfada bu ID'ye sahip adımı bul (Örn: id="step-card-25")
                    // NOT: Aşağıdaki adımda ID eklemeyi unutma!
                    const element = document.getElementById('step-card-' + stepId);
                    
                    if (element) {
                        // Oraya yumuşakça kaydır ve ortala
                        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        // İstersen kısa bir vurgu efekti de verebilirsin (opsiyonel)
                        element.classList.add('ring-2', 'ring-green-500', 'ring-offset-2');
                        setTimeout(() => element.classList.remove('ring-2', 'ring-green-500', 'ring-offset-2'), 2000);
                    }
                }, 500); // Sayfa tam yüklensin diye yarım saniye bekle
            <?php endif; ?>
        });
    </script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/show.blade.php ENDPATH**/ ?>