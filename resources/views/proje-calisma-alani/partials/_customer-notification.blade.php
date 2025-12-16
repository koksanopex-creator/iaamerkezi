@if($iaa->musteriSikayeti)
    @php
        $sikayet = $iaa->musteriSikayeti;
        // Yetki Kontrolü
        $canNotify = Auth::check() && Auth::user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi']);
    @endphp

    @if($canNotify)
        <div class="bg-white rounded-xl shadow-sm border border-indigo-100 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-violet-600 to-indigo-600 px-4 py-3 flex justify-between items-center">
                <h3 class="text-white font-bold text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Müşteri Takip Sistemi
                </h3>
            </div>

            <div class="p-5">
                {{-- ŞİFRE GÖSTERİM ALANI --}}
                @if(session('generated_password'))
                    <div class="mb-6 bg-indigo-50 border border-indigo-200 p-4 rounded-lg shadow-sm">
                        <p class="text-xs font-bold text-indigo-800 uppercase mb-2">YENİ OLUŞTURULAN MÜŞTERİ ŞİFRESİ:</p>
                        <div class="flex items-center justify-between bg-white p-3 rounded border border-indigo-200">
                            <code class="text-xl font-mono font-bold text-indigo-700 tracking-wider">{{ session('generated_password') }}</code>
                            <span class="text-xs text-gray-500 italic">(Bu şifre müşteriye e-posta olarak gönderildi)</span>
                        </div>
                        <p class="text-xs text-red-500 mt-2">* Bu şifreyi bir daha göremeyeceksiniz, gerekirse not alınız.</p>
                    </div>
                @endif

                @if(!$sikayet->musteri_bildirim_tarihi)
                    {{-- 1. DURUM: Henüz Bildirim Yapılmamış --}}
                    <div class="flex items-center justify-between bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                        <div class="flex items-start gap-3">
                            <div class="text-yellow-600 bg-yellow-100 p-2 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                            <div>
                                <h4 class="text-sm font-bold text-yellow-800">Bildirim Bekleniyor</h4>
                                <p class="text-xs text-yellow-700 mt-1">Müşteriye henüz takip bilgileri gönderilmemiş. Butona basarak otomatik şifre oluşturup gönderebilirsiniz.</p>
                            </div>
                        </div>
                        <form action="{{ route('proje.notify_customer', $iaa->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Bilgileri Gönder
                            </button>
                        </form>
                    </div>

                @else
                    {{-- 2. DURUM: Bildirim Yapılmış --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-sm font-bold text-green-800">Müşteri Bilgilendirildi</p>
                                    <p class="text-xs text-green-700 mt-1">
                                        {{ $sikayet->musteri_bildirim_tarihi->format('d.m.Y H:i') }} tarihinde<br>
                                        <strong>{{ \App\Models\User::find($sikayet->musteri_bildirim_yapan_id)->name ?? 'Sistem' }}</strong> tarafından.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex flex-col justify-center">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-bold text-gray-500 uppercase">Takip Linki</span>
                                <a href="{{ route('public.sikayet.show', $sikayet->takip_token) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Görüntüle &rarr;</a>
                            </div>
                            <code class="text-xs bg-white p-1 rounded border text-gray-600 truncate">{{ route('public.sikayet.show', $sikayet->takip_token) }}</code>
                            
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <form action="{{ route('proje.reset_customer_password', $iaa->id) }}" method="POST" onsubmit="return confirm('Müşterinin mevcut şifresi geçersiz olacak ve yeni bir şifre gönderilecek. Emin misiniz?');">
                                    @csrf
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Şifreyi Sıfırla ve Tekrar Gönder
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endif 
{{-- DÜZELTME: Sondaki @endif eklendi --}}