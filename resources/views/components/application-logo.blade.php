@if(isset($siteLogo) && $siteLogo)
    <img loading="eager" fetchpriority="high" src="{{ asset('storage/' . $siteLogo) }}" alt="Site Logosu" {{ $attributes }}>
@else
    <img src="{{ asset('favicon.png') }}" alt="Köksan Logo" {{ $attributes }}>
@endif