<!DOCTYPE html>
<html>
<head>
    <title>Şikayet Takip Bilgilendirmesi</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h3>Sayın <?php echo e($sikayet->musteri_adi); ?>,</h3>
    
    <p>
        <?php if($isReset): ?>
            Talebiniz üzerine şikayet takip şifreniz yenilenmiştir.
        <?php else: ?>
            "<?php echo e($sikayet->musteri_sikayet_konusu); ?>" konulu şikayetiniz sistemimize kaydedilmiş ve çözüm süreci başlatılmıştır.
        <?php endif; ?>
    </p>

    <div style="background: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <p><strong>Takip Linki:</strong> <a href="<?php echo e(route('public.sikayet.show', $sikayet->takip_token)); ?>"><?php echo e(route('public.sikayet.show', $sikayet->takip_token)); ?></a></p>
        <p><strong>Giriş Şifreniz:</strong> <span style="font-family: monospace; font-size: 16px; background: #fff; padding: 2px 5px;"><?php echo e($plainPassword); ?></span></p>
    </div>

    <p>Saygılarımızla,<br><?php echo e(config('app.name')); ?> Kalite Ekibi</p>
</body>
</html><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/emails/sikayet-takip-bilgilendirme.blade.php ENDPATH**/ ?>