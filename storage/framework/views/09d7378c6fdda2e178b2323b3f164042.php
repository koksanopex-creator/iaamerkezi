<?php $__env->startPush('pageTitle'); ?>
    Sistem Sağlık Paneli | 
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
            <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
                <?php echo e(__('Sistem Sağlık Paneli')); ?>

            </h2>
            <div class="flex items-center gap-4">
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-full border border-indigo-100 flex items-center gap-2">
                    <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
                    Canlı İzleme Aktif
                </span>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8" x-data="{ activeTab: 'access' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            
            <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-2xl w-fit mb-8 border border-slate-200">
                <button @click="activeTab = 'access'" 
                        :class="activeTab === 'access' ? 'bg-white text-indigo-600 shadow-sm border-slate-200' : 'text-slate-500 hover:text-slate-700'"
                        class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all border border-transparent">
                    Erişim Kontrolü
                </button>
                <button @click="activeTab = 'calibration'" 
                        :class="activeTab === 'calibration' ? 'bg-white text-indigo-600 shadow-sm border-slate-200' : 'text-slate-500 hover:text-slate-700'"
                        class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all border border-transparent flex items-center gap-2">
                    Veri Kalibrasyonu
                </button>
                <button @click="activeTab = 'score_calibration'" 
                        :class="activeTab === 'score_calibration' ? 'bg-white text-indigo-600 shadow-sm border-slate-200' : 'text-slate-500 hover:text-slate-700'"
                        class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all border border-transparent flex items-center gap-2">
                    Puan Kalibrasyonu
                </button>
                <button @click="activeTab = 'blade_routes'" 
                        :class="activeTab === 'blade_routes' ? 'bg-white text-indigo-600 shadow-sm border-slate-200' : 'text-slate-500 hover:text-slate-700'"
                        class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all border border-transparent flex items-center gap-2">
                    Blade Route Kontrolü
                </button>
            </div>

            
            <div x-show="activeTab === 'access'" x-transition x-data="healthChecker()" x-init="init()">
                <!-- Guide & Legend -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-8 overflow-hidden relative">
                    <div class="absolute right-0 top-0 -mr-16 -mt-16 w-48 h-48 bg-slate-50 rounded-full blur-3xl"></div>
                    
                    <div class="relative grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Color Guide -->
                        <div>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.172-1.172a4 4 0 015.656 0l1.172 1.172a4 4 0 010 5.656l-1.172 1.172a4 4 0 01-5.656 0l-1.172-1.172a4 4 0 010-5.656z"></path></svg>
                                Durum Kılavuzu
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex items-center gap-3 p-3 rounded-2xl bg-emerald-50 border border-emerald-100">
                                    <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                                    <span class="text-xs font-bold text-emerald-800">BAŞARILI (200 OK)</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 rounded-2xl bg-rose-50 border border-rose-100">
                                    <span class="w-3 h-3 bg-rose-500 rounded-full animate-pulse"></span>
                                    <span class="text-xs font-bold text-rose-800">HATA / ÇÖKME (500)</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 rounded-2xl bg-amber-50 border border-amber-100">
                                    <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
                                    <span class="text-xs font-bold text-amber-800">YETKİ YOK (403)</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100 text-slate-400">
                                    <span class="w-3 h-3 bg-slate-300 rounded-full"></span>
                                    <span class="text-xs font-bold italic text-slate-400">KULLANICI TANIMSIZ</span>
                                </div>
                            </div>
                        </div>

                        <!-- Role Selector -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    Rol Seçimi
                                </h4>
                                <div class="flex gap-2">
                                    <button @click="selectAllRoles()" class="text-[10px] font-bold text-indigo-600 hover:underline">Tümünü Seç</button>
                                    <span class="text-slate-300">|</span>
                                    <button @click="selectedRoles = []" class="text-[10px] font-bold text-rose-600 hover:underline">Temizle</button>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 max-h-[120px] overflow-y-auto p-1 custom-scrollbar">
                                <template x-for="(short, role) in roles_metadata" :key="role">
                                    <label :class="selectedRoles.includes(role) ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-slate-50 border-slate-100 text-slate-500 opacity-60'"
                                        class="flex items-center gap-2 px-3 py-1.5 rounded-full border text-[10px] font-bold cursor-pointer transition-all hover:scale-105 active:scale-95">
                                        <input type="checkbox" :value="role" x-model="selectedRoles" class="hidden">
                                        <span class="w-4 h-4 flex items-center justify-center rounded-full bg-white/20" x-text="short"></span>
                                        <span x-text="role"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group">
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Toplam Kontrol</p>
                        <h3 class="text-4xl font-black text-slate-800" x-text="summary.total">0</h3>
                    </div>
                    
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group">
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Sağlıklı Sayfalar</p>
                        <h3 class="text-4xl font-black text-emerald-600" x-text="summary.success">0</h3>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group">
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Hata/Çökme</p>
                        <h3 class="text-4xl font-black text-rose-600" x-text="summary.fail">0</h3>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group">
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Modül Uyarısı</p>
                        <h3 class="text-4xl font-black text-amber-600" x-text="summary.modelIssuesCount">0</h3>
                    </div>
                </div>

                <!-- Action Card & Progress Bar -->
                <div class="bg-indigo-600 rounded-3xl p-8 shadow-xl shadow-indigo-200 mb-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                    
                    <div class="relative">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-6">
                            <div class="text-white">
                                <h4 class="text-2xl font-bold mb-2">Sistemi Tara</h4>
                                <p class="text-indigo-100 max-w-xl">Seçilen <span class="font-bold underline" x-text="selectedRoles.length"></span> rol için <span class="font-bold underline" x-text="routes.length"></span> sayfayı analiz et.</p>
                            </div>
                            <button @click="startScan()" :disabled="scanning || selectedRoles.length === 0"
                                class="px-8 py-4 bg-white text-indigo-700 font-black rounded-2xl shadow-lg hover:bg-slate-50 transition-all flex items-center gap-3 disabled:opacity-50 group">
                                <template x-if="!scanning">
                                    <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                </template>
                                <template x-if="scanning">
                                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </template>
                                <span x-text="scanning ? 'Analiz Yapılıyor... ' + progress + '%' : 'Analizi Başlat'">Analizi Başlat</span>
                            </button>
                        </div>

                        <!-- Progress Bar UI -->
                        <div x-show="scanning || progress > 0" class="w-full bg-indigo-900/30 rounded-full h-4 relative overflow-hidden ring-4 ring-indigo-500/10">
                            <div class="h-full bg-white shadow-[0_0_20px_rgba(255,255,255,0.5)] transition-all duration-500 ease-out flex items-center justify-end px-2"
                                 :style="`width: ${progress}%`"
                                 :class="progress === 100 ? 'bg-emerald-400' : ''">
                                <span class="text-[8px] font-black text-indigo-900" x-show="progress > 5" x-text="progress + '%'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-show="results.length > 0 || modelIssues.length > 0" x-transition>
                    <!-- Left: Route Scan Results -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                                <h5 class="font-black text-slate-800 uppercase tracking-widest text-sm">Sayfa & Modül Sonuçları</h5>
                                <span class="text-xs text-slate-400 font-bold" x-show="lastScanTime">Son Tarama: <span x-text="lastScanTime"></span></span>
                            </div>
                            <div class="overflow-x-auto pb-24">
                                <table class="w-full text-left border-collapse">
                                    <tbody class="divide-y divide-slate-50">
                                        <template x-for="(res, index) in results" :key="index">
                                            <tr class="hover:bg-slate-50/30 transition-colors group">
                                                <td class="px-6 py-4">
                                                    <div class="flex flex-col">
                                                        <span class="text-[10px] font-black uppercase text-indigo-400 mb-0.5" x-text="res.module"></span>
                                                        <span class="text-sm font-bold text-slate-700" x-text="res.name"></span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <template x-for="(status, role) in res.role_results" :key="role">
                                                            <div class="group/role relative">
                                                                <button type="button" @click="showErrorDetails(res.name, role, status)"
                                                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-[10px] font-black border transition-all cursor-pointer shadow-sm hover:scale-105"
                                                                    :class="{
                                                                        'bg-emerald-50 text-emerald-600 border-emerald-100 hover:ring-2 ring-emerald-500/50': status.status === 'success',
                                                                        'bg-rose-50 text-rose-600 border-rose-100 hover:ring-2 ring-rose-500': status.status === 'danger',
                                                                        'bg-amber-50 text-amber-600 border-amber-100 hover:ring-2 ring-amber-500': status.status === 'warning',
                                                                        'bg-slate-50 text-slate-400 border-slate-100 hover:ring-2 ring-slate-400': status.status === 'info',
                                                                        'bg-indigo-50 text-indigo-400 border-indigo-100': status.status === 'loading'
                                                                    }"
                                                                    :title="role + ' - Tıklayarak detayı gör'">
                                                                    <span x-text="status.short"></span>
                                                                </button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <a :href="resolveUrl(res.url)" target="_blank" class="p-2 bg-slate-50 text-slate-400 rounded-lg hover:text-indigo-600 hover:bg-indigo-50 transition-all inline-block">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Model Integrity Section -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-6 border-b border-slate-50 bg-slate-50/50">
                                <h5 class="font-black text-slate-800 uppercase tracking-widest text-sm flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                    Model Bütünlüğü
                                </h5>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <template x-for="(issue, index) in modelIssues" :key="index">
                                        <div class="p-4 rounded-2xl border flex flex-col gap-2"
                                            :class="{'bg-rose-50 border-rose-100': issue.type === 'danger', 'bg-amber-50 border-amber-100': issue.type === 'warning'}">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-black uppercase" :class="{'text-rose-700': issue.type === 'danger', 'text-amber-700': issue.type === 'warning'}" x-text="issue.model"></span>
                                            </div>
                                            <p class="text-xs font-medium text-slate-700" x-text="issue.message"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="results.length === 0 && !scanning" class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-100 mt-8">
                    <h6 class="text-lg font-bold text-slate-600 mb-2">Analiz için hazır</h6>
                    <p class="text-sm text-slate-400">Rolleri seçin ve "Analizi Başlat" butonuna tıklayın.</p>
                </div>

            </div> 

            
            <div x-show="activeTab === 'calibration'" x-transition>
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.data-calibration', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-786719312-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>

            
            <div x-show="activeTab === 'score_calibration'" x-transition>
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.score-calibration', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-786719312-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>

            
            <div x-show="activeTab === 'blade_routes'" x-transition x-data="bladeRouteChecker()">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-black text-slate-800 flex items-center gap-2">
                                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                Blade Route Kontrolü
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">Tüm blade dosyalarındaki tanımlı olmayan rota bağlantılarını (route('...')) tespit eder.</p>
                        </div>
                        <button @click="checkBladeRoutes()" :disabled="checkingRoutes"
                            class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition-all flex items-center gap-2 disabled:opacity-50">
                            <span x-show="!checkingRoutes">Taramayı Başlat</span>
                            <span x-show="checkingRoutes" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Taranıyor...
                            </span>
                        </button>
                    </div>

                    <div x-show="routeResults !== null" class="mt-6 border-t border-slate-100 pt-6">
                        <template x-if="routeResults && routeResults.length === 0">
                            <div class="flex flex-col items-center justify-center py-12 bg-emerald-50 rounded-2xl border border-emerald-100">
                                <div class="w-16 h-16 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <h4 class="text-lg font-bold text-emerald-800">Harika! Hatalı Rota Bulunamadı</h4>
                                <p class="text-sm text-emerald-600">Tüm blade dosyalarındaki rotalar sorunsuz çalışıyor.</p>
                            </div>
                        </template>

                        <template x-if="routeResults && routeResults.length > 0">
                            <div>
                                <div class="flex items-center gap-3 mb-4 p-4 bg-rose-50 border border-rose-100 rounded-xl text-rose-700">
                                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <div>
                                        <h4 class="font-bold">Hatalı Rotalar Bulundu!</h4>
                                        <p class="text-sm mt-0.5"><span x-text="routeResults.length"></span> adet tanımlı olmayan rota tespit edildi.</p>
                                    </div>
                                </div>
                                <div class="overflow-x-auto rounded-xl border border-slate-200">
                                    <table class="w-full text-left text-sm border-collapse">
                                        <thead>
                                            <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                                                <th class="px-4 py-3 font-bold uppercase text-[11px] tracking-wider">Dosya Yolu (Blade)</th>
                                                <th class="px-4 py-3 font-bold uppercase text-[11px] tracking-wider text-rose-600">Bulunamayan Rota Adı</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            <template x-for="(err, index) in routeResults" :key="index">
                                                <tr class="hover:bg-slate-50 transition-colors">
                                                    <td class="px-4 py-3 font-mono text-xs text-slate-600" x-text="err.file"></td>
                                                    <td class="px-4 py-3 font-mono font-bold text-rose-600 bg-rose-50/30" x-text="err.route"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function healthChecker() {
            return {
                scanning: false,
                progress: 0,
                results: [],
                routes: [],
                roles_metadata: {},
                selectedRoles: [],
                modelIssues: [],
                summary: { total: 0, success: 0, fail: 0, modelIssuesCount: 0 },
                lastScanTime: null,
                baseUrl: '<?php echo e(url("/")); ?>'.replace(/\/$/, ""),

                resolveUrl(relativePath) {
                    if (!relativePath || relativePath === '#') return '#';
                    
                    // Eğer backend bize doğrudan http:// veya https:// ile başlıyorsa, bunu düzeltmek gerekebilir.
                    // Çünkü HealthCheckController "route(..., false)" kullanmasına rağmen bazen tam URL dönebilir.
                    let cleanPath = relativePath;
                    
                    if (relativePath.startsWith('http')) {
                        try {
                            const urlObj = new URL(relativePath);
                            cleanPath = urlObj.pathname + urlObj.search;
                        } catch (e) {
                            return relativePath;
                        }
                    }
                    
                    if (cleanPath.startsWith('/')) {
                        cleanPath = cleanPath.substring(1);
                    }
                    
                    const basePath = '<?php echo e(rtrim(asset(""), "/")); ?>';
                    return basePath + '/' + cleanPath;
                },

                showErrorDetails(pageName, roleName, status) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: `<div class="text-left"><span class="text-slate-800 font-black">${pageName}</span> <br> <span class="text-sm font-bold text-slate-500">${roleName}</span></div>`,
                            html: `
                                <div class="text-left mt-2">
                                    <div class="mb-3 flex items-center gap-2">
                                        <span class="font-bold text-slate-500">Durum Kodu:</span> 
                                        <span class="bg-slate-100 border px-2 py-0.5 rounded-lg text-slate-800 font-mono text-sm font-black">${status.code}</span>
                                    </div>
                                    <div class="font-bold text-slate-500 mb-1">Hata Mesajı / Detay:</div>
                                    <textarea readonly class="w-full h-48 p-4 bg-slate-900 text-emerald-400 font-mono text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-inner" onclick="this.select()">${status.message}</textarea>
                                    <p class="text-[11px] text-slate-400 mt-2 text-right italic">Kopyalamak için metne tıklayın (Ctrl+C)</p>
                                </div>
                            `,
                            icon: status.status === 'success' ? 'success' : (status.status === 'danger' ? 'error' : 'warning'),
                            width: '650px',
                            confirmButtonText: 'Kapat',
                            confirmButtonColor: '#4f46e5',
                            customClass: {
                                popup: 'rounded-3xl border border-slate-100 shadow-2xl',
                                title: 'border-b border-slate-100 pb-4',
                                confirmButton: 'rounded-xl font-bold px-8 py-3'
                            }
                        });
                    } else {
                        alert(roleName + "\\nDurum: " + status.code + "\\nMesaj: " + status.message);
                    }
                },

                async init() {
                    const res = await fetch('<?php echo e(route('admin.health.init')); ?>');
                    const data = await res.json();
                    this.routes = data.routes;
                    this.roles_metadata = data.roles;
                    this.selectAllRoles();
                },

                selectAllRoles() {
                    this.selectedRoles = Object.keys(this.roles_metadata);
                },

                async startScan() {
                    this.scanning = true;
                    this.progress = 0;
                    this.results = [];
                    this.summary = { total: 0, success: 0, fail: 0, modelIssuesCount: 0 };

                    const total = this.routes.length;

                    for (let i = 0; i < total; i++) {
                        const route = this.routes[i];
                        try {
                            const response = await fetch('<?php echo e(route('admin.health.scan')); ?>', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    route: route.route,
                                    name: route.name,
                                    module: route.module,
                                    roles: this.selectedRoles,
                                    param_model: route.param_model || null
                                })
                            });
                            const data = await response.json();
                            
                            if (data && data.result) {
                                this.results.push(data.result);
                                this.summary.success += (Object.keys(data.result.role_results).length - (data.fail_count || 0));
                            } else {
                                console.warn('Uyarı: Bu rota test edilemedi veya sonuç dönmedi.', route.name);
                            }
                            
                            this.summary.total++;
                            this.summary.fail += (data && data.fail_count ? data.fail_count : 0);
                            
                            this.progress = Math.round(((i + 1) / total) * 100);
                        } catch (e) {
                            console.error(e);
                        }
                    }

                    this.fetchModelIssues();
                    
                    this.scanning = false;
                    this.lastScanTime = new Date().toLocaleTimeString();
                },

                async fetchModelIssues() {
                    const res = await fetch('<?php echo e(route('admin.health.scan')); ?>', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    this.modelIssues = data.modelIssues;
                    this.summary.modelIssuesCount = data.modelIssues.length;
                }
            }
        }

        function bladeRouteChecker() {
            return {
                checkingRoutes: false,
                routeResults: null,

                async checkBladeRoutes() {
                    this.checkingRoutes = true;
                    this.routeResults = null;
                    
                    try {
                        const res = await fetch('<?php echo e(route('admin.health.check_blade')); ?>', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        this.routeResults = data.errors || [];
                    } catch (e) {
                        console.error('Blade scan error:', e);
                        this.routeResults = [];
                    }
                    
                    this.checkingRoutes = false;
                }
            }
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
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
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/health/index.blade.php ENDPATH**/ ?>