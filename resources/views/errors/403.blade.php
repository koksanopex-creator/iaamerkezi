@push('pageTitle')
    403 | 
@endpush

<x-app-layout>
    @php
        // Hata mesajını exception üzerinden al veya varsayılanı kullan
        $defaultMessage = 'Bu sayfaya erişim yetkiniz yok.';
        $exceptionMessage = $exception->getMessage() ?: $defaultMessage;

        // İngilizce mesajları Türkçeye çevirmek için basit bir harita
        $translations = [
            'User does not have the right roles.' => 'Bu işlem için gerekli role sahip değilsiniz.',
            'This action is unauthorized.' => 'Bu işlem yetkilendirilmedi.',
            'Bu raporu görüntüleme yetkiniz yok.' => 'Bu raporu görüntüleme yetkiniz yok.',
        ];

        // Mesajı çevir veya olduğu gibi kullan
        $displayMessage = $translations[$exceptionMessage] ?? $exceptionMessage;

        // İletişim Bilgileri
        $user = Auth::user();
        
        // 1. Bölüm Lideri
        $bolumLideri = null;
        if ($user && $user->bolum_id) {
            $bolumLideri = \App\Models\User::where('bolum_id', $user->bolum_id)
                ->where('id', '!=', $user->id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'Bölüm Lideri');
                })
                ->first();
        }

        // 2. Sistem Tasarım (Celal Karaman) - Sistemden çekiyoruz
        $sistemAdmin = \App\Models\User::where('email', 'celal.karaman@koksan.com')->first();
    @endphp

    <div class="min-h-[80vh] flex flex-col items-center justify-center p-4 overflow-hidden">
        <div class="max-w-2xl w-full text-center">
            <!-- İkon -->
            <div class="mb-8 relative">
                <div class="absolute inset-0 bg-red-100 rounded-full animate-ping opacity-20"></div>
                <div class="relative w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto text-red-500 shadow-sm border border-red-100">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>

            <!-- Başlık -->
            <h1 class="text-7xl font-black text-slate-800 tracking-tighter mb-2">403</h1>
            <h2 class="text-2xl font-bold text-slate-700 mb-6">Erişim İzniniz Yok</h2>

            <!-- Dinamik Açıklama -->
            <div class="bg-red-50 border border-red-100 rounded-2xl p-6 mb-8 shadow-sm">
                <p class="text-red-600 font-semibold leading-relaxed text-lg">
                    {{ $displayMessage }}
                </p>
                <p class="text-slate-500 text-sm mt-3">
                    Eğer bir hata olduğunu düşünüyorsanız veya yetki talep edecekseniz aşağıdaki kişilerle iletişime geçebilirsiniz.
                </p>
            </div>

            <!-- İletişim Kartları -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <!-- Bölüm Lideri Kartı -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 text-left flex items-start gap-4 shadow-sm hover:border-indigo-200 transition-colors">
                    @if($bolumLideri)
                        <img src="{{ $bolumLideri->profile_photo_url }}" class="w-14 h-14 rounded-xl object-cover shadow-sm ring-2 ring-slate-50" alt="{{ $bolumLideri->name }}">
                    @else
                        <div class="w-14 h-14 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 shrink-0 border border-slate-100">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">BÖLÜM YÖNETİCİSİ</h3>
                        @if($bolumLideri)
                            <p class="font-bold text-slate-700 truncate mb-1">{{ $bolumLideri->name }}</p>
                            <div class="space-y-1">
                                <div class="flex items-center text-xs text-slate-500">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    {{ $bolumLideri->email }}
                                </div>
                                <div class="flex items-center text-xs text-slate-500">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    {{ $bolumLideri->telefon ?: '-' }}
                                </div>
                            </div>
                        @else
                            <p class="font-bold text-slate-700">Kendi Bölüm Yöneticiniz</p>
                            <p class="text-xs text-slate-500 mt-1">Yetki onayı için yöneticinize danışın.</p>
                        @endif
                    </div>
                </div>

                <!-- Sistem Yöneticisi Kartı -->
                <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-5 text-left flex items-start gap-4 shadow-sm hover:border-indigo-200 transition-colors">
                    @if($sistemAdmin)
                        <img src="{{ $sistemAdmin->profile_photo_url }}" class="w-14 h-14 rounded-xl object-cover shadow-sm ring-2 ring-indigo-100" alt="Celal KARAMAN">
                    @else
                        <div class="w-14 h-14 bg-indigo-600 rounded-xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-indigo-100 border border-indigo-500">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mb-1.5">SİSTEM TASARIM VE YÖNETİMİ</h3>
                        <p class="font-bold text-slate-800 flex items-center gap-1.5 mb-1 truncate">
                            Celal KARAMAN
                            <svg class="w-3.5 h-3.5 text-amber-400 fill-current shrink-0" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </p>
                        <div class="space-y-1">
                            <div class="flex items-center text-xs text-slate-500">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                celal.karaman@koksan.com
                            </div>
                            <div class="flex items-center text-xs text-slate-500">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $sistemAdmin ? ($sistemAdmin->telefon ?: '-') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Butonlar -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center justify-center px-8 py-3.5 border border-slate-200 shadow-sm text-sm font-bold rounded-2xl text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300 focus:outline-none transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Geri Dön
                </a>

                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center justify-center px-8 py-3.5 border border-transparent text-sm font-bold rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-xl shadow-indigo-100 focus:outline-none transition-all duration-200 transform hover:-translate-y-1">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Anasayfaya Git
                </a>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-100">
                <p class="text-[10px] text-slate-400 font-mono tracking-widest uppercase">HATA KODU: 403_FORBIDDEN_ACCESS // GÜVENLİK PROTOKOLÜ AKTİF</p>
            </div>
        </div>
    </div>
        </div>
    </div>
</x-app-layout>