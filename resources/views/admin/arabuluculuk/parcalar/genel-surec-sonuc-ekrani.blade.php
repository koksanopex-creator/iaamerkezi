@if($case->status == 'kapatildi')
    @php
        // 1. GEREKLİ VERİLERİ TOPLUYORUZ
        $odeme = $case->payments->first();
        
        // A) SÜRE HESABI
        $baslangic = $case->created_at;
        $bitis = $case->updated_at;
        $farkSaat = $baslangic->diffInHours($bitis);
        $yuvarlanmisGun = $farkSaat > 0 ? ceil($farkSaat / 24) : 1;
        $detaySure = $baslangic->diffForHumans($bitis, ['parts' => 2, 'short' => true, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]);

        // B) KİŞİLER (BAŞLATAN VE KAPATAN)
        $baslatanKisi = $case->creator; 
        $kapatanLog = $case->logs->where('islem', 'DOSYA KAPATILDI')->last() ?? $case->logs->last();
        $kapatanKisi = $kapatanLog ? ($kapatanLog->user->name ?? 'Sistem') : 'Bilinmiyor';

        // C) ÖDEME VE FİNANSÇI
        $dekont = $case->files->where('doc_type', 'dekont')->last();
        if ($dekont) {
            $odemeTarihi = $dekont->created_at->format('d.m.Y');
            $finansci = $dekont->uploader->name ?? 'Finans Birimi';
        } else {
            $odemeTarihi = ($odeme && $odeme->odeme_tarihi) ? \Carbon\Carbon::parse($odeme->odeme_tarihi)->format('d.m.Y') : 'Belirtilmedi';
            $finansci = 'Finans Birimi';
        }

        // D) YASAL BELGELER
        $taslakAnlasma = $case->files->where('doc_type', 'taslak_anlasma')->last();
        $sonTutanak = $case->files->where('doc_type', 'arabuluculuk_son_tutanak')->last();
    @endphp

    <div class="bg-white border border-green-200 rounded-xl shadow-sm mb-8 overflow-hidden">
        {{-- Başlık Kısmı --}}
        <div class="bg-gradient-to-r from-green-50 to-white px-6 py-4 border-b border-green-100 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-green-500 text-white p-2 rounded-full shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-green-900 text-lg">Süreç Başarıyla Tamamlandı</h3>
                    <p class="text-xs text-green-700">Dosya arşivlendi ve kapatıldı.</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-gray-400 uppercase">Kapanış Tarihi</span>
                <p class="font-mono text-gray-700 font-bold">{{ $case->updated_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>

        <div class="p-6">
            {{-- 1. Üst İstatistikler --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                
                {{-- Süreç Ömrü --}}
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 flex flex-col justify-center">
                    <p class="text-xs text-gray-500 font-bold uppercase mb-1">Toplam Süreç</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-3xl font-black text-gray-800">{{ $yuvarlanmisGun }} GÜN</p>
                    </div>
                    <p class="text-[10px] text-gray-400 font-medium mt-1">({{ $detaySure }})</p>
                </div>

                {{-- Nihai Tutar --}}
                <div class="p-4 bg-green-50 rounded-lg border border-green-100 flex flex-col justify-center">
                    <p class="text-xs text-green-600 font-bold uppercase mb-1">Ödenen Tutar</p>
                    <p class="text-2xl font-black text-green-700">
                        {{ number_format($case->anlasilan_tutar, 2) }} <span class="text-sm font-normal text-green-600">TL</span>
                    </p>
                </div>

                {{-- Ödeme Tarihi --}}
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-100 flex flex-col justify-center">
                    <p class="text-xs text-blue-600 font-bold uppercase mb-1">Ödeme Tarihi</p>
                    <p class="text-xl font-bold text-blue-800 flex items-center gap-2">
                        <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $odemeTarihi }}
                    </p>
                </div>

                {{-- SÜRECİ BAŞLATAN (Turuncu Tema) --}}
                <div class="p-4 bg-orange-50 rounded-lg border border-orange-100 flex flex-col justify-center">
                    <p class="text-xs text-orange-600 font-bold uppercase mb-1">Süreci Başlatan</p>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-orange-200 flex items-center justify-center text-orange-700 font-bold text-xs uppercase">
                            {{ substr($baslatanKisi->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-orange-900 truncate" title="{{ $baslatanKisi->name ?? 'Bilinmiyor' }}">
                                {{ $baslatanKisi->name ?? 'Bilinmiyor' }}
                            </p>
                        </div>
                    </div>
                    <p class="text-[10px] text-orange-400 mt-1">Dosya Oluşturucu</p>
                </div>

                {{-- Kapatan Kişi --}}
                <div class="p-4 bg-purple-50 rounded-lg border border-purple-100 flex flex-col justify-center">
                    <p class="text-xs text-purple-600 font-bold uppercase mb-1">Dosyayı Kapatan</p>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-purple-200 flex items-center justify-center text-purple-700 font-bold text-xs uppercase">
                            {{ substr($kapatanKisi, 0, 1) }}
                        </div>
                        <p class="text-sm font-bold text-purple-900 truncate" title="{{ $kapatanKisi }}">
                            {{ $kapatanKisi }}
                        </p>
                    </div>
                    <p class="text-[10px] text-purple-400 mt-1">Hukuk Onayı</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- SOL KOLON: Transfer ve Belgeler --}}
                <div class="flex flex-col gap-8">
                    {{-- 2.a Transfer Bilgileri --}}
                    <div>
                        <h4 class="font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Transfer Bilgileri
                        </h4>
                        @if($odeme)
                            <ul class="space-y-3 text-sm">
                                <li class="flex justify-between">
                                    <span class="text-gray-500">Alıcı Adı Soyadı:</span>
                                    <span class="font-bold text-gray-800">{{ $odeme->odenecek_kisi }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-500">Banka:</span>
                                    <span class="font-bold text-gray-800">{{ $odeme->banka_adi }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-500">IBAN:</span>
                                    <span class="font-mono text-gray-600 bg-gray-100 px-2 rounded">{{ $odeme->iban }}</span>
                                </li>
                                <li class="flex justify-between items-center">
                                    <span class="text-gray-500">Dekont:</span>
                                    @if($dekont)
                                        <a href="{{ asset('storage/'.$dekont->dosya_yolu) }}" target="_blank" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200 font-bold flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Görüntüle
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">Yok</span>
                                    @endif
                                </li>
                            </ul>
                        @else
                            <p class="text-gray-400 italic text-sm">Ödeme bilgisi bulunamadı.</p>
                        @endif
                    </div>

                    {{-- 2.b Yasal Belgeler --}}
                    <div>
                        <h4 class="font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Yasal Belgeler
                        </h4>
                        <ul class="space-y-3 text-sm">
                            <li class="flex justify-between items-center">
                                <span class="text-gray-500">Taslak Anlaşma:</span>
                                @if($taslakAnlasma)
                                    <a href="{{ asset('storage/'.$taslakAnlasma->dosya_yolu) }}" target="_blank" class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200 font-bold flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Görüntüle
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">Yok</span>
                                @endif
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-500">Son Tutanak:</span>
                                @if($sonTutanak)
                                    <a href="{{ asset('storage/'.$sonTutanak->dosya_yolu) }}" target="_blank" class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200 font-bold flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                        Görüntüle
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">Yok</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- SAĞ KOLON: Süreç Zaman Çizelgesi --}}
                <div>
                    <h4 class="font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Süreç Geçmişi
                    </h4>
                    <div class="relative border-l-2 border-gray-200 ml-3 space-y-6">
                        <div class="relative pl-6">
                            <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-gray-200 border-2 border-white"></span>
                            <p class="text-xs text-gray-500">{{ $case->created_at->format('d.m.Y H:i') }}</p>
                            <p class="text-sm font-bold text-gray-800">Süreç Başlatıldı</p>
                            <p class="text-xs text-gray-500">Oluşturan: {{ $case->creator->name ?? 'Sistem' }}</p>
                        </div>
                        @if($case->arabulucu)
                            <div class="relative pl-6">
                                <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-blue-200 border-2 border-white"></span>
                                <p class="text-sm font-bold text-gray-800">Arabulucu Atandı</p>
                                <p class="text-xs text-gray-500">{{ $case->arabulucu->name }}</p>
                            </div>
                        @endif
                        @if($dekont)
                        <div class="relative pl-6">
                            <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-yellow-400 border-2 border-white"></span>
                            <p class="text-xs text-gray-500">{{ $dekont->created_at->format('d.m.Y H:i') }}</p>
                            <p class="text-sm font-bold text-gray-800">Ödeme Yapıldı</p>
                            <p class="text-xs text-gray-500">İşlemi Yapan: {{ $finansci }}</p>
                        </div>
                        @endif
                        <div class="relative pl-6">
                            <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-green-500 border-2 border-white animate-pulse"></span>
                            <p class="text-xs text-green-600">{{ $case->updated_at->format('d.m.Y H:i') }}</p>
                            <p class="text-sm font-bold text-green-800">Süreç Tamamlandı</p>
                            <p class="text-xs text-gray-500">Kapatan: {{ $kapatanKisi }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif