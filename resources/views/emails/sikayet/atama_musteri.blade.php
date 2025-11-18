<x-mail::message>
# Şikayetiniz Çözüm Sürecinde

Sayın **{{ $sikayet->musteri_adi }}**,

"**{{ $sikayet->musteri_sikayet_konusu }}**" konulu, **#{{ $sikayet->id }}** numaralı şikayetiniz, çözüm üretilmesi amacıyla **{{ $takim->ad }}** ekibimize atanmıştır.

Ekibimiz en kısa sürede incelemelere başlayacaktır.

Şikayetinizin güncel durumunu aşağıdaki linkten takip edebilirsiniz:

<x-mail::button :url="$takipLinki">
Şikayet Durumunu Takip Et
</x-mail::button>

Teşekkürler,<br>
{{ config('app.name') }}
</x-mail::message>