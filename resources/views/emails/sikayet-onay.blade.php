<x-mail::message>
{{-- Mailable sınıfından gönderilen işlenmiş metni buraya yazdırıyoruz --}}
{{ $bodyContent }}

{{-- Takip linki için bir buton ekleyelim --}}
<x-mail::button :url="$takipLinki">
Şikayet Takip Sayfasına Git
</x-mail::button>

{{-- Alt bilgi --}}
Teşekkürler,<br>
{{ config('app.name') }} Ekibi
</x-mail::message>