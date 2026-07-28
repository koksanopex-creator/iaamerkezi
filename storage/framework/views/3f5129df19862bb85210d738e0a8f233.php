




<div class="space-y-10 animate-fade-in-up mt-8 pb-4 relative clear-both block w-full">

    
    <?php if(request()->filled('start_date') || request()->filled('end_date')): ?>
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm text-blue-700 font-medium">Şu an <strong><?php echo e(request('start_date') ?? '...'); ?></strong> ile <strong><?php echo e(request('end_date') ?? '...'); ?></strong> tarihleri arasındaki verilere bakıyorsunuz.</p>
                <a href="<?php echo e(route('dashboard')); ?>" class="ml-auto text-xs font-bold text-blue-600 hover:text-blue-800 underline flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Temizle</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white/70 backdrop-blur-md rounded-2xl p-4 border border-gray-100 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="flex items-end gap-3 w-full md:w-auto">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Başlangıç Tarihi</label>
                <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="text-sm rounded-lg border-gray-200 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Bitiş Tarihi</label>
                <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="text-sm rounded-lg border-gray-200 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <button type="submit" class="px-4 py-2 h-[42px] bg-gray-800 hover:bg-gray-900 text-white text-sm font-bold rounded-lg transition-colors flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> Filtrele</button>
        </form>
    </div>
    


    
    
    
    <div class="bg-white/70 backdrop-blur-md rounded-2xl p-4 border border-gray-100 shadow-sm mb-6">
        <div class="flex items-center gap-3 flex-wrap">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-1 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                HIZLI ERİŞİM
            </span>
            <a href="#onay-bekleyenler"
               class="relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100 hover:bg-purple-100 hover:shadow-sm transition-all group overflow-visible">
<?php if(count($stats['onay_bekleyen_sikayetler'] ?? []) > 0): ?>
                   <span class="absolute -top-1 -right-1 flex h-3 w-3">
                       <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                       <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                   </span>
               <?php endif; ?>
                <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Onay Bekleyenler (<?php echo e(count($stats['onay_bekleyen_sikayetler'] ?? [])); ?>)
                <svg class="w-3 h-3 text-purple-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            <a href="#takim-listesi"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100 hover:bg-blue-100 hover:shadow-sm transition-all group">
                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Müşteri Şikayetleri (<?php echo e($stats['toplam_sikayet_sayisi'] ?? 0); ?>)
                <svg class="w-3 h-3 text-blue-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            <a href="#musteri-temsilcileri"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-100 hover:bg-emerald-100 hover:shadow-sm transition-all group">
                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Müşteri & Temsilciler
                <svg class="w-3 h-3 text-emerald-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            <a href="#iadeler"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-700 rounded-lg text-xs font-bold border border-rose-100 hover:bg-rose-100 hover:shadow-sm transition-all group">
                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                İadeler
                <svg class="w-3 h-3 text-rose-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            <a href="#ziyaretler"
               class="relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-100 hover:bg-indigo-100 hover:shadow-sm transition-all group overflow-visible">
               <?php if(isset($stats['ziyaretler']) && $stats['ziyaretler']->where('status', 'Beklemede')->count() > 0): ?>
                   <span class="absolute -top-1 -right-1 flex h-3 w-3">
                       <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                       <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                   </span>
               <?php endif; ?>
                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Ziyaretler
                <svg class="w-3 h-3 text-indigo-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            <a href="#bekleyen-davetler"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-100 hover:bg-amber-100 hover:shadow-sm transition-all group">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                Takım Davetleri
                <svg class="w-3 h-3 text-amber-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
        </div>
    </div>


    <?php if($stats['has_teams']): ?>
        
        
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

            
            <div class="group relative bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl p-5 shadow-lg overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div class="relative z-10 text-white">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-white/20 rounded-xl backdrop-blur-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-indigo-100 text-xs font-medium">Toplam Sorumlu Olunan Şikayet/Takım</p>
                    <h3 class="text-3xl font-bold mt-1"><?php echo e($stats['toplam_sikayet_sayisi'] ?? 0); ?></h3>
                    <div class="mt-3 flex items-center text-[10px] text-indigo-200 font-medium">
                        Çözüm Lideri Liderliği
                    </div>
                </div>
            </div>

            
            <a href="#onay-bekleyenler" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-purple-200 transition-all duration-300 group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-purple-50 group-hover:bg-purple-100 text-purple-600 transition-colors rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-gray-400 text-[10px] group-hover:text-purple-600 transition-colors font-medium">İncele →</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo e(count($stats['onay_bekleyen_sikayetler'] ?? [])); ?></h3>
                <p class="text-gray-500 text-xs mt-0.5">Onay Aşamasındaki Projeler</p>
                <div class="mt-3 w-full bg-gray-100 rounded-full h-1">
                    <?php
                        $oranOnay = ($stats['toplam_sikayet_sayisi'] ?? 0) > 0 ? (count($stats['onay_bekleyen_sikayetler']) / $stats['toplam_sikayet_sayisi']) * 100 : 0;
                    ?>
                    <div class="bg-purple-400 h-1 rounded-full transition-all duration-500" style="width: <?php echo e($oranOnay); ?>%"></div>
                </div>
            </a>

            
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-green-50 text-green-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo e($stats['cozulen_sikayetler_count'] ?? 0); ?></h3>
                <p class="text-gray-500 text-xs mt-0.5">Tamamlanan Şikayetler</p>
                <p class="text-green-600 text-[11px] mt-2 font-medium flex items-center">
                    <?php if(($stats['toplam_sikayet_sayisi'] ?? 0) > 0): ?>
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        %<?php echo e(round((($stats['cozulen_sikayetler_count'] ?? 0) / ($stats['toplam_sikayet_sayisi'] ?? 1)) * 100)); ?> Başarı
                    <?php else: ?>
                        %0 Başarı
                    <?php endif; ?>
                </p>
            </div>
            
        </div>

        
    
    
    <?php if(count($stats['onay_bekleyen_sikayetler'] ?? []) > 0): ?>
        <?php
            $onaylayanlar = $stats['onay_bekleyen_sikayetler']->pluck('onaylayacak_kisi.name')->unique()->filter()->values();
        ?>
        <div id="onay-bekleyenler" 
            class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 scroll-mt-24 mb-8"
            x-data="{ 
                search: '', 
                approver: '', 
                duration: '', 
                limit: 4, 
                expanded: false 
            }"
        >
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-white flex flex-col gap-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-black text-purple-900 text-sm tracking-tight uppercase flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                            Onay Aşamasındaki Şikayet Projeleri (<?php echo e(count($stats['onay_bekleyen_sikayetler'])); ?>)
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium italic">Onay sürecinde olan ve takibini yapmanız gereken şikayet projelerinin güncel durumları ve onaylayacak yetkililer.</p>
                    </div>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="relative">
                        <input type="text" x-model="search" placeholder="Konu ile ara..." class="w-full pl-8 pr-4 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                        <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <select x-model="approver" class="w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                        <option value="">Tüm Onaylayacaklar</option>
                        <?php $__currentLoopData = $onaylayanlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($name); ?>"><?php echo e($name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <select x-model="duration" class="w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                        <option value="">Bekleme Süresi (Tümü)</option>
                        <option value="3">3+ Gündür Bekleyenler</option>
                        <option value="7">7+ Gündür Bekleyenler</option>
                    </select>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
                    <?php $__currentLoopData = $stats['onay_bekleyen_sikayetler']->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $beklemeSaat = $sikayet->onaya_gonderilme_tarihi->diffInHours(now());
                            $beklemeSuresi = ceil($beklemeSaat / 24);
                        ?>
                        <div 
                            class="rounded-2xl border border-gray-100 hover:border-purple-300 hover:shadow-xl bg-white transition-all duration-300 flex flex-col justify-between overflow-hidden matched-approval"
                            x-data="{
                                info: {
                                    t: '<?php echo e(strtolower($sikayet->musteri_sikayet_konusu)); ?>',
                                    a: '<?php echo e($sikayet->onaylayacak_kisi->name); ?>',
                                    d: <?php echo e($beklemeSuresi); ?>,
                                    i: <?php echo e($index); ?>

                                },
                                isVisible() {
                                    const matchSearch = this.search === '' || this.info.t.includes(this.search.toLowerCase());
                                    const matchApprover = this.approver === '' || this.info.a === this.approver;
                                    const matchDuration = this.duration === '' || (
                                        this.duration === '3' ? this.info.d >= 3 : 
                                        this.duration === '7' ? this.info.d >= 7 : true
                                    );
                                    return matchSearch && matchApprover && matchDuration;
                                }
                            }"
                            x-show="isVisible() && (expanded || info.i < limit)"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                        >
                            <div class="p-6">
                                <div class="space-y-4">
                                    
                                    <div>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Şikayet Konusu</span>
                                        <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>" class="text-sm font-black text-gray-900 hover:text-purple-600 transition-colors leading-tight block">
                                            <?php echo e($sikayet->musteri_sikayet_konusu); ?>

                                        </a>
                                    </div>

                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Kategori</span>
                                            <span class="text-xs font-bold text-gray-700 block"><?php echo e($sikayet->sikayetKategori->ad ?? 'Genel'); ?></span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Müşteri / Firma</span>
                                            <a href="<?php echo e(route('musteri.profil.show', $sikayet->customer_id)); ?>" class="text-xs font-bold text-indigo-700 hover:text-indigo-50 block truncate transition-colors underline decoration-indigo-200 underline-offset-2">
                                                <?php echo e($sikayet->customer->firma_adi ?? $sikayet->customer->name ?? 'N/A'); ?>

                                            </a>
                                        </div>
                                    </div>

                                    
                                    <div class="flex items-center justify-between pt-2 border-t border-gray-50">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Güncel Durum</span>
                                            <div class="flex items-center gap-2">
                                                <?php echo $sikayet->musteri_durum_badge; ?>

                                            </div>
                                            <?php if($sikayet->onaya_gonderilme_tarihi): ?>
                                                <?php
                                                    $beklemeSaat = $sikayet->onaya_gonderilme_tarihi->diffInHours(now());
                                                    $beklemeSuresi = ceil($beklemeSaat / 24);
                                                ?>
                                                <div class="mt-2 inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-black tracking-tight border <?php echo e($beklemeSuresi >= 3 ? 'bg-red-50 text-red-600 border-red-200 animate-pulse shadow-sm' : 'bg-amber-50 text-amber-700 border-amber-200'); ?>">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <?php echo e($beklemeSuresi); ?> GÜNDÜR ONAY BEKLİYOR
                                                </div>
                                                <div class="flex items-center gap-1 mt-1 text-[8px] font-medium text-gray-400 opacity-75">
                                                    Onaya Gönderim: <?php echo e($sikayet->onaya_gonderilme_tarihi->format('d.m.Y H:i')); ?>

                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($sikayet->onaylayacak_kisi): ?>
                                            <div class="text-right">
                                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Onaylayacak Kişi</span>
                                                <div class="flex items-center justify-end gap-2">
                                                    <div class="flex flex-col items-end">
                                                        <span class="text-xs font-bold text-gray-900 leading-none"><?php echo e($sikayet->onaylayacak_kisi->name); ?></span>
                                                        <span class="text-[10px] text-gray-500 mt-1"><?php echo e($sikayet->onaylayacak_kisi->unvan); ?></span>
                                                    </div>
                                                    <img src="<?php echo e($sikayet->onaylayacak_kisi->profile_photo_url); ?>" class="w-8 h-8 rounded-full border border-gray-100 shadow-sm" alt="">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-purple-50 border-t border-purple-100 flex items-center justify-between">
                                <span class="text-[10px] font-bold text-purple-400 uppercase tracking-widest">ONAY BEKLİYOR</span>
                                <a href="<?php echo e($sikayet->iaa_id ? route('proje.workspace.show', $sikayet->iaa_id) : route('admin.sikayetler.show', $sikayet->id)); ?>" class="text-xs font-black text-purple-600 hover:text-purple-800 underline decoration-purple-200 underline-offset-2">İncele &rarr;</a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="bg-gray-50 border-t border-gray-100 p-3 text-center" x-show="document.querySelectorAll('.matched-approval').length > 4 || expanded">
                <button type="button" @click="expanded = !expanded; limit = expanded ? 999 : 4" class="text-xs font-bold text-purple-600 hover:text-purple-800 inline-flex items-center gap-1">
                    <span x-text="expanded ? 'Gizle' : 'Tümünü Göster (' + document.querySelectorAll('.matched-approval').length + ')' "></span>
                    <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
        </div>
    <?php endif; ?>

    
    
    
    <div id="takim-listesi" 
         x-data="{ 
            search: '<?php echo e(request('sikayet_arama', '')); ?>', 
            customer: '<?php echo e(request('sikayet_musteri_id', '')); ?>', 
            bolum: '<?php echo e(request('sikayet_bolum_id', '')); ?>', 
            status: '<?php echo e(request('sikayet_durum', '')); ?>',
            expanded: false,
            limit: 4
         }" 
         x-init="$watch('search, customer, bolum, status', () => { expanded = false; limit = 4; })"
         class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-10 scroll-mt-24">
            
            <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-100 p-2.5 rounded-xl text-blue-600 shadow-sm border border-blue-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-gray-900 text-lg tracking-tight uppercase flex items-center gap-3">
                                AKTİF ŞİKAYETLER
                                <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-xs font-black shadow-lg shadow-blue-200/50"><?php echo e(count($stats['aktif_sikayetler_projeler'])); ?></span>
                            </h3>
                            <p class="text-xs text-gray-500 font-medium">Lideri olduğunuz ve işlemde olan şikayetlerin takımları ve ilerlemeleri.</p>
                        </div>
                    </div>
                </div>

                
                <div class="mt-4 flex items-center gap-2 flex-wrap bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-1 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg> FİLTRELE:</span>
                    </div>
                    
                    <div class="relative">
                        <input type="text" x-model="search" placeholder="Başlık ile ara..." class="text-xs rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-1.5 h-auto ps-8 w-40 md:w-56">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <select x-model="customer" class="text-xs rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-1.5 h-auto max-w-[200px]">
                        <option value="">Tüm Müşteriler</option>
                        <?php $__currentLoopData = $stats['aktif_sikayet_musterileri']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mO): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($mO->id); ?>"><?php echo e($mO->firma_adi ?? $mO->name ?? $mO->ad_soyad); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    <select x-model="bolum" class="text-xs rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-1.5 h-auto">
                        <option value="">Tüm Bölümler</option>
                        <?php $__currentLoopData = isset($tumBolumler) ? $tumBolumler : \App\Models\Bolum::orderBy('ad')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolumO): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($bolumO->id); ?>"><?php echo e($bolumO->ad); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    <button type="button" @click="search = ''; customer = ''; bolum = ''; status = '';" class="px-3 py-1.5 bg-gray-50 text-gray-700 hover:bg-gray-100 text-xs font-bold rounded-lg border border-gray-200 transition-colors">Sıfırla</button>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start" id="aktif-sikayet-cards">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['aktif_sikayetler_projeler']->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $workflow = $sikayet->iaaProjesi ? $sikayet->iaaProjesi->ilerleme_verisi : null;
                            $squad = $sikayet->cozumTakimi;
                            $cardData = [
                                't' => mb_strtolower($sikayet->musteri_sikayet_konusu),
                                'c' => (string)$sikayet->customer_id,
                                'b' => (string)($sikayet->sikayetKategori->bolum_id ?? ''),
                                's' => (string)$sikayet->musteri_durum,
                                'i' => $index
                            ];
                        ?>
                        <div 
                            class="rounded-2xl border border-gray-100 hover:border-blue-300 hover:shadow-xl bg-white transition-all duration-300 flex flex-col justify-between overflow-hidden sikayet-card matched-complaint group"
                            x-data="{ 
                                info: <?php echo e(json_encode($cardData)); ?>,
                                isVisible() {
                                    const matchSearch = this.search === '' || this.info.t.includes(this.search.toLowerCase());
                                    const matchCustomer = this.customer === '' || this.info.c === this.customer;
                                    const matchBolum = this.bolum === '' || this.info.b === this.bolum;
                                    const matchStatus = this.status === '' || this.info.s === this.status;
                                    return matchSearch && matchCustomer && matchBolum && matchStatus;
                                }
                            }"
                            x-show="isVisible() && (expanded || info.i < limit)"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                        >
                            
                            <div class="p-6">
                                <div class="space-y-4">
                                    
                                    <div>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Şikayet Konusu</span>
                                        <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>" class="text-sm font-black text-gray-900 group-hover:text-blue-600 transition-colors leading-tight block">
                                            <?php echo e($sikayet->musteri_sikayet_konusu); ?>

                                        </a>
                                    </div>

                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Kategori</span>
                                            <span class="text-xs font-bold text-gray-700 block"><?php echo e($sikayet->sikayetKategori->ad ?? 'Genel'); ?></span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Müşteri / Firma</span>
                                            <a href="<?php echo e(route('musteri.profil.show', $sikayet->customer_id)); ?>" class="text-xs font-bold text-blue-700 hover:text-blue-500 block truncate transition-colors underline decoration-blue-200 underline-offset-2">
                                                <?php echo e($sikayet->customer->firma_adi ?? $sikayet->customer->name ?? 'N/A'); ?>

                                            </a>
                                        </div>
                                    </div>

                                    
                                    <div class="flex items-center justify-between pt-2 border-t border-gray-50">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Güncel Durum</span>
                                            <div class="flex items-center gap-2">
                                                <?php echo $sikayet->musteri_durum_badge; ?>

                                            </div>
                                            <?php
                                                $aktifBeklemeSaat = $sikayet->updated_at->diffInHours(now());
                                                $aktifBeklemeSuresi = ceil($aktifBeklemeSaat / 24);
                                            ?>
                                            <div class="mt-2 inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-black tracking-tight border <?php echo e($aktifBeklemeSuresi >= 5 ? 'bg-red-50 text-red-600 border-red-200 animate-pulse shadow-sm' : 'bg-blue-50 text-blue-700 border-blue-200'); ?>">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <?php echo e($aktifBeklemeSuresi); ?> GÜNDÜR ÇÖZÜMÜNÜZÜ BEKLİYOR
                                            </div>
                                            <div class="flex items-center gap-1 mt-1 text-[8px] font-medium text-gray-400 opacity-75">
                                                Son İşlem: <?php echo e($sikayet->updated_at->format('d.m.Y H:i')); ?>

                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Takım</span>
                                            <span class="text-xs font-bold text-indigo-600"><?php echo e($squad ? $squad->ad : 'Atanmadı'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <?php if($workflow && $workflow['toplam'] > 0): ?>
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">PROJE İLERLEME</span>
                                        <span class="text-xs font-black text-blue-600">%<?php echo e(round($workflow['yuzde'])); ?></span>
                                    </div>
                                    <div class="w-full bg-white rounded-full h-2 shadow-inner p-0.5 border border-gray-200">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-1 rounded-full transition-all duration-700 shadow-sm" style="width: <?php echo e($workflow['yuzde']); ?>%"></div>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-[9px] font-bold text-gray-400 uppercase">Aktif Aşama:</span>
                                        <span class="text-[9px] font-black text-indigo-700 uppercase tracking-tight"><?php echo e($sikayet->iaaProjesi->aktif_asama_metni ?? 'İşlem Bekleniyor'); ?></span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">İş Akışı Bekleniyor</span>
                                    <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>" class="text-[10px] font-black text-blue-600 hover:text-blue-800 underline decoration-blue-200 underline-offset-2">Süreç Başlat &rarr;</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-span-1 xl:col-span-2 bg-gray-50 border border-gray-200 border-dashed rounded-xl p-8 text-center flex flex-col items-center justify-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v16m14 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <h4 class="text-gray-900 font-bold mb-1">Takıma Atanmış Şikayet Yok</h4>
                            <p class="text-sm text-gray-500">Şu anda lideri olduğunuz çözüm takımı için aktif bir şikayet bulunmamaktadır.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-gray-50 border-t border-gray-100 p-3 text-center" x-show="document.querySelectorAll('.matched-complaint').length > limit">
                <button type="button" @click="expanded = !expanded; limit = expanded ? 999 : 4" class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1">
                    <span x-text="expanded ? 'Gizle' : 'Tümünü Göster (' + document.querySelectorAll('.matched-complaint').length + ')' "></span>
                    <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
        </div>

<div id="musteri-temsilcileri" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8 scroll-mt-24">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white flex justify-between items-center">
                <h3 class="font-black text-emerald-900 text-sm tracking-tight uppercase flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Sorumlu Olunan Müşteriler (<?php echo e(isset($stats['sorumlu_musteriler']) ? count($stats['sorumlu_musteriler']) : 0); ?>)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[10px] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-3 font-medium">Müşteri Firması & Şikayetler</th>
                            <th class="px-6 py-3 font-medium">İletişim & Adres</th>
                            <th class="px-6 py-3 font-medium">Firma Yetkilileri</th>
                            <th class="px-6 py-3 font-medium text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $stats['sorumlu_musteriler']->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $musteri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $aktifler = isset($stats['liderin_sikayetleri']) ? $stats['liderin_sikayetleri']->where('customer_id', $musteri->id)->whereIn('musteri_durum', ['Yeni', 'İşlemde', 'Atandı', 'İnceleniyor', 'Devam Ediyor', 'Revize Ediliyor'])->count() : 0;
                                $cozulenler = isset($stats['liderin_sikayetleri']) ? $stats['liderin_sikayetleri']->where('customer_id', $musteri->id)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])->count() : 0;
                            ?>
                            <tr class="hover:bg-emerald-50/30 transition-colors toggle-row <?php echo e($index >= 5 ? 'hidden' : ''); ?>" data-index="<?php echo e($index); ?>">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <?php if($musteri->logo_path): ?>
                                            <img src="<?php echo e(asset('storage/' . $musteri->logo_path)); ?>" class="w-10 h-10 rounded-lg object-contain bg-white border border-gray-100 shadow-sm" alt="<?php echo e($musteri->firma_adi ?? $musteri->name); ?>">
                                        <?php else: ?>
                                            <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($musteri->firma_adi ?? $musteri->name ?? '-')); ?>&color=047857&background=d1fae5&rounded=true&bold=true" class="w-10 h-10 rounded-lg border border-gray-100 shadow-sm" alt="">
                                        <?php endif; ?>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <a href="<?php echo e(route('musteri.profil.show', $musteri->id)); ?>" class="font-bold text-emerald-800 hover:text-emerald-500 transition-colors underline decoration-emerald-200 underline-offset-2"><?php echo e($musteri->name ?? $musteri->firma_adi ?? '-'); ?></a>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                                <?php if($aktifler > 0): ?>
                                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200"><?php echo e($aktifler); ?> Aktif Şikayet</span>
                                                <?php endif; ?>
                                                <?php if($cozulenler > 0): ?>
                                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200"><?php echo e($cozulenler); ?> Çözümlenmiş</span>
                                                <?php endif; ?>
                                                <?php if($aktifler == 0 && $cozulenler == 0): ?>
                                                    <span class="text-[10px] text-gray-400">Şikayet Yok</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-700 flex items-center gap-1"><svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg> <?php echo e($musteri->phone ?? $musteri->telefon ?? 'Telefon Yok'); ?></div>
                                    <div class="text-xs text-gray-700 mt-1 flex items-center gap-1"><svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg> <?php echo e($musteri->email ?? $musteri->eposta ?? 'E-posta Yok'); ?></div>
                                    <div class="text-[10px] text-gray-500 mt-1 max-w-[200px] truncate" title="<?php echo e($musteri->address ?? $musteri->adres ?? '-'); ?>"><?php echo e($musteri->address ?? $musteri->adres ?? '-'); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if($musteri->users && $musteri->users->count() > 0): ?>
                                        <div class="flex flex-col gap-2">
                                            <?php $__currentLoopData = $musteri->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yetkili): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="group relative flex items-center gap-2 cursor-help p-1 -ml-1 hover:bg-gray-50 rounded">
                                                    <img src="<?php echo e($yetkili->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($yetkili->name)); ?>" class="w-6 h-6 rounded-full border border-gray-200" alt="">
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-semibold text-gray-800"><?php echo e($yetkili->name); ?> <span class="text-[10px] text-gray-500 font-normal">(<?php echo e($yetkili->gorev_tanimi ?? 'Yetkili'); ?>)</span></span>
                                                    </div>
                                                    <!-- Hover Tooltip -->
                                                    <div class="absolute left-0 bottom-full mb-1 hidden group-hover:block w-56 bg-gray-900 border border-gray-700 shadow-xl rounded-lg p-3 z-50">
                                                        <div class="mb-2 pb-2 border-b border-gray-700">
                                                            <p class="text-white font-bold text-xs"><?php echo e($yetkili->name); ?></p>
                                                            <p class="text-gray-400 text-[10px]"><?php echo e($yetkili->gorev_tanimi ?? 'Firma Yetkilisi'); ?></p>
                                                        </div>
                                                        <div class="flex flex-col gap-1.5">
                                                            <div class="flex items-center gap-2 text-gray-300 text-[11px]">
                                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                                <span><?php echo e($yetkili->email ?? 'Belirtilmemiş'); ?></span>
                                                            </div>
                                                            <div class="flex items-center gap-2 text-gray-300 text-[11px]">
                                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                                <span><?php echo e($yetkili->phone ?? $yetkili->telefon ?? 'Belirtilmemiş'); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400 italic">Yetkili ataması yok</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?php echo e(route('musteri.profil.show', $musteri->id)); ?>" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 hover:bg-emerald-100 transition-colors">Profili Aç</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        <p class="text-sm">Şu anda sorumlu olduğunuz kayıtlı müşteri bulunmuyor.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(isset($stats['sorumlu_musteriler']) && count($stats['sorumlu_musteriler']) > 5): ?>
            <div class="bg-gray-50 border-t border-gray-100 p-3 text-center">
                <button onclick="
                    let isExpanded = this.dataset.expanded === 'true';
                    this.closest('div[id]').querySelectorAll('.toggle-row').forEach(el => {
                        Number(el.dataset.index) >= 5 && (isExpanded ? el.classList.add('hidden') : el.classList.remove('hidden'));
                    });
                    this.dataset.expanded = isExpanded ? 'false' : 'true';
                    this.querySelector('span').innerText = isExpanded ? 'Tümünü Göster (<?php echo e(count($stats['sorumlu_musteriler'])); ?>)' : 'Gizle';
                    this.querySelector('svg').classList.toggle('rotate-180');
                " data-expanded="false" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 inline-flex items-center gap-1">
                    <span>Tümünü Göster (<?php echo e(count($stats['sorumlu_musteriler'])); ?>)</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
            <?php endif; ?>
        </div>

<div id="bekleyen-davetler" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8 scroll-mt-24">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-white flex justify-between items-center">
                <h3 class="font-black text-amber-900 text-sm tracking-tight uppercase flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    TAKIM DAVETLERİ
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[10px] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-3 font-medium">Şikayet Projesi</th>
                            <th class="px-6 py-3 font-medium">Davet Edilen Takım</th>
                            <th class="px-6 py-3 font-medium">Davetli Kullanıcı</th>
                            <th class="px-6 py-3 font-medium">Geçen Süre</th>
                            <th class="px-6 py-3 font-medium text-right">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php
                            $hasAnyInvites = false;
                            $inviteCount = 0;
                        ?>
                        <?php $__currentLoopData = $stats['aktif_sikayetler_projeler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($sikayet->cozumTakimi && $sikayet->cozumTakimi->davetiyeler && $sikayet->cozumTakimi->davetiyeler->count() > 0): ?>
                                <?php $hasAnyInvites = true; ?>
                                <?php $__currentLoopData = $sikayet->cozumTakimi->davetiyeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $davet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $inviteCount++; ?>
                                    <tr class="hover:bg-amber-50/30 transition-colors toggle-row <?php echo e($inviteCount > 5 ? 'hidden' : ''); ?>" data-index="<?php echo e($inviteCount); ?>">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800"><?php echo e(Str::limit($sikayet->musteri_sikayet_konusu, 40)); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs text-gray-700 font-semibold"><?php echo e($sikayet->cozumTakimi->ad); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-[11px] text-gray-800 font-bold bg-gray-100 px-2 py-1 rounded-md inline-block"><?php echo e($davet->davetEdilen->name ?? 'Bilinmiyor'); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-[11px] text-gray-500"><?php echo e($davet->created_at->diffForHumans()); ?> gönderildi</div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded border border-amber-200">Yanıt Bekliyor</span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                        <?php if(!$hasAnyInvites): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <p class="text-sm">Şu anda yanıt bekleyen bir takım davetiyeniz bulunmuyor.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(isset($inviteCount) && $inviteCount > 5): ?>
            <div class="bg-gray-50 border-t border-gray-100 p-3 text-center">
                <button onclick="
                    let isExpanded = this.dataset.expanded === 'true';
                    this.closest('div[id]').querySelectorAll('.toggle-row').forEach(el => {
                        Number(el.dataset.index) > 5 && (isExpanded ? el.classList.add('hidden') : el.classList.remove('hidden'));
                    });
                    this.dataset.expanded = isExpanded ? 'false' : 'true';
                    this.querySelector('span').innerText = isExpanded ? 'Tümünü Göster (<?php echo e($inviteCount); ?>)' : 'Gizle';
                    this.querySelector('svg').classList.toggle('rotate-180');
                " data-expanded="false" class="text-xs font-bold text-amber-600 hover:text-amber-800 inline-flex items-center gap-1">
                    <span>Tümünü Göster (<?php echo e($inviteCount); ?>)</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
            <?php endif; ?>
        </div>

<div id="iadeler" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8 scroll-mt-24">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-rose-50 to-white flex justify-between items-center">
                <h3 class="font-black text-rose-900 text-sm tracking-tight uppercase flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Sorumlu Olunan Şikayetlere Bağlı İadeler
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[10px] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-3 font-medium">Şikayet & Müşteri</th>
                            <th class="px-6 py-3 font-medium">İade Tarihi</th>
                            <th class="px-6 py-3 font-medium">Miktar</th>
                            <th class="px-6 py-3 font-medium">Sebep / Tür</th>
                            <th class="px-6 py-3 font-medium text-right">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = isset($stats['iadeler']) ? $stats['iadeler'] : []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $iade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $sikayetUrl = $iade->musteriSikayeti->iaa_id 
                                    ? route('proje.workspace.show', $iade->musteriSikayeti->iaa_id) 
                                    : route('admin.sikayetler.show', $iade->musteri_sikayet_id);
                            ?>
                            <tr class="hover:bg-rose-50/30 transition-colors toggle-row <?php echo e($index >= 5 ? 'hidden' : ''); ?>" data-index="<?php echo e($index); ?>">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800"><a href="<?php echo e($sikayetUrl); ?>" class="hover:text-rose-600"><?php echo e(Str::limit($iade->musteriSikayeti->musteri_sikayet_konusu ?? 'Bilinmiyor', 30)); ?></a></div>
                                    <div class="text-[11px] text-gray-500"><?php echo e($iade->musteriSikayeti->customer->firma_adi ?? $iade->musteriSikayeti->customer->name ?? $iade->musteriSikayeti->customer->ad_soyad ?? '-'); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-700 font-semibold"><?php echo e($iade->iade_tarihi ? $iade->iade_tarihi->format('d.m.Y') : '-'); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-800 font-bold"><?php echo e($iade->miktar); ?> <?php echo e($iade->birim); ?></div>
                                    <div class="text-[10px] text-gray-500">Toplam PM: <?php echo e($iade->toplam_parti_miktari ?? '-'); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-700"><?php echo e(Str::limit($iade->iade_sebebi ?? '-', 40)); ?></div>
                                    <div class="text-[10px] text-gray-500"><?php echo e($iade->urun_turu ?? '-'); ?></div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?php echo e($sikayetUrl); ?>" class="text-xs font-bold text-rose-600 hover:text-rose-800 bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-100 hover:bg-rose-100 transition-colors">İncele</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        <p class="text-sm">Şu anda onaylanmış şikayetlere ait iade bulunmuyor.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(isset($stats['iadeler']) && count($stats['iadeler']) > 5): ?>
            <div class="bg-gray-50 border-t border-gray-100 p-3 text-center">
                <button onclick="
                    let isExpanded = this.dataset.expanded === 'true';
                    this.closest('div[id]').querySelectorAll('.toggle-row').forEach(el => {
                        Number(el.dataset.index) >= 5 && (isExpanded ? el.classList.add('hidden') : el.classList.remove('hidden'));
                    });
                    this.dataset.expanded = isExpanded ? 'false' : 'true';
                    this.querySelector('span').innerText = isExpanded ? 'Tümünü Göster (<?php echo e(count($stats['iadeler'])); ?>)' : 'Gizle';
                    this.querySelector('svg').classList.toggle('rotate-180');
                " data-expanded="false" class="text-xs font-bold text-rose-600 hover:text-rose-800 inline-flex items-center gap-1">
                    <span>Tümünü Göster (<?php echo e(count($stats['iadeler'])); ?>)</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
            <?php endif; ?>
        </div>

<div id="ziyaretler" class="mb-8 scroll-mt-24">
            <div class="bg-white rounded-3xl border border-indigo-100 shadow-[0_10px_40px_rgba(79,70,229,0.05)] overflow-hidden relative">
                
                <div class="px-6 lg:px-8 py-6 border-b border-indigo-50 bg-gradient-to-r from-indigo-50/50 to-purple-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-lg lg:text-xl font-black text-gray-900 tracking-tight">Sorumlu Olunan Şikayetlere Bağlı Ziyaretler</h4>
                            <p class="text-xs text-gray-500 font-bold mt-0.5">Planlanan ve gerçekleştirilen ziyaret takibi</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <form method="GET" action="<?php echo e(url()->current()); ?>#ziyaretler" class="flex items-center gap-2 bg-white/60 p-1 rounded-xl border border-gray-100">
                            <input type="date" name="ziyaret_start_date" value="<?php echo e(request('ziyaret_start_date')); ?>" class="text-[10px] border-transparent bg-transparent rounded-lg px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-gray-600">
                            <span class="text-gray-300">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </span>
                            <input type="date" name="ziyaret_end_date" value="<?php echo e(request('ziyaret_end_date')); ?>" class="text-[10px] border-transparent bg-transparent rounded-lg px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-gray-600">
                            <button type="submit" class="p-2 bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </form>
                        <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100 shadow-sm">ZİYARET TAKİBİ</span>
                    </div>
                </div>

                
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.super-admin-visit-table', [
                    'bolumIds' => [],
                    'iaaIds' => isset($stats['ziyaretler']) ? collect($stats['ziyaretler'])->pluck('iaa_id')->unique()->filter()->toArray() : [],
                    'hideHeader' => true,
                    'startDate' => request('ziyaret_start_date'),
                    'endDate' => request('ziyaret_end_date')
                ]);

$__html = app('livewire')->mount($__name, $__params, 'cozum-lideri-visit-table-'.Auth::id(), $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>
        </div>

        
    <?php else: ?>
        <div class="bg-gradient-to-br from-yellow-50 to-orange-100 border border-yellow-200 rounded-2xl shadow-lg overflow-hidden p-8 flex flex-col items-center justify-center text-center mt-8">
            <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-yellow-900 mb-2">Henüz Bir Takıma Lider Değilsiniz</h3>
            <p class="text-yellow-700">Şu anda herhangi bir şikayet için özel olarak oluşturulmuş "Çözüm Takımı" lideri olarak atanmamışsınız.</p>
        </div>
    <?php endif; ?>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/dashboard/partials/cozum-lideri.blade.php ENDPATH**/ ?>