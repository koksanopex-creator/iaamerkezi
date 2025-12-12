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
                <?php echo e(__('Disiplin Dosyaları & Tutanaklar')); ?>

            </h2>
            
            <?php if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Bölüm Lideri', 'Hukuk Yöneticisi'])): ?>
                <a href="<?php echo e(route('admin.disiplin.create')); ?>" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-700 shadow-md transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Yeni Tutanak Oluştur
                </a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            
            <?php if(session('success')): ?>
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex justify-between items-center" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <?php echo e(session('success')); ?>

                    </div>
                    <button @click="show = false" class="text-green-600">&times;</button>
                </div>
            <?php endif; ?>

            
            <?php if(isset($filterMessage) && $filterMessage): ?>
                <?php
                    $colors = [
                        'success' => 'bg-green-50 border-green-500 text-green-700',
                        'info' => 'bg-blue-50 border-blue-500 text-blue-700',
                        'warning' => 'bg-amber-50 border-amber-500 text-amber-700',
                    ][$filterType ?? 'info'];
                ?>
                
                <div class="<?php echo e($colors); ?> border-l-4 p-4 mb-6 rounded-r-lg shadow-sm flex items-start gap-3 animate-fade-in-down">
                    <div class="mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-sm">Görüntüleme Kapsamı</p>
                        <p class="text-sm"><?php echo e($filterMessage); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-200">
                
                
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <form action="<?php echo e(route('admin.disiplin.index')); ?>" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                        
                        
                        <div class="w-full md:w-1/3">
                            <label class="text-xs font-bold text-gray-500 uppercase">Personel Ara</label>
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Ad Soyad..." class="w-full mt-1 border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        
                        <div class="w-full md:w-1/4">
                            <label class="text-xs font-bold text-gray-500 uppercase">Durum</label>
                            <select name="durum" class="w-full mt-1 border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tümü</option>
                                <option value="Taslak" <?php echo e(request('durum') == 'Taslak' ? 'selected' : ''); ?>>Taslak</option>
                                <option value="Savunma Bekleniyor" <?php echo e(request('durum') == 'Savunma Bekleniyor' ? 'selected' : ''); ?>>Savunma Bekleniyor</option>
                                <option value="Kurulda" <?php echo e(request('durum') == 'Kurulda' ? 'selected' : ''); ?>>Kurulda</option>
                                <option value="Karar Verildi" <?php echo e(request('durum') == 'Karar Verildi' ? 'selected' : ''); ?>>Karar Verildi</option>
                            </select>
                        </div>

                        
                        <div class="flex gap-2">
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-gray-700 transition">Filtrele</button>
                            <?php if(request()->anyFilled(['search', 'durum'])): ?>
                                <a href="<?php echo e(route('admin.disiplin.index')); ?>" class="bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-md text-sm font-bold hover:bg-gray-50 transition">Temizle</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No / Raporlayan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personel</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suç / İhlal</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Puan (Öneri)</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $durumRenk = match($case->durum) {
                                        'Taslak' => 'gray',
                                        'Savunma Bekleniyor' => 'yellow',
                                        'Kurul İncelemesinde', 'Kurulda' => 'blue',
                                        'Karar Verildi' => 'green',
                                        'İptal Edildi' => 'red',
                                        default => 'gray'
                                    };
                                    
                                    // Matris verisi tooltip için hazırlanıyor
                                    $matrisBilgi = "Etki: " . ($case->impact->puan ?? '?') . " | Kapsam: " . ($case->scope->puan ?? '?') . " | Tekrar: " . $case->tekrar_sayisi . ". Kez";
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">#<?php echo e($case->id); ?></div>
                                        <div class="text-[10px] text-gray-400 mt-1" title="Raporlayan">
                                            <span class="font-bold">R:</span> <?php echo e(Str::limit($case->reporter->name ?? '?', 15)); ?>

                                        </div>
                                        <div class="text-[10px] text-gray-400"><?php echo e($case->created_at->format('d.m.Y')); ?></div>
                                    </td>

                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs uppercase">
                                                    <?php echo e(substr($case->user->name, 0, 1)); ?>

                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900"><?php echo e($case->user->name); ?></div>
                                                <div class="text-xs text-gray-500"><?php echo e($case->user->bolum->ad ?? 'Bölümsüz'); ?></div>
                                            </div>
                                        </div>
                                    </td>

                                    
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-semibold line-clamp-1" title="<?php echo e($case->behavior->tanim ?? ''); ?>">
                                            <?php echo e(Str::limit($case->behavior->tanim ?? 'Silinmiş Kayıt', 40)); ?>

                                        </div>
                                        <div class="text-xs text-gray-500"><?php echo e($case->behavior->category->ad ?? '-'); ?></div>
                                    </td>

                                    
                                    <td class="px-6 py-4 text-center bg-gray-50/50 cursor-help" title="<?php echo e($matrisBilgi); ?>">
                                        <div class="text-sm font-black text-gray-800"><?php echo e($case->hesaplanan_puan); ?> Puan</div>
                                        <div class="text-[10px] text-indigo-600 font-bold mt-1 uppercase tracking-wide">
                                            <?php echo e($case->sistem_oneri_ceza); ?>

                                        </div>
                                    </td>

                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?php echo e($durumRenk); ?>-100 text-<?php echo e($durumRenk); ?>-800 border border-<?php echo e($durumRenk); ?>-200">
                                            <?php echo e($case->durum); ?>

                                        </span>
                                        
                                        
                                        <?php if($case->durum == 'Savunma Bekleniyor'): ?>
                                            <div class="text-[10px] text-red-600 mt-1 animate-pulse font-extrabold flex justify-center items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                İŞLEM BEKLİYOR
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            
                                            
                                            <?php
                                                $isSuperAdmin = Auth::user()->hasRole('Superadmin');
                                                $isReporter = Auth::id() == $case->reporter_id;
                                                $isHukukAdmin = Auth::user()->hasRole('Hukuk Admini');
                                                
                                                // KURAL: Savunma henüz verilmediyse işlem yapılabilir
                                                $isEditableStatus = $case->durum == 'Savunma Bekleniyor';
                                            ?>

                                            
                                            <a href="<?php echo e(route('admin.disiplin.show', $case->id)); ?>" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-2 py-1 rounded border border-indigo-100 font-bold text-xs">
                                                AÇ
                                            </a>

                                            
                                            
                                            
                                            <?php if($isSuperAdmin || (($isReporter || $isHukukAdmin) && $isEditableStatus)): ?>
                                                <a href="<?php echo e(route('admin.disiplin.edit', $case->id)); ?>" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-2 py-1 rounded border border-blue-100 font-bold text-xs">
                                                    DÜZ
                                                </a>
                                            <?php endif; ?>

                                            
                                            
                                            
                                            <?php if($isSuperAdmin || (($isReporter || $isHukukAdmin) && $isEditableStatus)): ?>
                                                <form action="<?php echo e(route('admin.disiplin.destroy', $case->id)); ?>" method="POST" onsubmit="return confirm('Bu tutanağı silmek istediğinize emin misiniz?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-2 py-1 rounded border border-red-100 font-bold text-xs">
                                                        SİL
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <span class="font-medium">Kayıt bulunamadı.</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <div class="p-4 border-t border-gray-200 bg-gray-50">
                    <?php echo e($cases->links()); ?>

                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/disiplin/index.blade.php ENDPATH**/ ?>