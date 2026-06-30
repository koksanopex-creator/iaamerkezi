@extends('layouts.app')

@push('pageTitle')
Başvuru Onay Bekliyor | 
@endpush

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 relative overflow-hidden py-12" x-data="{ showChangeDept: false }">
    <!-- Dekoratif Arka Plan Elemanları -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-amber-500/20 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-orange-500/20 blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-5xl px-6 relative z-10">
        <!-- Kart -->
        <div class="bg-white/80 backdrop-blur-xl border border-white/40 shadow-2xl rounded-3xl p-8 sm:p-10 transform transition-all duration-500 hover:shadow-amber-500/10">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-left shadow-sm flex items-start">
                    <svg class="w-5 h-5 text-emerald-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm font-semibold text-emerald-700 leading-snug">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl text-left shadow-sm flex items-start">
                    <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-red-700 leading-snug">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Animasyonlu İkon ve Başlık -->
            <div class="text-center mb-10">
                @if(isset($user) && $user->rejected_at)
                    <div class="relative w-20 h-20 mx-auto mb-6">
                        <div class="absolute inset-0 bg-rose-200 rounded-full animate-ping opacity-75"></div>
                        <div class="relative w-20 h-20 bg-gradient-to-br from-rose-500 to-red-600 rounded-full flex items-center justify-center shadow-lg shadow-rose-500/30 transform transition-transform duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-3xl font-extrabold text-rose-700 mb-3 tracking-tight">Başvurunuz Reddedildi</h2>
                    <p class="text-slate-600 text-sm leading-relaxed max-w-xl mx-auto mb-4">
                        İAA sistemine erişim talebiniz bölüm yöneticileri veya sistem yöneticisi tarafından reddedilmiştir.
                    </p>
                    @if($user->rejection_reason)
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 text-left max-w-2xl mx-auto inline-block w-full">
                        <h4 class="text-xs font-bold text-rose-800 uppercase tracking-wider mb-2">Reddetme Sebebi</h4>
                        <p class="text-rose-700 text-sm">{{ $user->rejection_reason }}</p>
                    </div>
                    @endif
                @else
                    <div class="relative w-20 h-20 mx-auto mb-6">
                        <div class="absolute inset-0 bg-amber-200 rounded-full animate-ping opacity-75"></div>
                        <div class="relative w-20 h-20 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg shadow-amber-500/30 transform transition-transform duration-300 hover:rotate-3">
                            <svg class="w-10 h-10 text-white animate-[spin_3s_linear_infinite]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-3xl font-extrabold text-slate-800 mb-3 tracking-tight">Başvurunuz Onay Bekliyor</h2>
                    <p class="text-slate-600 text-sm leading-relaxed max-w-xl mx-auto">
                        İAA sistemine erişim talebiniz başarıyla alınmıştır. Sisteme tam erişim sağlayabilmeniz için hesabınızın yöneticiler tarafından onaylanması beklenmektedir.
                    </p>
                @endif
            </div>

            <!-- Dinamik Bilgiler -->
            @if(isset($user) && $user)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                
                {{-- Başvuru Özeti Kutusu --}}
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6 space-y-5 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200 pb-3 mb-3">Başvuru Özeti</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1.5">Başvuru Sahibi</p>
                            <p class="text-sm font-bold text-slate-700">{{ $user->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $user->email }}</p>
                        </div>
                        
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1.5">Başvuru Tarihi</p>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-sm font-bold text-slate-700">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="h-px bg-slate-200 w-full my-1"></div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1.5">Talep Edilen Bölüm</p>
                            <p class="text-sm font-bold text-slate-700">{{ $user->bolum ? $user->bolum->ad : 'Belirtilmedi' }}</p>
                        </div>
                        <button type="button" @click="showChangeDept = !showChangeDept" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline decoration-indigo-300 decoration-2 underline-offset-2 transition-colors">
                            Bölümü Değiştir
                        </button>
                    </div>

                    <div>
                        <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1.5">Bekleme Süresi</p>
                        <div class="flex items-center gap-1.5 text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-100 inline-flex">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $user->created_at->diffForHumans(['parts' => 2, 'join' => ' ve ']) }}
                        </div>
                    </div>
                </div>

                {{-- Onaylayacak Kişiler Kutusu --}}
                <div class="bg-amber-50/50 border border-amber-100 rounded-2xl p-6 shadow-sm flex flex-col h-full">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-amber-600/70 border-b border-amber-200/50 pb-3 mb-4">Onay Beklenen Kişiler</h3>
                    
                    <div class="flex-1 space-y-4">
                        @if(count($liderler) > 0)
                            @foreach($liderler as $lider)
                                <div class="flex items-start gap-4 bg-white p-3.5 rounded-xl border border-amber-100 shadow-sm transition-all hover:shadow-md">
                                    <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-inner">
                                        {{ mb_substr($lider->name, 0, 1) }}
                                    </div>
                                    <div class="overflow-hidden py-0.5">
                                        <p class="text-sm font-bold text-slate-800 truncate">{{ $lider->name }}</p>
                                        <p class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider truncate mb-1">Bölüm Lideri</p>
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 mt-1">
                                            @if($lider->phone)
                                                <p class="text-xs text-slate-500 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg> {{ $lider->phone }}</p>
                                            @endif
                                            <p class="text-xs text-slate-500 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg> {{ $lider->email }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-start gap-4 bg-white p-3.5 rounded-xl border border-amber-100 shadow-sm">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-inner">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="overflow-hidden py-0.5">
                                    <p class="text-sm font-bold text-slate-800 truncate">Sistem Yöneticisi</p>
                                    <p class="text-xs text-slate-500 mt-1">Bu bölüm için atanmış özel bir lider bulunmuyor. Onayınız IT tarafından verilecektir.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- 24 Saat Sonra Hatırlat Butonu Aktif Olur (Alpine JS Sayaç) --}}
                    @if(!$user->rejected_at)
                    @php
                        $cacheKey = 'sso_reminder_' . $user->id;
                        $alreadyReminded = \Illuminate\Support\Facades\Cache::has($cacheKey);
                        $targetDate = $user->created_at->copy()->addHours(24)->toIso8601String();
                    @endphp

                    <div class="mt-5 pt-5 border-t border-amber-200/50" 
                         x-data="{
                            targetDate: new Date('{{ $targetDate }}').getTime(),
                            alreadyReminded: {{ $alreadyReminded ? 'true' : 'false' }},
                            isActive: false,
                            timeLeftText: 'Hesaplanıyor...',
                            interval: null,
                            init() {
                                if (this.alreadyReminded) return;
                                this.update();
                                this.interval = setInterval(() => this.update(), 1000);
                            },
                            update() {
                                const now = new Date().getTime();
                                const distance = this.targetDate - now;
                                
                                if (distance < 0) {
                                    this.isActive = true;
                                    clearInterval(this.interval);
                                    return;
                                }
                                
                                const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                const s = Math.floor((distance % (1000 * 60)) / 1000);
                                
                                this.timeLeftText = `Hatırlatma için: ${h}s ${m}d ${s}sn kaldı`;
                            }
                         }">
                         
                        @if($alreadyReminded)
                            <div class="text-center text-sm font-bold text-emerald-600 bg-white py-3 px-4 rounded-xl border border-emerald-200 shadow-sm flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Hatırlatma mesajınız iletildi
                            </div>
                        @else
                            <form action="{{ route('sso.hatirlat') }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        :disabled="!isActive"
                                        :class="isActive ? 'bg-amber-500 hover:bg-amber-600 text-white hover:shadow-lg transform hover:-translate-y-0.5' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                        class="w-full inline-flex items-center justify-center gap-2 text-sm font-bold px-5 py-3 rounded-xl transition-all duration-300 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                    <span x-text="isActive ? 'Yöneticilere Hatırlat' : timeLeftText"></span>
                                </button>
                            </form>
                        @endif
                        <p class="text-[11px] text-amber-700/60 mt-2.5 text-center italic font-medium">
                            * İlgili bölüm yöneticilerine sistem üzerinden 24 saatte bir hatırlatma gönderebilirsiniz.
                        </p>
                    </div>
                    @else
                    <div class="mt-5 pt-5 border-t border-amber-200/50 flex flex-col gap-3">
                        <form action="{{ route('sso.basvuru_guncelle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="bolum_id" value="{{ $user->bolum_id }}">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-5 py-3 rounded-xl transition-all duration-300 shadow-sm transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 15H19"></path></svg>
                                Süreci Sıfırla ve Tekrar Başvur
                            </button>
                        </form>
                        
                        <button type="button" @click="showChangeDept = true" class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold px-5 py-3 rounded-xl transition-all duration-300 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            Farklı Bir Bölüme Başvur
                        </button>
                    </div>
                    @endif
                </div>

            </div>

            {{-- Bölüm Değiştirme Formu (Alpine ile açılır/kapanır) --}}
            <div x-show="showChangeDept" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="mb-8 p-6 bg-indigo-50 border border-indigo-100 rounded-2xl relative shadow-inner">
                <button @click="showChangeDept = false" class="absolute top-4 right-4 text-indigo-400 hover:text-indigo-600 bg-white p-1 rounded-full shadow-sm hover:shadow transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h4 class="text-base font-bold text-indigo-900">Başvuru Departmanını Değiştir</h4>
                </div>
                <form action="{{ route('sso.basvuru_guncelle') }}" method="POST" class="flex flex-col sm:flex-row gap-4 items-end max-w-2xl">
                    @csrf
                    <div class="w-full">
                        <label class="block text-xs font-bold text-indigo-700 uppercase tracking-wider mb-2">Yeni Departman Seçin</label>
                        <select name="bolum_id" required class="w-full text-sm font-medium text-slate-700 border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow py-2.5 shadow-sm">
                            <option value="">-- Bölüm Seçin --</option>
                            @foreach($bolumler as $b)
                                <option value="{{ $b->id }}" {{ $user->bolum_id == $b->id ? 'selected' : '' }}>{{ $b->ad }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full sm:w-auto flex-shrink-0 inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-8 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        Güncelle
                    </button>
                </form>
                <p class="text-xs text-indigo-500/80 mt-3 font-medium bg-white/50 inline-block px-3 py-1.5 rounded-lg border border-indigo-100/50">
                    💡 Departman değiştirdiğinizde, yeni departmanın yöneticisine anında bildirim gönderilecektir.
                </p>
            </div>
            @endif

            <!-- Alt Butonlar -->
            <div class="flex justify-center pt-4 border-t border-slate-100">
                <a href="{{ rtrim(env('CENTRAL_SSO_URL', 'http://localhost:8001'), '/') }}" 
                   class="group inline-flex items-center justify-center py-3 px-8 border border-slate-200 text-sm font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 shadow-sm transition-all duration-300">
                    <svg class="h-5 w-5 mr-2 text-slate-400 group-hover:text-slate-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Merkezi Panele Geri Dön
                </a>
            </div>
            
            <p class="text-xs text-slate-400 mt-5 font-medium text-center">
                Sistemden çıkış yapmak isterseniz Merkezi Paneli kullanabilirsiniz.
            </p>
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
