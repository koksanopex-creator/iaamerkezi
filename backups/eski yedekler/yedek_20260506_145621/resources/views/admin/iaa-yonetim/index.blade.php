@push('pageTitle')
    İAA Yönetimi | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('İyileştirmeye Açık Alan Yönetimi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Başarı Mesajı --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                     class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
                </div>
            @endif
            
            {{-- İstatistik Kartları --}}
            @include('admin.iaa-yonetim.partials.stats-cards')

            {{-- ================= SEKME NAVİGASYON ================= --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" x-data="{ activeTab: localStorage.getItem('activeTab') || 'aktif-projeler' }">
                
            {{-- MOBİL İÇİN DROPDOWN SEKMELER --}}
                <div class="sm:hidden p-4 border-b border-gray-200 bg-gray-50">
                    <label for="tabs" class="sr-only">Sekme Seçiniz</label>
                    <select id="tabs" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium text-gray-700" 
                            x-model="activeTab" 
                            @change="switchTab($event.target.value)">
                        
                        {{-- 1. AKTİF PROJELER --}}
                        <option value="aktif-projeler">🔵 Aktif Projeler ({{ $atanmisOlanlar->count() }})</option>
                        
                        {{-- 2. ONAY BEKLEYENLER --}}
                        @if(auth()->user()->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi', 'Direktör']))
                            <option value="onay-bekleyenler">🟡 Onay Bekleyenler ({{ $onayToplam }})</option>
                        @endif



                        {{-- 4. HAVUZ & TALEPLER --}}
                        @role('Superadmin')
                            @php
                                $mobilHavuzToplam = $havuzdakiler->count() + $talepAlanOneriler->count();
                            @endphp
                            <option value="havuz-talepler">🔘 Havuz & Talepler ({{ $mobilHavuzToplam }})</option>
                        @endrole

                        {{-- 5. TAMAMLANANLAR --}}
                        <option value="tamamlananlar">🟢 Tamamlananlar ({{ $sonTamamlananlar->count() }})</option>

                        {{-- 6. REDDEDİLENLER --}}
                        @if(auth()->user()->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi']))
                            @php
                                $mobilRedToplam = $tamamlanmasiReddedilenler->count();
                                if(auth()->user()->hasRole('Superadmin')) {
                                    $mobilRedToplam += $reddedilenler->count();
                                }
                            @endphp
                            <option value="reddedilenler">🔴 Reddedilenler ({{ $mobilRedToplam }})</option>
                        @endif
                    </select>
                </div>

                {{-- MASAÜSTÜ İÇİN RENKLİ SEKMELER (DÜZENLENMİŞ GRİD YAPI) --}}
                <div class="hidden sm:block border-b border-gray-200">
                    {{-- 
                        DÜZELTME: 'flex overflow-x-auto' yerine 'grid' yapısı kullanıldı.
                        grid-cols-3: Tablette 3 yan yana
                        xl:grid-cols-6: Büyük ekranda 6 yan yana (hepsi sığar)
                    --}}
                    <nav class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6" aria-label="Tabs">
                        
                        {{-- 1. AKTİF PROJELER (MAVİ) --}}
                        <button @click="switchTab('aktif-projeler'); activeTab = 'aktif-projeler'" 
                                :class="activeTab === 'aktif-projeler' 
                                    ? 'border-blue-500 text-blue-700 bg-blue-50' 
                                    : 'border-transparent text-blue-600 hover:text-blue-800 hover:bg-blue-50/50'"
                                class="w-full justify-center py-4 px-2 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="truncate">Aktif Projeler</span>
                            @if($atanmisOlanlar->count() > 0)
                                <span :class="activeTab === 'aktif-projeler' ? 'bg-blue-200 text-blue-800' : 'bg-blue-100 text-blue-600'" 
                                      class="ml-1 py-0.5 px-2 rounded-full text-xs font-bold transition-colors">
                                    {{ $atanmisOlanlar->count() }}
                                </span>
                            @endif
                        </button>

                        {{-- 2. ONAY BEKLEYENLER (SARI) --}}
                        @if(auth()->user()->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi', 'Direktör']))
                        <button @click="switchTab('onay-bekleyenler'); activeTab = 'onay-bekleyenler'" 
                                :class="activeTab === 'onay-bekleyenler' 
                                    ? 'border-yellow-500 text-yellow-700 bg-yellow-50' 
                                    : 'border-transparent text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50/50'"
                                class="w-full justify-center py-4 px-2 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="truncate">Onay Bekleyenler</span>
                            @if($onayToplam > 0)
                                <span :class="activeTab === 'onay-bekleyenler' ? 'bg-yellow-200 text-yellow-800' : 'bg-yellow-100 text-yellow-600'"
                                      class="ml-1 py-0.5 px-2 rounded-full text-xs font-bold transition-colors">
                                    {{ $onayToplam }}
                                </span>
                            @endif
                        </button>
                        @endif



                        {{-- 3. HAVUZ & TALEPLER (GRİ) --}}
                        @role('Superadmin')
                        <button @click="switchTab('havuz-talepler'); activeTab = 'havuz-talepler'"
                                :class="activeTab === 'havuz-talepler' 
                                    ? 'border-gray-500 text-gray-700 bg-gray-100' 
                                    : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                                class="w-full justify-center py-4 px-2 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span class="truncate">Havuz & Talepler</span>
                            {{-- DÜZELTME: SAYAÇ EKLENDİ --}}
                            @php
                                $havuzToplam = $havuzdakiler->count() + $talepAlanOneriler->count();
                            @endphp
                            @if($havuzToplam > 0)
                                <span :class="activeTab === 'havuz-talepler' ? 'bg-gray-200 text-gray-800' : 'bg-gray-100 text-gray-600'"
                                      class="ml-1 py-0.5 px-2 rounded-full text-xs font-bold transition-colors">
                                    {{ $havuzToplam }}
                                </span>
                            @endif
                        </button>
                        @endrole

                        {{-- 4. TAMAMLANANLAR (YEŞİL) --}}
                        <button @click="switchTab('tamamlananlar'); activeTab = 'tamamlananlar'"
                                :class="activeTab === 'tamamlananlar' 
                                    ? 'border-green-500 text-green-700 bg-green-50' 
                                    : 'border-transparent text-green-600 hover:text-green-800 hover:bg-green-50/50'"
                                class="w-full justify-center py-4 px-2 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            <span class="truncate">Tamamlananlar</span>
                            @if($sonTamamlananlar->count() > 0)
                                <span :class="activeTab === 'tamamlananlar' ? 'bg-green-200 text-green-800' : 'bg-green-100 text-green-600'"
                                      class="ml-1 py-0.5 px-2 rounded-full text-xs font-bold transition-colors">
                                    {{ $sonTamamlananlar->count() }}
                                </span>
                            @endif
                        </button>

                        {{-- 5. REDDEDİLENLER (KIRMIZI) --}}
                        @if(auth()->user()->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi']))
                        <button @click="switchTab('reddedilenler'); activeTab = 'reddedilenler'"
                                :class="activeTab === 'reddedilenler' 
                                    ? 'border-red-500 text-red-700 bg-red-50' 
                                    : 'border-transparent text-red-600 hover:text-red-800 hover:bg-red-50/50'"
                                class="w-full justify-center py-4 px-2 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="truncate">Reddedilenler</span>
                            @php 
                                $redToplam = $tamamlanmasiReddedilenler->count();
                                if(auth()->user()->hasRole('Superadmin')) {
                                    $redToplam += $reddedilenler->count();
                                }
                            @endphp
                            @if($redToplam > 0)
                                <span :class="activeTab === 'reddedilenler' ? 'bg-red-200 text-red-800' : 'bg-red-100 text-red-600'"
                                      class="ml-1 py-0.5 px-2 rounded-full text-xs font-bold transition-colors">
                                    {{ $redToplam }}
                                </span>
                            @endif
                        </button>
                        @endif
                    </nav>
                </div>

                {{-- ================= SEKME İÇERİKLERİ ================= --}}
                <div class="p-4 sm:p-6 bg-gray-50 min-h-[400px]">
                    
                    {{-- 1. İÇERİK: AKTİF PROJELER --}}
                    <div id="tab-aktif-projeler" class="tab-content space-y-6">
                        @if($atanmisOlanlar->isNotEmpty())
                            @include('admin.iaa-yonetim.partials.atanmis-projeler-table', [
                                'iaas' => $atanmisOlanlar, 
                                'type' => 'atanmis', 
                                'title' => 'Atanmış / Revize Edilen Projeler', 
                                'color' => 'blue' 
                            ])
                        @else
                             <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-200">
                                Aktif (Atanmış veya Revize edilen) proje bulunmamaktadır.
                            </div>
                        @endif
                    </div>

                    {{-- 2. İÇERİK: ONAY BEKLEYENLER --}}
                    <div id="tab-onay-bekleyenler" class="tab-content space-y-6 hidden">
                        
                        {{-- A. BÖLÜM ONAYI (Kalite Yöneticisi veya Admin) --}}
                        @if($bolumOnayiBekleyenler->isNotEmpty())
                            @include('admin.iaa-yonetim.partials.bolum-onayi-bekleyenler-table', ['iaas' => $bolumOnayiBekleyenler])
                        @endif

                        {{-- B. DİREKTÖR ONAYI (Direktör veya Admin) --}}
                        @if($direktorOnayiBekleyenler->isNotEmpty())
                            @include('admin.iaa-yonetim.partials.direktor-onayi-bekleyenler-table', ['iaas' => $direktorOnayiBekleyenler])
                        @endif

                        {{-- C. YÖNETİCİ & DİĞERLERİ (Sadece Admin) --}}
                        @role('Superadmin')
                            @if($yoneticiOnayiBekleyenler->isNotEmpty())
                                @include('admin.iaa-yonetim.partials.yonetici-onayi-bekleyenler-table', ['iaas' => $yoneticiOnayiBekleyenler, 'title' => 'Yönetici Onayı Bekleyen Tamamlanmış Projeler', 'color' => 'purple'])
                            @endif
                            @if($onayBekleyenMisafirler->isNotEmpty())
                                @include('admin.iaa-yonetim.partials.onay-bekleyen-misafirler-table', ['iaas' => $onayBekleyenMisafirler, 'type' => 'onay', 'title' => 'Misafirlerden Gelen Öneriler', 'color' => 'yellow'])
                            @endif
                            @if($onayBekleyenKullanicilar->isNotEmpty())
                                @include('admin.iaa-yonetim.partials.onay-bekleyen-kullanicilar-table', ['iaas' => $onayBekleyenKullanicilar, 'type' => 'onay', 'title' => 'Kayıtlı Kullanıcılardan Gelen Öneriler', 'color' => 'yellow'])
                            @endif
                        @endrole

                        {{-- Eğer Hiçbir Şey Yoksa --}}
                        @if($onayToplam == 0)
                             <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-200">
                                Onay bekleyen herhangi bir proje bulunmamaktadır.
                            </div>
                        @endif
                    </div>



                    {{-- 3. İÇERİK: HAVUZ & TALEPLER --}}
                    @role('Superadmin')
                    <div id="tab-havuz-talepler" class="tab-content space-y-6 hidden">
                        @if($talepAlanOneriler->isNotEmpty())
                            @include('admin.iaa-yonetim.partials.talep-alan-oneriler-table', ['iaas' => $talepAlanOneriler, 'title' => 'Talep Alan Öneriler', 'color' => 'blue'])
                        @endif
                        @if($havuzdakiler->isNotEmpty())
                            @include('admin.iaa-yonetim.partials.havuzdaki-oneriler-table', ['iaas' => $havuzdakiler, 'type' => 'havuz', 'title' => 'Havuzdaki Öneriler', 'color' => 'gray'])
                        @endif
                        @if($havuzdakiler->isEmpty() && $talepAlanOneriler->isEmpty())
                             <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-200">
                                Havuz boş.
                            </div>
                        @endif
                    </div>
                    @endrole

                    {{-- 4. İÇERİK: TAMAMLANANLAR --}}
                    <div id="tab-tamamlananlar" class="tab-content space-y-6 hidden">
                        @if($sonTamamlananlar->isNotEmpty())
                            @include('admin.iaa-yonetim.partials.tamamlanmis-projeler-ozet-table', [
                                'iaas' => $sonTamamlananlar, 
                                'title' => 'Tamamlanan Projeler', 
                                'color' => 'green' 
                            ])
                        @else
                            <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-200">
                                Henüz tamamlanmış bir proje bulunmamaktadır.
                            </div>
                        @endif
                    </div>

                    {{-- 5. İÇERİK: REDDEDİLENLER --}}
                    @if(auth()->user()->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi']))
                    <div id="tab-reddedilenler" class="tab-content space-y-6 hidden">
                        
                        {{-- Triyaj Redleri --}}
                        @if($reddedilenler->isNotEmpty())
                            @include('admin.iaa-yonetim.partials.reddedilen-oneriler-table', [
                                'iaas' => $reddedilenler, 
                                'type' => 'reddedilmis', 
                                'title' => 'Reddedilen Öneriler', 
                                'color' => 'red'
                            ])
                        @endif

                        {{-- Tamamlanma Redleri --}}
                        @if($tamamlanmasiReddedilenler->isNotEmpty())
                            @include('admin.iaa-yonetim.partials.tamamlanmasi-reddedilen-projeler-table', [
                                'iaas' => $tamamlanmasiReddedilenler, 
                                'title' => 'Tamamlanması Reddedilen Projeler'
                            ])
                        @endif

                        @if($reddedilenler->isEmpty() && $tamamlanmasiReddedilenler->isEmpty())
                             <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-200">
                                Reddedilen proje bulunmamaktadır.
                            </div>
                        @endif
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}
    @include('admin.iaa-yonetim.partials.all-modals')
    
    @if ($errors->any() && session('error_modal_id'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'reddet-modal-{{ session('error_modal_id') }}' }));
        });
    </script>
    @endif

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            
            const targetContent = document.getElementById('tab-' + tabName);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                localStorage.setItem('activeTab', tabName);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const userHasBolumOnayi = @json($bolumOnayiBekleyenler->isNotEmpty() && !auth()->user()->hasRole('Superadmin'));
            let defaultTab = localStorage.getItem('activeTab') || 'aktif-projeler';
            
            if (userHasBolumOnayi) { defaultTab = 'onay-bekleyenler'; }
            
            const dropdown = document.getElementById('tabs');
            if(dropdown) { dropdown.value = defaultTab; }

            if (!document.getElementById('tab-' + defaultTab)) {
                switchTab('aktif-projeler');
            } else {
                switchTab(defaultTab);
            }
        });
    </script>
</x-app-layout>