<x-mail::message>
# Yeni Yorum Bildirimi

"**{{ $proje->baslik }}**" başlıklı projeye yeni bir yorum eklendi.

---

**Yorum Yapan:** {{ $yorum->yapan_kisi_adi }}
**Tarih:** {{ $yorum->created_at->format('d.m.Y H:i') }}

**Yorum:**
<x-mail::panel>
{{ $yorum->yorum }}
</x-mail::panel>

@if($yorum->dosya_adi)
**Ek:** {{ $yorum->dosya_adi }}
@endif

<x-mail::button :url="$projeLinki">
Proje Çalışma Alanına Git
</x-mail::button>

Teşekkürler,<br>
{{ config('app.name') }}
</x-mail::message>