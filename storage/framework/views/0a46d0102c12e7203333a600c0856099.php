<?php
    $last = $sonSikayetler->first();
?>




<?php if($last): ?>
<div class="rounded-lg border border-red-200 bg-red-50/60 p-3 mb-4 shadow-sm">

    
    <div class="flex justify-between items-center mb-2">
        <h3 class="text-sm font-extrabold text-red-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 2a10 10 0 1010 10A10 10 0 0012 2z"/>
            </svg>
            Son Sisteme Düşen Şikayet
        </h3>

        <a href="<?php echo e(route('admin.sikayetler.show', $last->id)); ?>" class="text-[11px] font-semibold text-indigo-600 hover:underline">
            → Detay
        </a>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-6 gap-2 text-[11px] leading-tight">

        
        <div class="p-2 bg-white rounded border text-gray-700">
            <p class="text-[9px] uppercase text-gray-400 font-semibold">Müşteri</p>
            <p class="font-bold truncate" title="<?php echo e($last->musteri_adi); ?>"><?php echo e(Str::limit($last->musteri_adi, 20)); ?></p>
        </div>

        
        <div class="p-2 bg-white rounded border text-gray-700">
            <p class="text-[9px] uppercase text-gray-400 font-semibold">Kategori</p>
            <p class="font-bold truncate"><?php echo e($last->sikayetKategori->ad ?? 'Genel'); ?></p>
        </div>

        
        <div class="p-2 bg-white rounded border text-gray-700">
            <p class="text-[9px] uppercase text-gray-400 font-semibold">Sorumlu Birim</p>
            <p class="font-bold truncate"><?php echo e($last->cozumTakimi->ad ?? 'Atanmamış'); ?></p>
        </div>

        
        <div class="p-2 bg-white rounded border text-gray-700 col-span-2">
            <p class="text-[9px] uppercase text-gray-400 font-semibold">Konu</p>
            <p class="font-bold truncate"><?php echo e($last->musteri_sikayet_konusu); ?></p>
        </div>

        
        <div class="p-2 bg-white rounded border text-gray-700">
            <p class="text-[9px] uppercase text-gray-400 font-semibold">Durum</p>
            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full whitespace-nowrap
                <?php if(trim(mb_strtolower($last->musteri_durum)) === 'yeni'): ?> bg-yellow-100 text-yellow-800
                <?php elseif($last->musteri_durum === 'İşlemde'): ?> bg-blue-100 text-blue-800
                <?php elseif($last->musteri_durum === 'Geciken'): ?> bg-red-100 text-red-800
                <?php elseif(in_array($last->musteri_durum, ['Kapatıldı','Çözümlendi'])): ?> bg-green-100 text-green-800
                <?php else: ?> bg-gray-100 text-gray-700 <?php endif; ?>">
                <?php echo e($last->musteri_durum); ?>

            </span>
        </div>

        
        <div class="p-2 bg-white rounded border text-gray-700">
            <p class="text-[9px] uppercase text-gray-400 font-semibold">Tarih</p>
            <p class="font-bold">
                <?php echo e(\Carbon\Carbon::parse($last->musteri_sikayet_tarihi)->locale('tr')->isoFormat('D MMM YYYY')); ?>

            </p>
        </div>
    </div>

    
    <?php if($last->musteri_feedback): ?>
        <div class="mt-2 p-2 bg-white rounded border border-gray-200">
            <?php
                $renk = match($last->musteri_feedback) {
                    'Onaylandı' => 'text-green-700 bg-green-50 border-green-200',
                    'Reddedildi' => 'text-red-700 bg-red-50 border-red-200',
                    default => 'text-yellow-700 bg-yellow-50 border-yellow-200'
                };
            ?>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-gray-500 uppercase">Müşteri Kararı:</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold border <?php echo e($renk); ?>">
                    <?php echo e($last->musteri_feedback); ?>

                </span>
                <?php if($last->musteri_feedback_note): ?>
                    <span class="text-[10px] text-gray-400 italic truncate max-w-[200px]">"<?php echo e($last->musteri_feedback_note); ?>"</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php endif; ?>





<?php if($sonSikayetler->isNotEmpty()): ?>
    <div class="bg-white rounded-xl shadow-sm border border-indigo-100 p-3 flex items-center gap-4 relative overflow-hidden"
         x-data="{
           activeNews: 0,
           newsCount: <?php echo e($sonSikayetler->take(5)->count()); ?>,
           timer: null,
           init() { this.startTimer(); },
           startTimer() { this.timer = setInterval(() => { this.activeNews = (this.activeNews + 1) % this.newsCount; }, 5000); },
           stopTimer() { clearInterval(this.timer); }
         }">

        
        <button @click="activeNews = (activeNews - 1 + newsCount) % newsCount" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white shadow px-2 py-1 rounded-full text-gray-600 hover:bg-gray-100 z-20">◀</button>

        
        <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 animate-pulse">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
        </div>

        
        <button @click="activeNews = (activeNews + 1) % newsCount" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white shadow px-2 py-1 rounded-full text-gray-600 hover:bg-gray-100 z-20">▶</button>

        <div class="flex-1 h-8 relative overflow-hidden" @mouseenter="stopTimer()" @mouseleave="startTimer()">
            <?php $__currentLoopData = $sonSikayetler->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $tarih = \Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->locale('tr')->isoFormat('D MMMM YYYY');
                    
                    $isGecikmis = ($sikayet->musteri_durum != 'Kapatıldı' && $sikayet->musteri_cozum_son_tarihi && $sikayet->musteri_cozum_son_tarihi < now());

                    $satirStili = match($sikayet->musteri_durum) {
                        'Yeni' => 'bg-yellow-100 border-yellow-300 text-yellow-900',
                        'İşlemde' => 'bg-blue-100 border-blue-300 text-blue-900',
                        'Kapatıldı', 'Çözümlendi' => 'bg-green-100 border-green-300 text-green-900',
                        'Yeniden Açıldı', 'Revize Ediliyor' => 'bg-orange-100 border-orange-300 text-orange-900',
                        default => 'bg-gray-100 border-gray-300 text-gray-900'
                    };
                    
                    if($isGecikmis) $satirStili = 'bg-red-100 border-red-300 text-red-900';
                    
                    $kategori = $sikayet->sikayetKategori->ad ?? 'Genel';
                ?>

                <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>" target="_blank"
                   class="absolute top-0 left-0 w-full h-full flex items-center px-3 rounded-lg border transition-all duration-500 ease-in-out transform cursor-pointer hover:brightness-95 <?php echo e($satirStili); ?>"
                   x-show="activeNews === <?php echo e($index); ?>"
                   x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0"
                   x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-6">
                    
                    <div class="flex items-center gap-1 text-sm w-full overflow-hidden font-medium">
                        <span class="font-bold uppercase text-xs opacity-80">[<?php echo e($kategori); ?>]</span>
                        <span class="mx-1 opacity-50">-</span>
                        <span class="font-bold whitespace-nowrap"><?php echo e($tarih); ?></span>
                        <span class="whitespace-nowrap opacity-80">&nbsp;tarihinde&nbsp;</span>
                        <span class="font-bold whitespace-nowrap"><?php echo e(Str::limit($sikayet->musteri_adi, 20)); ?></span>
                        <span class="whitespace-nowrap opacity-80">&nbsp;tarafından&nbsp;</span>
                        <span class="italic font-bold truncate">"<?php echo e(Str::limit($sikayet->musteri_sikayet_konusu, 40)); ?>"</span>
                        <span class="whitespace-nowrap opacity-80">&nbsp;şikayeti gelmiştir. Durumu:&nbsp;</span>
                        
                        <span class="bg-white/60 px-2 py-0.5 rounded text-[10px] font-black uppercase shadow-sm whitespace-nowrap"><?php echo e($sikayet->musteri_durum); ?></span>
                        
                        <?php if($isGecikmis): ?> 
                            <span class="ml-2 bg-red-600 text-white px-2 py-0.5 rounded text-[10px] font-black animate-pulse shadow-sm whitespace-nowrap">GECİKTİ!</span> 
                        <?php endif; ?>

                        
                        <?php if($sikayet->musteri_feedback): ?>
                            <?php
                                $fbClass = match($sikayet->musteri_feedback) {
                                    'Onaylandı' => 'bg-green-100 text-green-800 border-green-200',
                                    'Reddedildi' => 'bg-red-100 text-red-800 border-red-200',
                                    default => 'bg-yellow-100 text-yellow-800 border-yellow-200'
                                };
                            ?>
                            <span class="ml-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase border <?php echo e($fbClass); ?>">
                                <?php echo e($sikayet->musteri_feedback); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/raporlar/partials/executive/latest-complaints.blade.php ENDPATH**/ ?>