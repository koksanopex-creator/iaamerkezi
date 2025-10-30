<x-guest-layout> {{-- Mevcut guest layout'unu kullanıyoruz --}}
    <div class="mb-6 pb-6 border-b border-gray-200 flex justify-between items-start">
        <div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">Şikayet Detayları</h3>
            <p class="text-sm text-gray-600">Şikayet No: <span class="font-semibold text-indigo-600">#{{ $sikayet->id }}</span> (Takip Kodu: {{ $sikayet->takip_token ?? 'N/A' }})</p>
        </div>
        {{-- Durum Badge'i --}}
        <div>
            {!! $sikayet->musteri_durum_badge !!} {{-- Modeldeki accessor'u kullanıyoruz --}}
        </div>
    </div>

    {{-- Başarı/Hata Mesajları --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert"><p>{{ session('success') }}</p></div>
    @endif
    @if (session('error'))
         <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert"><p>{{ session('error') }}</p></div>
    @endif
    @if ($errors->any())
         <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            {{-- ... Hata listesi ... --}}
        </div>
    @endif

    {{-- Şikayet Bilgileri (Readonly Gösterim) --}}
    <div class="space-y-5 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
            <div><strong class="text-gray-600">Müşteri Adı:</strong> <span class="text-gray-800">{{ $sikayet->musteri_adi }}</span></div>
            <div><strong class="text-gray-600">E-posta:</strong> <span class="text-gray-800">{{ $sikayet->musteri_iletisim }}</span></div>
            <div><strong class="text-gray-600">Konum Tipi:</strong> <span class="text-gray-800">{{ $sikayet->konum_tipi }}</span></div>
            <div><strong class="text-gray-600">Şikayet Tarihi:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->format('d.m.Y') }}</span></div>
             {{-- Kategori (Eklendi) --}}
             <div class="md:col-span-2"><strong class="text-gray-600">Kategori:</strong> <span class="text-gray-800">{{ $sikayet->sikayetKategori->ad ?? 'Belirtilmemiş' }}</span></div>
        </div>
        <div class="mt-4">
            <strong class="text-gray-600 block mb-1">Şikayet Konusu:</strong>
            <p class="text-gray-800 font-medium">{{ $sikayet->musteri_sikayet_konusu }}</p>
        </div>
        <div>
            <strong class="text-gray-600 block mb-1">Şikayet Detayı:</strong>
            <p class="text-gray-700 whitespace-pre-wrap bg-gray-50 p-3 rounded border border-gray-200">{{ $sikayet->musteri_sikayet_detayi }}</p>
        </div>

        {{-- Yüklenmiş Dosyalar --}}
        @if($sikayet->dosyalar && $sikayet->dosyalar->count() > 0)
        <div>
            <strong class="text-gray-600 block mb-1">Eklenen Dosyalar:</strong>
            <ul class="list-disc list-inside space-y-1">
                @foreach($sikayet->dosyalar as $dosya)
                <li>
                    <a href="{{ asset('storage/' . $dosya->dosya_yolu) }}" target="_blank" class="text-indigo-600 hover:underline text-sm">
                        {{ $dosya->orijinal_adi }} <span class="text-gray-400 text-xs">({{ $dosya->mime_tipi }})</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    {{-- === Dinamik Bölüm: Duruma Göre Gösterilecekler === --}}
    {{-- TODO: Controller mantığına göre bu bölümler görünüp/gizlenecek --}}

    {{-- 1. Düzenleme Kutusu (Eğer kilitlenmemişse ve durum 'Yeni' ise) --}}
    @if(is_null($sikayet->edit_locked_at) && $sikayet->musteri_durum == 'Yeni')
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-center mb-6">
             <p class="text-sm text-blue-700 mb-3">Şikayetiniz henüz işleme alınmadı. İsterseniz detayları güncelleyebilirsiniz.</p>
             <a href="{{ route('public.sikayet.edit', ['token' => $sikayet->takip_token]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 transition">
                 Şikayeti Düzenle
             </a>
        </div>
    @endif

    {{-- 2. Durum Bilgisi ve Loglar (Kilitlenmişse veya İşlemdeyse) --}}
    @if((!is_null($sikayet->edit_locked_at) || $sikayet->musteri_durum != 'Yeni') && $sikayet->musteri_durum != 'Kapatıldı')
        <div class="pt-6 border-t border-gray-200 mb-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Şikayet Süreci İlerlemesi</h4>
            
            @if($sikayet->musteri_durum == 'İşlemde')
             <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg mb-4">
                 <p class="text-sm text-indigo-700 font-medium flex items-center gap-2">
                     <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                     <span>Şikayetiniz şu anda <strong>{{ $sikayet->cozumTakimi->ad ?? 'ilgili birim' }}</strong> tarafından incelenmektedir.</span>
                 </p>
            </div>
            @endif

            {{-- Şikayet Loglarını Göster --}}
            @if($sikayet->loglar->whereNotNull('user_id')->isNotEmpty()) {{-- Sadece kullanıcı (admin/takım) loglarını al --}}
            <div class="space-y-4">
                @foreach($sikayet->loglar->whereNotNull('user_id') as $log)
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center">
                            {{-- Log eylemine göre bir ikon gösterebiliriz (opsiyonel) --}}
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-800">{{ $log->eylem }}</p>
                            <p class="text-sm text-gray-600 italic">"{{ $log->aciklama }}"</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $log->created_at->format('d.m.Y H:i') }} - ({{ $log->user->name ?? 'Sistem' }})</p>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
             <p class="text-sm text-gray-500">Şikayetinizle ilgili henüz bir işlem logu bulunmamaktadır.</p>
            @endif
        </div>
    @endif
    
    {{-- 3. Çözüm Geri Bildirim Formu (Eğer durum 'Kapatıldı' ise) --}}
    @if($sikayet->musteri_durum == 'Kapatıldı')
        <div class="pt-6 border-t border-gray-200">
            <h4 class="text-lg font-semibold text-gray-800 mb-2">Çözüm Değerlendirmeniz</h4>
            @if($sikayet->musteri_feedback)
                {{-- Geri bildirim verilmişse göster --}}
                <div class="p-4 {{ $sikayet->musteri_feedback == 'Onaylandı' ? 'bg-green-50 border-green-200' : ($sikayet->musteri_feedback == 'Reddedildi' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200') }} border rounded-lg mb-4">
                    <p class="text-sm font-medium {{ $sikayet->musteri_feedback == 'Onaylandı' ? 'text-green-800' : ($sikayet->musteri_feedback == 'Reddedildi' ? 'text-red-800' : 'text-yellow-800') }}">
                        Geri bildiriminiz: <strong>{{ $sikayet->musteri_feedback }}</strong>
                    </p>
                    @if($sikayet->musteri_feedback_note)
                     <p class="text-xs text-gray-600 mt-1 italic">Notunuz: "{{ $sikayet->musteri_feedback_note }}"</p>
                    @endif
                </div>
            @else
                {{-- Geri bildirim verilmemişse formu göster --}}
                <p class="text-sm text-gray-600 mb-4">Şikayetiniz çözümlenmiştir. Lütfen çözümü değerlendirerek aşağıdaki butonlardan birini seçiniz.</p>
                <form method="POST" action="{{ route('public.sikayet.storeFeedback', ['token' => $sikayet->takip_token]) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="feedback_note" class="block text-sm font-medium text-gray-700 mb-1">Ek Not (Reddetme veya Revizyon için açıklama ekleyebilirsiniz):</label>
                        <textarea name="feedback_note" id="feedback_note" rows="3" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm resize-y" placeholder="Çözümle ilgili ek yorumlarınız...">{{ old('feedback_note') }}</textarea>
                        @error('feedback_note') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button type="submit" name="feedback" value="Onaylandı" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">Çözümü Onayla</button>
                        <button type="submit" name="feedback" value="Reddedildi" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Çözümü Reddet</button>
                        <button type="submit" name="feedback" value="Revizyon İstendi" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">Revizyon İste</button>
                    </div>
                    @error('feedback') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </form>
            @endif
        </div>
    @endif

    {{-- Butonlar --}}
    <div class="mt-8 pt-6 border-t border-gray-200 text-right">
         {{-- TODO: Belki bir çıkış yap butonu eklenebilir (Session'ı temizlemek için) --}}
         <a href="{{ url('/') }}" class="inline-flex items-center px-5 py-2.5 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
            Ana Sayfaya Dön
        </a>
    </div>

</x-guest-layout>