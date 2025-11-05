{{-- Bu dosya, raporlar sayfasındaki detay popup'ının içeriğini oluşturur --}}
<div class="flex justify-between items-start">
    <h2 class="text-2xl font-bold text-gray-900 pr-4">{{ $iaa->baslik }}</h2>
    <x-secondary-button x-on:click="$dispatch('close')">&times;</x-secondary-button>
</div>
<hr class="my-4">
<div class="space-y-4 text-sm max-h-[70vh] overflow-y-auto pr-2">
    <p><strong>Öneren:</strong> 
        @if($iaa->gonderen)
            {{ $iaa->gonderen->name }} ({{ $iaa->gonderen->bolum->ad ?? '' }})
        @else
            {{ $iaa->guest_name }} (Misafir)
        @endif
    </p>
    <p><strong>İlgili Alan/Bölüm:</strong> {{ $iaa->bolum->ad ?? $iaa->ilgili_alan }}</p>
    <p><strong>Gönderim Tarihi:</strong> {{ $iaa->created_at->format('d.m.Y H:i') }}</p>
    <div class="mt-4 p-4 bg-gray-50 rounded-md border">
        <h4 class="font-semibold mb-2">Mevcut Durum:</h4>
        <p class="prose prose-sm max-w-none">{!! nl2br(e($iaa->mevcut_durum)) !!}</p>
    </div>
    <div class="mt-4 p-4 bg-blue-50 rounded-md border">
        <h4 class="font-semibold mb-2">Öneri:</h4>
        <p class="prose prose-sm max-w-none">{!! nl2br(e($iaa->oneri)) !!}</p>
    </div>
    
    {{-- ================= YENİ EKLENEN RESİM BÖLÜMÜ ================= --}}
    @if($iaa->resimler->isNotEmpty())
    <div class="mt-4">
        <h4 class="font-semibold mb-2">Eklenen Resimler:</h4>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            @foreach($iaa->resimler as $resim)
                {{-- 🚨 DÜZELTME BURADA: Storage::url() yerine asset('storage/...') kullanıldı --}}
                <a href="{{ asset('storage/' . $resim->dosya_yolu) }}" target="_blank" class="block group relative">
                    <img src="{{ asset('storage/' . $resim->dosya_yolu) }}" alt="İAA Resmi" class="rounded-lg object-cover w-full h-28 transform group-hover:scale-105 transition-transform duration-300">
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>