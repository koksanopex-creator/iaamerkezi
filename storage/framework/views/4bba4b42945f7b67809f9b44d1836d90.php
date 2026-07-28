<?php $__env->startPush('pageTitle'); ?>
    Tüm Bekleyen İşler | 
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
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .table-container {
            max-width: 100%;
            overflow-x: auto;
        }

        table {
            table-layout: fixed;
            width: 100%;
        }
    </style>

     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in">
            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-2">
                        <li>
                            <a href="<?php echo e(route('dashboard')); ?>"
                                class="text-xs font-bold text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-widest">
                                Dashboard
                            </a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-slate-300 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-xs font-bold text-slate-800 uppercase tracking-widest">Bekleyen
                                İşler</span>
                        </li>
                    </ol>
                </nav>
                <h2 class="font-black text-3xl text-slate-900 leading-tight tracking-tight">
                    Bekleyen İş <span class="text-indigo-600">Havuzu</span>
                </h2>
                <p class="text-slate-500 text-sm font-medium mt-1">Sistem genelindeki tüm aksiyon bekleyen süreçlerin
                    merkezi yönetim alanı.</p>
            </div>

            <button onclick="window.history.back()"
                class="inline-flex items-center px-5 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 hover:border-indigo-200 hover:bg-indigo-50/30 hover:text-indigo-600 transition-all shadow-sm active:scale-95 group">
                <svg class="w-5 h-5 mr-2 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Geri Dön
            </button>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12 bg-[#f8fafc]">
        <div class="max-w-[1600px] mx-auto sm:px-6 lg:px-8">

            <!-- ÜST İSTATİSTİK KARTLARI -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 animate-fade-in"
                style="animation-delay: 0.1s">
                <!-- Toplam Kartı -->
                <div
                    class="glass-card p-6 rounded-[2rem] shadow-sm border border-white flex justify-between items-center group transition-all hover:shadow-xl hover:-translate-y-1">
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Toplam İş</p>
                        <h4 class="text-3xl font-black text-slate-900"><?php echo e($stats['toplam']); ?></h4>
                        <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-full">AKTİF
                            HAVUZ</span>
                    </div>
                    <div
                        class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 group-hover:rotate-12 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>

                <!-- Onay Bekleyen Kartı -->
                <div
                    class="glass-card p-6 rounded-[2rem] shadow-sm border border-white flex justify-between items-center group transition-all hover:shadow-xl hover:-translate-y-1">
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Onay Bekleyen</p>
                        <h4 class="text-3xl font-black text-slate-900"><?php echo e($stats['onay_bekleyen']); ?></h4>
                        <span class="text-[10px] font-bold text-amber-500 bg-amber-50 px-2 py-0.5 rounded-full">KRİTİK
                            ADIM</span>
                    </div>
                    <div
                        class="w-14 h-14 bg-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-200 group-hover:rotate-12 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                </div>

                <!-- Aktif İşlemdeki Kartı -->
                <div
                    class="glass-card p-6 rounded-[2rem] shadow-sm border border-white flex justify-between items-center group transition-all hover:shadow-xl hover:-translate-y-1">
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">İşlemde</p>
                        <h4 class="text-3xl font-black text-slate-900"><?php echo e($stats['aktif_islemde']); ?></h4>
                        <span
                            class="text-[10px] font-bold text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full">SÜREÇ
                            DEVAM EDİYOR</span>
                    </div>
                    <div
                        class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200 group-hover:rotate-12 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>

                <!-- Arabuluculuk Kartı -->
                <div
                    class="glass-card p-6 rounded-[2rem] shadow-sm border border-white flex justify-between items-center group transition-all hover:shadow-xl hover:-translate-y-1">
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Arabuluculuk</p>
                        <h4 class="text-3xl font-black text-slate-900"><?php echo e($stats['arabuluculuk']); ?></h4>
                        <span
                            class="text-[10px] font-bold text-purple-500 bg-purple-50 px-2 py-0.5 rounded-full">HUKUKSAL
                            SÜREÇ</span>
                    </div>
                    <div
                        class="w-14 h-14 bg-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-200 group-hover:rotate-12 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- FİLTRELEME ALANI -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 mb-8 animate-fade-in"
                style="animation-delay: 0.2s">
                <form action="<?php echo e(route('admin.tum-bekleyen-isler')); ?>" method="GET"
                    class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">İş
                            Kategorisi</label>
                        <select name="tur"
                            class="w-full h-12 rounded-2xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all cursor-pointer">
                            <option value="">Tüm Kategoriler</option>
                            <?php $__currentLoopData = $turler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tur); ?>" <?php echo e(request('tur') == $tur ? 'selected' : ''); ?>><?php echo e($tur); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">İlgili
                            Bölüm</label>
                        <select name="bolum"
                            class="w-full h-12 rounded-2xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all cursor-pointer">
                            <option value="">Tüm Bölümler</option>
                            <?php $__currentLoopData = $bolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($bolum); ?>" <?php echo e(request('bolum') == $bolum ? 'selected' : ''); ?>><?php echo e($bolum); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Güncel
                            Durum</label>
                        <select name="durum"
                            class="w-full h-12 rounded-2xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all cursor-pointer">
                            <option value="">Tüm Durumlar</option>
                            <?php $__currentLoopData = $durumlarListesi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $durum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $displayDurum = match($durum) {
                                        'Bölüm Onayı Bekliyor', 'Onay Bekliyor' => 'Bölüm Kalite Yöneticisi Onayı Bekliyor',
                                        'Yönetici Onayı Bekliyor' => 'Final Onay Bekliyor',
                                        'talep_onayi_bekliyor_kalite' => 'Müşteri Talebi: Kalite Onayı Bekliyor',
                                        'talep_onayi_bekliyor_direktor' => 'Müşteri Talebi: Direktör Onayı Bekliyor',
                                        'talep_onayi_bekliyor_superadmin' => 'Müşteri Talebi: Yönetim Onayı Bekliyor',
                                        'hatali_bildirim_onayi_bekliyor_kalite' => 'Hatalı Bildirim: Kalite Onayı Bekliyor',
                                        'hatali_bildirim_onayi_bekliyor_direktor' => 'Hatalı Bildirim: Direktör Onayı Bekliyor',
                                        'hatali_bildirim_onayi_bekliyor_superadmin' => 'Hatalı Bildirim: Yönetim Onayı Bekliyor',
                                        default => $durum
                                    };
                                ?>
                                <option value="<?php echo e($durum); ?>" <?php echo e(request('durum') == $durum ? 'selected' : ''); ?>><?php echo e($displayDurum); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit"
                            class="flex-1 h-12 bg-slate-900 text-white rounded-2xl text-sm font-black hover:bg-indigo-600 transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            SORGULA
                        </button>
                        <?php if(request()->anyFilled(['tur', 'bolum', 'durum'])): ?>
                            <a href="<?php echo e(route('admin.tum-bekleyen-isler')); ?>"
                                class="w-12 h-12 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center hover:bg-slate-200 transition-all group"
                                title="Sıfırla">
                                <svg class="w-5 h-5 group-hover:rotate-180 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- TABLO ALANI -->
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden animate-fade-in"
                style="animation-delay: 0.3s">
                <div class="table-container custom-scrollbar">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th
                                    class="px-8 py-6 font-black text-[11px] text-slate-500 uppercase tracking-widest w-[160px]">
                                    Kategori</th>
                                <th
                                    class="px-8 py-6 font-black text-[11px] text-slate-500 uppercase tracking-widest min-w-[350px]">
                                    Başlık / Konu</th>
                                <th
                                    class="px-8 py-6 font-black text-[11px] text-slate-500 uppercase tracking-widest w-[220px]">
                                    Sorumlu & Bölüm</th>
                                <th
                                    class="px-8 py-6 font-black text-[11px] text-slate-500 uppercase tracking-widest w-[200px] text-center">
                                    Durum</th>
                                <th
                                    class="px-8 py-6 font-black text-[11px] text-slate-500 uppercase tracking-widest w-[140px] text-center">
                                    Bekleme</th>
                                <th
                                    class="px-8 py-6 font-black text-[11px] text-slate-500 uppercase tracking-widest w-[100px] text-right">
                                    İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php $__empty_1 = true; $__currentLoopData = $bekleyenIsler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $is): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-indigo-50/20 transition-all group">
                                    <td class="px-8 py-7">
                                        <?php if($is['tur'] == 'İAA'): ?>
                                            <span
                                                class="inline-flex px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black border border-indigo-100 shadow-sm leading-none uppercase">
                                                İAA
                                            </span>
                                        <?php elseif($is['tur'] == 'Müşteri Şikayeti'): ?>
                                            <span
                                                class="inline-flex px-3 py-1 bg-rose-50 text-rose-600 rounded-lg text-[10px] font-black border border-rose-100 shadow-sm leading-none uppercase">
                                                Şikayet
                                            </span>
                                        <?php elseif($is['tur'] == 'Arabuluculuk'): ?>
                                            <span
                                                class="inline-flex px-3 py-1 bg-purple-50 text-purple-600 rounded-lg text-[10px] font-black border border-purple-100 shadow-sm leading-none uppercase">
                                                Arabulucu
                                            </span>
                                        <?php elseif($is['tur'] == 'Disiplin'): ?>
                                            <span
                                                class="inline-flex px-3 py-1 bg-violet-50 text-violet-600 rounded-lg text-[10px] font-black border border-violet-100 shadow-sm leading-none uppercase">
                                                Disiplin
                                            </span>
                                        <?php elseif($is['tur'] == 'Ziyaret Planı'): ?>
                                            <span
                                                class="inline-flex px-3 py-1 bg-teal-50 text-teal-600 rounded-lg text-[10px] font-black border border-teal-100 shadow-sm leading-none uppercase">
                                                Ziyaret
                                            </span>
                                        <?php elseif($is['tur'] == 'Kullanıcı Kaydı'): ?>
                                            <span
                                                class="inline-flex px-3 py-1 bg-slate-900 text-white rounded-lg text-[10px] font-black border border-slate-950 shadow-sm leading-none uppercase">
                                                Yeni Üye
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="inline-flex px-3 py-1 bg-slate-50 text-slate-600 rounded-lg text-[10px] font-black border border-slate-100 shadow-sm leading-none uppercase">
                                                Kayıt
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-8 py-7">
                                        <div class="flex flex-col max-w-full overflow-hidden">
                                            <span
                                                class="text-slate-900 font-extrabold text-sm mb-1 line-clamp-2 leading-relaxed"
                                                title="<?php echo e($is['konu']); ?>">
                                                <?php echo e($is['konu']); ?>

                                            </span>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-slate-400">#<?php echo e($is['id']); ?></span>
                                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                <?php if($is['oncelik'] == 'Yüksek'): ?>
                                                    <span
                                                        class="text-[9px] font-black text-red-500 uppercase tracking-tighter">Acil
                                                        Aksiyon</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-7">
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2 mb-1.5">
                                                <div
                                                    class="w-7 h-7 bg-slate-100 rounded-full flex items-center justify-center text-[10px] font-black text-slate-500 border border-slate-200">
                                                    <?php echo e(substr($is['personel'], 0, 1)); ?>

                                                </div>
                                                <span
                                                    class="text-slate-800 font-extrabold text-xs truncate max-w-[150px]"><?php echo e($is['personel']); ?></span>
                                            </div>
                                            <span
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-tight"><?php echo e($is['bolum']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-7 text-center">
                                        <?php $roundedGun = ceil($is['gun']); ?>
                                        <?php if(isset($is['status_html'])): ?>
                                            <div class="inline-flex justify-center scale-90 origin-center">
                                                <?php echo $is['status_html']; ?>

                                            </div>
                                        <?php else: ?>
                                            <span class="inline-block px-4 py-1.5 rounded-2xl bg-gray-100 text-gray-600 text-[10px] font-black border border-gray-200 leading-none truncate max-w-full" title="<?php echo e($is['durum']); ?>">
                                                <?php echo e(strtoupper($is['durum'] == 'Bölüm Onayı Bekliyor' ? 'Bölüm Kalite Yöneticisi Onayı Bekliyor' : ($is['durum'] == 'Yönetici Onayı Bekliyor' ? 'Final Onay Bekliyor' : $is['durum']))); ?>

                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-8 py-7 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span
                                                class="text-sm font-black <?php echo e($roundedGun > 7 ? 'text-red-600' : ($roundedGun > 3 ? 'text-amber-500' : 'text-slate-900')); ?>">
                                                <?php echo e($roundedGun); ?> <span
                                                    class="text-[10px] font-black text-slate-400 ml-0.5">GÜN</span>
                                            </span>
                                            <div class="w-10 h-1 bg-slate-100 rounded-full mt-1.5 overflow-hidden">
                                                <div class="h-full <?php echo e($roundedGun > 7 ? 'bg-red-500' : ($roundedGun > 3 ? 'bg-amber-500' : 'bg-emerald-500')); ?>"
                                                    style="width: <?php echo e(min(($roundedGun / 15) * 100, 100)); ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-7 text-right">
                                        <a href="<?php echo e($is['link']); ?>"
                                            class="inline-flex items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-2xl text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50/50 hover:rotate-12 transition-all shadow-sm group">
                                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-8 py-32 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 shadow-inner">
                                                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-black text-slate-900 mb-2">Havuz Temiz!</h3>
                                            <p class="text-slate-500 font-medium max-w-sm mx-auto">Seçtiğiniz kriterlerde
                                                bekleyen herhangi bir iş kaydı bulunmamaktadır.</p>
                                            <?php if(request()->anyFilled(['tur', 'bolum', 'durum'])): ?>
                                                <a href="<?php echo e(route('admin.tum-bekleyen-isler')); ?>"
                                                    class="mt-8 px-6 py-3 bg-indigo-50 text-indigo-600 rounded-2xl text-sm font-black hover:bg-indigo-100 transition-all border border-indigo-100">
                                                    Tüm Filtreleri Temizle
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($bekleyenIsler->hasPages()): ?>
                    <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
                        <?php echo e($bekleyenIsler->links()); ?>

                    </div>
                <?php endif; ?>
            </div>

            <!-- FOOTER INFO -->
            <div class="mt-12 flex flex-col md:flex-row justify-between items-center gap-4 px-8 animate-fade-in"
                style="animation-delay: 0.4s">
                <div class="flex items-center gap-2">
                    <span
                        class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-lg shadow-emerald-200 animate-pulse"></span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sistem Gerçek Zamanlı
                        Aktif</span>
                </div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                    © <?php echo e(date('Y')); ?> KVKK / IAA YÖNETİM MERKEZİ • TÜM HAKLARI SAKLIDIR
                </div>
                <div
                    class="flex items-center gap-1.5 grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Premium
                        Dashboard</span>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/tum-bekleyen-isler.blade.php ENDPATH**/ ?>