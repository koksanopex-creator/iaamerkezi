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
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                        <a href="<?php echo e(route('admin.bolumler.index')); ?>"
                            class="hover:text-indigo-600 transition">Bölümler</a>
                        <span>/</span>
                        <span><?php echo e($bolum->ad); ?></span>
                    </div>
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                        <?php echo e($bolum->ad); ?>

                        <span class="text-lg font-medium text-gray-400 ml-2">Paneli</span>
                    </h2>
                </div>

                <?php if(Auth::user()->hasRole('Superadmin')): ?>
                    <a href="<?php echo e(route('admin.bolumler.edit', $bolum)); ?>"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Ayarları Düzenle
                    </a>
                <?php endif; ?>
            </div>

            
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 relative">
                
                <div class="h-32 bg-gradient-to-r from-indigo-600 to-purple-700 relative">
                    <div class="absolute inset-0 opacity-20"
                        style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                    </div>
                </div>

                <div class="px-8 pb-8 flex flex-col md:flex-row items-end -mt-12 gap-6 relative z-10">
                    
                    <div class="flex-shrink-0">
                        <?php if($bolum->logo_yolu): ?>
                            <img src="<?php echo e(asset('storage/' . $bolum->logo_yolu)); ?>"
                                class="h-32 w-32 rounded-2xl object-cover border-4 border-white shadow-lg bg-white">
                        <?php else: ?>
                            <div
                                class="h-32 w-32 rounded-2xl bg-indigo-50 border-4 border-white shadow-lg flex items-center justify-center text-indigo-500 text-4xl font-bold">
                                <?php echo e(substr($bolum->ad, 0, 1)); ?>

                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <div class="flex-grow flex flex-col md:flex-row justify-between items-end w-full gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900"><?php echo e($bolum->ad); ?></h1>
                            <div class="flex items-center gap-4 mt-2 text-sm">
                                <span
                                    class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-bold uppercase tracking-wide border border-blue-100">
                                    <?php echo e($bolum->kategori->ad ?? 'Genel'); ?>

                                </span>
                                <span
                                    class="flex items-center font-medium <?php echo e($bolum->is_active ? 'text-green-600' : 'text-red-600'); ?>">
                                    <span
                                        class="w-2 h-2 rounded-full <?php echo e($bolum->is_active ? 'bg-green-500' : 'bg-red-500'); ?> mr-2"></span>
                                    <?php echo e($bolum->is_active ? 'Aktif Departman' : 'Pasif'); ?>

                                </span>
                            </div>
                        </div>

                        
                        <div class="flex gap-4">
                            
                            <?php if($bolum->sikayet_kategorileri_count > 0): ?>
                            <div class="text-center px-4 py-2 bg-red-50 rounded-lg border border-red-100">
                                <span class="block text-xl font-bold text-red-700"><?php echo e($bolum->sikayetler_count); ?></span>
                                <span class="text-[10px] text-red-500 font-bold uppercase">Şikayet</span>
                            </div>
                            <?php endif; ?>

                            
                            <div class="text-center px-4 py-2 bg-indigo-50 rounded-lg border border-indigo-100">
                                <span class="block text-xl font-bold text-indigo-700"><?php echo e($iaa_count); ?></span>
                                <span class="text-[10px] text-indigo-500 font-bold uppercase">İAA</span>
                            </div>

                            
                            <div class="text-center px-4 py-2 bg-orange-50 rounded-lg border border-orange-100">
                                <span class="block text-xl font-bold text-orange-700"><?php echo e($disiplin_count); ?></span>
                                <span class="text-[10px] text-orange-500 font-bold uppercase">Disiplin</span>
                            </div>

                            
                            <div class="text-center px-4 py-2 bg-blue-50 rounded-lg border border-blue-100">
                                <span class="block text-xl font-bold text-blue-700"><?php echo e(count($users)); ?></span>
                                <span class="text-[10px] text-blue-500 font-bold uppercase">Personel</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                
                <div class="<?php echo e($bolum->has_machines ? 'lg:col-span-1' : 'lg:col-span-3'); ?> space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Bölüm Personeli
                            </h3>
                            <span class="text-sm text-gray-400 bg-gray-50 px-2 py-1 rounded-md"><?php echo e($users->count()); ?>

                                Kişi</span>
                        </div>

                        <div class="space-y-4">
                            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php /** @var \App\Models\User $user */ ?>
                                <div
                                    class="group flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition border border-transparent hover:border-gray-100">
                                    <a href="<?php echo e(route('profile.show', $user->id)); ?>" class="relative block">
                                        <?php if($user->avatar || $user->profile_photo_path): ?>
                                            <img class="h-12 w-12 rounded-full object-cover ring-2 ring-white shadow-sm"
                                                src="<?php echo e($user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('storage/' . $user->avatar)); ?>"
                                                alt="<?php echo e($user->name); ?>">
                                        <?php else: ?>
                                            <div
                                                class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-indigo-600 font-bold text-lg ring-2 ring-white shadow-sm">
                                                <?php echo e(substr($user->name, 0, 1)); ?>

                                            </div>
                                        <?php endif; ?>
                                        <?php if($user->isOnline()): ?>
                                            <span
                                                class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white bg-green-400"></span>
                                        <?php endif; ?>
                                    </a>

                                    <div class="flex-grow min-w-0">
                                        <a href="<?php echo e(route('profile.show', $user->id)); ?>"
                                            class="text-sm font-bold text-gray-900 truncate block group-hover:text-indigo-600 transition"><?php echo e($user->name); ?></a>
                                        <p class="text-xs text-gray-500 truncate"><?php echo e($user->unvan ?? 'Personel'); ?></p>
                                    </div>

                                    <?php if($user->hasRole('Bölüm Lideri')): ?>
                                        <div class="flex-shrink-0" title="Bölüm Lideri">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.616a1 1 0 01.894-1.79l1.599.8L9 4.323V3a1 1 0 011-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div
                                    class="text-center py-8 text-gray-400 text-sm italic bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                    Bu bölüme henüz personel atanmamış.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <?php if($bolum->has_machines): ?>
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6"
                            x-data="{ addModalOpen: false, editModalOpen: false, selectedMachine: null }">

                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                        Makine Envanteri
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">Toplam <?php echo e($machines->count()); ?> makine kayıtlı.
                                    </p>
                                </div>
                                <?php if(Auth::user()->hasRole('Superadmin') || (Auth::user()->hasRole('Bölüm Lideri') && Auth::user()->bolum_id == $bolum->id)): ?>
                                <button @click="addModalOpen = true"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 px-4 rounded-lg shadow-sm transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Makine Ekle
                                </button>
                                <?php endif; ?>
                            </div>

                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Makine Adı</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Durum</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Kurulum</th>
                                            <?php if(Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Yonetim')): ?>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Ekleyen</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Kayıt Tarihi</th>
                                            <?php endif; ?>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $__empty_1 = true; $__currentLoopData = $machines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $machine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr class="hover:bg-gray-50 transition group">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-bold text-gray-900"><?php echo e($machine->name); ?></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php
                                                        $statusClasses = [
                                                            'active' => 'bg-green-100 text-green-800',
                                                            'maintenance' => 'bg-amber-100 text-amber-800',
                                                            'broken' => 'bg-red-100 text-red-800',
                                                            'inactive' => 'bg-gray-100 text-gray-800'
                                                        ];
                                                        $statusLabels = [
                                                            'active' => 'Aktif',
                                                            'maintenance' => 'Bakımda',
                                                            'broken' => 'Arızalı',
                                                            'inactive' => 'Pasif'
                                                        ];
                                                    ?>
                                                    <span
                                                        class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-full <?php echo e($statusClasses[$machine->status] ?? $statusClasses['inactive']); ?>">
                                                        <?php echo e($statusLabels[$machine->status] ?? $machine->status); ?>

                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo e($machine->installation_date ? \Carbon\Carbon::parse($machine->installation_date)->format('d.m.Y') : '-'); ?>

                                                </td>
                                                <?php if(Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Yonetim')): ?>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo e($machine->creator->name ?? '-'); ?>

                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo e($machine->created_at->format('d.m.Y H:i')); ?>

                                                </td>
                                                <?php endif; ?>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div
                                                        class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <?php if(Auth::user()->hasRole('Superadmin') || (Auth::user()->hasRole('Bölüm Lideri') && Auth::user()->bolum_id == $bolum->id)): ?>
                                                        <button @click="selectedMachine = <?php echo e($machine); ?>; editModalOpen = true"
                                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-md transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                viewBox="0 0 20 20" fill="currentColor">
                                                                <path
                                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                            </svg>
                                                        </button>
                                                        <?php endif; ?>
                                                        <?php if(Auth::user()->hasRole('Superadmin')): ?>
                                                            <form action="<?php echo e(route('admin.machines.destroy', $machine->id)); ?>"
                                                                method="POST"
                                                                onsubmit="return confirm('Silmek istediğinize emin misiniz?');"
                                                                class="inline">
                                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                                <button type="submit"
                                                                    class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                        viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-10 w-10 text-gray-300 mx-auto mb-2" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                                    </svg>
                                                    <span class="block">Henüz makine eklenmemiş.</span>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            
                            
                            
                            <div x-show="addModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                <div
                                    class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 transition-opacity" aria-hidden="true"
                                        @click="addModalOpen = false">
                                        <div class="absolute inset-0 bg-gray-900 opacity-75 backdrop-blur-sm"></div>
                                    </div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                        aria-hidden="true">&#8203;</span>
                                    <div
                                        class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <form action="<?php echo e(route('admin.bolumler.machines.store', $bolum->id)); ?>"
                                            method="POST">
                                            <?php echo csrf_field(); ?>
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="flex justify-between items-center mb-4">
                                                    <h3 class="text-lg leading-6 font-bold text-gray-900">Makine Ekle</h3>
                                                    <button type="button" @click="addModalOpen = false"
                                                        class="text-gray-400 hover:text-gray-500">
                                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Makine
                                                            Adı</label>
                                                        <input type="text" name="name"
                                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                            required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kurulum
                                                            Tarihi</label>
                                                        <input type="date" name="installation_date"
                                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                                                        <select name="status"
                                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                            <option value="active">Aktif</option>
                                                            <option value="maintenance">Bakımda</option>
                                                            <option value="broken">Arızalı</option>
                                                            <option value="inactive">Pasif</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit"
                                                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Kaydet</button>
                                                <button type="button" @click="addModalOpen = false"
                                                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">İptal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            
                            <div x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                <div
                                    class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 transition-opacity" aria-hidden="true"
                                        @click="editModalOpen = false">
                                        <div class="absolute inset-0 bg-gray-900 opacity-75 backdrop-blur-sm"></div>
                                    </div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                        aria-hidden="true">&#8203;</span>
                                    <div
                                        class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <form :action="`/admin/machines/${selectedMachine?.id}`" method="POST">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="flex justify-between items-center mb-4">
                                                    <h3 class="text-lg leading-6 font-bold text-gray-900">Makine Düzenle
                                                    </h3>
                                                    <button type="button" @click="editModalOpen = false"
                                                        class="text-gray-400 hover:text-gray-500">
                                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Makine
                                                            Adı</label>
                                                        <input type="text" name="name" x-model="selectedMachine?.name"
                                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                            required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kurulum
                                                            Tarihi</label>
                                                        <input type="date" name="installation_date"
                                                            x-model="selectedMachine?.installation_date"
                                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                                                        <select name="status" x-model="selectedMachine?.status"
                                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                            <option value="active">Aktif</option>
                                                            <option value="maintenance">Bakımda</option>
                                                            <option value="broken">Arızalı</option>
                                                            <option value="inactive">Pasif</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="submit"
                                                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Güncelle</button>
                                                <button type="button" @click="editModalOpen = false"
                                                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">İptal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endif; ?>

            </div>
            
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
                
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" 
                     x-data="{ addHammaddeModal: false, editHammaddeModal: false, selectedHammadde: null }">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                            Hammaddeler
                        </h3>
                        <?php if(Auth::user()->hasRole('Superadmin') || (Auth::user()->hasRole('Bölüm Lideri') && Auth::user()->bolum_id == $bolum->id)): ?>
                        <button @click="addHammaddeModal = true" class="text-emerald-600 hover:bg-emerald-50 px-3 py-1 rounded-lg text-sm font-bold transition flex items-center gap-1">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Ekle
                        </button>
                        <?php endif; ?>
                    </div>
                    
                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $hammaddeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hammadde): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-lg border border-transparent hover:border-gray-100 transition group">
                                <div class="flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full <?php echo e($hammadde->aktif_mi ? 'bg-emerald-500' : 'bg-gray-300'); ?>"></span>
                                    <span class="font-medium text-gray-700"><?php echo e($hammadde->ad); ?></span>
                                </div>
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <?php if(Auth::user()->hasRole('Superadmin') || (Auth::user()->hasRole('Bölüm Lideri') && Auth::user()->bolum_id == $bolum->id)): ?>
                                    <button @click="selectedHammadde = <?php echo e($hammadde); ?>; editHammaddeModal = true" class="text-indigo-500 hover:text-indigo-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                          <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                     <form action="<?php echo e(route('admin.bolumler.hammaddeler.delete', $hammadde->id)); ?>" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-sm text-gray-400 italic text-center py-4">Hammadde bulunamadı.</p>
                        <?php endif; ?>
                    </div>

                    
                    <div x-show="addHammaddeModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-gray-900 opacity-75 backdrop-blur-sm" @click="addHammaddeModal = false"></div>
                            <div class="bg-white rounded-2xl shadow-xl z-50 w-full max-w-md p-6 relative">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Hammadde Ekle</h3>
                                <form action="<?php echo e(route('admin.bolumler.hammaddeler.store', $bolum->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Hammadde Adı</label>
                                            <input type="text" name="ad" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        </div>
                                         <div class="flex items-center">
                                            <input type="checkbox" name="aktif_mi" value="1" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label class="ml-2 block text-sm text-gray-900">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" @click="addHammaddeModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">İptal</button>
                                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium">Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="editHammaddeModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-gray-900 opacity-75 backdrop-blur-sm" @click="editHammaddeModal = false"></div>
                            <div class="bg-white rounded-2xl shadow-xl z-50 w-full max-w-md p-6 relative">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Hammadde Düzenle</h3>
                                <form :action="`/admin/hammaddeler/${selectedHammadde?.id}`" method="POST">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Hammadde Adı</label>
                                            <input type="text" name="ad" x-model="selectedHammadde?.ad" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        </div>
                                         <div class="flex items-center">
                                            <input type="checkbox" name="aktif_mi" value="1" :checked="selectedHammadde?.aktif_mi" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label class="ml-2 block text-sm text-gray-900">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" @click="editHammaddeModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">İptal</button>
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">Güncelle</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" 
                     x-data="{ addVersiyonModal: false, editVersiyonModal: false, selectedVersiyon: null }">
                    <div class="flex justify-between items-center mb-6">
                         <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Ürün Versiyonları
                        </h3>
                        <?php if(Auth::user()->hasRole('Superadmin') || (Auth::user()->hasRole('Bölüm Lideri') && Auth::user()->bolum_id == $bolum->id)): ?>
                        <button @click="addVersiyonModal = true" class="text-cyan-600 hover:bg-cyan-50 px-3 py-1 rounded-lg text-sm font-bold transition flex items-center gap-1">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Ekle
                        </button>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $versiyonlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $versiyon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                             <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-lg border border-transparent hover:border-gray-100 transition group">
                                <div class="flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full <?php echo e($versiyon->aktif_mi ? 'bg-cyan-500' : 'bg-gray-300'); ?>"></span>
                                    <span class="font-medium text-gray-700"><?php echo e($versiyon->ad); ?></span>
                                </div>
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <?php if(Auth::user()->hasRole('Superadmin') || (Auth::user()->hasRole('Bölüm Lideri') && Auth::user()->bolum_id == $bolum->id)): ?>
                                    <button @click="selectedVersiyon = <?php echo e($versiyon); ?>; editVersiyonModal = true" class="text-indigo-500 hover:text-indigo-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                          <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                     <form action="<?php echo e(route('admin.bolumler.versiyonlar.delete', $versiyon->id)); ?>" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-sm text-gray-400 italic text-center py-4">Versiyon bulunamadı.</p>
                        <?php endif; ?>
                    </div>

                    
                    <div x-show="addVersiyonModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-gray-900 opacity-75 backdrop-blur-sm" @click="addVersiyonModal = false"></div>
                            <div class="bg-white rounded-2xl shadow-xl z-50 w-full max-w-md p-6 relative">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Versiyon Ekle</h3>
                                <form action="<?php echo e(route('admin.bolumler.versiyonlar.store', $bolum->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Versiyon Adı</label>
                                            <input type="text" name="ad" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        </div>
                                         <div class="flex items-center">
                                            <input type="checkbox" name="aktif_mi" value="1" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label class="ml-2 block text-sm text-gray-900">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" @click="addVersiyonModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">İptal</button>
                                        <button type="submit" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium">Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="editVersiyonModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-gray-900 opacity-75 backdrop-blur-sm" @click="editVersiyonModal = false"></div>
                            <div class="bg-white rounded-2xl shadow-xl z-50 w-full max-w-md p-6 relative">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Versiyon Düzenle</h3>
                                <form :action="`/admin/versiyonlar/${selectedVersiyon?.id}`" method="POST">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Versiyon Adı</label>
                                            <input type="text" name="ad" x-model="selectedVersiyon?.ad" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        </div>
                                         <div class="flex items-center">
                                            <input type="checkbox" name="aktif_mi" value="1" :checked="selectedVersiyon?.aktif_mi" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label class="ml-2 block text-sm text-gray-900">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" @click="editVersiyonModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">İptal</button>
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">Güncelle</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            
            <?php if(isset($sikayetler) && $sikayetler->isNotEmpty() && $bolum->sikayet_kategorileri_count > 0): ?>
            <div id="sikayetler" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-8">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Bölüm Şikayetleri
                            <span class="ml-2 px-2.5 py-0.5 rounded-full bg-red-100 text-red-600 text-xs font-bold"><?php echo e($sikayetler->total()); ?></span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Bu bölüme ait tüm müşteri şikayetleri.</p>
                    </div>
                    
                    
                    <form method="GET" action="<?php echo e(route('admin.bolumler.dashboard', $bolum->id)); ?>#sikayetler" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                        <select name="customer_id" class="text-sm border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                             <option value="">Tüm Müşteriler</option>
                             <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($customer->id); ?>" <?php echo e(request('customer_id') == $customer->id ? 'selected' : ''); ?>><?php echo e($customer->name); ?></option>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                         <select name="status" class="text-sm border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                             <option value="">Tüm Durumlar</option>
                             <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>" <?php echo e(request('status') == $status ? 'selected' : ''); ?>><?php echo e($status); ?></option>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="text-sm border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">Filtrele</button>
                        <?php if(request()->hasAny(['customer_id', 'status', 'start_date'])): ?>
                            <a href="<?php echo e(route('admin.bolumler.dashboard', $bolum->id)); ?>#sikayetler" class="text-gray-500 text-sm hover:text-red-500 underline">Temizle</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#ID</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Müşteri</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Konu / Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Durum</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tarih</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Çözüm Takımı</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                             <?php $__currentLoopData = $sikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-4 py-4 whitespace-nowrap text-xs font-bold text-gray-400">#<?php echo e($sikayet->id); ?></td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-bold text-gray-900 line-clamp-2" title="<?php echo e($sikayet->customer->name ?? $sikayet->musteri_adi ?? 'Bilinmiyor'); ?>"><?php echo e($sikayet->customer->name ?? $sikayet->musteri_adi ?? 'Bilinmiyor'); ?></div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="max-w-[200px] sm:max-w-[250px]">
                                            <div class="text-sm text-gray-900 font-medium truncate" title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>">
                                                <?php echo e($sikayet->musteri_sikayet_konusu); ?>

                                            </div>
                                            <div class="text-xs text-gray-500 truncate mt-0.5" title="<?php echo e($sikayet->sikayetKategori->ad ?? '-'); ?> > <?php echo e($sikayet->sikayetAltKategori->ad ?? 'Genel'); ?>">
                                                <?php echo e($sikayet->sikayetKategori->ad ?? '-'); ?> > <?php echo e($sikayet->sikayetAltKategori->ad ?? 'Genel'); ?>

                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <?php echo $sikayet->musteri_durum_badge; ?>

                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($sikayet->musteri_sikayet_tarihi ? $sikayet->musteri_sikayet_tarihi->format('d.m.Y') : '-'); ?>

                                    </td>
                                    <td class="px-4 py-4 relative">
                                        <?php if($sikayet->cozumTakimi): ?>
                                            <div class="group/team relative inline-block">
                                                 <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 cursor-help transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    <?php echo e($sikayet->cozumTakimi->ad); ?>

                                                 </span>

                                                 
                                                 <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-64 bg-gray-900/95 backdrop-blur text-white text-xs rounded-xl py-3 px-4 shadow-2xl opacity-0 scale-95 group-hover/team:opacity-100 group-hover/team:scale-100 transition duration-200 pointer-events-none z-50">
                                                     <div class="font-bold border-b border-gray-700 pb-2 mb-2 text-indigo-300"><?php echo e($sikayet->cozumTakimi->ad); ?> Üyeleri</div>
                                                     <ul class="space-y-1.5">
                                                         <?php $__empty_1 = true; $__currentLoopData = $sikayet->cozumTakimi->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                            <li class="flex items-center justify-between">
                                                                <span><?php echo e($uye->name); ?></span>
                                                                <?php if($uye->id == $sikayet->cozumTakimi->lider_id): ?>
                                                                    <span class="text-[10px] bg-indigo-500 px-1.5 rounded text-white">Lider</span>
                                                                <?php endif; ?>
                                                            </li>
                                                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                            <li class="italic text-gray-500">Üye bulunamadı.</li>
                                                         <?php endif; ?>
                                                     </ul>
                                                     
                                                     <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-8 border-transparent border-t-gray-900/95"></div>
                                                 </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm">
                                         <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>" class="text-indigo-600 hover:text-indigo-900 font-bold hover:underline">Detay</a>
                                         <?php if($sikayet->iaaProjesi): ?>
                                             <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaaProjesi->id)); ?>" class="ml-3 text-purple-600 hover:text-purple-900 font-bold hover:underline text-xs bg-purple-50 px-2 py-1 rounded">Proje</a>
                                         <?php endif; ?>
                                    </td>
                                </tr>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if($sikayetler->hasPages()): ?>
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <?php echo e($sikayetler->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            
            <?php if(isset($disiplinDosyalari) && $disiplinDosyalari->isNotEmpty()): ?>
            <div id="disiplin" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-8">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                        Bölüm Disiplin Dosyaları
                        <span class="ml-2 px-2.5 py-0.5 rounded-full bg-orange-100 text-orange-600 text-xs font-bold"><?php echo e($disiplinDosyalari->count()); ?></span>
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tarih</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Personel</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">İhlal (Suç)</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                       <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $disiplinDosyalari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($dosya->olay_tarihi ? $dosya->olay_tarihi->format('d.m.Y') : '-'); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">
                                                <?php echo e(substr($dosya->user->name ?? '?', 0, 1)); ?>

                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-bold text-gray-900"><?php echo e($dosya->user->name ?? 'Bilinmiyor'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 line-clamp-1" title="<?php echo e($dosya->behavior->name ?? '-'); ?>"><?php echo e($dosya->behavior->name ?? '-'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <?php echo e($dosya->durum); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="#" class="text-orange-600 hover:text-orange-900 font-bold hover:underline">Görüntüle</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                       </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if((Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Yonetim')) && isset($machineLogs)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 mt-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Makine İşlem Geçmişi
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Makine</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detaylar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $machineLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($log->created_at->format('d.m.Y H:i')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($log->user->name ?? 'Silinmiş Kullanıcı'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($log->machine->name ?? ($log->details['deleted_machine_name'] ?? '-')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo e($log->action == 'Ekleme' ? 'bg-green-100 text-green-800' : 
                                        ($log->action == 'Silme' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')); ?>">
                                        <?php echo e($log->action); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="max-w-xs truncate">
                                        <?php if(is_array($log->details)): ?>
                                            <?php $__currentLoopData = $log->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(!is_array($value)): ?>
                                                    <span class="font-medium"><?php echo e($key); ?>:</span> <?php echo e($value); ?><br>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <?php echo e($log->details); ?>

                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Bu bölüme ait henüz makine işlem kaydı bulunmamaktadır.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/bolumler/dashboard.blade.php ENDPATH**/ ?>