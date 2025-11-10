<x-mail::message>
# Yeni Müşteri Şikayeti Bildirimi

Sisteme yeni bir müşteri şikayeti girişi yapıldı.

**Şikayet Özeti:**

* **Müşteri:** {{ $sikayet->musteri_adi }}
* **Şikayet Kodu:** {{ $sikayet->sikayet_kodu ?? 'N/A' }}
* **Konu:** {{ $sikayet->musteri_sikayet_konusu }}
* **Tarih:** {{ $sikayet->created_at->format('d.m.Y H:i') }}

**Şikayet Detayı:**
@if(!empty(trim($sikayet->sikayet_detayi)))
<x-mail::panel>
{!! nl2br(e($sikayet->sikayet_detayi)) !!}
</x-mail::panel>
@else
(Şikayet detayı girilmemiştir.)
@endif

Şikayetin tamamını incelemek ve işlem yapmak için aşağıdaki butonu kullanabilirsiniz:

<x-mail::button :url="$detayLinki">
Şikayeti Görüntüle
</x-mail::button>

Teşekkürler,<br>
{{ config('mail.from.name') }}
</x-mail::message>