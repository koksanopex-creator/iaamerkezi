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
    <?php $__env->startPush('pageTitle', 'Disiplin Kurulu Portalı | '); ?>
    <?php $__env->startPush('styles'); ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php $__env->stopPush(); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative z-10">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-gradient-to-tr from-indigo-600 to-violet-600 rounded-2xl shadow-lg shadow-indigo-500/30 ring-4 ring-indigo-500/10">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-black text-3xl text-gray-800 tracking-tight leading-none">
                            Disiplin Kurulu <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600">Portalı</span>
                        </h2>
                    </div>
                </div>
                <p class="text-gray-500 text-sm font-medium ml-14">Kurul üyeleri, istatistikler ve interaktif toplantı yönetimi.</p>
            </div>

            <div class="flex items-center gap-3" x-data="{}">
                <?php if($uyeYonetimiYetkisi): ?>
                    <button @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-member-modal' }))" 
                            class="group relative inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-bold rounded-2xl transition-all hover:bg-indigo-700 hover:scale-105 active:scale-95 shadow-xl shadow-indigo-500/25 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Üye Ekle/Ata
                    </button>
                <?php endif; ?>
                <?php if (! (auth()->user()->hasRole('Yonetim'))): ?>
                    <button @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'plan-meeting-modal' }))" 
                            class="group relative inline-flex items-center px-6 py-3 bg-emerald-600 text-white text-sm font-bold rounded-2xl transition-all hover:bg-emerald-700 hover:scale-105 active:scale-95 shadow-xl shadow-emerald-500/25 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Toplantı Planla
                    </button>
                <?php endif; ?>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 space-y-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ 
        activeTab: <?php echo e(json_encode($activeTab ?: request('activeTab', 'overview'))); ?>,
        meetingSearch: '',
        meetingStatusFilter: '',
        viewMode: localStorage.getItem('kurulUyeViewMode') || 'grid',
        init() {
            const savedTab = localStorage.getItem('kurulActiveTab');
            if (savedTab) this.activeTab = savedTab;
        },
        setActiveTab(tab) {
            this.activeTab = tab;
            localStorage.setItem('kurulActiveTab', tab);
        },
        setViewMode(mode) {
            this.viewMode = mode;
            localStorage.setItem('kurulUyeViewMode', mode);
        }
    }">
        
        <!-- HATA MESAJLARI -->
        <?php if($errors->any()): ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-3xl shadow-xl shadow-rose-500/10 mb-6 animate-fade-in-down">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-rose-500 text-white rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="font-black text-sm uppercase tracking-wide">Bir Hata Oluştu!</span>
                </div>
                <ul class="list-disc list-inside text-xs font-bold space-y-1 ml-11">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- BAŞARI MESAJLARI -->
        <?php if(session('success')): ?>
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-3xl flex items-center justify-between shadow-xl shadow-emerald-500/10 animate-fade-in-down">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-500 text-white rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="font-black text-sm uppercase tracking-wide"><?php echo e(session('success')); ?></span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        <?php endif; ?>
        
        <!-- FİLTRELER ÇUBUĞU -->
        <div class="bg-white/60 backdrop-blur-xl rounded-[2rem] border border-white/40 p-5 shadow-2xl shadow-indigo-500/5 ring-1 ring-black/5">
            <form action="<?php echo e(route('admin.disiplin.kurul.index')); ?>" method="GET" class="flex flex-wrap items-end gap-6">
                <div class="space-y-2 min-w-[180px]">
                    <label class="text-[10px] font-black text-indigo-400 uppercase tracking-widest ml-2">Başlangıç Tarihi</label>
                    <div class="relative group">
                        <input type="date" name="baslangic" value="<?php echo e($baslangic); ?>" class="w-full bg-indigo-50/30 border-none ring-2 ring-indigo-100 focus:ring-4 focus:ring-indigo-400 text-indigo-900 rounded-2xl transition-all text-sm py-2.5">
                    </div>
                </div>
                <div class="space-y-2 min-w-[180px]">
                    <label class="text-[10px] font-black text-indigo-400 uppercase tracking-widest ml-2">Bitiş Tarihi</label>
                    <div class="relative group">
                        <input type="date" name="bitis" value="<?php echo e($bitis); ?>" class="w-full bg-indigo-50/30 border-none ring-2 ring-indigo-100 focus:ring-4 focus:ring-indigo-400 text-indigo-900 rounded-2xl transition-all text-sm py-2.5">
                    </div>
                </div>
                <div class="space-y-2 min-w-[240px]">
                    <label class="text-[10px] font-black text-indigo-400 uppercase tracking-widest ml-2">Bölüm / Departman</label>
                    <select name="bolum_id" class="w-full bg-indigo-50/30 border-none ring-2 ring-indigo-100 focus:ring-4 focus:ring-indigo-400 text-indigo-900 rounded-2xl transition-all text-sm py-2.5 appearance-none">
                        <option value="">Tüm Bölümler</option>
                        <?php $__currentLoopData = $bolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($bolum->id); ?>" <?php echo e($bolumId == $bolum->id ? 'selected' : ''); ?>><?php echo e($bolum->ad); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex gap-3 ml-auto md:ml-0">
                    <button type="submit" class="p-3 bg-indigo-600 hover:bg-black text-white rounded-2xl transition-all shadow-lg shadow-indigo-500/30 hover:shadow-black/20 flex items-center gap-2 group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span class="text-sm font-bold pr-2 hidden sm:inline">Uygula</span>
                    </button>
                    <a href="<?php echo e(route('admin.disiplin.kurul.index')); ?>" class="p-3 bg-indigo-100 hover:bg-indigo-200 text-indigo-600 rounded-2xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                </div>
            </form>
        </div>

        <!-- ÖZET KARTLARI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Kurul Üyeleri -->
            <div @click="setActiveTab('members'); window.scrollTo({top: document.getElementById('kurul-tabs').offsetTop - 100, behavior: 'smooth'})" 
                 class="relative group overflow-hidden bg-white rounded-[2.5rem] border border-white p-6 shadow-xl transition-all hover:-translate-y-2 hover:shadow-indigo-500/20 cursor-pointer">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em]">Kurul Üyeleri</p>
                        <h3 class="text-4xl font-black text-gray-800 mt-2 tracking-tighter"><?php echo e($tumUyeler->count()); ?></h3>
                    </div>
                    <div class="p-3.5 bg-indigo-50 text-indigo-600 rounded-2xl shadow-inner ring-1 ring-indigo-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-2 relative z-10">
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded-lg uppercase whitespace-nowrap"><?php echo e($baskanlar->count()); ?> Başkan</span>
                    <span class="px-2.5 py-1 bg-gray-50 text-gray-500 text-[10px] font-black rounded-lg uppercase whitespace-nowrap"><?php echo e($uyeler->count()); ?> Üye</span>
                </div>
            </div>

            <!-- Toplam Toplantı -->
            <div @click="setActiveTab('meetings'); meetingStatusFilter = 'tamamlandı'; window.scrollTo({top: document.getElementById('kurul-tabs').offsetTop - 100, behavior: 'smooth'})" 
                 class="relative group overflow-hidden bg-white rounded-[2.5rem] border border-white p-6 shadow-xl transition-all hover:-translate-y-2 hover:shadow-emerald-500/20 cursor-pointer">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em]">Yapılan Toplantılar</p>
                        <h3 class="text-4xl font-black text-gray-800 mt-2 tracking-tighter"><?php echo e($tamamlananToplantiSayisi); ?></h3>
                    </div>
                    <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-2xl shadow-inner ring-1 ring-emerald-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-between relative z-10">
                    <span class="text-[10px] text-emerald-600 font-black uppercase tracking-widest whitespace-nowrap">TAMAMLANAN</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                </div>
            </div>

            <!-- Bekleyen Gündem -->
            <div @click="setActiveTab('overview'); document.getElementById('kurul-gundemi').scrollIntoView({ behavior: 'smooth', block: 'start' })" 
                 class="relative group overflow-hidden bg-white rounded-[2.5rem] border border-white p-6 shadow-xl transition-all hover:-translate-y-2 hover:shadow-rose-500/20 cursor-pointer">
                <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em]">Bekleyen Gündem</p>
                        <h3 class="text-4xl font-black text-gray-800 mt-2 tracking-tighter"><?php echo e($bekleyenGundemSayisi); ?></h3>
                    </div>
                    <div class="p-3.5 bg-rose-50 text-rose-600 rounded-2xl shadow-inner ring-1 ring-rose-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-between relative z-10">
                    <span class="text-[10px] text-rose-600 font-black uppercase tracking-widest whitespace-nowrap">ACİL GÖRÜŞÜLECEK</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                </div>
            </div>

            <!-- Planlanan Toplantı -->
            <div @click="setActiveTab('meetings'); meetingStatusFilter = 'planlandı'; window.scrollTo({top: document.getElementById('kurul-tabs').offsetTop - 100, behavior: 'smooth'})" 
                 class="relative group overflow-hidden bg-white rounded-[2.5rem] border border-white p-6 shadow-xl transition-all hover:-translate-y-2 hover:shadow-sky-500/20 cursor-pointer">
                <div class="absolute top-0 right-0 w-32 h-32 bg-sky-500/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-[10px] font-black text-sky-500 uppercase tracking-[0.2em]">Gelecek Oturumlar</p>
                        <h3 class="text-4xl font-black text-gray-800 mt-2 tracking-tighter"><?php echo e($planlananToplantiSayisi); ?></h3>
                    </div>
                    <div class="p-3.5 bg-sky-50 text-sky-600 rounded-2xl shadow-inner ring-1 ring-sky-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-between relative z-10">
                    <span class="text-[10px] text-sky-600 font-black uppercase tracking-widest whitespace-nowrap">PLANLANMIŞ</span>
                    <?php if($planlananToplantiSayisi > 0): ?>
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-sky-500"></span>
                        </span>
                    <?php else: ?>
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ortalama Katılım -->
            <div @click="setActiveTab('members'); window.scrollTo({top: document.getElementById('kurul-tabs').offsetTop - 100, behavior: 'smooth'})" 
                 class="relative group overflow-hidden bg-white rounded-[2.5rem] border border-white p-6 shadow-xl transition-all hover:-translate-y-2 hover:shadow-amber-500/20 cursor-pointer">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em]">Katılım Oranı</p>
                        <h3 class="text-4xl font-black text-gray-800 mt-2 tracking-tighter">%<?php echo e($ortalamKatilim); ?></h3>
                    </div>
                    <div class="p-3.5 bg-amber-50 text-amber-600 rounded-2xl shadow-inner ring-1 ring-amber-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-2 relative z-10">
                    <div class="flex -space-x-2">
                        <?php $__currentLoopData = $tumUyeler->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <img src="<?php echo e($u->profile_photo_url); ?>" alt="<?php echo e($u->name); ?>" class="w-7 h-7 rounded-full border-2 border-white shadow-sm ring-1 ring-amber-100 object-cover">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <span class="text-[10px] text-amber-600 font-bold ml-1">Kümülatif Ortalama</span>
                </div>
            </div>

            <!-- Karar Sayısı -->
            <div @click="setActiveTab('archive'); window.scrollTo({top: document.getElementById('kurul-tabs').offsetTop - 100, behavior: 'smooth'})" 
                 class="relative group overflow-hidden bg-white rounded-[2.5rem] border border-white p-6 shadow-xl transition-all hover:-translate-y-2 hover:shadow-purple-500/20 cursor-pointer">
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-[10px] font-black text-purple-500 uppercase tracking-[0.2em]">Sonuçlanan Disiplin Dosyaları</p>
                        <h3 class="text-4xl font-black text-gray-800 mt-2 tracking-tighter"><?php echo e($kararVerilenDosya); ?> / <?php echo e($toplamDosya); ?></h3>
                    </div>
                    <div class="p-3.5 bg-purple-50 text-purple-600 rounded-2xl shadow-inner ring-1 ring-purple-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-6 space-y-2 relative z-10">
                    <div class="flex justify-between items-center px-1">
                        <span class="text-[10px] text-purple-600 font-black uppercase">Verimlilik</span>
                        <?php $yuzde = $toplamDosya > 0 ? ($kararVerilenDosya / $toplamDosya) * 100 : 0; ?>
                        <span class="text-[10px] text-purple-600 font-black">%<?php echo e(round($yuzde)); ?></span>
                    </div>
                    <div class="w-full bg-purple-50 h-2.5 rounded-full overflow-hidden p-0.5 ring-1 ring-purple-100">
                        <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-full rounded-full transition-all duration-1000" style="width: <?php echo e($yuzde); ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB NAVİGASYONU -->
        <div id="kurul-tabs" class="flex items-center gap-2 border-b border-gray-100 p-1.5 bg-white/40 backdrop-blur-md rounded-[1.5rem] w-full overflow-x-auto custom-scrollbar shadow-lg shadow-indigo-500/5 ring-1 ring-black/5">
            <button @click="setActiveTab('overview')" :class="activeTab === 'overview' ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-500/30' : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50/50'" class="flex-1 justify-center whitespace-nowrap px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-3 relative">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Genel Bakış
                <?php if($bekleyenGundemSayisi > 0): ?>
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-600 border-2 border-white shadow-sm"></span>
                    </span>
                <?php endif; ?>
            </button>
            <button @click="setActiveTab('members')" :class="activeTab === 'members' ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-500/30' : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50/50'" class="flex-1 justify-center whitespace-nowrap px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-3">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354l1.1 2.226c.458.923 1.63 1.776 2.603 1.895l1.98.243c.974.12 1.258.995.632 1.623l-1.44 1.442c-.443.444-.68 1.155-.526 1.76l.342 1.983c.153.605-.516 1.246-1.12.827l-1.782-1.236c-.504-.35-1.328-.35-1.832 0l-1.782 1.236c-.604.42-1.273-.222-1.12-.827l.342-1.983c.154-.605-.083-1.216-.526-1.76l-1.44-1.442c-.626-.628-.342-1.503.632-1.623l1.98-.243c.973-.12 2.145-.972 2.603-1.895L12 4.354z"/></svg>
                Üyeler & Performans
            </button>
            <button @click="setActiveTab('meetings')" :class="activeTab === 'meetings' ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-500/30' : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50/50'" class="flex-1 justify-center whitespace-nowrap px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-3 relative">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Toplantı Takvimi
                <?php if($planlananToplantiSayisi > 0): ?>
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-600 border-2 border-white shadow-sm"></span>
                    </span>
                <?php endif; ?>
            </button>
            <button @click="setActiveTab('archive')" :class="activeTab === 'archive' ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-500/30' : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50/50'" class="flex-1 justify-center whitespace-nowrap px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-3">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Dosya Arşivi
            </button>
            <button @click="setActiveTab('history')" :class="activeTab === 'history' ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-500/30' : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50/50'" class="flex-1 justify-center whitespace-nowrap px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-3">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Üyelik Arşivi
            </button>
        </div>

        <!-- İÇERİK ALANLARI -->
        <div class="space-y-8">
            
            <!-- GENEL BAKIŞ TABI -->
            <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-12">
                
                <!-- SON KARARLAR (ÜST PANEL - YATAY KAYDIRMALI) -->
                <div class="space-y-6">
                    <h4 class="text-gray-800 font-black text-xl flex items-center gap-3 px-2">
                        <span class="w-2 h-6 bg-gradient-to-b from-purple-400 to-purple-600 rounded-full"></span>
                        Son Kararlar
                    </h4>
                    
                    <div x-data="{
                            scrollInterval: null,
                            startScroll() {
                                this.scrollInterval = setInterval(() => {
                                    let maxScroll = this.$el.scrollWidth - this.$el.clientWidth;
                                    if (this.$el.scrollLeft >= maxScroll - 10) {
                                        this.$el.scrollTo({ left: 0, behavior: 'smooth' });
                                    } else {
                                        this.$el.scrollBy({ left: 344, behavior: 'smooth' });
                                    }
                                }, 3000);
                            },
                            stopScroll() {
                                clearInterval(this.scrollInterval);
                            }
                         }"
                         x-init="startScroll()"
                         @mouseenter="stopScroll()"
                         @mouseleave="startScroll()"
                         class="flex overflow-x-auto gap-6 pb-6 custom-scrollbar snap-x scroll-smooth">
                        <?php $__currentLoopData = $sonKararlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $karar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="min-w-[320px] max-w-[320px] shrink-0 snap-start bg-white p-5 rounded-3xl border border-gray-100 shadow-xl shadow-gray-500/5 hover:border-purple-200 transition-colors relative overflow-hidden group">
                                <div class="flex items-center gap-4 relative z-10">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-2xl overflow-hidden ring-4 ring-purple-50 group-hover:ring-purple-100 transition-all">
                                            <?php if($karar->user): ?>
                                                <img src="<?php echo e($karar->user->profile_photo_url); ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-purple-500 rounded-full border-2 border-white shadow-sm"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h6 class="text-sm font-black text-gray-800 truncate"><?php echo e($karar->user->name ?? 'Silinmiş Personel'); ?></h6>
                                        
                                        <?php
                                            $iMeeting = $karar->toplantilar()->where('durum', 'tamamlandı')->latest()->first();
                                        ?>

                                        <?php if($iMeeting && $iMeeting->kararMaddeleri->count() > 0): ?>
                                            <div class="mt-1 space-y-1">
                                                <?php $__currentLoopData = $iMeeting->kararMaddeleri->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <p class="text-[9px] font-bold text-slate-500 line-clamp-1 flex items-center gap-1">
                                                        <span class="w-1 h-1 bg-purple-400 rounded-full shrink-0"></span>
                                                        <span class="truncate"><?php echo e($m->icerik); ?></span>
                                                    </p>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-[9px] font-black text-purple-400 uppercase tracking-tighter truncate mt-0.5"><?php echo e($karar->behavior->tanim ?? 'Disiplin Vukuatı'); ?></p>
                                        <?php endif; ?>

                                        <div class="flex items-center gap-2 mt-3">
                                            <span class="text-[10px] text-gray-400 font-bold font-mono"><?php echo e($karar->karar_tarihi ? $karar->karar_tarihi->format('d.m.Y') : $karar->created_at->format('d.m.Y')); ?></span>
                                            <a href="<?php echo e(route('admin.disiplin.show', $karar)); ?>" class="text-[10px] font-black text-indigo-500 hover:text-indigo-700 underline decoration-indigo-200 underline-offset-2">Arşive Git</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="absolute right-[-10px] bottom-[-10px] opacity-10 group-hover:opacity-20 transition-opacity">
                                    <svg class="w-16 h-16 text-purple-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zm-1-11v-3a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1zm0 5v-3a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1z"/></svg>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($sonKararlar->isEmpty()): ?>
                            <div class="w-full bg-gray-50/50 border border-dashed border-gray-200 p-8 rounded-3xl text-center">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Henüz karar kaydı yok</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- KURUL GÜNDEMİ & TOPLANTI AKIŞI (ALT PANEL) -->
                <div class="space-y-6" id="kurul-gundemi">
                    <div class="flex items-center justify-between px-2">
                        <h4 class="text-gray-800 font-black text-xl flex items-center gap-3">
                            <span class="w-2 h-6 bg-gradient-to-b from-indigo-400 to-indigo-600 rounded-full"></span>
                            Kurul Gündemi & Toplantı Akışı
                        </h4>
                        <div class="flex items-center gap-4">
                            
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" x-model="meetingSearch" placeholder="Başlık veya gündem ara..." class="block w-full sm:w-64 pl-11 pr-4 py-2 bg-white border-gray-200 focus:ring-2 focus:ring-indigo-500/20 rounded-xl transition-all font-bold text-xs placeholder-gray-400">
                            </div>

                            <div class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-full ring-1 ring-indigo-100">
                                <?php echo e($paginatedGenelBakis->total()); ?> Toplam Kayıt
                            </div>
                            
                            
                            <div class="flex items-center gap-2">
                                <select x-model="meetingStatusFilter" class="bg-white border-gray-200 rounded-xl text-[10px] font-black uppercase tracking-wider focus:ring-indigo-500 focus:border-indigo-500 py-2 pr-8 transition-all">
                                    <option value="">TÜM DURUMLAR</option>
                                    <option value="gündem_bekliyor">GÜNDEM BEKLEYENLER</option>
                                    <option value="planlandı">PLANLANANLAR</option>
                                    <option value="devam_ediyor">DEVAM EDENLER</option>
                                    <option value="tamamlandı">TAMAMLANANLAR</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/70 backdrop-blur-2xl rounded-[2.5rem] border border-white/50 shadow-2xl shadow-indigo-500/5 ring-1 ring-black/[0.02] overflow-hidden">
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse min-w-[1000px]">
                                <thead>
                                    <tr class="bg-gray-50/50 border-b border-gray-100">
                                        <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[9px] w-12 text-center">No</th>
                                        <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">İşlem / Gündem</th>
                                        <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">Tarih & Saat</th>
                                        <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">Tür / Lokasyon</th>
                                        <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">Sorumlu / Planlayan</th>
                                        <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">Katılımcılar</th>
                                        <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">Durum</th>
                                        <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px] w-48 text-right">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <?php $__empty_1 = true; $__currentLoopData = $paginatedGenelBakis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="hover:bg-indigo-50/20 transition-all group" 
                                            x-show="(meetingSearch === '' || <?php echo e(json_encode(strtolower($t->baslik))); ?>.includes(meetingSearch.toLowerCase())) && (meetingStatusFilter === '' || '<?php echo e($t->durum); ?>' === meetingStatusFilter)">
                                            <td class="px-6 py-6 text-center">
                                                <span class="text-xs font-black text-gray-300">#<?php echo e($loop->iteration); ?></span>
                                            </td>
                                            <td class="px-5 py-6">
                                                <div class="font-black text-gray-800 text-base group-hover:text-indigo-600 transition-colors"><?php echo e($t->baslik); ?></div>
                                                <?php if($t->disiplinDosyalari->count() > 0): ?>
                                                    <div class="text-[10px] text-indigo-400 font-black mt-1.5 flex flex-wrap items-center gap-1.5 uppercase tracking-tighter">
                                                        <?php $__currentLoopData = $t->disiplinDosyalari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="flex items-center gap-1 bg-indigo-50/50 px-1.5 py-0.5 rounded border border-indigo-100/50">
                                                                <span class="text-indigo-600">REF: #<?php echo e($dosya->id); ?></span>
                                                                <?php if($dosya->user): ?>
                                                                    <span class="text-gray-400">|</span>
                                                                    <span class="text-gray-600"><?php echo e($dosya->user->name); ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-5 py-6 whitespace-nowrap">
                                                <?php if($t->baslangic_tarihi && $t->durum !== 'gündem_bekliyor'): ?>
                                                    <div class="text-gray-800 font-black"><?php echo e(\Carbon\Carbon::parse($t->baslangic_tarihi)->translatedFormat('d F Y')); ?></div>
                                                    <div class="text-[11px] text-gray-400 font-bold mt-0.5"><?php echo e(\Carbon\Carbon::parse($t->baslangic_tarihi)->format('H:i')); ?></div>
                                                <?php else: ?>
                                                    <span class="text-[10px] font-bold text-gray-300 italic">Atanmadı</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-5 py-6">
                                                <div class="px-2 py-0.5 inline-block rounded-lg bg-gray-100 text-[10px] font-black uppercase text-gray-500 mb-1.5"><?php echo e($t->tur); ?></div>
                                                <div class="text-xs text-gray-800 font-medium italic flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                    <?php echo e($t->yer ?: '-'); ?>

                                                </div>
                                            </td>
                                            <td class="px-5 py-6">
                                                <div class="flex items-center gap-2">
                                                    <?php if($t->olusturan): ?>
                                                        <img src="<?php echo e($t->olusturan->profile_photo_url); ?>" class="w-6 h-6 rounded-full border border-gray-100">
                                                        <span class="text-[11px] font-black text-gray-700"><?php echo e($t->olusturan->name); ?></span>
                                                    <?php else: ?>
                                                        <div class="w-6 h-6 rounded-full bg-gray-50 flex items-center justify-center border border-gray-100">
                                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                        </div>
                                                        <span class="text-[11px] font-black text-gray-400 italic">Sistem / Beklemede</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-5 py-6">
                                                <div class="flex -space-x-3 hover:-space-x-1 transition-all duration-300">
                                                    <?php $__empty_2 = true; $__currentLoopData = $t->katilimcilar->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                        <?php if($kat->user): ?>
                                                            <img src="<?php echo e($kat->user->profile_photo_url); ?>" class="w-8 h-8 rounded-full border-2 border-white shadow-sm ring-1 ring-gray-100" title="<?php echo e($kat->user->name); ?>">
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                        <span class="text-[9px] font-bold text-gray-300 italic">-</span>
                                                    <?php endif; ?>
                                                    <?php if($t->katilimcilar->count() > 5): ?>
                                                        <div class="w-8 h-8 rounded-full bg-gray-50 border-2 border-white shadow-sm flex items-center justify-center text-[8px] text-gray-500 font-black">+<?php echo e($t->katilimcilar->count() - 5); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-5 py-6 whitespace-nowrap">
                                                <?php
                                                    $statusClasses = match($t->durum) {
                                                        'planlandı' => 'bg-blue-50 text-blue-600 ring-blue-100',
                                                        'devam_ediyor' => 'bg-amber-50 text-amber-600 ring-amber-200 animate-pulse',
                                                        'tamamlandı' => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
                                                        'iptal' => 'bg-rose-50 text-rose-600 ring-rose-100',
                                                        'gündem_bekliyor' => 'bg-gray-100 text-gray-500 ring-gray-200',
                                                        default => 'bg-gray-50 text-gray-400 ring-gray-100'
                                                    };
                                                    $statusLabel = match($t->durum) {
                                                        'planlandı' => 'PLANLANDI',
                                                        'devam_ediyor' => 'DEVAM EDİYOR',
                                                        'tamamlandı' => 'TAMAMLANDI',
                                                        'iptal' => 'İPTAL EDİLDİ',
                                                        'gündem_bekliyor' => 'GÜNDEM BEKLEYEN',
                                                        default => strtoupper($t->durum)
                                                    };
                                                ?>
                                                <span class="px-3 py-1 rounded-full text-[9px] font-black tracking-widest ring-1 <?php echo e($statusClasses); ?>">
                                                    <?php echo e($statusLabel); ?>

                                                </span>
                                            </td>
                                            <td class="px-5 py-6 text-right">
                                                <?php if($t->type === 'meeting'): ?>
                                                    <a href="<?php echo e(route('admin.disiplin.kurul.toplanti.show', $t->original_id)); ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-50 text-gray-400 hover:bg-indigo-600 hover:text-white transition-all shadow-sm group/btn">
                                                        <svg class="w-5 h-5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?php echo e(route('admin.disiplin.show', $t->original_id)); ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm group/btn">
                                                        <svg class="w-5 h-5 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="8" class="px-6 py-12 text-center text-gray-400 italic font-medium">Henüz kayıtlı bir gündem veya toplantı bulunmuyor.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                            <?php echo e($paginatedGenelBakis->links()); ?>

                        </div>
                    </div>
                </div>
            </div>

            <!-- ÜYELER VE PERFORMANS TABI -->
            <div x-show="activeTab === 'members'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
                <div class="flex items-center justify-between px-2">
                    <h4 class="text-gray-800 font-black text-xl flex items-center gap-3">
                        <span class="w-2 h-6 bg-gradient-to-b from-indigo-400 to-indigo-600 rounded-full"></span>
                        Kurul Üyesi Karneleri
                    </h4>
                    <div class="flex items-center gap-1 bg-gray-100/50 p-1 rounded-xl ring-1 ring-black/5">
                        <button @click="setViewMode('grid')" :class="viewMode === 'grid' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="p-2 rounded-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        </button>
                        <button @click="setViewMode('list')" :class="viewMode === 'list' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="p-2 rounded-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                    </div>
                </div>

                
                <div x-show="viewMode === 'grid'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    <?php $__currentLoopData = $uyeStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white rounded-[2.5rem] border border-gray-100 overflow-hidden shadow-2xl shadow-indigo-500/5 group hover:border-indigo-400 transition-all hover:scale-[1.02]">
                            <!-- Header -->
                            <div class="p-6 bg-gradient-to-br from-gray-50/50 to-white border-b border-gray-100 relative">
                                <div class="absolute top-4 right-4">
                                    <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider <?php echo e($stat['rol'] === 'Başkan' ? 'bg-amber-100 text-amber-600 ring-1 ring-amber-200' : 'bg-gray-100 text-gray-500 ring-1 ring-gray-200'); ?>">
                                        <?php echo e($stat['rol']); ?>

                                    </span>
                                </div>
                                <div class="flex items-center gap-5">
                                    <div class="relative shrink-0">
                                        <img src="<?php echo e($stat['user']->profile_photo_url); ?>" alt="<?php echo e($stat['user']->name); ?>" class="w-20 h-20 rounded-[1.5rem] object-cover ring-4 ring-indigo-50 shadow-xl group-hover:ring-indigo-100 transition-all">
                                        <div class="absolute -bottom-2 -left-2 w-8 h-8 bg-black rounded-xl flex items-center justify-center text-white ring-4 ring-white shadow-lg">
                                            <span class="text-[10px] font-black">#<?php echo e($loop->iteration); ?></span>
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <h5 class="text-xl font-black text-gray-800 truncate"><?php echo e($stat['user']->name); ?></h5>
                                        <p class="text-indigo-600 text-[10px] font-black uppercase tracking-widest mt-1"><?php echo e($stat['user']->bolum->ad ?? 'Genel Yönetim'); ?></p>
                                        <div class="flex items-center gap-1.5 mt-2">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            <span class="text-[10px] text-gray-400 font-bold italic"><?php echo e($stat['katilim_tarihi'] ? $stat['katilim_tarihi']->translatedFormat('d M Y') : '-'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Body (Stats) -->
                            <div class="p-6 space-y-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Oturum Katılımı</p>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-3xl font-black text-gray-800">%<?php echo e($stat['katilim_orani']); ?></span>
                                        </div>
                                        <div class="w-full bg-gray-100 h-2 rounded-full mt-2 overflow-hidden shadow-inner">
                                            <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 h-full rounded-full transition-all duration-1000" style="width: <?php echo e($stat['katilim_orani']); ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Toplam Oylama</p>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-3xl font-black text-gray-800"><?php echo e($stat['oy_kullanilanSayisi']); ?></span>
                                            <span class="text-[10px] text-gray-400 font-black uppercase">Dosya</span>
                                        </div>
                                        <div class="text-[10px] text-gray-400 font-bold mt-2">Aktif katılım gösterdiği</div>
                                    </div>
                                </div>

                                <div class="bg-gray-50/50 p-4 rounded-3xl border border-gray-100 grid grid-cols-3 gap-2 ring-1 ring-black/5">
                                    <div class="text-center group-hover:scale-110 transition-transform">
                                        <p class="text-sm font-black text-emerald-600"><?php echo e($stat['leh_oy']); ?></p>
                                        <p class="text-[8px] text-gray-400 uppercase font-black tracking-tighter mt-1">Ceza Verilmesin</p>
                                    </div>
                                    <div class="text-center border-x border-gray-200 group-hover:scale-110 transition-transform">
                                        <p class="text-sm font-black text-rose-600"><?php echo e($stat['aleyh_oy']); ?></p>
                                        <p class="text-[8px] text-gray-400 uppercase font-black tracking-tighter mt-1">Ceza Verilsin</p>
                                    </div>
                                    <div class="text-center group-hover:scale-110 transition-transform">
                                        <p class="text-sm font-black text-gray-500"><?php echo e($stat['cekimser_oy']); ?></p>
                                        <p class="text-[8px] text-gray-400 uppercase font-black tracking-tighter mt-1">Çekimser</p>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <?php if($uyeYonetimiYetkisi): ?>
                                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                                        <form action="<?php echo e(route('admin.disiplin.kurul.uye.cikar', $stat['user'])); ?>" method="POST" id="remove-member-grid-<?php echo e($stat['user']->id); ?>">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="button" 
                                                    onclick="Swal.fire({
                                                        title: 'Emin misiniz?',
                                                        text: 'Bu üyeyi kuruldan çıkarmak istediğinize emin misiniz?',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#e11d48',
                                                        cancelButtonColor: '#64748b',
                                                        confirmButtonText: 'Evet, Çıkar',
                                                        cancelButtonText: 'Vazgeç',
                                                        customClass: {
                                                            popup: 'rounded-[2rem]',
                                                            confirmButton: 'rounded-xl px-6 py-2.5 text-sm font-bold',
                                                            cancelButton: 'rounded-xl px-6 py-2.5 text-sm font-bold'
                                                        }
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            document.getElementById('remove-member-grid-<?php echo e($stat['user']->id); ?>').submit();
                                                        }
                                                    })"
                                                    class="group/btn inline-flex items-center px-4 py-2 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded-xl transition-all text-[10px] font-black uppercase tracking-widest ring-1 ring-rose-200 hover:ring-rose-600">
                                                <svg class="w-3.5 h-3.5 mr-2 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Üyeliği Bitir
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <div x-show="viewMode === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white rounded-[2rem] border border-gray-100 shadow-2xl shadow-indigo-500/5 overflow-hidden ring-1 ring-black/[0.02]">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Üye</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Rol / Bölüm</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Katılım Oranı</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Performans (CV/CVM/Ç)</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">İşlem</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php $__currentLoopData = $uyeStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <img src="<?php echo e($stat['user']->profile_photo_url); ?>" class="w-10 h-10 rounded-xl object-cover ring-2 ring-indigo-50 group-hover:ring-indigo-200 transition-all">
                                                <div>
                                                    <p class="text-sm font-black text-gray-800"><?php echo e($stat['user']->name); ?></p>
                                                    <p class="text-[9px] text-gray-400 italic"><?php echo e($stat['katilim_tarihi'] ? $stat['katilim_tarihi']->translatedFormat('d M Y') : '-'); ?>'den beri</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider <?php echo e($stat['rol'] === 'Başkan' ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-500'); ?>">
                                                <?php echo e($stat['rol']); ?>

                                            </span>
                                            <p class="text-[10px] text-indigo-600 font-bold mt-1"><?php echo e($stat['user']->bolum->ad ?? 'Genel Yönetim'); ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <span class="text-sm font-black text-gray-800">%<?php echo e($stat['katilim_orani']); ?></span>
                                                <div class="w-20 bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                                    <div class="bg-indigo-500 h-full rounded-full" style="width: <?php echo e($stat['katilim_orani']); ?>%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[10px] font-black" title="Ceza Verilmesin"><?php echo e($stat['leh_oy']); ?></span>
                                                <span class="px-2 py-0.5 bg-rose-50 text-rose-600 rounded text-[10px] font-black" title="Ceza Verilsin"><?php echo e($stat['aleyh_oy']); ?></span>
                                                <span class="px-2 py-0.5 bg-gray-50 text-gray-500 rounded text-[10px] font-black" title="Çekimser"><?php echo e($stat['cekimser_oy']); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <?php if($uyeYonetimiYetkisi): ?>
                                                <form action="<?php echo e(route('admin.disiplin.kurul.uye.cikar', $stat['user'])); ?>" method="POST" class="inline" id="remove-member-list-<?php echo e($stat['user']->id); ?>">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button type="button" 
                                                            onclick="Swal.fire({
                                                                title: 'Üye Çıkarılsın mı?',
                                                                text: '<?php echo e($stat['user']->name); ?> isimli üyeyi kuruldan çıkarmak istediğinize emin misiniz?',
                                                                icon: 'warning',
                                                                showCancelButton: true,
                                                                confirmButtonColor: '#e11d48',
                                                                cancelButtonColor: '#64748b',
                                                                confirmButtonText: 'Evet, Üyeliği Bitir',
                                                                cancelButtonText: 'Vazgeç',
                                                                customClass: {
                                                                    popup: 'rounded-[2rem]'
                                                                }
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    document.getElementById('remove-member-list-<?php echo e($stat['user']->id); ?>').submit();
                                                                }
                                                            })"
                                                            class="text-rose-400 hover:text-rose-600 transition-colors p-2 hover:bg-rose-50 rounded-lg" title="Üyeliği Bitir">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TOPLANTI TAKVİMİ TABI -->
            <div x-show="activeTab === 'meetings'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
                <div class="flex items-center justify-between px-2">
                    <h4 class="text-gray-800 font-black text-xl flex items-center gap-3">
                        <span class="w-2 h-6 bg-gradient-to-b from-emerald-400 to-emerald-600 rounded-full"></span>
                        Toplantı & Gündem Arşivi
                    </h4>
                </div>

                <div class="bg-white/70 backdrop-blur-2xl rounded-[2.5rem] border border-white/50 shadow-2xl shadow-indigo-500/5 ring-1 ring-black/[0.02] overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1000px]">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[9px] w-12 text-center">No</th>
                                    <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">Başlık / Detay</th>
                                    <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">Tarih</th>
                                    <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">İlgili Dosyalar</th>
                                    <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">Durum</th>
                                    <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px] w-48 text-right">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php $__empty_1 = true; $__currentLoopData = $paginatedTakvim; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-indigo-50/20 transition-all group"
                                        x-show="(meetingSearch === '' || <?php echo e(json_encode(strtolower($t->baslik))); ?>.includes(meetingSearch.toLowerCase())) && (meetingStatusFilter === '' || '<?php echo e($t->durum); ?>' === meetingStatusFilter)">
                                        <td class="px-6 py-6 text-center">
                                            <span class="text-xs font-black text-gray-300">#<?php echo e($loop->iteration); ?></span>
                                        </td>
                                        <td class="px-5 py-6">
                                            <div class="font-black text-gray-800 text-base group-hover:text-indigo-600 transition-colors"><?php echo e($t->baslik); ?></div>
                                            <div class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-tighter"><?php echo e($t->tur); ?> | <?php echo e($t->yer ?: 'Lokasyon Belirtilmedi'); ?></div>
                                        </td>
                                        <td class="px-5 py-6 whitespace-nowrap">
                                            <div class="text-gray-800 font-black"><?php echo e($t->baslangic_tarihi->translatedFormat('d F Y')); ?></div>
                                            <div class="text-[11px] text-gray-400 font-bold mt-0.5"><?php echo e($t->baslangic_tarihi->format('H:i')); ?></div>
                                        </td>
                                        <td class="px-5 py-6">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <?php $__empty_2 = true; $__currentLoopData = $t->disiplinDosyalari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                    <span class="px-2 py-0.5 bg-gray-50 text-gray-500 rounded text-[10px] font-bold border border-gray-100">#<?php echo e($dosya->id); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                    <span class="text-[10px] text-gray-300 italic">Dosya yok</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-5 py-6">
                                            <?php
                                                $color = match($t->durum) {
                                                    'planlandı' => 'bg-blue-50 text-blue-600 ring-blue-100',
                                                    'devam_ediyor' => 'bg-amber-50 text-amber-600 ring-amber-200 animate-pulse',
                                                    'tamamlandı' => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
                                                    'iptal' => 'bg-rose-50 text-rose-600 ring-rose-100',
                                                    default => 'bg-gray-50 text-gray-400 ring-gray-100'
                                                };
                                            ?>
                                            <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-[0.1em] ring-1 <?php echo e($color); ?>">
                                                <?php echo e($t->durum); ?>

                                            </span>
                                        </td>
                                        <td class="px-5 py-6 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="<?php echo e(route('admin.disiplin.kurul.toplanti.show', $t->original_id)); ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-black text-white hover:bg-indigo-600 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="6" class="px-8 py-20 text-center text-gray-400 font-black text-sm uppercase tracking-widest italic">Kayıtlı toplantı bulunamadı.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- DOSYA ARŞİVİ TABI -->
            <div x-show="activeTab === 'archive'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
                <div class="flex items-center justify-between px-2">
                    <h4 class="text-gray-800 font-black text-xl flex items-center gap-3">
                        <span class="w-2 h-6 bg-gradient-to-b from-purple-400 to-purple-600 rounded-full"></span>
                        Disiplin Dosyası Arşivi
                    </h4>
                    <div class="px-4 py-1.5 bg-purple-50 text-purple-600 text-[10px] font-black uppercase tracking-widest rounded-full ring-1 ring-purple-100">
                        <?php echo e($arsivdekiDosyalar->total()); ?> Toplam Kayıt
                    </div>
                </div>

                <div class="bg-white/70 backdrop-blur-2xl rounded-[2.5rem] border border-white/50 shadow-2xl shadow-indigo-500/5 ring-1 ring-black/[0.02] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[1000px]">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-6 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px] w-12 text-center">No</th>
                                    <th class="px-6 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">Personel</th>
                                    <th class="px-6 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">Vukuat / Davranış</th>
                                    <th class="px-6 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">Kayıt Tarihi</th>
                                    <th class="px-6 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px]">Durum</th>
                                    <th class="px-6 py-5 font-black text-gray-400 uppercase tracking-[0.2em] text-[10px] text-right">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php $__empty_1 = true; $__currentLoopData = $arsivdekiDosyalar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-purple-50/20 transition-all group">
                                        <td class="px-6 py-6 text-center">
                                            <span class="text-xs font-black text-gray-300">#<?php echo e($case->id); ?></span>
                                        </td>
                                        <td class="px-6 py-6">
                                            <div class="flex items-center gap-4">
                                                <img src="<?php echo e($case->user->profile_photo_url); ?>" class="w-10 h-10 rounded-2xl object-cover ring-2 ring-gray-100 shadow-sm">
                                                <div>
                                                    <div class="font-black text-gray-800 text-sm group-hover:text-purple-600 transition-colors"><?php echo e($case->user->name); ?></div>
                                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter"><?php echo e($case->user->bolum->ad ?? 'Bölüm Belirtilmemiş'); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <div class="text-xs font-black text-gray-700 leading-relaxed"><?php echo e($case->behavior_text); ?></div>
                                            <?php if($case->subject): ?>
                                                <div class="text-[10px] text-gray-400 mt-1 font-medium italic line-clamp-1">"<?php echo e($case->subject); ?>"</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-6 text-xs font-bold text-gray-500">
                                            <?php echo e($case->created_at->translatedFormat('d M Y')); ?>

                                            <div class="text-[10px] text-gray-300 mt-0.5"><?php echo e($case->created_at->format('H:i')); ?></div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <?php
                                                $statusColor = match($case->durum) {
                                                    'Karar Verildi' => 'bg-emerald-50 text-emerald-600 ring-emerald-500/20',
                                                    'Kurulda' => 'bg-indigo-50 text-indigo-600 ring-indigo-500/20',
                                                    'Savunma Bekleniyor' => 'bg-amber-50 text-amber-600 ring-amber-500/20',
                                                    'İptal' => 'bg-rose-50 text-rose-600 ring-rose-500/20',
                                                    default => 'bg-gray-50 text-gray-600 ring-gray-500/20'
                                                };
                                            ?>
                                            <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest ring-1 <?php echo e($statusColor); ?>">
                                                <?php echo e($case->durum); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-6 text-right">
                                            <a href="<?php echo e(route('admin.disiplin.show', $case)); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-black text-white hover:bg-purple-600 rounded-xl transition-all shadow-lg shadow-black/10 hover:shadow-purple-500/30">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span class="text-[10px] font-black uppercase tracking-widest">Dosyayı Aç</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="px-8 py-20 text-center">
                                            <div class="flex flex-col items-center gap-4">
                                                <div class="p-6 bg-gray-50 rounded-full text-gray-200">
                                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <p class="text-gray-400 font-black text-sm uppercase tracking-widest italic">Kayıtlı dosya bulunamadı.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        <?php echo e($arsivdekiDosyalar->links()); ?>

                    </div>
                </div>
            </div>

            <!-- ÜYELİK ARŞİVİ TABI -->
            <div x-show="activeTab === 'history'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
                <div class="flex items-center justify-between px-2">
                    <h4 class="text-gray-800 font-black text-xl flex items-center gap-3">
                        <span class="w-2 h-6 bg-gradient-to-b from-amber-400 to-amber-600 rounded-full"></span>
                        Üyelik Arşivi & Atama Logları
                    </h4>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left border-collapse table-auto">
                        <thead>
                            <tr class="bg-gray-50 font-black text-gray-400 uppercase tracking-[0.1em] text-[10px]">
                                <th class="px-8 py-5">Personel / Atama</th>
                                <th class="px-8 py-5">Rol</th>
                                <th class="px-8 py-5">Görev Süresi</th>
                                <th class="px-8 py-5">Referans / Kayıt</th>
                                <th class="px-8 py-5">Aktif Durum</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php $__empty_1 = true; $__currentLoopData = $uyelikGecmisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gecmis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-amber-50/10 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <?php
                                                // User soft delete veya hard delete kontrolü
                                                $isDeleted = !$gecmis->user || $gecmis->user->trashed();
                                                $displayName = $gecmis->user ? $gecmis->user->name : 'Silinmiş Kullanıcı';
                                            ?>
                                            
                                            <?php if($gecmis->user && !$gecmis->user->trashed()): ?>
                                                <img src="<?php echo e($gecmis->user->profile_photo_url); ?>" class="w-10 h-10 rounded-2xl object-cover ring-2 ring-gray-100">
                                                <div>
                                                    <div class="font-black text-gray-800"><?php echo e($displayName); ?></div>
                                                    <div class="text-[10px] text-gray-400 font-bold uppercase"><?php echo e($gecmis->user->bolum->ad ?? '-'); ?></div>
                                                </div>
                                            <?php else: ?>
                                                <div class="w-10 h-10 rounded-2xl bg-gray-50 flex items-center justify-center border border-dashed border-gray-200 opacity-60">
                                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                </div>
                                                <div class="opacity-60">
                                                    <div class="font-black text-gray-400 italic strike-through"><?php echo e($displayName); ?></div>
                                                    <div class="text-[10px] text-gray-300 font-bold uppercase"><?php echo e($gecmis->user ? ($gecmis->user->bolum->ad ?? 'Bölüm Yok') : 'SİSTEMDEN SİLİNDİ'); ?></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-tighter <?php echo e($gecmis->rol === 'baskan' ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-500'); ?>">
                                            <?php echo e($gecmis->rol === 'baskan' ? 'Şura Başkanı' : 'Kurul Üyesi'); ?>

                                        </span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-xs font-mono font-black space-y-1">
                                            <div class="text-emerald-500 flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full <?php echo e(!$isDeleted ? 'animate-pulse' : ''); ?>"></span> <?php echo e($gecmis->katilim_tarihi->format('d.m.Y')); ?></div>
                                            <?php if($gecmis->aktif && !$isDeleted): ?>
                                                <div class="text-indigo-400 text-[9px] uppercase tracking-widest">Hizmet Veriyor</div>
                                            <?php else: ?>
                                                <div class="text-rose-400 flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-rose-400 rounded-full"></span> <?php echo e($gecmis->ayrilma_tarihi ? $gecmis->ayrilma_tarihi->format('d.m.Y') : ($isDeleted ? 'İşten Ayrıldı' : 'Bilinmiyor')); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-[10px] space-y-1.5">
                                            <div class="flex items-center gap-1.5"><span class="w-1 h-1 bg-gray-300 rounded-full"></span> <span class="text-gray-400 font-bold uppercase">Tanımlayan:</span> <span class="text-gray-700 font-black"><?php echo e($gecmis->ekleyen->name ?? 'SYSTEM'); ?></span></div>
                                            <?php if(!$gecmis->aktif): ?>
                                                <div class="flex items-center gap-1.5"><span class="w-1 h-1 bg-gray-300 rounded-full"></span> <span class="text-gray-400 font-bold uppercase">Azleden:</span> <span class="text-gray-700 font-black"><?php echo e($gecmis->cikaran->name ?? '-'); ?></span></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <?php if($gecmis->aktif && !$isDeleted): ?>
                                            <div class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-[1.25rem] text-[10px] font-black uppercase tracking-widest ring-1 ring-emerald-100">
                                                Aktif Görevli
                                            </div>
                                        <?php else: ?>
                                            <div class="inline-flex items-center px-4 py-1.5 bg-gray-100 text-gray-400 rounded-[1.25rem] text-[10px] font-black uppercase tracking-widest ring-1 ring-gray-200">
                                                Görev Süresi Bitti
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="5" class="px-8 py-20 text-center text-gray-400 font-black uppercase italic tracking-widest">Arşiv kaydı bulunamadı.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <style>
                @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
                .blink { animation: blink 2s infinite; }
            </style>

        </div>
    </div>

    <!-- MODALLAR -->
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'add-member-modal','show' => false,'focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'add-member-modal','show' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'focusable' => true]); ?>
        <form action="<?php echo e(route('admin.disiplin.kurul.uye.ekle')); ?>" method="POST" class="bg-white rounded-[3rem] overflow-hidden shadow-[0_0_100px_rgba(0,0,0,0.1)]">
            <?php echo csrf_field(); ?>
            <div class="bg-gradient-to-tr from-indigo-700 to-violet-800 p-8 pb-12 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-10">
                    <svg class="w-32 h-32 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-3xl font-black text-white tracking-tight">Üye Ataması</h3>
                    <p class="text-indigo-100 text-sm font-medium mt-2">Disiplin kuruluna yeni yetkili veya başkan atayın.</p>
                </div>
            </div>

            <div class="p-8 -mt-8 bg-white rounded-t-[3rem] relative z-20 space-y-8">
                <div class="space-y-2.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Atanacak Personel</label>
                    <select name="user_id" required class="w-full bg-gray-50 border-none ring-2 ring-gray-100 focus:ring-4 focus:ring-indigo-500 text-gray-800 rounded-2xl py-3.5 transition-all font-bold">
                        <option value="">Lütfen listeden seçim yapın...</option>
                        <?php $__currentLoopData = \App\Models\User::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> (<?php echo e($user->bolum->ad ?? '-'); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kurul Yetkisi</label>
                        <select name="rol" required class="w-full bg-gray-50 border-none ring-2 ring-gray-100 focus:ring-4 focus:ring-indigo-500 text-gray-800 rounded-2xl py-3.5 transition-all font-bold appearance-none">
                            <option value="uye">Kurul Üyesi</option>
                            <option value="baskan">Kurul Başkanı</option>
                        </select>
                    </div>
                    <div class="space-y-2.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Başlangıç Tarihi</label>
                        <input type="date" name="katilim_tarihi" value="<?php echo e(now()->format('Y-m-d')); ?>" required class="w-full bg-gray-50 border-none ring-2 ring-gray-100 focus:ring-4 focus:ring-indigo-500 text-gray-800 rounded-2xl py-3.5 transition-all font-bold">
                    </div>
                </div>

                <div class="space-y-2.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Dahili Notlar / Dayanak</label>
                    <textarea name="notlar" rows="3" class="w-full bg-gray-50 border-none ring-2 ring-gray-100 focus:ring-4 focus:ring-indigo-500 text-gray-800 rounded-2xl py-4 transition-all font-medium" placeholder="Atama karar no veya gerekçe..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <button type="button" x-on:click="$dispatch('close')" class="px-8 py-4 bg-gray-50 hover:bg-gray-100 text-gray-500 font-bold rounded-2xl transition-all uppercase tracking-widest text-[10px]">Vazgeç</button>
                    <button type="submit" class="px-10 py-4 bg-indigo-600 hover:bg-black text-white font-black rounded-2xl transition-all shadow-xl shadow-indigo-500/20 uppercase tracking-widest text-[10px]">Atamayı Onayla</button>
                </div>
            </div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'plan-meeting-modal','show' => false,'focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plan-meeting-modal','show' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'focusable' => true]); ?>
        <form action="<?php echo e(route('admin.disiplin.kurul.toplanti.store')); ?>" method="POST" class="bg-white rounded-3xl overflow-hidden" x-data="{ modalTab: 'kurul' }">
            <?php echo csrf_field(); ?>

            
            <div class="bg-emerald-600 px-8 py-6 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-widest mb-1">Disiplin Kurulu</p>
                    <h3 class="text-xl font-bold text-white tracking-tight">Oturum Planla</h3>
                </div>
                <button type="button" x-on:click="$dispatch('close')" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            
            <div class="max-h-[80vh] overflow-y-auto divide-y divide-gray-100">

                
                <div class="px-8 py-5">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Toplantı Başlığı</label>
                    <input type="text" name="baslik" required placeholder="Başlığı buraya giriniz..." class="w-full bg-gray-50 border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 text-gray-800 rounded-xl px-4 py-3 text-sm font-medium transition-all outline-none"/>
                </div>

                
                <div class="px-8 py-5 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Oturum Türü</label>
                        <select name="tur" class="w-full bg-gray-50 border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 text-gray-800 rounded-xl px-3 py-3 text-sm font-medium transition-all outline-none appearance-none cursor-pointer">
                            <option value="olağan">Olağan</option>
                            <option value="olağanüstü">Olağanüstü</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Süre (dk)</label>
                        <input type="number" name="planlanan_sure_dk" value="60" required class="w-full bg-gray-50 border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 text-gray-800 rounded-xl px-3 py-3 text-sm font-medium transition-all outline-none"/>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tarih ve Saat</label>
                        <input type="datetime-local" name="baslangic_tarihi" required class="w-full bg-gray-50 border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 text-gray-800 rounded-xl px-3 py-3 text-sm font-medium transition-all outline-none"/>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Yer / Konum</label>
                        <input type="text" name="yer" placeholder="Örn: Salon A" class="w-full bg-gray-50 border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 text-gray-800 rounded-xl px-3 py-3 text-sm font-medium transition-all outline-none"/>
                    </div>
                </div>

                
                <div class="px-8 py-5">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">İlgili Disiplin Dosyaları</label>
                    <div class="max-h-40 overflow-y-auto bg-gray-50 border border-gray-200 rounded-xl p-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <?php $__currentLoopData = $secilebilirDosyalar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-start gap-3 p-3 bg-white rounded-lg border border-gray-100 cursor-pointer hover:border-emerald-300 transition-colors group">
                                <input type="checkbox" name="disiplin_dosyalari[]" value="<?php echo e($case->id); ?>" class="mt-1 w-4 h-4 rounded text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                <div class="flex-1">
                                    <div class="text-sm font-bold text-gray-800 group-hover:text-emerald-700 transition-colors"><?php echo e($case->user->name); ?></div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] text-gray-500 font-mono">#<?php echo e($case->id); ?></span>
                                        <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 text-[9px] font-black uppercase"><?php echo e($case->durum); ?></span>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                
                <div class="px-8 py-5">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Gündem / Notlar <span class="normal-case font-medium text-gray-300 tracking-normal">(opsiyonel)</span>
                    </label>
                    <textarea name="icerik" rows="2" placeholder="Gündem maddeleri ve toplantı detayları..." class="w-full bg-gray-50 border border-gray-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 text-gray-800 rounded-xl px-4 py-3 text-sm font-medium transition-all outline-none resize-none"></textarea>
                </div>

                
                <div class="px-8 py-5">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Katılımcı Yönetimi</label>

                        <div class="flex gap-1.5 bg-gray-100 p-1 rounded-xl">
                            <button type="button" @click="modalTab = 'kurul'" :class="modalTab === 'kurul' ? 'bg-white text-emerald-700 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-1.5 rounded-lg text-[11px] font-bold transition-all">Kurul Üyeleri</button>
                            <button type="button" @click="modalTab = 'sistem'" :class="modalTab === 'sistem' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-1.5 rounded-lg text-[11px] font-bold transition-all">Sistem Kullanıcıları</button>
                            <button type="button" @click="modalTab = 'dis'" :class="modalTab === 'dis' ? 'bg-white text-amber-700 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-1.5 rounded-lg text-[11px] font-bold transition-all">Dış Katılımcı</button>
                        </div>
                    </div>

                    
                    <div x-show="modalTab === 'kurul'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto pr-1">
                            <?php $__currentLoopData = $tumUyeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-gray-50 hover:bg-emerald-50 border border-transparent hover:border-emerald-200 cursor-pointer transition-all group">
                                    <input type="checkbox" name="katilimcilar[]" value="<?php echo e($uye->id); ?>" checked class="w-4 h-4 rounded text-emerald-600 border-gray-300 focus:ring-emerald-500 flex-shrink-0"/>
                                    <img src="<?php echo e($uye->profile_photo_url); ?>" alt="<?php echo e($uye->name); ?>" class="w-8 h-8 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-emerald-700"><?php echo e($uye->name); ?></p>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wide">
                                            <?php if($baskanlar->contains($uye)): ?> Başkan <?php else: ?> Kurul Üyesi <?php endif; ?>
                                        </p>
                                    </div>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div x-show="modalTab === 'sistem'" x-data="{ search: '' }" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="mb-3">
                            <input type="text" x-model="search" placeholder="İsme veya bölüme göre ara..." class="w-full bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 text-gray-800 rounded-xl px-4 py-2.5 text-sm transition-all outline-none"/>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1">
                            <?php $__currentLoopData = $tumPersonel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label x-show="'<?php echo e(strtolower($sUser->name)); ?> <?php echo e(strtolower($sUser->bolum->ad ?? '')); ?>'.includes(search.toLowerCase())" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-gray-50 hover:bg-indigo-50 border border-transparent hover:border-indigo-200 cursor-pointer transition-all group">
                                    <input type="checkbox" name="sistem_katilimcilari[]" value="<?php echo e($sUser->id); ?>" class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500 flex-shrink-0"/>
                                    <img src="<?php echo e($sUser->profile_photo_url); ?>" alt="<?php echo e($sUser->name); ?>" class="w-8 h-8 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-indigo-700"><?php echo e($sUser->name); ?></p>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wide"><?php echo e($sUser->bolum->ad ?? 'Bölüm yok'); ?></p>
                                    </div>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div x-show="modalTab === 'dis'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">
                            <p class="text-xs text-amber-700 font-medium mb-4">Sisteme kayıtlı olmayan bir kişiyi e-posta ile davet edebilirsiniz.</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-2">Ad Soyad</label>
                                    <input type="text" name="dis_katilimci_adi" placeholder="Dış katılımcı adı" class="w-full bg-white border border-amber-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 text-gray-800 rounded-xl px-4 py-3 text-sm font-medium transition-all outline-none"/>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-2">E-posta Adresi</label>
                                    <input type="email" name="dis_katilimci_email" placeholder="ornek@sirket.com" class="w-full bg-white border border-amber-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 text-gray-800 rounded-xl px-4 py-3 text-sm font-medium transition-all outline-none"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-6 py-2.5 bg-white border border-gray-200 hover:bg-gray-100 text-gray-600 text-xs font-bold uppercase tracking-widest rounded-xl transition-all">Vazgeç</button>
                <button type="submit" class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-md shadow-emerald-500/20">Toplantıyı Planla</button>
            </div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
        .animate-float { animation: float 3s ease-in-out infinite; }
    </style>
    
    <?php $__currentLoopData = collect($yaklasanToplantılar)->merge($toplantıGecmisi); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'edit-meeting-modal-'.e($t->id).'','show' => false,'focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit-meeting-modal-'.e($t->id).'','show' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'focusable' => true]); ?>
            <form action="<?php echo e(route('admin.disiplin.kurul.toplanti.update', $t)); ?>" method="POST" class="bg-white rounded-3xl overflow-hidden" x-data="{ modalTab: 'kurul' }">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                
                <div class="px-8 py-6 bg-gradient-to-r from-indigo-600 to-violet-600 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-xl">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <h3 class="text-white font-black uppercase tracking-widest text-sm">Toplantıyı Düzenle</h3>
                    </div>
                    <button type="button" x-on:click="$dispatch('close')" class="text-white/60 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                
                <div class="divide-y divide-gray-100">
                    <div class="px-8 py-5">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Toplantı Başlığı</label>
                        <input type="text" name="baslik" value="<?php echo e($t->baslik); ?>" required class="w-full bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 text-gray-800 rounded-xl px-4 py-3 text-sm font-medium transition-all outline-none"/>
                    </div>

                    <div class="px-8 py-5 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tür</label>
                            <select name="tur" class="w-full bg-gray-50 border border-gray-200 focus:border-indigo-400 rounded-xl px-3 py-3 text-sm font-medium transition-all appearance-none cursor-pointer outline-none">
                                <option value="olağan" <?php echo e($t->tur == 'olağan' ? 'selected' : ''); ?>>Olağan</option>
                                <option value="olağanüstü" <?php echo e($t->tur == 'olağanüstü' ? 'selected' : ''); ?>>Olağanüstü</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Süre (dk)</label>
                            <input type="number" name="planlanan_sure_dk" value="<?php echo e($t->planlanan_sure_dk); ?>" required class="w-full bg-gray-50 border border-gray-200 focus:border-indigo-400 rounded-xl px-3 py-3 text-sm font-medium outline-none"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tarih</label>
                            <input type="datetime-local" name="baslangic_tarihi" value="<?php echo e($t->baslangic_tarihi->format('Y-m-d\TH:i')); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-3 text-sm font-medium outline-none"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Yer</label>
                            <input type="text" name="yer" value="<?php echo e($t->yer); ?>" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-3 text-sm font-medium outline-none"/>
                        </div>
                    </div>

                    <div class="px-8 py-5">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">İlgili Disiplin Dosyaları</label>
                        <div class="max-h-40 overflow-y-auto bg-gray-50 border border-gray-200 rounded-xl p-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <?php $__currentLoopData = $secilebilirDosyalar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-start gap-3 p-3 bg-white rounded-lg border border-gray-100 cursor-pointer hover:border-indigo-300 transition-colors group">
                                    <input type="checkbox" name="disiplin_dosyalari[]" value="<?php echo e($case->id); ?>" <?php echo e($t->disiplinDosyalari->contains($case->id) ? 'checked' : ''); ?> class="mt-1 w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <div class="flex-1">
                                        <div class="text-sm font-bold text-gray-800 group-hover:text-indigo-700 transition-colors"><?php echo e($case->user->name); ?></div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] text-gray-500 font-mono">#<?php echo e($case->id); ?></span>
                                            <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 text-[9px] font-black uppercase"><?php echo e($case->durum); ?></span>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="px-8 py-5">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">İçerik / Gündem</label>
                        <textarea name="icerik" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium transition-all outline-none resize-none"><?php echo e($t->icerik); ?></textarea>
                    </div>

                    
                    <div class="px-8 py-5">
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Katılımcı Yönetimi</label>

                            <div class="flex gap-1.5 bg-gray-100 p-1 rounded-xl">
                                <button type="button" @click="modalTab = 'kurul'" :class="modalTab === 'kurul' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-1.5 rounded-lg text-[11px] font-bold transition-all">Kurul Üyeleri</button>
                                <button type="button" @click="modalTab = 'sistem'" :class="modalTab === 'sistem' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-1.5 rounded-lg text-[11px] font-bold transition-all">Sistem Kullanıcıları</button>
                                <button type="button" @click="modalTab = 'dis'" :class="modalTab === 'dis' ? 'bg-white text-amber-700 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-1.5 rounded-lg text-[11px] font-bold transition-all">Dış Katılımcı</button>
                            </div>
                        </div>

                        
                        <div x-show="modalTab === 'kurul'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto pr-1">
                                <?php $__currentLoopData = $tumUyeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $isKatilimci = $t->katilimcilar->contains('user_id', $uye->id); ?>
                                    <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-gray-50 hover:bg-indigo-50 border border-transparent <?php echo e($isKatilimci ? 'border-indigo-200 bg-indigo-50/30' : ''); ?> hover:border-indigo-200 cursor-pointer transition-all group">
                                        <input type="checkbox" name="katilimcilar[]" value="<?php echo e($uye->id); ?>" <?php echo e($isKatilimci ? 'checked' : ''); ?> class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500 flex-shrink-0"/>
                                        <img src="<?php echo e($uye->profile_photo_url); ?>" alt="<?php echo e($uye->name); ?>" class="w-8 h-8 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-indigo-700"><?php echo e($uye->name); ?></p>
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wide">
                                                <?php if($baskanlar->contains($uye)): ?> Başkan <?php else: ?> Kurul Üyesi <?php endif; ?>
                                            </p>
                                        </div>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        
                        <div x-show="modalTab === 'sistem'" x-data="{ search: '' }" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="mb-3">
                                <input type="text" x-model="search" placeholder="İsme veya bölüme göre ara..." class="w-full bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 text-gray-800 rounded-xl px-4 py-2.5 text-sm transition-all outline-none"/>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1">
                                <?php $__currentLoopData = $tumPersonel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $isKatilimci = $t->katilimcilar->contains('user_id', $sUser->id); ?>
                                    <label x-show="'<?php echo e(strtolower($sUser->name)); ?> <?php echo e(strtolower($sUser->bolum->ad ?? '')); ?>'.includes(search.toLowerCase())" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-gray-50 hover:bg-indigo-50 border border-transparent <?php echo e($isKatilimci ? 'border-indigo-200 bg-indigo-50/30' : ''); ?> hover:border-indigo-200 cursor-pointer transition-all group">
                                        <input type="checkbox" name="sistem_katilimcilari[]" value="<?php echo e($sUser->id); ?>" <?php echo e($isKatilimci ? 'checked' : ''); ?> class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500 flex-shrink-0"/>
                                        <img src="<?php echo e($sUser->profile_photo_url); ?>" alt="<?php echo e($sUser->name); ?>" class="w-8 h-8 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-indigo-700"><?php echo e($sUser->name); ?></p>
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wide"><?php echo e($sUser->bolum->ad ?? 'Bölüm yok'); ?></p>
                                        </div>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        
                        <?php $disKat = $t->katilimcilar->where('rol', 'davetli')->first(); ?>
                        <div x-show="modalTab === 'dis'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">
                                <p class="text-xs text-amber-700 font-medium mb-4">Sisteme kayıtlı olmayan bir kişiyi e-posta ile davet edebilirsiniz.</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-2">Ad Soyad</label>
                                        <input type="text" name="dis_katilimci_adi" value="<?php echo e($disKat ? $disKat->dis_katilimci_adi : ''); ?>" placeholder="Dış katılımcı adı" class="w-full bg-white border border-amber-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 text-gray-800 rounded-xl px-4 py-3 text-sm font-medium transition-all outline-none"/>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-2">E-posta Adresi</label>
                                        <input type="email" name="dis_katilimci_email" value="<?php echo e($disKat ? $disKat->dis_katilimci_email : ''); ?>" placeholder="ornek@sirket.com" class="w-full bg-white border border-amber-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 text-gray-800 rounded-xl px-4 py-3 text-sm font-medium transition-all outline-none"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 text-xs font-bold uppercase tracking-widest rounded-xl transition-all">Vazgeç</button>
                    <button type="submit" class="px-8 py-2.5 bg-indigo-600 hover:bg-black text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-md">Değişiklikleri Kaydet</button>
                </div>
            </form>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php $__env->startPush('scripts'); ?>
        <script>
            // Bekleyen Gündem Başlık Uyarısı
            (function() {
                const count = <?php echo e($bekleyenGundemSayisi); ?>;
                if (count > 0) {
                    const originalTitle = document.title;
                    const alertTitle = `(${count}) Bekleyen Gündem!`;
                    let isOriginal = true;

                    setInterval(() => {
                        document.title = isOriginal ? alertTitle : originalTitle;
                        isOriginal = !isOriginal;
                    }, 2000); // 2 saniyede bir değişir
                }
            })();
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
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/disiplin/kurul.blade.php ENDPATH**/ ?>