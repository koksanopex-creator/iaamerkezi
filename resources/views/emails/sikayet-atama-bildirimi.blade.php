<x-mail::message>
# Tarafınıza Müşteri Şikayeti Atanmıştır

Aşağıdaki müşteri şikayeti, çözüm için **{{ $team->ad }}** ekibinize (
@if($atananKullanici)
    özellikle **{{ $atananKullanici->name }}** size
@endif
) atanmıştır.

**Şikayet Özeti:**

* **Müşteri:** {{ $sikayet->musteri_adi }}
* **Şikayet Kodu:** {{ $sikayet->sikayet_kodu ?? 'N/A' }}
* **Konu:** {{ $sikayet->musteri_sikayet_konusu }}
* **Atama Tarihi:** {{ now()->format('d.m.Y H:i') }}

**Şikayet Detayı:**
@if(!empty(trim($sikayet->sikayet_detayi)))
<x-mail::panel>
{!! nl2br(e($sikayet->sikayet_detayi)) !!}
</x-mail::panel>
@else
(Şikayet detayı girilmemiştir.)
@endif

Lütfen şikayeti inceleyip gerekli aksiyonları alınız.

<x-mail::button :url="$detayLinki">
Şikayeti Görüntüle
</x-mail::button>

Teşekkürler,<br>
{{ config('mail.from.name') }}
</x-mail::message>