<!DOCTYPE html>
<html>
<head>
    <title>Giriş Bilgileriniz</title>
</head>
<body style="font-family: sans-serif; color: #333;">
    <div style="padding: 20px; border: 1px solid #eee; border-radius: 5px;">
        <h2 style="color: #4338ca;">Sayın {{ $user->name }},</h2>
        <p>Köksan Müşteri Portalı hesabınız oluşturulmuştur.</p>
        
        <div style="background: #f3f4f6; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <p><strong>E-posta:</strong> {{ $user->email }}</p>
            <p><strong>Geçici Şifreniz:</strong> <span style="color: #dc2626; font-weight: bold; font-size: 16px;">{{ $plainPassword }}</span></p>
        </div>

        <p>Giriş yapmak için: <a href="https://kys.koksan.com/iaa/login">kys.koksan.com/iaa/login</a></p>
        <p>Lütfen giriş yaptıktan sonra şifrenizi değiştiriniz.</p>
    </div>
</body>
</html>