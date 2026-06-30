<x-mail::message>
# Disiplin Kurulu Toplantı Bilgilendirmesi

Sayın Kurul Üyesi/Katılımcı,

**"{{ $toplanti->baslik }}"** konulu toplantı ile ilgili yeni bir gelişme bulunmaktadır.

**Durum:** {{ ucfirst($tur) }}
**Mesaj:** {{ $mesaj }}

@if($tur === 'tamamlandı' && $toplanti->toplanti_karari)
## Alınan Kararlar:
{{ $toplanti->toplanti_karari }}
@endif

Toplantı detaylarını görmek ve alınan kararları incelemek için aşağıdaki butona tıklayabilirsiniz.

<x-mail::button :url="route('admin.disiplin.kurul.toplanti.show', $toplanti->id)">
Toplantı Detaylarını Gör
</x-mail::button>

Bilgilerinize sunarız.

Teşekkürler,<br>
{{ config('app.name') }}
</x-mail::message>
