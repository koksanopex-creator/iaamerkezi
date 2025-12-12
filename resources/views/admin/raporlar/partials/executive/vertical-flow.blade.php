<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
        <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
            <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
            Son Müşteri Şikayetleri Akışı
        </h3>
        <span class="text-xs text-gray-400">Otomatik Kaydırma</span>
    </div>

    <div class="relative h-80 overflow-hidden bg-gray-50/30">
        <div class="absolute w-full animate-vertical-scroll hover:pause space-y-2">
            @foreach($sonSikayetler->merge($sonSikayetler) as $sikayet)
                @php
                    $ayEtiketi = \Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->locale('tr')->isoFormat('MMMM YYYY');
                    // Not: Loop merge edildiği için index logic'i basitleştirildi, her item için tarih kontrolü yapılıyor.
                @endphp

                @php
                    $tarih = \Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->locale('tr')->isoFormat('D MMMM YYYY');
                    $isGecikmis = ($sikayet->musteri_durum != 'Kapatıldı' && $sikayet->musteri_cozum_son_tarihi && $sikayet->musteri_cozum_son_tarihi < now());
                    
                    $rowColor = match($sikayet->musteri_durum) {
                        'Yeni' => 'bg-yellow-50 border border-yellow-200',
                        'İşlemde' => 'bg-blue-50 border border-blue-200',
                        'Kapatıldı', 'Çözümlendi' => 'bg-green-50 border border-green-200',
                        'Yeniden Açıldı', 'Revize Ediliyor' => 'bg-orange-50 border border-orange-200',
                        default => 'bg-white border border-gray-200'
                    };
                    if($isGecikmis) $rowColor = 'bg-red-50 border border-red-200';
                @endphp

                <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}" target="_blank"
                   class="block rounded-xl px-4 py-3 {{ $rowColor }} shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            @php $ilkDosya = $sikayet->dosyalar->first(); @endphp
                            @if($ilkDosya && str_contains($ilkDosya->mime_tipi, 'image'))
                                <img src="{{ asset('storage/'.$ilkDosya->dosya_yolu) }}" class="w-10 h-10 rounded-lg object-cover border">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-bold text-gray-800">
                                    {{ $sikayet->musteri_adi }}
                                    <span class="font-normal text-gray-500">– {{ $sikayet->sikayetKategori->ad ?? '' }}</span>
                                </p>
                                <p class="text-xs text-gray-700 truncate w-80">{{ $sikayet->musteri_sikayet_konusu }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded {{ $isGecikmis ? 'bg-red-200 text-red-800' : 'bg-white/60 text-gray-800 border border-gray-200' }}">{{ $sikayet->musteri_durum }}</span>
                            <p class="text-[10px] text-gray-500 mt-1 font-semibold">{{ $tarih }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>

<style>
    @keyframes vertical-scroll {
        0% { transform: translateY(0); }
        100% { transform: translateY(-50%); }
    }
    .animate-vertical-scroll {
        animation: vertical-scroll 20s linear infinite;
    }
    .hover\:pause:hover {
        animation-play-state: paused;
    }
</style>