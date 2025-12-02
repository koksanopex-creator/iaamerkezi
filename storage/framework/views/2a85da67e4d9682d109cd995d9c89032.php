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
            <div>
                <h2 class="font-black text-3xl text-gray-800 tracking-tight">Yönetim</h2>
                <p class="text-sm text-gray-500">Genel Bakış ve Performans Analizi</p>
            </div>
            <span class="text-sm font-bold text-gray-600 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
                <?php echo e(\Carbon\Carbon::now()->locale('tr')->isoFormat('D MMMM YYYY, dddd')); ?>

            </span>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-[98%] mx-auto space-y-6">

            
            <div x-data="{ mode: 'month' }" class="flex items-center gap-3 mb-4"></div>

            
<div 
    x-data="{
        mode: 'month',
        modes: ['month','all'],
        index: 0,
        init() {
            setInterval(() => {
                this.index = (this.index + 1) % this.modes.length;
                this.mode = this.modes[this.index];
            }, 5000);
        }
    }"
    class="space-y-4"
>

                
                <?php
                    $last = $sonSikayetler->first();
                ?>

                <?php
                    $last = $sonSikayetler->first();

                    $lastCozumTarihi = $last?->kurul_onay_tarihi 
                        ? \Carbon\Carbon::parse($last->kurul_onay_tarihi)->locale('tr')->isoFormat('D MMMM YYYY')
                        : '—';
                ?>


                <?php if($last): ?>
<div class="rounded-lg border border-red-200 bg-red-50/60 p-3 mb-4 shadow-sm">

    
    <div class="flex justify-between items-center mb-2">
        <h3 class="text-sm font-extrabold text-red-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M12 2a10 10 0 1010 10A10 10 0 0012 2z"/>
            </svg>
            Son Sisteme Düşen Şikayet
        </h3>

        <a href="<?php echo e(route('admin.sikayetler.show', $last->id)); ?>"
           class="text-[11px] font-semibold text-indigo-600 hover:underline">
            → Detay
        </a>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-6 gap-2 text-[11px] leading-tight">

        
        <div class="p-2 bg-white rounded border text-gray-700">
            <p class="text-[9px] uppercase text-gray-400 font-semibold">Müşteri</p>
            <p class="font-bold truncate"><?php echo e($last->musteri_adi); ?></p>
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
            <span class="
                px-3 py-1 text-[10px] font-bold rounded-full whitespace-nowrap
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'bg-yellow-100 text-yellow-800' => trim(mb_strtolower($last->musteri_durum)) === 'yeni',

                    'bg-blue-100 text-blue-800' => $last->musteri_durum === 'İşlemde',
                    'bg-red-100 text-red-800' => $last->musteri_durum === 'Geciken',
                    'bg-green-100 text-green-800' => in_array($last->musteri_durum, ['Kapatıldı','Çözümlendi']),
                    'bg-gray-100 text-gray-700' => !in_array($last->musteri_durum, ['Yeni','İşlemde','Geciken','Kapatıldı','Çözümlendi'])
                ]); ?>"
            ">
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
</div>
<?php endif; ?>



                
<div 
    x-data="{
        mode: 'month',
        modes: ['month', 'all'],
        index: 0,
        init() {
            setInterval(() => {
                this.index = (this.index + 1) % this.modes.length;
                this.mode = this.modes[this.index];
            }, 5000);
        }
    }"
    class="flex flex-col gap-3"
>

    
    <div class="flex gap-2">
        <button 
            @click="mode = 'month'; index = 0"
            :class="mode === 'month' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600'"
            class="px-3 py-1.5 rounded-lg shadow text-sm border transition">
            Bu Ay
        </button>

        <button 
            @click="mode = 'all'; index = 1"
            :class="mode === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600'"
            class="px-3 py-1.5 rounded-lg shadow text-sm border transition">
            Tüm Zamanlar
        </button>
    </div>

    
    <div class="flex gap-1 justify-center mt-1">
        <template x-for="(m, i) in modes">
            <div 
                class="w-2 h-2 rounded-full transition"
                :class="index === i ? 'bg-indigo-600 scale-125' : 'bg-gray-300'">
            </div>
        </template>
    </div>
    
    <div class="text-center mt-1 mb-3">
        <span 
            x-show="mode === 'month'" 
            class="text-xl font-bold text-gray-700"
        >
            Bu Ay (<?php echo e(now()->locale('tr')->translatedFormat('F Y')); ?>)
        </span>

        <span 
            x-show="mode === 'all'" 
            class="text-xl font-bold text-gray-700"
        >
            Tüm Zamanlar
        </span>
    </div>

</div>


                
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 max-w-5xl">

                    
                    <div class="p-3 bg-white rounded-xl shadow border">
                        <p class="text-[10px] font-bold text-gray-500 uppercase">Toplam</p>
                        <p x-show="mode === 'month'" class="text-xl font-black text-gray-800">
                            <?php echo e($kpiMonthly['toplam']); ?>

                        </p>
                        <p x-show="mode === 'all'" class="text-xl font-black text-indigo-700">
                            <?php echo e($kpi['toplam']); ?>

                        </p>
                    </div>

                    
                    <div class="p-3 bg-white rounded-xl shadow border">
                        <p class="text-[10px] font-bold text-blue-600 uppercase">Açık / İşlemde</p>
                        <p x-show="mode === 'month'" class="text-xl font-black text-blue-600">
                            <?php echo e($kpiMonthly['acik']); ?>

                        </p>
                        <p x-show="mode === 'all'" class="text-xl font-black text-blue-600">
                            <?php echo e($kpi['acik']); ?>

                        </p>
                    </div>

                    
                    <div class="p-3 bg-white rounded-xl shadow border">
                        <p class="text-[10px] font-bold text-red-600 uppercase">Geciken</p>
                        <p x-show="mode === 'month'" class="text-xl font-black text-red-600">
                            <?php echo e($kpiMonthly['geciken']); ?>

                        </p>
                        <p x-show="mode === 'all'" class="text-xl font-black text-red-600">
                            <?php echo e($kpi['geciken']); ?>

                        </p>
                    </div>

                    
                    <div class="p-3 bg-white rounded-xl shadow border">
                        <p class="text-[10px] font-bold text-green-600 uppercase">Çözülen</p>
                        <p x-show="mode === 'month'" class="text-xl font-black text-green-600">
                            <?php echo e($kpiMonthly['cozulen']); ?>

                        </p>
                        <p x-show="mode === 'all'" class="text-xl font-black text-green-600">
                            <?php echo e($kpi['cozulen']); ?>

                        </p>
                    </div>

                    
                    <div class="p-3 bg-white rounded-xl shadow border">
                        <p class="text-[10px] font-bold text-purple-600 uppercase">Ort. Çözüm Hızı</p>
                        <p x-show="mode === 'month'" class="text-xl font-black text-purple-600">
                            <?php echo e($kpiMonthly['ortalama_sure']); ?> gün
                        </p>
                        <p x-show="mode === 'all'" class="text-xl font-black text-purple-600">
                            <?php echo e($kpi['ortalama_sure']); ?> gün
                        </p>
                    </div>

                </div>

            </div> 

            
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

                    
                    <button 
                        @click="activeNews = (activeNews - 1 + newsCount) % newsCount"
                        class="absolute left-2 top-1/2 -translate-y-1/2 bg-white shadow px-2 py-1 rounded-full text-gray-600 hover:bg-gray-100 z-20">
                        ◀
                    </button>

                    
                    <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 animate-pulse">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    </div>

                    
                    <button 
                        @click="activeNews = (activeNews + 1) % newsCount"
                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-white shadow px-2 py-1 rounded-full text-gray-600 hover:bg-gray-100 z-20">
                        ▶
                    </button>

                    <div class="flex-1 h-8 relative overflow-hidden" @mouseenter="stopTimer()" @mouseleave="startTimer()">
                        <?php $__currentLoopData = $sonSikayetler->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $tarih = \Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)
                                    ->locale('tr')
                                    ->isoFormat('D MMMM YYYY');

                                $isGecikmis = (
                                    $sikayet->musteri_durum != 'Kapatıldı' &&
                                    $sikayet->musteri_cozum_son_tarihi &&
                                    $sikayet->musteri_cozum_son_tarihi < now()
                                );

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
                               x-transition:enter="transition ease-out duration-500"
                               x-transition:enter-start="opacity-0 translate-y-6"
                               x-transition:enter-end="opacity-100 translate-y-0"
                               x-transition:leave="transition ease-in duration-300"
                               x-transition:leave-start="opacity-100 translate-y-0"
                               x-transition:leave-end="opacity-0 -translate-y-6">
                                <div class="flex items-center gap-1 text-sm w-full overflow-hidden font-medium">
                                    <span class="font-bold uppercase text-xs opacity-80">[<?php echo e($kategori); ?>]</span>
                                    <span class="mx-1 opacity-50">-</span>
                                    <span class="font-bold whitespace-nowrap"><?php echo e($tarih); ?></span>
                                    <span class="whitespace-nowrap opacity-80">&nbsp;tarihinde&nbsp;</span>
                                    <span class="font-bold whitespace-nowrap">
                                        <?php echo e(Str::limit($sikayet->musteri_adi, 20)); ?>

                                    </span>
                                    <span class="whitespace-nowrap opacity-80">&nbsp;tarafından&nbsp;</span>
                                    <span class="italic font-bold truncate">
                                        "<?php echo e(Str::limit($sikayet->musteri_sikayet_konusu, 40)); ?>"
                                    </span>
                                    <span class="whitespace-nowrap opacity-80">&nbsp;şikayeti gelmiştir. Durumu:&nbsp;</span>
                                    <span class="bg-white/60 px-2 py-0.5 rounded text-[10px] font-black uppercase shadow-sm whitespace-nowrap">
                                        <?php echo e($sikayet->musteri_durum); ?>

                                    </span>
                                    <?php if($isGecikmis): ?>
                                        <span class="ml-2 bg-red-600 text-white px-2 py-0.5 rounded text-[10px] font-black animate-pulse shadow-sm whitespace-nowrap">
                                            GECİKTİ!
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                        Son Müşteri Şikayetleri Akışı
                    </h3>
                    <span class="text-xs text-gray-400">Otomatik Kaydırma</span>
                </div>

                
                <div class="relative h-80 overflow-hidden bg-gray-50/30">
                    <div class="absolute w-full animate-vertical-scroll hover:pause space-y-2">
                        <?php $__currentLoopData = $sonSikayetler->merge($sonSikayetler); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        
                            <?php
                                $ayEtiketi = \Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->locale('tr')->isoFormat('MMMM YYYY');
                                $oncekiAy = $loop->first 
                                    ? null 
                                    : \Carbon\Carbon::parse($sonSikayetler->merge($sonSikayetler)[$loop->index - 1]->musteri_sikayet_tarihi)
                                        ->locale('tr')->isoFormat('MMMM YYYY');
                            ?>

                            <?php if($ayEtiketi !== $oncekiAy): ?>
                                <div class="px-4 py-1 text-sm font-bold text-gray-700 bg-gray-100 border-y border-gray-300 sticky top-0 z-20">
                                    📅 <?php echo e($ayEtiketi); ?>

                                </div>
                            <?php endif; ?>
                           
                            <?php
                                $tarih = \Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)
                                    ->locale('tr')
                                    ->isoFormat('D MMMM YYYY');

                                $isGecikmis = (
                                    $sikayet->musteri_durum != 'Kapatıldı' &&
                                    $sikayet->musteri_cozum_son_tarihi &&
                                    $sikayet->musteri_cozum_son_tarihi < now()
                                );

                                if ($isGecikmis) {
                                    $rowColor = 'bg-red-50 border border-red-200';
                                } else {
                                    switch ($sikayet->musteri_durum) {
                                        case 'Yeni':
                                            $rowColor = 'bg-yellow-50 border border-yellow-200';
                                            break;
                                        case 'İşlemde':
                                            $rowColor = 'bg-blue-50 border border-blue-200';
                                            break;
                                        case 'Kapatıldı':
                                        case 'Çözümlendi':
                                            $rowColor = 'bg-green-50 border border-green-200';
                                            break;
                                        case 'Yeniden Açıldı':
                                        case 'Revize Ediliyor':
                                            $rowColor = 'bg-orange-50 border border-orange-200';
                                            break;
                                        default:
                                            $rowColor = 'bg-white border border-gray-200';
                                    }
                                }
                            ?>

                            <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>"
                               target="_blank"
                               class="block rounded-xl px-4 py-3 <?php echo e($rowColor); ?> shadow-sm hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    
                                    <div class="flex items-center gap-3">
                                        <?php $ilkDosya = $sikayet->dosyalar->first(); ?>
                                        <?php if($ilkDosya && str_contains($ilkDosya->mime_tipi, 'image')): ?>
                                            <img src="<?php echo e(asset('storage/'.$ilkDosya->dosya_yolu)); ?>"
                                                 class="w-10 h-10 rounded-lg object-cover border">
                                        <?php else: ?>
                                            <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center text-gray-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        <?php endif; ?>

                                        <div>
                                            <p class="text-sm font-bold text-gray-800">
                                                <?php echo e($sikayet->musteri_adi); ?>

                                                <span class="font-normal text-gray-500">
                                                    – <?php echo e($sikayet->sikayetKategori->ad ?? ''); ?>

                                                </span>
                                            </p>
                                            <p class="text-xs text-gray-700 truncate w-80">
                                                <?php echo e($sikayet->musteri_sikayet_konusu); ?>

                                            </p>
                                        </div>
                                    </div>

                                    
                                    <div class="text-right">
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded
                                            <?php echo e($isGecikmis ? 'bg-red-200 text-red-800' : 'bg-white/60 text-gray-800 border border-gray-200'); ?>">
                                            <?php echo e($sikayet->musteri_durum); ?>

                                        </span>
                                        <p class="text-[10px] text-gray-500 mt-1 font-semibold">
                                            <?php echo e($tarih); ?>

                                        </p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            
            <div 
                x-data="{
                    slide: 0,
                    total: <?php echo e(ceil(count($bolumPerformansi) / 2)); ?>,
                    init() {
                        setInterval(() => {
                            this.slide = (this.slide + 1) % this.total;
                        }, 9000);
                    }
                }" 
                class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 relative overflow-hidden"
            >
                <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                    
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 13h2l2 7 4-14 3 9 2-6h5" />
                    </svg>
                    Bölüm Performans Karnesi
                </h3>

                <div class="overflow-hidden relative">
                    <div 
                        class="flex transition-all duration-700"
                        :style="'transform: translateX(-' + (slide * 100) + '%)'"
                    >
                        <?php $__currentLoopData = array_chunk($bolumPerformansi->toArray(), 2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="min-w-full grid grid-cols-1 md:grid-cols-2 gap-4 px-1">
                                <?php $__currentLoopData = $chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="border rounded-xl p-4 hover:shadow-md transition-shadow bg-white relative overflow-hidden">
                                    
                                    <div class="absolute top-0 right-0 flex gap-1 p-1">

                                        <?php
                                            $currentYear = now()->year;
                                            $yearsToShow = [$currentYear, $currentYear - 1, $currentYear - 2];
                                        ?>

                                        <?php $__currentLoopData = $yearsToShow; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="bg-orange-100 text-orange-700 rounded-bl-lg px-2 py-0.5 text-[9px] border-b border-l text-center">
                                                <span class="font-bold"><?php echo e($yy); ?></span>
                                                <span class="block text-[8px]">
                                                    T: <?php echo e($bolum['yillik_detay'][$yy]['toplam'] ?? 0); ?>

                                                    | Ç: <?php echo e($bolum['yillik_detay'][$yy]['cozulen'] ?? 0); ?>

                                                </span>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </div>



                                        <h4 class="font-bold text-gray-800 text-base mb-2"><?php echo e($bolum['ad']); ?></h4>

                                        
                                        <div class="mb-2">
                                            <div class="flex justify-between text-xs mb-1">
                                                <span class="text-gray-500">Başarı</span>
                                                <span class="font-bold <?php echo e($bolum['basari_orani'] < 50 ? 'text-red-600' : 'text-green-600'); ?>">
                                                    %<?php echo e($bolum['basari_orani']); ?>

                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-2">
                                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full" 
                                                     style="width: <?php echo e($bolum['basari_orani']); ?>%">
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="flex justify-between text-xs pt-2 border-t border-gray-100">
                                            <div class="text-center w-1/4">
                                                <span class="block text-gray-400 text-[11px]">Toplam</span>
                                                <span class="font-bold"><?php echo e($bolum['toplam']); ?></span>
                                            </div>
                                            <div class="text-center w-1/4">
                                                <span class="block text-gray-400 text-[11px]">Hız</span>
                                                <span class="font-bold"><?php echo e($bolum['ort_sure']); ?> Gün</span>
                                            </div>
                                            <div class="text-center w-1/4">
                                                <span class="block text-gray-400 text-[11px]">İşlemde</span>
                                                <span class="font-bold text-blue-600"><?php echo e($bolum['islemde'] ?? $bolum['acik']); ?></span>
                                            </div>
                                            <div class="text-center w-1/4">
                                                <span class="block text-gray-400 text-[11px]">Kapanan</span>
                                                <span class="font-bold text-green-600"><?php echo e($bolum['kapandi'] ?? $bolum['cozulen']); ?></span>
                                            </div>
                                        </div>

                                        
<div class="mt-4 border-t pt-3">

    <h5 class="font-bold text-gray-700 text-xs mb-2">Yıllık Performans Özeti</h5>

    <table class="w-full text-[11px] text-gray-700 border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-100 text-gray-600">
            <tr>
                <th class="px-2 py-1 text-left">Yıl</th>
                <th class="px-2 py-1 text-center w-16">Toplam</th>
                <th class="px-2 py-1 text-center w-16">Çözülen</th>
                <th class="px-2 py-1 text-center w-16">Açık</th>
                <th class="px-2 py-1 text-center w-16">Geciken</th>
                <th class="px-2 py-1 text-center w-16">Ortalama Süre</th>
            </tr>
        </thead>

        <tbody>
            <?php
                $currentYear = now()->year;
                $yearsToShow = [$currentYear, $currentYear - 1, $currentYear - 2];
            ?>

            <?php $__currentLoopData = $yearsToShow; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-t border-gray-200 hover:bg-gray-50">
                    <td class="px-2 py-1 font-bold"><?php echo e($yy); ?></td>
                    <td class="px-2 py-1 text-center font-semibold">
                        <?php echo e($bolum['yillik_detay'][$yy]['toplam'] ?? 0); ?>

                    </td>
                    <td class="px-2 py-1 text-center text-green-600 font-bold">
                        <?php echo e($bolum['yillik_detay'][$yy]['cozulen'] ?? 0); ?>

                    </td>
                    <td class="px-2 py-1 text-center text-blue-600 font-bold">
                        <?php echo e($bolum['yillik_detay'][$yy]['acik'] ?? 0); ?>

                    </td>
                    <td class="px-2 py-1 text-center text-red-600 font-bold">
                        <?php echo e($bolum['yillik_detay'][$yy]['geciken'] ?? 0); ?>

                    </td>
                    <td class="px-2 py-1 text-center text-purple-600 font-bold">
                        <?php echo e($bolum['yillik_detay'][$yy]['ortalama'] ?? 0); ?> gün
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>


                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    
                    <button 
                        @click="slide = (slide - 1 + total) % total"
                        class="absolute top-1/2 -left-2 -translate-y-1/2 bg-white shadow rounded-full p-2 hover:bg-gray-100 transition"
                    >
                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    
                    <button 
                        @click="slide = (slide + 1) % total"
                        class="absolute top-1/2 -right-2 -translate-y-1/2 bg-white shadow rounded-full p-2 hover:bg-gray-100 transition"
                    >
                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            
            <div 
                x-data="{
                    gSlide: 0,
                    total: 4,
                    init() {
                        setInterval(() => {
                            this.gSlide = (this.gSlide + 1) % this.total;
                        }, 7000);
                    }
                }"
                class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mt-6 relative overflow-hidden"
            >
                <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                    📊 Yönetim Analitik Dashboard
                </h3>

                <div class="relative w-full overflow-hidden">
                    <div class="flex transition-all duration-700"
                         :style="'transform: translateX(-' + (gSlide * 100) + '%)'">

                        
                        <div class="min-w-full px-4">
                            <div class="bg-white rounded-xl shadow p-4 border border-gray-200 h-[420px] flex flex-col">
                                <h4 class="text-sm font-bold text-gray-700 mb-3 text-center">Aylık Şikayet Trendi</h4>
                                <div class="flex-1" id="trendChart"></div>
                            </div>
                        </div>

                        
                        <div class="min-w-full px-4">
                            <div class="bg-white rounded-xl shadow p-4 border border-gray-200 h-[420px] flex flex-col">
                                <h4 class="text-sm font-bold text-gray-700 mb-3 text-center">Kategori Dağılımı</h4>
                                <div class="flex-1" id="catChart"></div>
                            </div>
                        </div>

                        
                        <div class="min-w-full px-4">
                            <div class="bg-white rounded-xl shadow p-4 border border-gray-200 h-[420px] flex flex-col">
                                <h4 class="text-sm font-bold text-gray-700 mb-3 text-center">Durum Analizi</h4>
                                <div class="flex-1" id="statusChart"></div>
                            </div>
                        </div>

                        
                        <div class="min-w-full px-4">
                            <div class="bg-white rounded-xl shadow p-4 border border-gray-200 h-[420px] flex flex-col">
                                <h4 class="text-sm font-bold text-gray-700 mb-3 text-center">Aylık Çözüm Hızı (Gün)</h4>
                                <div class="flex-1" id="speedChart"></div>
                            </div>
                        </div>
                    </div>

                    
                    <button 
                        @click="gSlide = (gSlide - 1 + total) % total"
                        class="absolute top-1/2 left-3 -translate-y-1/2 bg-white shadow rounded-full p-2 hover:bg-gray-100"
                    >
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    
                    <button 
                        @click="gSlide = (gSlide + 1) % total"
                        class="absolute top-1/2 right-3 -translate-y-1/2 bg-white shadow rounded-full p-2 hover:bg-gray-100"
                    >
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                        <template x-for="i in total">
                            <div 
                                @click="gSlide = i - 1"
                                class="w-3 h-3 rounded-full cursor-pointer transition"
                                :class="gSlide === (i - 1) ? 'bg-indigo-600' : 'bg-gray-300'">
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </div>
    </div>

    
    <style>
        @keyframes vertical-scroll {
            0% { transform: translateY(0); }
            100% { transform: translateY(-50%); }
        }
        .animate-vertical-scroll {
            animation: vertical-scroll 20s linear infinite;
        }
        .hover\:pause:hover {
            animation-play-state: paused;
        }
    </style>

    <?php $__env->startPush('scripts'); ?>
        
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                // ==============================
                // 1) Aylık Şikayet Trendi (Bar)
                // ==============================
                const trendOptions = {
                    chart: {
                        type: 'bar',
                        height: 360,
                        toolbar: { show: false }
                    },
                    series: [{
                        name: 'Şikayet',
                        data: <?php echo json_encode($charts['trend_data'], 15, 512) ?>
                    }],
                    xaxis: {
                        categories: <?php echo json_encode($charts['labels'], 15, 512) ?>,
                        labels: {
                            style: { fontSize: '11px' }
                        }
                    },
                    yaxis: {
                        min: 0,
                        labels: {
                            style: { fontSize: '11px' }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '11px',
                            fontWeight: 'bold'
                        },
                        offsetY: -10
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '45%'
                        }
                    },
                    colors: ['#4F46E5'],
                    grid: {
                        strokeDashArray: 4
                    }
                };

                const trendChartEl = document.querySelector('#trendChart');
                if (trendChartEl) {
                    new ApexCharts(trendChartEl, trendOptions).render();
                }

                // ==============================
                // 2) Kategori Dağılımı (Pie)
                // ==============================
                const catSeries = <?php echo json_encode($charts['cat_data'], 15, 512) ?>;
                const catLabels = <?php echo json_encode($charts['cat_labels'], 15, 512) ?>;

                const catOptions = {
    chart: {
        type: 'donut',
        height: 380,
        toolbar: { show: false }
    },

    series: catSeries,
    labels: catLabels.map((label, i) => {
        return `${label} (${catSeries[i]})`; // Legend'e toplam sayı eklendi
    }),

    legend: {
        show: true,
        position: 'right',
        fontSize: '13px',
        labels: {
            colors: '#333'
        }
    },

    tooltip: {
        enabled: false   // Sol üstteki kutu KAPALI
    },

    stroke: {
        width: 2,
        colors: ['#fff']
    },

    dataLabels: {
        enabled: true,
        style: {
            fontSize: '12px',
            fontWeight: '600',
            colors: ['#333']
        },

        // ❗ Etiketleri dışarı taşıran sihir burada
        offset: 40,  

        dropShadow: { enabled: false },

        formatter: function (value, opts) {
            const total = opts.w.globals.series.reduce((a, b) => a + b, 0);
            const val = opts.w.globals.series[opts.seriesIndex];
            const percent = ((val / total) * 100).toFixed(1);

            return `${opts.w.globals.labels[opts.seriesIndex]} (${percent}%)`;
        }
    },

    plotOptions: {
        pie: {
            donut: {
                size: '65%'
            },
            dataLabels: {
                offset: 40, // Dışarı taşıma mesafesi
                minAngleToShowLabel: 10
            }
        }
    },

    colors: [
        '#3B82F6', '#EC4899', '#10B981', '#F59E0B',
        '#6366F1', '#8B5CF6', '#22D3EE', '#A3E635'
    ]
};


                const catChartEl = document.querySelector('#catChart');
                if (catChartEl) {
                    new ApexCharts(catChartEl, catOptions).render();
                }

                // ==============================
                // 3) Durum Analizi (Donut)
                // ==============================
                const statusSeries = <?php echo json_encode($charts['status_data'], 15, 512) ?>;

                const statusOptions = {
                    chart: {
                        type: 'donut',
                        height: 360
                    },
                    labels: ['Açık','Çözülen','Geciken'],
                    series: statusSeries,
                    legend: {
                        show: true,
                        position: 'right',
                        fontSize: '11px'
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold',
                            colors: ['#fff']
                        },
                        dropShadow: { enabled: false },
                        formatter: function (val, opts) {
                            const value = opts.w.globals.series[opts.seriesIndex];
                            return value;
                        }
                    },
                    colors: ['#3B82F6','#10B981','#EF4444'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '60%'
                            }
                        }
                    }
                };

                const statusChartEl = document.querySelector('#statusChart');
                if (statusChartEl) {
                    new ApexCharts(statusChartEl, statusOptions).render();
                }

                // ==============================
                // 4) Aylık Çözüm Hızı (Line)
                // ==============================
                const speedOptions = {
                    chart: {
                        type: 'line',
                        height: 360,
                        toolbar: { show: false }
                    },
                    series: [{
                        name: 'Ort. Gün',
                        data: <?php echo json_encode($charts['speed_data'], 15, 512) ?>
                    }],
                    xaxis: {
                        categories: <?php echo json_encode($charts['labels'], 15, 512) ?>,
                        labels: {
                            style: { fontSize: '11px' }
                        }
                    },
                    yaxis: {
                        min: 0,
                        labels: {
                            style: { fontSize: '11px' }
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '11px',
                            fontWeight: 'bold'
                        }
                    },
                    markers: {
                        size: 4
                    },
                    colors: ['#8B5CF6'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 0.4,
                            opacityFrom: 0.4,
                            opacityTo: 0.0
                        }
                    },
                    grid: {
                        strokeDashArray: 4
                    }
                };

                const speedChartEl = document.querySelector('#speedChart');
                if (speedChartEl) {
                    new ApexCharts(speedChartEl, speedOptions).render();
                }

            });
        </script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/raporlar/executive.blade.php ENDPATH**/ ?>