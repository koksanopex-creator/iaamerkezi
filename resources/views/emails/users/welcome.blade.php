<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Köksan Portal'a Hoşgeldiniz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #1a3c5e;
            color: #ffffff;
            padding: 24px 32px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
        }

        .body {
            padding: 32px;
            color: #333333;
            line-height: 1.7;
        }

        .info-box {
            background: #f0f6ff;
            border-left: 4px solid #1a3c5e;
            padding: 16px 20px;
            margin: 24px 0;
            border-radius: 4px;
        }

        .info-box p {
            margin: 6px 0;
        }

        .info-box .label {
            font-weight: bold;
            color: #1a3c5e;
            min-width: 180px;
            display: inline-block;
        }

        .btn {
            display: inline-block;
            margin-top: 24px;
            padding: 12px 28px;
            background-color: #1a3c5e;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 15px;
        }

        .footer {
            background: #f9f9f9;
            border-top: 1px solid #e5e5e5;
            text-align: center;
            padding: 16px;
            color: #999999;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Köksan Portal'a Hoşgeldiniz!</h1>
        </div>
        <div class="body">
            <p>Merhaba <strong>{{ $user->name }}</strong>,</p>
            <p>Köksan Portal sistemine kullanıcı kaydınız başarıyla oluşturulmuştur. Aşağıdaki bilgilerle sisteme giriş
                yapabilirsiniz.</p>

            <div class="info-box">
                <p><span class="label">Sistem URL:</span> <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
                </p>
                <p><span class="label">Kullanıcı Adınız (E-posta):</span> {{ $user->email }}</p>
                <p><span class="label">Şifreniz:</span> {{ $rawPassword }}</p>
                @if($user->bolum)
                    <p><span class="label">Bağlı Olduğunuz Bölüm:</span> {{ $user->bolum->ad }}</p>
                @endif
            </div>

            <p>Güvenliğiniz için ilk girişinizden sonra şifrenizi değiştirmenizi tavsiye ederiz.</p>

            <a href="{{ config('app.url') }}" class="btn">Sisteme Giriş Yap</a>
        </div>
        <div class="footer">
            <p>Bu e-posta otomatik olarak oluşturulmuştur, lütfen yanıtlamayınız.</p>
            <p>&copy; {{ date('Y') }} Köksan Pet ve Plastik A.Ş. — Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>

</html>