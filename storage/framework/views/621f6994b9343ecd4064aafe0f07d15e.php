<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Köksan Müşteri Portalı - Giriş Bilgileri</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    <tr>
                        <td align="center" style="padding: 30px 40px; background-color: #0056b3;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px;">KÖKSAN</h1>
                            <p style="color: #e0e0e0; margin: 5px 0 0 0; font-size: 14px;">Müşteri Portalı</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #333; margin-top: 0; font-size: 20px;">Hoş Geldiniz, Sayın <?php echo e($user->name); ?></h2>
                            <p style="color: #555; line-height: 1.6; font-size: 16px;">
                                <?php echo nl2br(e($customBody)); ?>

                            </p>
                            
                            <div style="background-color: #f8f9fa; border-left: 4px solid #0056b3; padding: 20px; margin: 25px 0;">
                                <p style="margin: 0 0 10px 0; font-size: 15px;"><strong>E-posta:</strong> <span style="color: #0056b3;"><?php echo e($user->email); ?></span></p>
                                <p style="margin: 0; font-size: 15px;"><strong>Geçici Şifre:</strong> <span style="color: #d9534f; font-family: monospace; font-size: 17px;"><?php echo e($plainPassword); ?></span></p>
                            </div>

                            <div align="center" style="margin: 35px 0;">
                                <a href="<?php echo e(url('/iaa/login')); ?>" style="background-color: #0056b3; color: #ffffff; padding: 15px 35px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                                    Portala Giriş Yap
                                </a>
                            </div>

                            <p style="color: #888; font-size: 13px; font-style: italic; border-top: 1px solid #eee; padding-top: 20px;">
                                * Güvenliğiniz için lütfen giriş yaptıktan sonra geçici şifrenizi değiştirmeyi unutmayınız.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 20px; background-color: #f1f1f1; color: #999; font-size: 12px;">
                            © 2026 Köksan A.Ş. Tüm Hakları Saklıdır.<br>
                            Bu e-posta otomatik olarak gönderilmiştir, lütfen yanıtlamayınız.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html><?php /**PATH /var/www/kys_koksan/iaa/resources/views/emails/new-customer-user.blade.php ENDPATH**/ ?>