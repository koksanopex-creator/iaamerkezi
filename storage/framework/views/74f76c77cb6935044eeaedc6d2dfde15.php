<div x-data="{
        gSlide: 0,
        total: 5,
        init() {
            setInterval(() => {
                this.gSlide = (this.gSlide + 1) % this.total;
            }, 9000);
        }
    }"
    class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mt-6 relative overflow-hidden">
    
    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">📊 Yönetim Analitik Dashboard</h3>

    <div class="relative w-full overflow-hidden">
        <div class="flex transition-all duration-700" :style="'transform: translateX(-' + (gSlide * 100) + '%)'">
            
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
            
            <div class="min-w-full px-2">
                <div class="bg-white/50 rounded-xl p-2 h-[420px] flex flex-col">
                    <h4 class="text-sm font-bold text-gray-700 mb-2 text-center">Bölüm Bazlı Müşteri Geri Bildirim Dağılımı</h4>
                    
                    
                    <div class="grid grid-cols-3 gap-2 h-full">
                        
                        
                        <div class="flex flex-col items-center justify-center border-r border-gray-200 pr-2">
                            <h5 class="text-xs font-bold text-green-600 mb-1 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span> Onaylananlar
                            </h5>
                            <div id="feedbackApprovedChart" class="w-full flex-1"></div>
                        </div>

                        
                        <div class="flex flex-col items-center justify-center border-r border-gray-200 px-2">
                            <h5 class="text-xs font-bold text-red-600 mb-1 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Reddedilenler
                            </h5>
                            <div id="feedbackRejectedChart" class="w-full flex-1"></div>
                        </div>

                        
                        <div class="flex flex-col items-center justify-center pl-2">
                            <h5 class="text-xs font-bold text-yellow-600 mb-1 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Revizyon
                            </h5>
                            <div id="feedbackRevisionChart" class="w-full flex-1"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        
        <button @click="gSlide = (gSlide - 1 + total) % total" class="absolute top-1/2 left-3 -translate-y-1/2 bg-white shadow rounded-full p-2 hover:bg-gray-100">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button @click="gSlide = (gSlide + 1) % total" class="absolute top-1/2 right-3 -translate-y-1/2 bg-white shadow rounded-full p-2 hover:bg-gray-100">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-2">
                <template x-for="i in total">
                    <div @click="gSlide = i - 1" class="w-3 h-3 rounded-full cursor-pointer transition" :class="gSlide === (i - 1) ? 'bg-indigo-600' : 'bg-gray-300'"></div>
                </template>
            </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/raporlar/partials/executive/charts-slider.blade.php ENDPATH**/ ?>