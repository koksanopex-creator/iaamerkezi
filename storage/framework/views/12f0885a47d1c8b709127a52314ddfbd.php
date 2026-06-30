<?php $__env->startPush('pageTitle'); ?>
    Bölüm Puanları Analizi | 
<?php $__env->stopPush(); ?>

<?php $__env->startPush('head'); ?>
<style>
    @media print {
        .no-print, nav, aside, footer, .py-12 > div > div:nth-child(1), .py-12 > div > div:nth-child(2) {
            display: none !important;
        }
        .max-w-7xl { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
        .bg-white { border: none !important; box-shadow: none !important; }
        .py-12 { padding-top: 0 !important; padding-bottom: 0 !important; }
        table { font-size: 10px !important; }
    }
</style>
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
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    Bölüm Puanları Analizi
                </h2>
                <p class="text-xs text-gray-400 font-medium mt-1">Departman bazlı başarı ve puan dökümü dökümü</p>
            </div>
            <div class="flex items-center gap-2 no-print">
                <div class="flex bg-white border border-gray-100 rounded-xl p-1 shadow-sm mr-2">
                    <a href="<?php echo e(route('tum-bolum-puanlari.export.excel', request()->all())); ?>" class="p-2 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Excel İndir">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 00-2-2H5a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v8m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
                    </a>
                    <a href="<?php echo e(route('tum-bolum-puanlari.export.pdf', request()->all())); ?>" class="p-2 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="PDF İndir">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </a>
                    <button onclick="window.print()" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Yazdır">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2m8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2z"></path></svg>
                    </button>
                </div>
                <a href="<?php echo e(route('puan-durumu')); ?>" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-50 transition shadow-sm">
                    Genel Puan Durumu
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Filtre Bilgi Mesajı -->
            <?php if(request('start_date') || request('end_date')): ?>
                <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 rounded-r-2xl shadow-sm animate-pulse-slow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-indigo-700">
                                <span class="font-black">FİLTRE AKTİF:</span> 
                                <span class="font-bold underline"><?php echo e(\Carbon\Carbon::parse($start_date)->format('d.m.Y')); ?></span> ile 
                                <span class="font-bold underline"><?php echo e(\Carbon\Carbon::parse($end_date)->format('d.m.Y')); ?></span> tarihleri arasındaki performans verileri görüntülenmektedir.
                            </p>
                        </div>
                        <div class="ml-auto">
                            <a href="<?php echo e(route('tum-bolum-puanlari')); ?>" class="text-xs font-bold text-indigo-500 hover:text-indigo-700 uppercase tracking-tighter">Filtreyi Kaldır</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Dominans Analizi (Üst Analiz) -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div class="md:w-5/12">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-1">Puan Dökümü & Analiz</h3>
                        <p class="text-[10px] text-gray-400 font-medium">Net puana ulaşan hesaplama özeti</p>
                        
                        <div class="mt-4 flex flex-col gap-2">
                            <!-- Net Skor -->
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-indigo-600 tracking-tighter"><?php echo e(number_format($netTotal, 0)); ?></span>
                                <span class="text-[10px] font-black text-gray-400 uppercase">SİSTEM NET PUANI</span>
                            </div>

                            <!-- Brüt-Net Detayı -->
                            <div class="bg-gray-50/50 rounded-2xl p-3 border border-gray-100 flex flex-col gap-2 mt-1">
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-gray-500 font-bold">Kazanılan Brüt Başarı:</span>
                                    <span class="text-emerald-600 font-black">+<?php echo e(number_format($grossTotal, 0)); ?></span>
                                </div>
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-gray-500 font-bold">Disiplin Kesintileri:</span>
                                    <span class="text-rose-600 font-black"><?php echo e(number_format($penaltyTotal, 0)); ?></span>
                                </div>
                                <div class="border-t border-dashed border-gray-200 mt-1 pt-1 flex justify-between items-center text-[11px] font-black">
                                    <span class="text-slate-600 italic">Net Sonuç:</span>
                                    <span class="text-indigo-600"><?php echo e(number_format($netTotal, 0)); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between mb-2">
                            <span class="text-[10px] font-black text-gray-400 tracking-tighter uppercase">BRÜT BAŞARI PAYLAŞIMI (İlk 3: %<?php echo e(number_format($dominanceStats['top1']['percent'] + $dominanceStats['top2']['percent'] + $dominanceStats['top3']['percent'], 1)); ?>)</span>
                            <span class="text-[10px] font-black text-indigo-500 underline uppercase cursor-help" title="Brüt kazanılan başarı (<?php echo e($grossTotal); ?>) içindeki ağırlığı gösterir">Hangi Bölüm Kazandı?</span>
                        </div>
                        <div class="flex h-4 w-full rounded-full overflow-hidden bg-gray-50 border border-gray-100 shadow-inner p-0.5">
                            <div class="h-full bg-indigo-500 transition-all duration-1000 shadow-lg rounded-full" style="width: <?php echo e($dominanceStats['top1']['percent']); ?>%" title="<?php echo e($dominanceStats['top1']['name']); ?>: %<?php echo e($dominanceStats['top1']['percent']); ?>"></div>
                            <div class="h-full bg-blue-400 transition-all duration-1000 shadow-md rounded-full -ml-1 border-l border-white/20" style="width: <?php echo e($dominanceStats['top2']['percent']); ?>%" title="<?php echo e($dominanceStats['top2']['name']); ?>: %<?php echo e($dominanceStats['top2']['percent']); ?>"></div>
                            <div class="h-full bg-teal-300 transition-all duration-1000 shadow-sm rounded-full -ml-1 border-l border-white/20" style="width: <?php echo e($dominanceStats['top3']['percent']); ?>%" title="<?php echo e($dominanceStats['top3']['name']); ?>: %<?php echo e($dominanceStats['top3']['percent']); ?>"></div>
                        </div>
                        <div class="flex flex-wrap gap-4 mt-4">
                            <div class="flex items-center gap-1.5 px-2 py-1 bg-gray-50 rounded-lg">
                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                                <span class="text-[10px] font-bold text-gray-600 truncate max-w-[120px]"><?php echo e($dominanceStats['top1']['name']); ?> (%<?php echo e($dominanceStats['top1']['percent']); ?>)</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-2 py-1 bg-gray-50 rounded-lg">
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                                <span class="text-[10px] font-bold text-gray-600 truncate max-w-[120px]"><?php echo e($dominanceStats['top2']['name']); ?> (%<?php echo e($dominanceStats['top2']['percent']); ?>)</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-2 py-1 bg-gray-50 rounded-lg">
                                <div class="w-1.5 h-1.5 rounded-full bg-teal-300"></div>
                                <span class="text-[10px] font-bold text-gray-600 truncate max-w-[120px]"><?php echo e($dominanceStats['top3']['name']); ?> (%<?php echo e($dominanceStats['top3']['percent']); ?>)</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Background Deco -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/20 rounded-bl-full -mr-16 -mt-16"></div>
            </div>

            <!-- Kategori Bazlı Performans Kartları (Büyükten Küçüğe) -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <?php $__currentLoopData = $categoryBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 relative overflow-hidden transition hover:shadow-md group">
                        <?php
                            $iconColors = [
                                'iaa' => 'bg-emerald-50 text-emerald-600',
                                'resol' => 'bg-blue-50 text-blue-600',
                                'suggest' => 'bg-amber-50 text-amber-600',
                                'entry' => 'bg-indigo-50 text-indigo-600',
                                'disc' => 'bg-rose-50 text-rose-600',
                            ];
                            $icons = [
                                'iaa' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                                'resol' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.757c.738 0 1.107.892.586 1.414l-7.414 7.414a1 1 0 01-1.414 0L3.414 11.414c-.522-.522-.152-1.414.586-1.414H8V4a1 1 0 011-1h4a1 1 0 011 1v6z"></path></svg>',
                                'suggest' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>',
                                'entry' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
                                'disc' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                            ];
                        ?>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center <?php echo e($iconColors[$cat['icon']]); ?> transition group-hover:scale-110">
                                <?php echo $icons[$cat['icon']]; ?>

                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest"><?php echo e($cat['label']); ?></span>
                                <span class="text-sm font-black text-gray-800"><?php echo e(number_format($cat['score'], 0)); ?></span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex-1 bg-gray-100 h-1 rounded-full overflow-hidden mr-2">
                                <div class="h-full bg-indigo-500 opacity-50" style="width: <?php echo e($cat['percentage']); ?>%"></div>
                            </div>
                            <span class="text-[9px] font-black text-indigo-500 uppercase tracking-tighter text-right w-8">%<?php echo e($cat['percentage']); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- 1. Kendi Bölüm İstatistiklerim (Header Stats) -->
            <?php if($myDeptStats): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kendi Bölümümün Puanı -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-50 rounded-full opacity-50 group-hover:scale-110 transition duration-500"></div>
                        <div class="relative z-10">
                            <div class="text-[10px] font-black text-purple-400 uppercase tracking-widest mb-1">BÖLÜMÜNÜZÜN TOPLAMI</div>
                            <div class="text-xs font-bold text-gray-500 mb-2"><?php echo e($myDeptStats->ad); ?></div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl font-black text-gray-900"><?php echo e(number_format($myDeptStats->total_score, 0)); ?></span>
                                <span class="text-xs font-bold text-gray-400">Puan</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kişisel Katkım -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition duration-500"></div>
                        <div class="relative z-10 text-center">
                            <div class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">KİŞİSEL KATKINIZ</div>
                            <div class="text-3xl font-black text-blue-600 mb-1">%<?php echo e($myContribution['percentage']); ?></div>
                            <div class="text-[10px] font-bold text-gray-400">Bölüm puanına <?php echo e(number_format($myContribution['puan'], 0)); ?> puan sağladınız.</div>
                            
                            <div class="mt-3 w-full bg-gray-50 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full transition-all duration-1000" style="width: <?php echo e($myContribution['percentage']); ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Bölüm Birincisi (MVP) -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-110 transition duration-500"></div>
                        <div class="relative z-10">
                            <div class="text-[10px] font-black text-amber-400 uppercase tracking-widest mb-2">BÖLÜMÜNÜZÜN YILDIZI</div>
                            <?php if($myDeptStats->birinci): ?>
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <?php if($myDeptStats->birinci->profile_photo_path): ?>
                                            <img src="<?php echo e(asset('storage/'.$myDeptStats->birinci->profile_photo_path)); ?>" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-amber-100">
                                        <?php else: ?>
                                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg ring-2 ring-amber-100">
                                                <?php echo e(mb_substr($myDeptStats->birinci->name, 0, 1)); ?>

                                            </div>
                                        <?php endif; ?>
                                        <div class="absolute -top-2 -right-2 bg-amber-500 text-white w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900"><?php echo e($myDeptStats->birinci->name); ?></div>
                                        <div class="text-[10px] font-bold text-amber-500 uppercase"><?php echo e(number_format($myDeptStats->birinci->period_puan, 0)); ?> Puan Topladı</div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-xs text-gray-400 italic">Henüz veri yok</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 2. Filtreleme Paneli -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <form action="<?php echo e(route('tum-bolum-puanlari')); ?>" method="GET" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Bölüm Adı -->
                        <div class="relative group">
                            <span class="absolute left-3 top-2.5 text-gray-400 group-focus-within:text-purple-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </span>
                            <input type="text" name="dept_name" value="<?php echo e(request('dept_name')); ?>" placeholder="Bölüm Ara..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-purple-500/10 focus:border-purple-500 transition focus:bg-white outline-none">
                        </div>

                        <!-- Bölüm Lideri -->
                        <div class="relative group">
                            <span class="absolute left-3 top-2.5 text-gray-400 group-focus-within:text-purple-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            <input type="text" name="dept_leader" value="<?php echo e(request('dept_leader')); ?>" placeholder="Lider Ara..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-purple-500/10 focus:border-purple-500 transition focus:bg-white outline-none">
                        </div>

                        <!-- Başlangıç -->
                        <div class="relative group">
                            <span class="absolute left-3 top-2.5 text-gray-400 group-focus-within:text-purple-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </span>
                            <input type="date" name="start_date" value="<?php echo e($start_date); ?>" class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-purple-500/10 focus:border-purple-500 transition focus:bg-white outline-none">
                        </div>

                        <!-- Bitiş -->
                        <div class="relative group">
                            <span class="absolute left-3 top-2.5 text-gray-400 group-focus-within:text-purple-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </span>
                            <input type="date" name="end_date" value="<?php echo e($end_date); ?>" class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-purple-500/10 focus:border-purple-500 transition focus:bg-white outline-none">
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-gray-50">
                        <div class="flex flex-wrap gap-2">
                            <?php
                                $quickFilters = [
                                    'Bu Hafta' => [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')],
                                    'Geçen Hafta' => [now()->subWeek()->startOfWeek()->format('Y-m-d'), now()->subWeek()->endOfWeek()->format('Y-m-d')],
                                    'Bu Ay' => [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')],
                                    'Bu Yıl' => [now()->startOfYear()->format('Y-m-d'), now()->endOfYear()->format('Y-m-d')],
                                ];
                            ?>
                            <?php $__currentLoopData = $quickFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $dates): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('tum-bolum-puanlari', ['start_date' => $dates[0], 'end_date' => $dates[1]])); ?>" 
                                   class="px-3 py-1.5 rounded-lg text-[10px] font-black tracking-wider uppercase transition border <?php echo e($start_date == $dates[0] && $end_date == $dates[1] ? 'bg-purple-600 border-purple-600 text-white shadow-md' : 'bg-white border-gray-100 text-gray-500 hover:border-purple-200 hover:text-purple-600'); ?>">
                                    <?php echo e($label); ?>

                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="flex gap-2">
                            <a href="<?php echo e(route('tum-bolum-puanlari')); ?>" class="px-5 py-2 text-xs font-bold text-gray-400 hover:text-gray-600 transition">Temizle</a>
                            <button type="submit" class="px-8 py-2 bg-purple-600 text-white text-xs font-black rounded-xl hover:bg-purple-700 transition shadow-lg shadow-purple-200">
                                Sorgula
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 3. Bölüm Listesi -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="font-bold text-gray-800">Bölüm Sıralaması</h3>
                    <p class="text-[10px] text-gray-400 font-medium">Toplam puanlarına göre alfabetik döküm</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-20">SIRA</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">BÖLÜM ANALİZİ</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">BÖLÜM LİDERİ</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">BÖLÜM BİRİNCİSİ (MVP)</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">TOPLAM PUAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php $__empty_1 = true; $__currentLoopData = $bolumPuanListesi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50/50 transition group <?php echo e(auth()->user()->bolum_id == $item->id ? 'bg-purple-50/30' : ''); ?>">
                                    <td class="px-6 py-5">
                                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm <?php echo e($index == 0 ? 'bg-amber-100 text-amber-700 shadow-sm border border-amber-200' : ($index == 1 ? 'bg-slate-100 text-slate-600 shadow-sm border border-slate-200' : ($index == 2 ? 'bg-orange-50 text-orange-600 shadow-sm border border-orange-200' : 'bg-gray-50 text-gray-400 border border-transparent'))); ?>">
                                            #<?php echo e($index + 1); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-4">
                                            <?php if($item->logo_yolu): ?>
                                                <img src="<?php echo e(asset('storage/'.$item->logo_yolu)); ?>" class="w-12 h-12 rounded-2xl object-contain shadow-sm bg-white p-2 border border-gray-50 transition group-hover:scale-105">
                                            <?php else: ?>
                                                <?php
                                                    $colors = ['from-purple-500 to-indigo-600', 'from-blue-500 to-cyan-600', 'from-emerald-500 to-teal-600', 'from-rose-500 to-pink-600', 'from-amber-500 to-orange-600'];
                                                    $gradient = $colors[$item->id % count($colors)];
                                                ?>
                                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?php echo e($gradient); ?>/20 flex items-center justify-center border border-gray-50 transition group-hover:scale-105">
                                                    <span class="text-sm font-black <?php echo e(str_replace('from-', 'text-', explode(' ', $gradient)[0])); ?>">
                                                        <?php echo e(mb_substr($item->ad, 0, 1)); ?>

                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <?php if($item->id > 0): ?>
                                                    <a href="<?php echo e(route('bolum-puanlari', ['bolum' => $item->id, 'start_date' => $start_date, 'end_date' => $end_date])); ?>" class="text-sm font-bold text-gray-900 hover:text-purple-600 transition truncate block max-w-[200px]">
                                                        <?php echo e($item->ad); ?>

                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-sm font-bold text-gray-400 cursor-default block max-w-[200px]">
                                                        <?php echo e($item->ad); ?>

                                                    </span>
                                                <?php endif; ?>
                                                <?php if($item->id > 0 && auth()->user()->bolum_id == $item->id): ?>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black bg-purple-600 text-white uppercase tracking-tighter">SİZİN BÖLÜMÜNÜZ</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <?php if($item->lider): ?>
                                            <a href="<?php echo e(route('profile.show', $item->lider->id)); ?>" class="flex items-center gap-3 group/lider">
                                                <?php if($item->lider->profile_photo_path): ?>
                                                    <img src="<?php echo e(asset('storage/'.$item->lider->profile_photo_path)); ?>" class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-100 ring-offset-2 transition group-hover/lider:ring-purple-300">
                                                <?php else: ?>
                                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-400 uppercase tracking-widest border border-gray-200">
                                                        <?php echo e(mb_substr($item->lider->name, 0, 1)); ?>

                                                    </div>
                                                <?php endif; ?>
                                                <div class="text-[11px] font-bold text-gray-600 group-hover/lider:text-purple-600 transition"><?php echo e($item->lider->name); ?></div>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-[10px] text-gray-300 italic font-medium uppercase tracking-widest">Atanmamış</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-5">
                                        <?php if($item->birinci && $item->total_score > 0): ?>
                                            <a href="<?php echo e(route('profile.puanlar', $item->birinci->id)); ?>" class="flex items-center gap-3 group/mvp">
                                                <div class="relative">
                                                    <?php if($item->birinci->profile_photo_path): ?>
                                                        <img src="<?php echo e(asset('storage/'.$item->birinci->profile_photo_path)); ?>" class="w-8 h-8 rounded-full object-cover">
                                                    <?php else: ?>
                                                        <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-[10px] font-black text-indigo-400 border border-indigo-100">
                                                            <?php echo e(mb_substr($item->birinci->name, 0, 1)); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-amber-400 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="text-[11px] font-bold text-gray-800 group-hover/mvp:text-purple-600 transition"><?php echo e($item->birinci->name); ?></div>
                                                    <div class="text-[9px] font-black text-amber-500 uppercase tracking-tighter"><?php echo e(number_format($item->birinci->period_puan, 0)); ?> PUAN / %<?php echo e($item->birinci_katki_orani); ?> KATKI</div>
                                                </div>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-[10px] text-gray-300 italic font-medium uppercase tracking-widest">Kayıt Yok</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <div class="flex flex-col items-end">
                                            <span class="text-lg font-black text-gray-900"><?php echo e(number_format($item->total_score, 0)); ?></span>
                                            <div class="flex items-center gap-1">
                                                <div class="w-12 bg-gray-50 h-1 rounded-full overflow-hidden">
                                                    <div class="bg-purple-500 h-full opacity-50" style="width: <?php echo e(min(100, $item->total_score / 10)); ?>%"></div>
                                                </div>
                                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">PUAN</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-100 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            <span class="text-sm text-gray-400 font-medium">Bize uygun bir bölüm bulamadı. Filtreleri kontrol ediniz.</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
<?php endif; ?>
<?php /**PATH /var/www/kys_koksan/iaa/resources/views/dashboard/tum-bolumler.blade.php ENDPATH**/ ?>