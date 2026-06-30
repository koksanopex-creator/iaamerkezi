<?php $__env->startPush('pageTitle'); ?>
    <?php echo e($iaa->baslik); ?> | 
<?php $__env->stopPush(); ?>

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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
            <span class="text-gray-500"><?php echo e(__('İAA Detayı:')); ?></span> <?php echo e($iaa->baslik); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            
            <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-8">

                
                <div class="lg:col-span-2 space-y-8">

                    
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                        <div class="p-6 sm:p-8">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-wide">İyileştirme Önerisi</p>
                                    <h1 class="mt-1 text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight"><?php echo e($iaa->baslik); ?></h1>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                        <div class="p-6 sm:p-8">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Mevcut Durum / Problem Tanımı</h3>
                            </div>
                            <div class="mt-4 pl-14 text-gray-600 leading-relaxed prose max-w-none">
                                <?php echo nl2br(e($iaa->mevcut_durum)); ?>

                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                        <div class="p-6 sm:p-8">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">İyileştirme Önerisi</h3>
                            </div>
                            <div class="mt-4 pl-14 text-gray-600 leading-relaxed prose max-w-none">
                                <?php echo nl2br(e($iaa->oneri)); ?>

                            </div>
                        </div>
                    </div>

                    
                    <?php if($iaa->resimler->isNotEmpty()): ?>
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                            <div class="p-6 sm:p-8">
                                <h3 class="text-xl font-bold text-gray-800 mb-4">Eklenen Resimler</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <?php $__currentLoopData = $iaa->resimler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(asset('storage/' . $resim->dosya_yolu)); ?>" target="_blank" class="block group relative">
                                            <img src="<?php echo e(asset('storage/' . $resim->dosya_yolu)); ?>" alt="İAA Resmi" class="rounded-lg object-cover w-full h-40 transform group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                                                <svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="mt-8 lg:mt-0 space-y-8">
                    
                    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-2 border-indigo-100">
                            <div class="p-6 bg-indigo-50/50">
                                <h3 class="text-lg font-bold text-indigo-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Yönetici İşlemleri
                                </h3>
                                
                                <div class="space-y-3">
                                    <?php if($iaa->durum === 'Onay Bekliyor'): ?>
                                        <button x-data @click="$dispatch('open-modal', 'onayla-modal-<?php echo e($iaa->id); ?>')" 
                                                class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Öneriyi Onayla
                                        </button>

                                        <button x-data @click="$dispatch('open-modal', 'reddet-modal-<?php echo e($iaa->id); ?>')" 
                                                class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 transition-all">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Öneriyi Reddet
                                        </button>

                                        <form action="<?php echo e(route('admin.iaa-yonetim.destroy', $iaa->id)); ?>" method="POST" onsubmit="return confirm('Bu öneriyi kalıcı olarak silmek istediğinize emin misiniz?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" 
                                                    class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition-all">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Kalıcı Olarak Sil
                                            </button>
                                        </form>
                                    <?php elseif(in_array($iaa->durum, ['Havuzda', 'Reddedildi'])): ?>
                                        <form action="<?php echo e(route('admin.iaa-yonetim.geriAl', $iaa->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <button type="submit" 
                                                    class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                Kararı Geri Al
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <p class="text-[10px] text-gray-400 text-center mt-2 italic">* Bu panel sadece Superadmin yetkisine sahip kullanıcılara görünür.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($iaa->atanan_takim_id): ?>
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-emerald-200">
                             <div class="p-6 bg-emerald-50/30">
                                <h3 class="text-lg font-bold text-emerald-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                    Proje Atama Bilgileri
                                </h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 uppercase">Sorumlu Takım</p>
                                            <a href="<?php echo e(route('takimlar.show', $iaa->atanan_takim_id)); ?>" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors">
                                                <?php echo e($iaa->atananTakim->ad); ?>

                                            </a>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 uppercase">Atanma Tarihi</p>
                                            <p class="text-sm font-bold text-gray-900"><?php echo e($iaa->iaaTalebi?->start_date ? \Carbon\Carbon::parse($iaa->iaaTalebi->start_date)->format('d.m.Y') : $iaa->updated_at->format('d.m.Y')); ?></p>
                                        </div>
                                    </div>

                                    <?php if(in_array($iaa->durum, ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi'])): ?>
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">İşlem Süresi</p>
                                                <p class="text-sm font-bold text-emerald-700">
                                                    <?php echo e($iaa->completion_duration_in_days ?? 'Belirlenmedi'); ?>

                                                </p>
                                            </div>
                                        </div>
                                    <?php elseif($iaa->iaaTalebi?->due_date): ?>
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">Kalan Süre</p>
                                                <?php
                                                    $dueDate = \Carbon\Carbon::parse($iaa->iaaTalebi->due_date);
                                                    $diff = ceil(now()->diffInDays($dueDate, false));
                                                ?>
                                                <p class="text-sm font-bold <?php if($diff < 0): ?> text-red-600 <?php else: ?> text-gray-900 <?php endif; ?>">
                                                    <?php if($diff < 0): ?>
                                                        <?php echo e(abs((int)$diff)); ?> gün gecikti
                                                    <?php else: ?>
                                                        <?php echo e((int)$diff); ?> gün kaldı
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <a href="<?php echo e(route('proje.workspace.show', $iaa->id)); ?>" 
                                       class="mt-2 w-full flex items-center justify-center px-4 py-3 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 transition-all transform hover:scale-[1.02]">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        Proje Çalışma Alanına Git
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Öneri Bilgileri</h3>
                            <div class="space-y-4">
                                    <div class="flex items-center space-x-3"><div class="flex-shrink-0 w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-semibold text-gray-800">Öneren</p>
                                        <?php if($iaa->gonderen): ?>
                                        <p class="text-gray-600">
                                            <a href="<?php echo e(route('profile.show', $iaa->gonderen->id)); ?>" class="text-indigo-600 hover:text-indigo-800 hover:underline transition-colors font-medium">
                                                <?php echo e($iaa->gonderen->name); ?>

                                            </a>
                                        </p>
                                        <?php else: ?>
                                        <p class="text-gray-600"><?php echo e($iaa->guest_name); ?> 
                                            <span class="text-xs text-white bg-gray-500 px-1.5 py-0.5 rounded-full ml-1">Misafir</span>
                                        </p>
                                        <?php endif; ?>
                                    </div></div>
                                    <div class="flex items-center space-x-3"><div class="flex-shrink-0 w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div><div class="text-sm"><p class="font-semibold text-gray-800">İlgili Alan / Bölüm</p><?php if($iaa->bolum): ?><p class="text-gray-600"><?php echo e($iaa->bolum->ad); ?></p><?php else: ?><p class="text-gray-600"><?php echo e($iaa->ilgili_alan); ?></p><?php endif; ?></div></div>
                                    <div class="flex items-center space-x-3"><div class="flex-shrink-0 w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-sm">
                                                <p class="font-semibold text-gray-800">Gönderim Tarihi</p><p class="text-gray-600"><?php echo e($iaa->created_at->format('d.m.Y H:i')); ?></p>
                                            </div>
                                    </div>
                                    

                                        
                                        
                                        
                                        <div class="border-t border-gray-200 mt-4 pt-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0 w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div class="text-sm flex-grow">
                                                    <div class="flex justify-between items-center">
                                                        <p class="font-semibold text-gray-800">Mevcut Durum</p>
                                                        <?php echo $iaa->durum_etiketi; ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                            </div>
                        </div>
                    </div>

                    
<?php if($iaa->oneren_kazanc_miktar || $iaa->oneren_butce_miktar): ?>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Öneri Sahibinin Tahminleri</h3>
            
            <div class="grid grid-cols-2 gap-4">
                
                
                <div class="col-span-2 bg-gradient-to-br from-indigo-500 to-purple-600 p-4 rounded-lg text-center shadow-lg">
                    <p class="text-sm text-indigo-100 uppercase font-semibold">Öngörülen Puan</p>
                    <p class="text-4xl font-bold text-white mt-1">
                        
                        
                        
                        <?php
                            // Basit bir hesaplama mantığı (Senin formülüne göre düzenle)
                            // Eğer veritabanında kayıtlıysa direkt $iaa->oneren_puan yazabilirsin.
                            $tahminiPuan = 0;
                            if($iaa->oneren_butce_miktar > 0) {
                                // Örnek Formül: (Kazanç / Bütçe) * Risk (Risk öneren girmediği için varsayılan 3 alındı)
                                $tahminiPuan = ($iaa->oneren_kazanc_miktar / $iaa->oneren_butce_miktar) * 3; 
                            }
                        ?>
                        
                        
                        <?php echo e(number_format($tahminiPuan, 0, ',', '.')); ?>

                    </p>
                </div>

                
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                    <p class="text-xs text-green-700 uppercase font-semibold">Tahmini Kazanç</p>
                    <p class="text-xl font-bold text-green-800">
                        <?php echo e(number_format($iaa->oneren_kazanc_miktar, 0, ',', '.')); ?> <?php echo e($iaa->oneren_kazanc_birim); ?>

                    </p>
                </div>

                
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                    <p class="text-xs text-red-700 uppercase font-semibold">Tahmini Bütçe</p>
                    <p class="text-xl font-bold text-red-800">
                        <?php echo e(number_format($iaa->oneren_butce_miktar, 0, ',', '.')); ?> <?php echo e($iaa->oneren_butce_birim); ?>

                    </p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

                    
                    <?php if(in_array($iaa->durum, ['Havuzda', 'Talep Edildi', 'Atandı'])): ?>
                         <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                             <div class="p-6">

                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Yönetici Puanlaması</h3>

                                    
                                    <?php if(auth()->guard()->check()): ?>
                                        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
                                            <?php if($iaa->puan): ?>
                                                <button x-data @click.prevent="$dispatch('open-modal', 'puan-duzenle-modal-<?php echo e($iaa->id); ?>')" 
                                                        class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-slate-600 hover:bg-slate-700">
                                                    Düzenle
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                 <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2 bg-gradient-to-br from-indigo-500 to-purple-600 p-4 rounded-lg text-center shadow-lg"><p class="text-sm text-indigo-100 uppercase font-semibold">Toplam Puan</p><p class="text-4xl font-bold text-white mt-1"><?php echo e(number_format($iaa->puan, 0, ',', '.')); ?></p></div>
                                    <div class="bg-gray-100 border border-gray-200 rounded-lg p-3 text-center"><p class="text-xs text-gray-600 uppercase font-semibold">Risk</p><p class="text-xl font-bold text-gray-800"><?php echo e($iaa->risk); ?> / 5</p></div>
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center"><p class="text-xs text-green-700 uppercase font-semibold">Kazanç</p><p class="text-lg font-bold text-green-800"><?php echo e(number_format($iaa->kazanc_miktar, 0, ',', '.')); ?> <?php echo e($iaa->kazanc_birim); ?></p></div>
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center col-span-2"><p class="text-xs text-red-700 uppercase font-semibold">Bütçe</p><p class="text-lg font-bold text-red-800"><?php echo e(number_format($iaa->butce_miktar, 0, ',', '.')); ?> <?php echo e($iaa->butce_birim); ?></p></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            
            
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="<?php echo e((url()->previous() && url()->previous() !== url()->current()) ? url()->previous() : route('iaa.havuz')); ?>" class="inline-flex items-center space-x-2 bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Geri Dön</span>
                </a>
                
                <?php if($iaa->durum === 'Havuzda'): ?>
                    <div class="w-full sm:w-auto">
                        <?php if(auth()->user()->lideriOlduguTakimlar->isNotEmpty()): ?>
                            <?php if($talepEdilenIaaIdleri->contains($iaa->id)): ?>
                                <div class="space-y-2">
                                    <button class="w-full inline-flex justify-center text-sm font-semibold text-gray-500 bg-gray-200 border border-transparent rounded-md shadow-sm px-4 py-2 cursor-not-allowed" disabled>
                                        Talep Edildi
                                    </button>
                                    
                                    <form action="<?php echo e(route('iaa.talebiGeriCek', $iaa->id)); ?>" method="POST" onsubmit="return confirm('Talebinizi geri çekmek istediğinize emin misiniz?');">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="w-full inline-flex justify-center text-sm font-semibold text-red-600 bg-red-50 border border-red-200 rounded-md shadow-sm px-4 py-2 hover:bg-red-100 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Talebi Geri Çek
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <button x-data @click="$dispatch('open-modal', 'talep-et-modal-<?php echo e($iaa->id); ?>')" class="w-full inline-flex justify-center text-sm font-semibold text-white bg-indigo-600 border border-transparent rounded-md shadow-sm px-4 py-2 hover:bg-indigo-700">
                                    Takımın Adına Talep Et
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <div title="Öneri talep edebilmek için bir takım lideri olmalısınız.">
                                <button class="w-full inline-flex justify-center text-sm font-semibold text-white bg-indigo-300 border border-transparent rounded-md shadow-sm px-4 py-2 cursor-not-allowed">
                                    Takımın Adına Talep Et
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <?php if($iaa->durum === 'Havuzda' && auth()->user()->lideriOlduguTakimlar->isNotEmpty()): ?>
        <?php echo $__env->make('iaa.partials.talep-et-modal', ['iaa' => $iaa, 'liderOlduguTakimlar' => $liderOlduguTakimlar], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <?php echo $__env->make('admin.iaa-yonetim.partials.onayla-modal', ['iaa' => $iaa, 'paraBirimleri' => $paraBirimleri], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.iaa-yonetim.partials.reddet-modal', ['iaa' => $iaa], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/iaa/show.blade.php ENDPATH**/ ?>