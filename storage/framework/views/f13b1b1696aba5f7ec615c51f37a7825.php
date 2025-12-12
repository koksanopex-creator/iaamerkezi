<?php if($sonSikayetler->isNotEmpty()): ?>
    <div class="bg-white rounded-xl shadow-sm border border-indigo-100 p-3 flex items-center gap-4 relative overflow-hidden"
            x-data="{
            activeNews: 0,
            newsCount: <?php echo e($sonSikayetler->take(5)->count()); ?>,
            timer: null,
            init() { this.startTimer(); },
            startTimer() { 
                this.timer = setInterval(() => { 
                    this.activeNews = (this.activeNews + 1) % this.newsCount; 
                }, 5000); 
            },
            stopTimer() { clearInterval(this.timer); }
            }">

        <button @click="activeNews = (activeNews - 1 + newsCount) % newsCount" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white shadow px-2 py-1 rounded-full text-gray-600 hover:bg-gray-100 z-20">◀</button>

        <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 animate-pulse">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
            </svg>
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
                ?>

                <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>" target="_blank"
                    class="absolute top-0 left-0 w-full h-full flex items-center px-3 rounded-lg border transition-all duration-500 ease-in-out transform cursor-pointer hover:brightness-95 <?php echo e($satirStili); ?>"
                    x-show="activeNews === <?php echo e($index); ?>"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-6"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-6">
                    <div class="flex items-center gap-1 text-sm w-full overflow-hidden font-medium">
                        <span class="font-bold uppercase text-xs opacity-80">[<?php echo e($sikayet->sikayetKategori->ad ?? 'Genel'); ?>]</span>
                        <span class="mx-1 opacity-50">-</span>
                        <span class="font-bold whitespace-nowrap"><?php echo e($tarih); ?></span>
                        <span class="whitespace-nowrap opacity-80">&nbsp;tarihinde&nbsp;</span>
                        <span class="font-bold whitespace-nowrap"><?php echo e(Str::limit($sikayet->musteri_adi, 20)); ?></span>
                        <span class="whitespace-nowrap opacity-80">&nbsp;tarafından&nbsp;</span>
                        <span class="italic font-bold truncate">"<?php echo e(Str::limit($sikayet->musteri_sikayet_konusu, 40)); ?>"</span>
                        <span class="whitespace-nowrap opacity-80">&nbsp;şikayeti. Durumu:&nbsp;</span>
                        <span class="bg-white/60 px-2 py-0.5 rounded text-[10px] font-black uppercase shadow-sm whitespace-nowrap"><?php echo e($sikayet->musteri_durum); ?></span>
                        <?php if($isGecikmis): ?> <span class="ml-2 bg-red-600 text-white px-2 py-0.5 rounded text-[10px] font-black animate-pulse shadow-sm whitespace-nowrap">GECİKTİ!</span> <?php endif; ?>
                        <?php if($sikayet->musteri_feedback): ?> <span class="ml-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase border bg-white/50"><?php echo e($sikayet->musteri_feedback); ?></span> <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/raporlar/partials/executive/horizontal-ticker.blade.php ENDPATH**/ ?>