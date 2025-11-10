<x-mail::message>
# Müşteri Şikayeti Atama Bilgilendirmesi

Aşağıdaki müşteri şikayeti, çözüm için **{{ $team->ad }}** ekibine atanmıştır.

Bu e-posta, "Ek Bildirim E-postaları (Atama)" listesinde olduğunuz için size bilgilendirme amacıyla gönderilmiştir.

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

Atanan projenin çalışma alanını incelemek için aşağıdaki butonu kullanabilirsiniz:

<x-mail::button :url="$detayLinki">
Projeyi Görüntüle
</x-mail::button>

Teşekkürler,<br>
{{ config('mail.from.name') }}
</x-mail::message>