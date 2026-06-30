@extends('layouts.app')

@push('pageTitle')
Hoş Geldiniz | Başvuru Tamamlama | 
@endpush

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 relative overflow-hidden py-12">
    <!-- Dekoratif Arka Plan Elemanları -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-500/20 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-blue-500/20 blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-6xl px-4 sm:px-6 relative z-10">
        <div class="bg-white/90 backdrop-blur-xl border border-white shadow-2xl rounded-[2.5rem] overflow-hidden flex flex-col md:flex-row transform transition-all duration-500 hover:shadow-indigo-500/10">
            
            <!-- Sol Panel (Görsel / Karşılama) -->
            <div class="md:w-5/12 bg-gradient-to-br from-indigo-600 via-indigo-700 to-blue-800 p-10 sm:p-14 text-white flex flex-col justify-between relative overflow-hidden">
                <!-- Sol Panel Arka Plan Deseni -->
                <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+CgkJPGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmZmZmYiLz4KPC9zdmc+')] mix-blend-overlay"></div>
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-blue-400/20 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/20 mb-8 shadow-inner">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h2 class="text-4xl sm:text-5xl font-extrabold tracking-tight mb-4 leading-tight">İAA Sistemine<br><span class="text-indigo-200">Hoş Geldiniz</span></h2>
                    <p class="text-indigo-100 text-lg leading-relaxed max-w-sm">
                        Şirketimizin iç süreçlerine erişim sağlamak için son bir adımınız kaldı. Lütfen bilgilerinizi eksiksiz tamamlayın.
                    </p>
                </div>

                <div class="relative z-10 mt-12 flex items-center gap-4">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-400 border-2 border-indigo-700 flex items-center justify-center text-xs font-bold shadow-md">K1</div>
                        <div class="w-10 h-10 rounded-full bg-blue-400 border-2 border-indigo-700 flex items-center justify-center text-xs font-bold shadow-md">K2</div>
                        <div class="w-10 h-10 rounded-full bg-purple-400 border-2 border-indigo-700 flex items-center justify-center text-xs font-bold shadow-md">K3</div>
                    </div>
                    <p class="text-sm font-medium text-indigo-200">Onlarca departman tek bir sistemde buluşuyor.</p>
                </div>
            </div>

            <!-- Sağ Panel (Form) -->
            <div class="md:w-7/12 p-10 sm:p-14 lg:p-16 flex flex-col justify-center bg-white">
                
                <div class="mb-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold tracking-wider uppercase mb-6">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        Profil Doğrulama
                    </div>
                    <h3 class="text-3xl font-extrabold text-slate-800 mb-4">Merhaba, <span class="text-indigo-600">{{ $centralUser['first_name'] }}!</span> 👋</h3>
                    <div class="p-5 bg-slate-50 border border-slate-100 rounded-2xl shadow-sm">
                        <p class="text-slate-600 text-base leading-relaxed">
                            Hesabınız başarıyla oluşturuldu. İyileştirmeye Açık Alanlar (<strong class="text-slate-800">İAA</strong>) sisteminde işlem yapabilmeniz için <strong>bağlı olduğunuz departmanı (bölümü)</strong> seçmeniz gerekmektedir. Seçiminizin ardından bölüm yöneticinize onay talebi gönderilecektir.
                        </p>
                    </div>
                </div>

                <form action="{{ route('sso.basvuru_kaydet') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="relative group">
                        <label class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-3 transition-colors group-hover:text-indigo-600">Departman / Bölüm Seçimi</label>
                        
                        <!-- Özel Arama Yapılabilir Dropdown (Alpine.js) -->
                        <div x-data="{
                            open: false,
                            search: '',
                            selectedId: '',
                            selectedName: 'Lütfen size ait departmanı seçiniz...',
                            bolumler: [
                                @foreach($bolumler as $bolum)
                                @php
                                    $leaderNames = isset($liderler[$bolum->id]) ? $liderler[$bolum->id]->pluck('name')->implode(', ') : '--';
                                @endphp
                                {
                                    id: '{{ $bolum->id }}',
                                    name: '{{ addslashes($bolum->ad) }}',
                                    leader: '{{ addslashes($leaderNames) }}'
                                },
                                @endforeach
                            ],
                            get filteredBolumler() {
                                if (this.search === '') {
                                    return this.bolumler;
                                }
                                return this.bolumler.filter(b => b.name.toLowerCase().includes(this.search.toLowerCase()) || b.leader.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            selectBolum(bolum) {
                                this.selectedId = bolum.id;
                                this.selectedName = bolum.name;
                                this.open = false;
                                this.search = '';
                            }
                        }" class="relative w-full" @click.away="open = false">
                            
                            <!-- Form Gönderimi için Gizli Input -->
                            <input type="hidden" name="bolum_id" :value="selectedId" required>

                            <!-- Açma/Kapatma Butonu -->
                            <button type="button" @click="open = !open" 
                                class="flex w-full items-center justify-between pl-5 pr-4 py-4 text-left text-lg font-medium text-slate-800 border rounded-2xl transition-all duration-300 shadow-sm cursor-pointer focus:outline-none"
                                :class="open ? 'bg-white border-indigo-500 ring-4 ring-indigo-500/20' : 'bg-slate-50 border-slate-200 hover:bg-white hover:border-indigo-300'">
                                
                                <span x-text="selectedName" :class="selectedId === '' ? 'text-slate-500' : 'text-slate-800'"></span>
                                <svg class="h-6 w-6 text-indigo-500 transform transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <!-- Açılır Menü -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" 
                                class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden" style="display: none;">
                                
                                <!-- Arama Alanı -->
                                <div class="p-3 border-b border-slate-100 bg-slate-50">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </div>
                                        <input type="text" x-model="search" placeholder="Bölüm veya yönetici ara..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white" @click.stop @keydown.enter.prevent>
                                    </div>
                                </div>

                                <!-- Sonuç Listesi -->
                                <ul class="max-h-60 overflow-y-auto py-2 scrollbar-thin scrollbar-thumb-slate-200">
                                    <template x-for="bolum in filteredBolumler" :key="bolum.id">
                                        <li>
                                            <button type="button" @click="selectBolum(bolum)" class="w-full text-left px-5 py-3 hover:bg-indigo-50 focus:bg-indigo-50 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-1 group outline-none">
                                                <span class="font-semibold text-slate-800 group-hover:text-indigo-700" x-text="bolum.name"></span>
                                                <span class="text-xs font-medium px-2.5 py-1 rounded-md bg-slate-100 text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600 flex items-center gap-1.5 whitespace-nowrap border border-transparent group-hover:border-indigo-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    <span x-text="bolum.leader"></span>
                                                </span>
                                            </button>
                                        </li>
                                    </template>
                                    <li x-show="filteredBolumler.length === 0" class="px-5 py-4 text-center text-sm text-slate-500">
                                        Aramanıza uygun bölüm bulunamadı.
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <p class="mt-3 text-sm text-slate-500 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Yanlış departman seçimi onay sürecinizi uzatabilir.
                        </p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="group relative w-full sm:w-auto inline-flex justify-center items-center gap-3 py-4 px-10 border border-transparent text-base font-bold rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/30 shadow-lg shadow-indigo-600/30 transition-all duration-300 transform hover:-translate-y-1">
                            <svg class="h-5 w-5 text-indigo-200 group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Başvurumu Tamamla ve Onaya Gönder
                        </button>
                    </div>
                </form>

                <!-- Alt Bilgi / Sistem Tasarımcısı Kartı -->
                <div class="mt-12 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs font-medium text-slate-400">
                        Güvenliğiniz için bu adım zorunludur.
                    </p>
                    <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 hover:bg-white hover:shadow-md transition-all duration-300">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs border border-indigo-200">
                            CK
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Sistem Tasarımcısı</span>
                            <span class="text-sm font-bold text-slate-700">Celal Karaman</span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    body {
        font-family: 'Inter', sans-serif;
    }
</style>
@endsection
