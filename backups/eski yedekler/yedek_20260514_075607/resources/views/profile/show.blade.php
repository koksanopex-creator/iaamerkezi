@php
    // Yetki kontrolü: Şikayet sekmesini kimler görebilir?
    $sikayetGormeYetkisi = $user->hasRole([
        'Superadmin',
        'Müşteri Şikayeti Kurulu',
        'Müşteri Şikayeti Çözüm Lideri',
        'Bölüm Kalite Yöneticisi'
    ]);
@endphp

@push('pageTitle')
    {{ $user->name }} | 
@endpush

@php
    $isSuperadmin = auth()->check() && auth()->user()->hasRole('Superadmin');
    $isTrashed = method_exists($user, 'trashed') && $user->trashed();
@endphp

<x-app-layout>
    @if($isTrashed && !$isSuperadmin)
        {{-- DİĞER KULLANICILAR İÇİN BİLGİLENDİRME EKRANI --}}
        <div class="max-w-4xl mx-auto py-16 px-4">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                {{-- ÜST KIRMIZI ALAN --}}
                <div class="bg-gradient-to-r from-red-500 to-rose-600 pt-12 pb-24 px-8 relative overflow-hidden text-center">
                    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    <svg class="w-16 h-16 text-white/80 mx-auto relative z-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <h2 class="text-3xl font-black text-white tracking-tight relative z-10">Personel Sistemden Ayrılmıştır</h2>
                </div>
                
                <div class="px-8 sm:px-12 pb-12 bg-white relative">
                    
                    {{-- PROFİL FOTOĞRAFI VE İSİM --}}
                    <div class="-mt-16 flex flex-col items-center mb-8 relative z-20">
                        <div class="w-32 h-32 bg-white rounded-full p-2 shadow-xl border border-gray-100">
                            @if($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover">
                            @else
                                <div class="w-full h-full rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-4xl font-bold text-gray-500 uppercase">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <h3 class="mt-4 text-3xl font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-gray-500 font-medium uppercase tracking-widest text-sm mt-1">{{ $user->unvan ?? 'Eski Personel' }}</p>
                    </div>

                    <p class="text-center text-lg text-gray-600 mb-10 leading-relaxed max-w-2xl mx-auto">
                        Aradığınız personelin sistemimizle ilişiği kesilmiş ve hesabı <span class="font-bold text-red-600">pasif</span> duruma getirilmiştir.
                    </p>

                    {{-- ORGANİZASYON BİLGİSİ (ESKİ BAĞLANTILARI) --}}
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 mb-10">
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-6 text-center">Önceki Organizasyon Bilgileri</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Bölüm --}}
                            <div class="flex flex-col items-center text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-10 h-10 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Bağlı Olduğu Bölüm</span>
                                <span class="text-sm font-bold text-gray-800 mt-1">{{ $user->bolum->ad ?? 'Belirtilmedi' }}</span>
                            </div>

                            {{-- Lider --}}
                            <div class="flex flex-col items-center text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-10 h-10 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Bölüm Lideri</span>
                                @if(isset($bolumManager) && $bolumManager)
                                    <a href="{{ route('profile.show', $bolumManager->id) }}" class="mt-1 flex items-center gap-2 group">
                                        @if($bolumManager->profile_photo_path)
                                            <img src="{{ asset('storage/' . $bolumManager->profile_photo_path) }}" class="w-6 h-6 rounded-full object-cover">
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-500">{{ substr($bolumManager->name, 0, 1) }}</div>
                                        @endif
                                        <span class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $bolumManager->name }}</span>
                                    </a>
                                @else
                                    <span class="text-sm font-bold text-gray-800 mt-1">-</span>
                                @endif
                            </div>

                            {{-- Direktör --}}
                            <div class="flex flex-col items-center text-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-10 h-10 bg-purple-50 text-purple-500 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Direktör</span>
                                @if(isset($director) && $director)
                                    <a href="{{ route('profile.show', $director->id) }}" class="mt-1 flex items-center gap-2 group">
                                        @if($director->profile_photo_path)
                                            <img src="{{ asset('storage/' . $director->profile_photo_path) }}" class="w-6 h-6 rounded-full object-cover">
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-500">{{ substr($director->name, 0, 1) }}</div>
                                        @endif
                                        <span class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $director->name }}</span>
                                    </a>
                                @else
                                    <span class="text-sm font-bold text-gray-800 mt-1">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- GÖREV DEVRİ UYARISI --}}
                    <div class="bg-orange-50/50 rounded-xl p-5 border border-orange-100 flex items-start gap-4 max-w-2xl mx-auto">
                        <div class="bg-orange-100 p-2 rounded-lg text-orange-600 shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">Devredilen Sorumluluklar</h4>
                            <p class="text-sm text-gray-600">
                                Bu personelin daha önceden dahil olduğu <strong>Çözüm Takımları</strong>, projeler ve şikayet onay süreçleri sistem tarafından korunmaktadır. Bekleyen görevler ve iletişim talepleri için doğrudan yukarida belirtilen yöneticilerle veya takımın yeni lideriyle iletişime geçebilirsiniz.
                            </p>
                        </div>
                    </div>
                    
                    {{-- AKSİYON BUTONLARI --}}
                    <div class="flex flex-col sm:flex-row justify-center gap-4 mt-10">
                        <button onclick="window.history.back()" class="px-8 py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl shadow-sm hover:bg-gray-50 transition-all">
                            Önceki Sayfaya Dön
                        </button>
                        <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 transition-all">
                            Ana Sayfaya Git
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- GRAFİK KÜTÜPHANESİ --}}
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        {{-- 1. ÜST BANNER VE PROFİL KARTI --}}
        @include('profile.partials.show.header')

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-12">

            {{-- 2. ANA İSTATİSTİKLER --}}
            @include('profile.partials.show.stats')

            {{-- 3. SEKME YAPISI (Tab Container) --}}
            <div x-data="{ activeTab: '{{ session('active_tab', request('tab', (isset($isCustomerRep) && $isCustomerRep ? 'sikayetler' : 'performans'))) }}' }"
                class="bg-white rounded-2xl shadow-xl overflow-hidden min-h-[600px]">

                {{-- Sekme Butonları --}}
                @include('profile.partials.show.tabs-nav', ['activeTasksCount' => $activeTasks->count()])

                {{-- Sekme İçerikleri --}}
                <div class="p-6 bg-gray-50 min-h-[500px]">

                    @if(!isset($isCustomerRep) || !$isCustomerRep)
                        @include('profile.partials.show.tab-performance')
                    @endif

                    @if($canViewActiveTasks && (!isset($isCustomerRep) || !$isCustomerRep))
                        @include('profile.partials.show.tab-aktif-gorevler')
                    @endif

                    @if($sikayetGormeYetkisi || (isset($isCustomerRep) && $isCustomerRep))
                        @include('profile.partials.show.tab-complaints')
                    @endif

                    @if(isset($isCustomerRep) && $isCustomerRep)
                        @include('profile.partials.show.tab-colleagues')
                    @endif

                    @include('profile.partials.show.tab-comments')

                    @role('Superadmin')
                    @include('profile.partials.show.tab-security')
                    @endrole

                    {{-- DİSİPLİN İÇERİĞİ (Müşteriler Göremez) --}}
                    @if(!isset($isCustomerRep) || !$isCustomerRep)
                        @include('profile.partials.show.tab-disciplinary')
                    @endif
                </div>
            </div>
        </div>

        {{-- 4. JAVASCRIPT --}}
        @include('profile.partials.show.scripts')
    @endif

</x-app-layout>