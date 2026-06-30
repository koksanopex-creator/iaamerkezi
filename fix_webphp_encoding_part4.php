<?php
$file = 'C:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/routes/web.php';
$content = file_get_contents($file);

// Replace using regex to bypass exact byte sequence issues
$content = preg_replace('/MÜ.+?TERİ/', 'MÜŞTERİ', $content);
$content = preg_replace('/ERİ.+?İMİ/', 'ERİŞİMİ', $content);
$content = preg_replace('/GİRİ.+? YAPMAYI/', 'GİRİŞ YAPMAYI', $content);
$content = preg_replace('/İ.+?LEMLERİ/', 'İŞLEMLERİ', $content);
$content = preg_replace('/AKI.+? .+?ABLONLARI/', 'AKIŞ ŞABLONLARI', $content);
$content = preg_replace('/BA.+?KAN/', 'BAŞKAN', $content);
$content = preg_replace('/2\. DI.+? AVUKAT/', '2. DIŞ AVUKAT', $content);

// For Şikayet, ŞİKAYET, ŞABLONLARI
$content = preg_replace('/.+?İKAYET/', 'ŞİKAYET', $content); // Careful, this might match too much. Let's be specific
$content = preg_replace('/MÜŞTERİ .+?İKAYET/', 'MÜŞTERİ ŞİKAYET', $content);
$content = preg_replace('/.+?ikayet Detayları/', 'Şikayet Detayları', $content);
$content = preg_replace('/Müşteri .+?ikayet/', 'Müşteri Şikayet', $content);
$content = preg_replace('/Tüm .+?ikayetler/', 'Tüm Şikayetler', $content);

$content = str_replace('Müşteri Ã…ÂžÃ‚Âžikayeti', 'Müşteri Şikayeti', $content); // Wait, explicit byte string from find_a.php
// Let's use array of byte strings that we read from the file itself!
$lines = file($file);
foreach ($lines as &$line) {
    if (strpos($line, 'ikayeti') !== false) {
        $line = preg_replace('/Müşteri .+?ikayeti/', 'Müşteri Şikayeti', $line);
    }
    if (strpos($line, 'MÜ') !== false && strpos($line, 'TERİ') !== false) {
        $line = preg_replace('/MÜ.+?TERİ/', 'MÜŞTERİ', $line);
    }
    if (strpos($line, 'İKAYET') !== false) {
        $line = preg_replace('/MÜŞTERİ .+?İKAYET/', 'MÜŞTERİ ŞİKAYET', $line);
    }
    if (strpos($line, 'ikayet') !== false && strpos($line, 'Müşteri') !== false) {
         $line = preg_replace('/Müşteri .+?ikayet/', 'Müşteri Şikayet', $line);
    }
    if (strpos($line, 'Tüm') !== false && strpos($line, 'ikayetler') !== false) {
         $line = preg_replace('/Tüm .+?ikayetler/', 'Tüm Şikayetler', $line);
    }
    if (strpos($line, 'ikayet Detayları') !== false) {
        $line = preg_replace('/.+?ikayet Detayları/', 'Şikayet Detayları', $line);
    }
    if (strpos($line, 'ikayet KATEGORİ') !== false) {
        $line = preg_replace('/.+?ikayet KATEGORİ/', 'Şikayet KATEGORİ', $line);
    }
    if (strpos($line, 'AKI') !== false && strpos($line, 'ABLONLARI') !== false) {
        $line = preg_replace('/AKI.+? .+?ABLONLARI/', 'AKIŞ ŞABLONLARI', $line);
    }
    if (strpos($line, 'BA') !== false && strpos($line, 'KAN') !== false && strpos($line, 'SADECE') !== false) {
        $line = preg_replace('/BA.+?KAN/', 'BAŞKAN', $line);
    }
    if (strpos($line, 'DI') !== false && strpos($line, 'AVUKAT') !== false) {
        $line = preg_replace('/DI.+? AVUKAT/', 'DIŞ AVUKAT', $line);
    }
    if (strpos($line, 'GİRİ') !== false && strpos($line, 'YAPMAYI') !== false) {
        $line = preg_replace('/GİRİ.+? YAPMAYI/', 'GİRİŞ YAPMAYI', $line);
    }
    if (strpos($line, 'ERİ') !== false && strpos($line, 'İMİ') !== false) {
        $line = preg_replace('/ERİ.+?İMİ/', 'ERİŞİMİ', $line);
    }
    if (strpos($line, 'İ') !== false && strpos($line, 'LEMLERİ') !== false && strpos($line, 'İADE') !== false) {
        $line = preg_replace('/İ.+?LEMLERİ/', 'İŞLEMLERİ', $line);
    }
    if (strpos($line, 'KURULU') !== false && strpos($line, 'LEMLERİ') !== false) {
        $line = preg_replace('/İ.+?LEMLERİ/', 'İŞLEMLERİ', $line);
    }
    // and replace any remaining Ã…ÂžÃ‚Âž with Ş directly
    $line = preg_replace('/Ã.+?ž/', 'Ş', $line);
}
file_put_contents($file, implode("", $lines));
echo "Fixed regex in web.php\n";
