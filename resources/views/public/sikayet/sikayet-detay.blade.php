<x-guest-layout> {{-- Mevcut guest layout'unuzu kullanmaya devam ediyoruz --}}

    <div class="mb-6 pb-6 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">Şikayet Detayları</h3>
                <p class="text-sm text-gray-600">
                    Şikayet No: <span class="font-semibold text-indigo-600">#{{ $sikayet->id }}</span>
                    (Takip Kodu: <span class="font-semibold text-gray-900">{{ $sikayet->takip_token ?? 'N/A' }}</span>)
                </p>
            </div>

           
            <div>
                @if($sikayet->iaa_id)
                    @php
                        $projeDurumu = \App\Models\Iaa::find($sikayet->iaa_id)->durum ?? 'Bilinmiyor';
                        $renk = match($projeDurumu) {
                            'Tamamlandı' => 'bg-green-100 text-green-800',
                            'Reddedildi', 'Tamamlanması Reddedildi' => 'bg-red-100 text-red-800',
                            'Atandı', 'İşlemde', 'Revize Ediliyor' => 'bg-blue-100 text-blue-800',
                            'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor' => 'bg-purple-100 text-purple-800', // Onay aşaması mor olsun
                            default => 'bg-yellow-100 text-yellow-800'
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $renk }}">
                        {{ $projeDurumu }}
                    </span>
                @else
                    {!! $sikayet->musteri_durum_badge !!}
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        <div class="bg-white shadow-lg rounded-lg p-5 flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-500">Geçen Süre</div>
                <div class="text-xl font-bold text-gray-900">
                    {{ (int)round(\Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->diffInDays(now())) + 1 }}. Gün
                </div>
            </div>
        </div>
        
        @if($sikayet->musteri_cozum_son_tarihi && $sikayet->musteri_durum != 'Kapatıldı')
            @php
                $simdi = now();
                $sonTarih = \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi);
                $geciktiMi = $simdi->isAfter($sonTarih);
            @endphp
            <div class="bg-white shadow-lg rounded-lg p-5 flex items-center gap-4 {{ $geciktiMi ? 'border-l-4 border-red-500' : '' }}">
                <div class="flex-shrink-0 w-12 h-12 {{ $geciktiMi ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }} rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008z" /></svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">{{ $geciktiMi ? 'Gecikme Süresi' : 'Çözüm İçin Kalan Süre' }}</div>
                    <div class="text-xl font-bold {{ $geciktiMi ? 'text-red-700' : 'text-gray-900' }}">
                        {{ $sonTarih->diffForHumans(null, true) }}
                    </div>
                </div>
            </div>
        @endif
        
        @if($sikayet->cozumTakimi)
             <div class="bg-white shadow-lg rounded-lg p-5 flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 00-3.741-.56c-.63-.042-1.249-.065-1.87-.065A18.707 18.707 0 005.25 18.72m12.75 0A18.707 18.707 0 0115 18.72m-3 0A18.707 18.707 0 009 18.72m9 0a8.966 8.966 0 00-9 0m9 0c1.943 0 3.713.463 5.25 1.285A18.707 18.707 0 0015 18.72m-6 0c1.943 0 3.713.463 5.25 1.285A18.707 18.707 0 019 18.72m-6 0A18.707 18.707 0 013 18.72m0 0c1.943 0 3.713.463 5.25 1.285A18.707 18.707 0 003 18.72z" /></svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">İlgili Ekip</div>
                    <div class="text-xl font-bold text-gray-900">{{ $sikayet->cozumTakimi->ad }}</div>
                </div>
            </div>
        @endif
    </div>
    {{-- Başarı/Hata Mesajları --}}
    @if (session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert"><p>{{ session('success') }}</p></div>
    @endif
    @if (session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert"><p>{{ session('error') }}</p></div>
    @endif
    @if ($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
            <strong class="font-bold">Hata!</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Şikayet Bilgileri</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 text-sm">
                <div><strong class="text-gray-500">Müşteri Adı:</strong> <span class="text-gray-900">{{ $sikayet->musteri_adi }}</span></div>
                <div><strong class="text-gray-500">E-posta:</strong> <span class="text-gray-900">{{ $sikayet->musteri_iletisim }}</span></div>
                <div><strong class="text-gray-500">Konum Tipi:</strong> <span class="text-gray-900">{{ $sikayet->konum_tipi }}</span></div>
                <div><strong class="text-gray-500">Şikayet Tarihi:</strong> <span class="text-gray-900">{{ \Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->format('d.m.Y') }}</span></div>
                <div class="md:col-span-2"><strong class="text-gray-500">Kategori:</strong> <span class="text-gray-900">{{ $sikayet->sikayetKategori->ad ?? 'Belirtilmemiş' }}</span></div>
                {{-- Alt Kategori Gösterimi --}}
                @if($sikayet->sikayetAltKategori)
                    <div class="md:col-span-2">
                        <strong class="text-gray-500">Alt Kategori:</strong> 
                        <span class="text-gray-900">{{ $sikayet->sikayetAltKategori->ad }}</span>
                    </div>
                @elseif($sikayet->sikayet_alt_kategori_diger)
                    <div class="md:col-span-2">
                        <strong class="text-gray-500">Alt Kategori (Diğer):</strong> 
                        <span class="text-gray-900">{{ $sikayet->sikayet_alt_kategori_diger }}</span>
                    </div>
                @endif
            </div>
            <div class="mt-6">
                <strong class="text-sm text-gray-500 block mb-1">Şikayet Konusu:</strong>
                <p class="text-lg text-gray-900 font-medium">{{ $sikayet->musteri_sikayet_konusu }}</p>
            </div>
            <div class="mt-4">
                <strong class="text-sm text-gray-500 block mb-1">Şikayet Detayı:</strong>
                <p class="text-gray-700 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $sikayet->musteri_sikayet_detayi }}</p>
            </div>
            @if($sikayet->dosyalar && $sikayet->dosyalar->count() > 0)
            <div class="mt-4">
                <strong class="text-sm text-gray-500 block mb-1">Eklenen Dosyalar:</strong>
                <ul class="list-disc list-inside space-y-1 pl-1">
                    @foreach($sikayet->dosyalar as $dosya)
                    <li>
                        <a href="{{ asset('storage/' . $dosya->dosya_olu) }}" target="_blank" class="text-indigo-600 hover:underline text-sm">
                            {{ $dosya->orijinal_adi }} <span class="text-gray-400 text-xs">({{ $dosya->mime_tipi }})</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
    {{-- 1. Düzenleme Kutusu --}}
    @if(is_null($sikayet->edit_locked_at) && $sikayet->musteri_durum == 'Yeni')
        <div class="p-6 bg-blue-50 border border-blue-200 rounded-lg text-center mb-8 shadow-sm">
             <p class="text-sm text-blue-700 mb-3">Şikayetiniz henüz işleme alınmadı. İsterseniz detayları güncelleyebilirsiniz.</p>
             <a href="{{ route('public.sikayet.edit', ['token' => $sikayet->takip_token]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 transition">
                 Şikayeti Düzenle
             </a>
        </div>
    @endif

    {{-- 2. Durum Bilgisi ve Loglar --}}
    @if((!is_null($sikayet->edit_locked_at) || $sikayet->musteri_durum != 'Yeni') && $sikayet->musteri_durum != 'Kapatıldı')
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Şikayet Süreci İlerlemesi</h3>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    </div>
                    <div class="ml-4">
                        <p class="font-medium text-gray-900">Şikayetiniz şu anda {{ $sikayet->cozumTakimi->ad ?? 'ekibimiz' }} tarafından incelenmektedir.</p>
                        <p class="text-sm text-gray-500">Durum: {{ $sikayet->musteri_durum }}</p>
                    </div>
                </div>

                @if($sikayet->iaa_id && $totalSteps > 0)
                <div class="flex items-start border-t border-gray-200 pt-6">
                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3m0 0l3-3m-3 3v-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4 w-full">
                        <p class="font-medium text-gray-900">Proje İlerlemesi: ({{ $completedSteps }} / {{ $totalSteps }} Adım Tamamlandı)</p>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $totalSteps > 0 ? ($completedSteps / $totalSteps) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                @endif

                @if($sikayet->loglar->whereNotNull('user_id')->isNotEmpty())
                    @foreach($sikayet->loglar->whereNotNull('user_id') as $log)
                        <div class="flex items-start border-t border-gray-200 pt-6">
                            <div class="flex-shrink-0 w-10 h-10 bg-gray-100 text-gray-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-gray-800">{{ $log->eylem }}</p>
                                <p class="text-sm text-gray-600 italic">"{{ $log->aciklama }}"</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $log->created_at->format('d.m.Y H:i') }} - ({{ $log->user->name ?? 'Sistem' }})</p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endif

    @if($sikayet->iaa_id && $yorumlar->isNotEmpty())
        @php
            $yorumSayisi = $yorumlar->count();
            $sonYorum = $yorumlar->first();
            // "Yeni" = Son yorumu müşteri (user_id = null) YAPMADIYSA
            $yeniYorumVarMi = $sonYorum && !is_null($sonYorum->user_id); 
        @endphp
        
        <div class="mt-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Proje Geçmişi ve Yorumlar</h3>
            
            {{-- Bu 'a' etiketi, tüm kartı tıklanabilir yapar ve JS'e gerek duymaz --}}
            <a href="{{ route('proje.workspace.show', $sikayet->iaa_id) }}" {{-- Proje çalışma alanına yönlendirir --}}
               class="block bg-white shadow-lg rounded-lg border border-gray-200 transition hover:shadow-xl hover:border-indigo-500">
                <div class="px-6 py-5 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 {{ $yeniYorumVarMi ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-500' }} rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443h2.884c1.584 0 2.863-1.279 2.863-2.863V12.76M2.25 12.76V6.226c0-1.6 1.123-2.994 2.707-3.227 1.087-.16 2.185-.283 3.293-.369V2.25l4.076 4.076c.296.296.678.443 1.037.443h2.884c1.584 0 2.863 1.279 2.863 2.863v6.534M2.25 12.76c0-1.6 1.123-2.994 2.707-3.227 1.087-.16 2.185-.283 3.293-.369V6.25" /></svg>
                        </div>
                        
                        <div>
                            @if($yeniYorumVarMi)
                                <span class="text-sm font-semibold text-indigo-600 flex items-center gap-2">
                                    <span class="relative flex h-3 w-3">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                    </span>
                                    Yeni Cevap Geldi
                                </span>
                                <p class="text-base font-semibold text-gray-900"><strong>{{ $sonYorum->yapan_kisi_adi }}</strong>'dan yeni bir yorumunuz var.</p>
                            @else
                                <p class="text-base font-semibold text-gray-900">Toplam {{ $yorumSayisi }} Yorum</p>
                                <p class="text-sm text-gray-500">Son yorum sizden geldi.</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-indigo-600 hidden md:block">Yorumları Gör ve Cevap Yaz</span>
                        <svg class="w-5 h-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                    </div>
                </div>
            </a>
        </div>
    @endif
    {{-- 3. Çözüm Geri Bildirim Formu (SADECE PROJE TAMAMLANDIYSA GÖSTER) --}}
    @php
        $projeTamamlandi = $sikayet->iaa_id && \App\Models\Iaa::find($sikayet->iaa_id)->durum == 'Tamamlandı';
    @endphp

    @if($projeTamamlandi)
        <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-8 border border-gray-200" 
             x-data="{ editing: {{ $sikayet->musteri_feedback ? 'false' : 'true' }} }">
            
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Çözüm Değerlendirmeniz</h3>
                
                {{-- Düzenleme Butonu (Eğer karar verilmişse görünür) --}}
                @if($sikayet->musteri_feedback)
                    <button @click="editing = true" x-show="!editing" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium underline">
                        Kararımı Değiştir
                    </button>
                @endif
            </div>

            <div class="p-6">
                {{-- A) KARAR VERİLMİŞSE GÖSTERİLECEK ALAN --}}
                <div x-show="!editing">
                    @if($sikayet->musteri_feedback)
                        <div class="p-6 {{ $sikayet->musteri_feedback == 'Onaylandı' ? 'bg-green-50 border-green-200' : ($sikayet->musteri_feedback == 'Reddedildi' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200') }} border rounded-xl flex items-center gap-4">
                            <div class="p-3 bg-white rounded-full shadow-sm">
                                 @if($sikayet->musteri_feedback == 'Onaylandı')
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                 @elseif($sikayet->musteri_feedback == 'Reddedildi')
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                 @else
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                 @endif
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">Geri bildiriminiz alındı: <span class="{{ $sikayet->musteri_feedback == 'Onaylandı' ? 'text-green-700' : ($sikayet->musteri_feedback == 'Reddedildi' ? 'text-red-700' : 'text-yellow-700') }}">{{ $sikayet->musteri_feedback }}</span></p>
                                @if($sikayet->musteri_feedback_note)
                                    <p class="text-sm text-gray-600 mt-1 italic">"{{ $sikayet->musteri_feedback_note }}"</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- B) FORM ALANI (Düzenleme modunda veya ilk kez girerken görünür) --}}
                <div x-show="editing" style="display: none;">
                    <p class="text-gray-600 mb-6">Sürecimiz tamamlanmıştır. Hizmet kalitemizi artırmak için lütfen sunulan çözümü değerlendiriniz.</p>
                    <form method="POST" action="{{ route('public.sikayet.storeFeedback', ['token' => $sikayet->takip_token]) }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="feedback_note" class="block text-sm font-bold text-gray-700 mb-2">Ek Notunuz (Opsiyonel)</label>
                            <textarea name="feedback_note" id="feedback_note" rows="3" class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm resize-y" placeholder="Düşüncelerinizi buraya yazabilirsiniz...">{{ $sikayet->musteri_feedback_note }}</textarea>
                            @error('feedback_note') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="flex flex-wrap gap-4">
                            <button type="submit" name="feedback" value="Onaylandı" class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Çözümü Onayla
                            </button>
                            
                            <button type="submit" name="feedback" value="Reddedildi" class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Çözümü Reddet
                            </button>
                            
                            <button type="submit" name="feedback" value="Revizyon İstendi" class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Revizyon İste
                            </button>
                        </div>
                        @error('feedback') <p class="text-red-500 text-sm font-medium mt-2 text-center">{{ $message }}</p> @enderror
                        
                        {{-- Vazgeç butonu (Sadece daha önce bir karar varsa göster) --}}
                        @if($sikayet->musteri_feedback)
                            <div class="text-center mt-2">
                                <button type="button" @click="editing = false" class="text-sm text-gray-500 hover:underline">Vazgeç</button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- === PROJE DURUMU VE BUTONLAR (GÜNCELLENDİ) === --}}
    {{-- ========================================================= --}}
    @if($sikayet->iaa_id)
        @php
             $proje = \App\Models\Iaa::find($sikayet->iaa_id);
             $projeDurumu = $proje ? $proje->durum : null;
             // Takım atanmış mı (Proje ID var ama takım atanmamışsa başlamamış demektir)
             $basladiMi = $proje && $proje->atanan_takim_id;
        @endphp

        <div class="mb-8 mt-6">
            {{-- DURUM A: Proje henüz bir takıma atanmamış (Süreç Hazırlanıyor) --}}
            @if(!$basladiMi)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 flex items-center gap-5 shadow-sm">
                    <div class="p-3 bg-amber-100 text-amber-600 rounded-full flex-shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg">Proje Henüz Başlatılmadı</h4>
                        <p class="text-sm text-gray-600">Şikayetiniz sistemimize alınmış ve proje kaydı oluşturulmuştur. Şu anda ilgili çözüm ekibinin atanması beklenmektedir.</p>
                    </div>
                </div>

            {{-- DURUM B: Proje Tamamlandı --}}
            @elseif($projeDurumu == 'Tamamlandı')
                <div class="bg-gradient-to-r from-emerald-50 to-green-50 border border-green-200 rounded-xl p-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-green-100 text-green-600 rounded-xl flex-shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg">Projeniz Tamamlandı</h4>
                            <p class="text-sm text-gray-600">Şikayetinizle ilgili yürütülen iyileştirme projesi başarıyla tamamlanmıştır. Detayları ve çözüm adımlarını inceleyebilirsiniz.</p>
                        </div>
                    </div>
                    
                    <a href="{{ route('proje.workspace.show', $sikayet->iaa_id) }}" class="whitespace-nowrap px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Tamamlanan Projeyi Gör
                    </a>
                </div>

                {{-- DURUM C: Proje Devam Ediyor (veya Onay Bekliyor) --}}
                @else
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-100 rounded-xl p-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-indigo-100 text-indigo-600 rounded-xl flex-shrink-0">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">
                                    @if(in_array($projeDurumu, ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor']))
                                        Onay Aşamasında
                                    @else
                                        Çözüm Süreci Devam Ediyor
                                    @endif
                                </h4>
                                <p class="text-sm text-gray-600">
                                    @if(in_array($projeDurumu, ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor']))
                                        Çalışmalar tamamlandı, şu anda yöneticiler tarafından son kontroller yapılıyor. Yakında sonuçlanacaktır.
                                    @else
                                        Şikayetiniz için bir proje oluşturuldu ve ekip çalışmaya başladı. Adımları takip edebilirsiniz.
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <a href="{{ route('proje.workspace.show', $sikayet->iaa_id) }}" class="whitespace-nowrap px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            Proje Adımlarını İncele
                        </a>
                    </div>
                @endif
        </div>
    @endif

    {{-- Butonlar --}}
    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between items-center">
        <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-gray-900 hover:underline">
            &larr; Ana Sayfaya Dön
        </a>
    </div>

</x-guest-layout>