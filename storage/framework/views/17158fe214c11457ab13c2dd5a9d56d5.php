<div class="relative bg-gray-900 pb-24 overflow-hidden shadow-2xl">
    
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-gray-900 to-black">
        <div class="absolute inset-0 bg-gradient-to-b from-white/5 via-transparent to-black/60"></div>
    </div>

    <div
        class="relative max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-8">

        
        <div class="flex items-center gap-6 w-full md:w-auto">
            <div class="relative flex-shrink-0"
                style="width: 112px; height: 112px; min-width: 112px; min-height: 112px;">
                <div
                    class="h-full w-full rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 p-1 shadow-2xl ring-4 ring-white/10">
                    <?php if($user->profile_photo_path): ?>
                        <img class="h-full w-full rounded-full object-cover border-4 border-gray-900"
                            src="<?php echo e(asset('storage/' . $user->profile_photo_path)); ?>" alt="<?php echo e($user->name); ?>">
                    <?php else: ?>
                        <div
                            class="h-full w-full rounded-full bg-gray-800 flex items-center justify-center text-4xl font-bold text-white uppercase border-4 border-gray-900">
                            <?php echo e(substr($user->name, 0, 1)); ?>

                        </div>
                    <?php endif; ?>
                </div>
                <div class="absolute bottom-1 right-1 bg-emerald-500 h-6 w-6 rounded-full border-4 border-gray-900 shadow-lg"
                    title="Çevrimiçi"></div>
            </div>

            <div class="text-left flex-1">
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white drop-shadow-lg">
                    <?php echo e($user->name); ?></h1>
                <div class="flex flex-wrap items-center justify-start gap-2 mt-3">
                    <span
                        class="px-3 py-1 rounded-lg text-xs font-bold bg-indigo-600 text-white shadow-md border border-indigo-400/50">
                        <?php echo e($user->roles->first()->name ?? 'Kullanıcı'); ?>

                    </span>
                    <?php if($user->bolum): ?>
                        <span
                            class="px-3 py-1 rounded-lg text-xs font-bold bg-white/10 text-white border border-white/20 backdrop-blur-sm">
                            <?php echo e($user->bolum->ad); ?>

                        </span>
                    <?php endif; ?>
                    <span
                        class="px-3 py-1 rounded-lg text-xs font-medium bg-black/30 text-gray-300 border border-white/10">
                        Son Giriş: <?php echo e($lastLogin ? $lastLogin->format('d.m.Y H:i') : 'Bilinmiyor'); ?>

                    </span>

                    <?php if(auth()->user()->hasRole('Superadmin') && auth()->id() != $user->id): ?>
                        <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>"
                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-amber-500 text-white hover:bg-amber-400 transition-colors shadow-md border border-amber-400 ml-2">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Yönetici Olarak Düzenle
                        </a>
                    <?php endif; ?>

                    <?php if(auth()->id() == $user->id): ?>
                        <a href="<?php echo e(route('profile.edit')); ?>"
                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-blue-600 text-white hover:bg-blue-500 transition-colors shadow-md border border-blue-500 ml-2">
                            Profili Düzenle
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <a href="<?php echo e(route('profile.puanlar', $user->id)); ?>"
            class="block transform transition hover:scale-105 w-full md:w-auto">
            <div
                class="bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-white/10 text-center min-w-[220px] shadow-2xl hover:bg-white/10 transition-all group relative overflow-hidden">
                <p
                    class="text-xs font-bold text-gray-300 uppercase tracking-widest group-hover:text-white transition-colors">
                    GENEL PUAN</p>
                <div class="flex items-center justify-center gap-2 mt-1 relative z-10">
                    <svg class="w-6 h-6 text-yellow-400 drop-shadow-md" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <p class="text-5xl font-black text-white tracking-tight drop-shadow-xl">
                        <?php echo e(number_format($user->toplam_puan ?? 0, 0, ',', '.')); ?></p>
                </div>
                <div
                    class="mt-2 inline-flex items-center px-3 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-gray-200 border border-white/10 relative z-10 shadow-inner">
                    <?php echo e($user->toplam_puan > 1000 ? '🏆 Usta Seviye' : ($user->toplam_puan > 500 ? '⭐ Uzman Seviye' : '🌱 Başlangıç')); ?>

                </div>
                <div
                    class="mt-4 pt-3 border-t border-white/10 text-[10px] text-gray-400 group-hover:text-white transition-colors flex justify-center items-center gap-1 relative z-10">
                    Detayları Gör <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/profile/partials/show/header.blade.php ENDPATH**/ ?>