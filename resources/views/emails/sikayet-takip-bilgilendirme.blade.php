<!DOCTYPE html>
<html>
<head>
    <title>Şikayet Takip Bilgilendirmesi</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h3>Sayın {{ $sikayet->musteri_adi }},</h3>
    
    <p>
        @if($isReset)
            Talebiniz üzerine şikayet takip şifreniz yenilenmiştir.
        @else
            "{{ $sikayet->musteri_sikayet_konusu }}" konulu şikayetiniz sistemimize kaydedilmiş ve çözüm süreci başlatılmıştır.
        @endif
    </p>

    <div style="background: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <p><strong>Takip Linki:</strong> <a href="{{ route('public.sikayet.show', $sikayet->takip_token) }}">{{ route('public.sikayet.show', $sikayet->takip_token) }}</a></p>
        <p><strong>Giriş Şifreniz:</strong> <span style="font-family: monospace; font-size: 16px; background: #fff; padding: 2px 5px;">{{ $plainPassword }}</span></p>
    </div>

    <p>Saygılarımızla,<br>{{ config('app.name') }} Kalite Ekibi</p>
</body>
</html>