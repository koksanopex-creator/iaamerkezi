@component('mail::message')
{!! nl2br(e($customBody)) !!}

@component('mail::button', ['url' => $trackingUrl])
Şikayeti Görüntüle
@endcomponent

Saygılarımızla,<br>
**{{ config('app.name') }}**
@endcomponent