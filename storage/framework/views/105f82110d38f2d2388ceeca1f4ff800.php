
<?php
    $jsSikayetData = $sonSikayetler->map(function($item) {
        
        $bitis = $item->kurul_onay_tarihi ? \Carbon\Carbon::parse($item->kurul_onay_tarihi) : null;
        $baslangic = \Carbon\Carbon::parse($item->musteri_sikayet_tarihi);
        
        // SÜRE HESABI: floatDiffInDays ile hassas hesap yapıp, round ile 1 basamağa yuvarlıyoruz (Örn: 1.4)
        $sure = $bitis ? round($baslangic->floatDiffInDays($bitis), 1) : null;

        $isGeciken = ($item->musteri_durum != 'Kapatıldı' && $item->musteri_durum != 'Çözümlendi') && 
                     ($item->musteri_cozum_son_tarihi && \Carbon\Carbon::parse($item->musteri_cozum_son_tarihi)->isPast());

        return [
            'id' => $item->id,
            'musteri' => Str::limit($item->musteri_adi, 15),
            'konu' => Str::limit($item->musteri_sikayet_konusu, 30),
            'bolum' => $item->cozumTakimi->ad ?? '-',
            'durum' => $item->musteri_durum,
            'tarih' => $baslangic->format('d.m.Y'),
            
            // TARİH FİLTRESİ İÇİN GEREKLİ VERİLER
            'month' => $baslangic->month,
            'year'  => $baslangic->year,

            'url' => route('admin.sikayetler.show', $item->id),
            'is_acik' => in_array($item->musteri_durum, ['Yeni', 'İşlemde', 'Revize Ediliyor', 'Yeniden Açıldı']),
            'is_cozulen' => in_array($item->musteri_durum, ['Kapatıldı', 'Çözümlendi']),
            'is_geciken' => $isGeciken,
            'sure' => $sure // Yuvarlanmış süre
        ];
    })->values();
?>

<div x-data="{
        mode: 'month',
        modes: ['month','all'],
        index: 0,
        activeTab: null,
        complaints: <?php echo e($jsSikayetData); ?>,
        
        // Şu anki tarih (Filtreleme için)
        currentMonth: <?php echo e(now()->month); ?>,
        currentYear: <?php echo e(now()->year); ?>,

        init() {
            setInterval(() => {
                if(this.activeTab === null) { 
                    this.index = (this.index + 1) % this.modes.length;
                    this.mode = this.modes[this.index];
                }
            }, 8000);
        },

        toggleTab(tabName) {
            this.activeTab = (this.activeTab === tabName) ? null : tabName;
        },

        // === GELİŞMİŞ FİLTRELEME FONKSİYONU ===
        get filteredList() {
            if (!this.activeTab) return [];
            
            // 1. ADIM: ZAMAN FİLTRESİ (Bu Ay / Tüm Zamanlar)
            let timeFiltered = this.complaints;
            
            if (this.mode === 'month') {
                // Sadece şikayet tarihi bu yıl ve bu ay olanları getir
                timeFiltered = timeFiltered.filter(i => i.month == this.currentMonth && i.year == this.currentYear);
            }
            // 'all' modunda filtrelemeye gerek yok, hepsi kalsın.

            // 2. ADIM: KART TÜRÜ FİLTRESİ (Açık, Geciken, Çözülen)
            if (this.activeTab === 'toplam') return timeFiltered;
            if (this.activeTab === 'acik') return timeFiltered.filter(i => i.is_acik);
            if (this.activeTab === 'geciken') return timeFiltered.filter(i => i.is_geciken);
            if (this.activeTab === 'cozulen') return timeFiltered.filter(i => i.is_cozulen);
            if (this.activeTab === 'hiz') return timeFiltered; 

            return [];
        },

        get activeColor() {
            const map = {
                'toplam': 'gray',
                'acik': 'blue',
                'geciken': 'red',
                'cozulen': 'green',
                'hiz': 'purple'
            };
            return map[this.activeTab] || 'gray';
        }
    }"
    class="space-y-6"
>

    
    <?php $last = $sonSikayetler->first(); ?>
    <?php if($last): ?>
        <div class="rounded-lg border border-red-200 bg-red-50/60 p-3 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition">
                <svg class="w-16 h-16 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            </div>
            <div class="flex justify-between items-center mb-2 relative z-10">
                <h3 class="text-sm font-extrabold text-red-700 flex items-center gap-2">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
                    </span>
                    Son Sisteme Düşen Şikayet
                </h3>
                <a href="<?php echo e(route('admin.sikayetler.show', $last->id)); ?>" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                    İncele <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-2 text-[11px] leading-tight relative z-10">
                <div class="p-2 bg-white rounded border border-red-100 text-gray-700 shadow-sm">
                    <p class="text-[9px] uppercase text-gray-400 font-bold tracking-wider">Müşteri</p>
                    <p class="font-bold truncate"><?php echo e($last->musteri_adi); ?></p>
                </div>
                <div class="p-2 bg-white rounded border border-red-100 text-gray-700 shadow-sm">
                    <p class="text-[9px] uppercase text-gray-400 font-bold tracking-wider">Kategori</p>
                    <p class="font-bold truncate"><?php echo e($last->sikayetKategori->ad ?? 'Genel'); ?></p>
                </div>
                <div class="p-2 bg-white rounded border border-red-100 text-gray-700 shadow-sm">
                    <p class="text-[9px] uppercase text-gray-400 font-bold tracking-wider">Sorumlu Birim</p>
                    <p class="font-bold truncate"><?php echo e($last->cozumTakimi->ad ?? 'Atanmamış'); ?></p>
                </div>
                <div class="p-2 bg-white rounded border border-red-100 text-gray-700 col-span-2 shadow-sm">
                    <p class="text-[9px] uppercase text-gray-400 font-bold tracking-wider">Konu</p>
                    <p class="font-bold truncate"><?php echo e($last->musteri_sikayet_konusu); ?></p>
                </div>
                <div class="p-2 bg-white rounded border border-red-100 text-gray-700 shadow-sm">
                    <p class="text-[9px] uppercase text-gray-400 font-bold tracking-wider">Durum</p>
                    <span class="inline-block px-2 py-0.5 text-[9px] font-bold rounded-full bg-red-100 text-red-700">
                        <?php echo e($last->musteri_durum); ?>

                    </span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="flex flex-col gap-2 border-b border-gray-200 pb-4">
        <div class="flex justify-center gap-2">
            <button @click="mode = 'month'; index = 0" :class="mode === 'month' ? 'bg-gray-800 text-white shadow-lg transform scale-105' : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-200'" class="px-5 py-1.5 rounded-full text-xs font-bold transition-all duration-300">
                Bu Ay
            </button>
            <button @click="mode = 'all'; index = 1" :class="mode === 'all' ? 'bg-gray-800 text-white shadow-lg transform scale-105' : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-200'" class="px-5 py-1.5 rounded-full text-xs font-bold transition-all duration-300">
                Tüm Zamanlar
            </button>
        </div>
        <div class="text-center mt-1">
             <span x-show="mode === 'month'" class="text-xs font-semibold text-gray-500 uppercase tracking-widest">
                <?php echo e(now()->locale('tr')->translatedFormat('F Y')); ?> Verileri
            </span>
            <span x-show="mode === 'all'" class="text-xs font-semibold text-gray-500 uppercase tracking-widest">
                Genel Toplam Verileri
            </span>
        </div>
    </div>

    
    <div>
        <h4 class="font-bold text-gray-700 text-sm uppercase mb-3 flex items-center gap-2">
            <span class="w-1 h-4 bg-gray-800 rounded-full"></span>
            Şikayet Durum Özeti <span class="text-[10px] text-gray-400 normal-case font-normal ml-auto">(Detay için karta tıklayın)</span>
        </h4>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
            <?php $__currentLoopData = [
                ['key' => 'toplam', 'label' => 'Toplam', 'color' => 'gray', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['key' => 'acik', 'label' => 'Açık / İşlemde', 'color' => 'blue', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['key' => 'geciken', 'label' => 'Geciken', 'color' => 'red', 'icon' => 'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['key' => 'cozulen', 'label' => 'Çözülen', 'color' => 'green', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['key' => 'hiz', 'data_key' => 'ortalama_sure', 'label' => 'Ort. Hız', 'color' => 'purple', 'unit' => 'gün', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z']
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpiItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <div @click="toggleTab('<?php echo e($kpiItem['key']); ?>')" 
                     class="cursor-pointer bg-white rounded-xl shadow-sm border border-gray-100 p-4 transition-all duration-300 hover:shadow-lg relative overflow-hidden group"
                     :class="activeTab === '<?php echo e($kpiItem['key']); ?>' ? 'ring-2 ring-<?php echo e($kpiItem['color']); ?>-500 ring-offset-2 bg-<?php echo e($kpiItem['color']); ?>-50' : 'hover:bg-gray-50'">
                    
                    <div class="absolute -right-2 -bottom-2 text-<?php echo e($kpiItem['color']); ?>-50 opacity-20 group-hover:opacity-40 transition-opacity transform group-hover:scale-110">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($kpiItem['icon']); ?>"/></svg>
                    </div>

                    <div class="relative z-10 flex flex-col items-center justify-center h-full">
                        <p class="text-[10px] font-bold text-<?php echo e($kpiItem['color']); ?>-500 uppercase tracking-widest mb-1"><?php echo e($kpiItem['label']); ?></p>
                        <div class="flex items-baseline gap-1">
                            <p x-show="mode === 'month'" class="text-3xl font-black text-gray-800" x-transition.opacity>
                                <?php echo e($kpiMonthly[$kpiItem['data_key'] ?? $kpiItem['key']]); ?>

                            </p>
                            <p x-show="mode === 'all'" class="text-3xl font-black text-gray-800" x-transition.opacity>
                                <?php echo e($kpi[$kpiItem['data_key'] ?? $kpiItem['key']]); ?>

                            </p>
                            <?php if(isset($kpiItem['unit'])): ?> <span class="text-xs font-bold text-gray-400"><?php echo e($kpiItem['unit']); ?></span> <?php endif; ?>
                        </div>
                        
                        <div class="mt-2 transition-transform duration-300" :class="activeTab === '<?php echo e($kpiItem['key']); ?>' ? 'rotate-180 text-<?php echo e($kpiItem['color']); ?>-600' : 'text-gray-300'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div x-show="activeTab !== null" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="mt-4 rounded-xl shadow-lg border p-4 relative z-20"
             :class="{
                'bg-gray-50 border-gray-200': activeTab === 'toplam',
                'bg-blue-50 border-blue-200': activeTab === 'acik',
                'bg-red-50 border-red-200': activeTab === 'geciken',
                'bg-green-50 border-green-200': activeTab === 'cozulen',
                'bg-purple-50 border-purple-200': activeTab === 'hiz'
             }"
        >
            
            <div class="flex justify-between items-center mb-3 border-b pb-2" :class="'border-' + activeColor + '-200'">
                <h5 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full" :class="'bg-' + activeColor + '-500'"></span>
                    
                    
                    <span x-text="activeTab ? activeTab.charAt(0).toUpperCase() + activeTab.slice(1) : ''"></span> Listesi 
                    
                    <span class="text-xs font-normal text-gray-500" x-text="'(' + filteredList.length + ' Kayıt)'"></span>
                </h5>
                <button @click="activeTab = null" class="text-gray-400 hover:text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>

            <div class="overflow-x-auto max-h-60 overflow-y-auto">
                
                <div x-show="filteredList.length === 0" class="text-center py-6 text-gray-400 italic text-sm">
                    Bu filtreye uygun kayıt bulunamadı (Son 10 kayıt içinde).
                </div>

                <table x-show="filteredList.length > 0" class="w-full text-xs text-left text-gray-600">
                    
                    <thead class="text-[10px] uppercase sticky top-0 z-10" 
                           :class="{
                                'bg-gray-100 text-gray-500': activeTab === 'toplam',
                                'bg-blue-100 text-blue-600': activeTab === 'acik',
                                'bg-red-100 text-red-600': activeTab === 'geciken',
                                'bg-green-100 text-green-600': activeTab === 'cozulen',
                                'bg-purple-100 text-purple-600': activeTab === 'hiz'
                           }">
                        <tr>
                            <th class="px-3 py-2 rounded-l-lg">Müşteri</th>
                            <th class="px-3 py-2">Konu</th>
                            <th class="px-3 py-2">Bölüm</th>
                            <th class="px-3 py-2">Durum</th>
                            <th x-show="activeTab === 'hiz'" class="px-3 py-2 text-center">Süre</th>
                            <th class="px-3 py-2 text-right">Tarih</th>
                            <th class="px-3 py-2 text-center rounded-r-lg">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200/50">
                        <template x-for="item in filteredList" :key="item.id">
                            <tr class="hover:bg-white transition">
                                <td class="px-3 py-2 font-bold" x-text="item.musteri"></td>
                                <td class="px-3 py-2" x-text="item.konu"></td>
                                <td class="px-3 py-2" x-text="item.bolum"></td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[9px]"
                                          :class="{
                                              'bg-yellow-100 text-yellow-800': item.durum === 'Yeni',
                                              'bg-blue-100 text-blue-800': item.durum === 'İşlemde',
                                              'bg-green-100 text-green-800': ['Kapatıldı', 'Çözümlendi'].includes(item.durum),
                                              'bg-red-100 text-red-800': ['Geciken', 'Yeniden Açıldı'].includes(item.durum) || item.is_geciken
                                          }"
                                          x-text="item.durum">
                                    </span>
                                </td>
                                <td x-show="activeTab === 'hiz'" class="px-3 py-2 text-center font-bold text-purple-600">
                                    <span x-text="item.sure ? item.sure + ' gün' : '-'"></span>
                                </td>
                                <td class="px-3 py-2 text-right text-gray-400 font-mono" x-text="item.tarih"></td>
                                <td class="px-3 py-2 text-center">
                                    <a :href="item.url" class="text-indigo-600 hover:underline font-bold">Git</a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    
    <div>
        <h4 class="font-bold text-gray-700 text-sm uppercase mb-3 flex items-center gap-2">
            <span class="w-1 h-4 bg-gray-800 rounded-full"></span>
            Müşteri Memnuniyet Analizi
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full">
            
            <div class="p-4 bg-green-50 border border-green-100 rounded-xl flex items-center justify-between shadow-sm hover:shadow-md transition group cursor-pointer">
                <div>
                    <p class="text-[10px] font-bold text-green-600 uppercase mb-1">Memnuniyet (Onay)</p>
                    <p class="text-2xl font-black text-green-700 group-hover:scale-110 transition-transform origin-left"><?php echo e($musteriKararIstatistikleri['onay_orani']); ?></p>
                </div>
                <div class="p-3 bg-white rounded-full text-green-500 shadow-sm group-hover:bg-green-500 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            
            <div class="p-4 bg-red-50 border border-red-100 rounded-xl flex items-center justify-between shadow-sm hover:shadow-md transition group cursor-pointer">
                <div>
                    <p class="text-[10px] font-bold text-red-600 uppercase mb-1">Memnuniyetsiz (Red)</p>
                    <p class="text-2xl font-black text-red-700 group-hover:scale-110 transition-transform origin-left"><?php echo e($musteriKararIstatistikleri['red_orani']); ?></p>
                </div>
                <div class="p-3 bg-white rounded-full text-red-500 shadow-sm group-hover:bg-red-500 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            
            <div class="p-4 bg-yellow-50 border border-yellow-100 rounded-xl flex items-center justify-between shadow-sm hover:shadow-md transition group cursor-pointer">
                <div>
                    <p class="text-[10px] font-bold text-yellow-600 uppercase mb-1">Revizyon Talebi</p>
                    <p class="text-2xl font-black text-yellow-700 group-hover:scale-110 transition-transform origin-left"><?php echo e($musteriKararIstatistikleri['revizyon']); ?></p>
                </div>
                <div class="p-3 bg-white rounded-full text-yellow-500 shadow-sm group-hover:bg-yellow-500 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
            </div>
        </div>
    </div>

</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/raporlar/partials/executive/kpi-overview.blade.php ENDPATH**/ ?>