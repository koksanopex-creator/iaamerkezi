<?php
$filePath = 'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/resources/views/layouts/navigation.blade.php';
$content = file_get_contents($filePath);

$replacements = [
    'Ä°' => 'İ',
    'ÅŸ' => 'ş',
    'Å?' => 'ş',
    'Ä±' => 'ı',
    'Ã§' => 'ç',
    'Ã?' => 'Ç',
    'Ã¶' => 'ö',
    'Ã–' => 'Ö',
    'Ã¼' => 'ü',
    'Ãœ' => 'Ü',
    'ÄŸ' => 'ğ',
    'Äž' => 'Ğ',
    'Åž' => 'Ş',
    'Ã‡' => 'Ç',
    'Mü?teri' => 'Müşteri',
    'Çalı?ma' => 'Çalışma',
    'İyile?tirme' => 'İyileştirme',
    'S?re?leri' => 'Süreçleri',
    '??? ' => '📈 ', // Emojis got corrupted to ???
    '???¡ï¸ ' => '⚙️ ',
    '???§ ' => '🔔 ',
    '???¨ ' => '💓 ',
    'â?¡ ' => '📋 ',
    'â?»ï¸ ' => '♻️ ',
    '???© ' => '📨 ',
    '??? ' => '📊 ',
    '???« ' => '👥 ',
    '???¬ ' => '🔨 ',
    'Çıkı? Yap' => 'Çıkış Yap',
    'İ?lem' => 'İşlem',
    'Geçmi?i' => 'Geçmişi',
    'Giri? Logları' => 'Giriş Logları',
    'Ba?kanı' => 'Başkanı',
    'Üyesi' => 'Üyesi',
    'Çözüm' => 'Çözüm',
    'Mü?teri' => 'Müşteri',
];

$content = strtr($content, $replacements);

// Any remaining literal `?` that are inside words like `Mü?teri`
$content = preg_replace('/Mü\?teri/u', 'Müşteri', $content);
$content = preg_replace('/Çalı\?ma/u', 'Çalışma', $content);
$content = preg_replace('/İyile\?tirme/u', 'İyileştirme', $content);
$content = preg_replace('/S\?re\?leri/u', 'Süreçleri', $content);
$content = preg_replace('/İ\?lem/u', 'İşlem', $content);
$content = preg_replace('/Geçmi\?i/u', 'Geçmişi', $content);
$content = preg_replace('/Giri\? Logları/u', 'Giriş Logları', $content);
$content = preg_replace('/Ba\?kanı/u', 'Başkanı', $content);
$content = preg_replace('/Çıkı\? Yap/u', 'Çıkış Yap', $content);
$content = preg_replace('/Ü\?yesi/u', 'Üyesi', $content);

file_put_contents($filePath, $content);
echo "Cleaned up corrupted characters.\n";
