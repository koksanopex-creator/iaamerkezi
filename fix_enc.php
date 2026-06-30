<?php
$files = [
    'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/app/Models/User.php',
    'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/app/Policies/MusteriSikayetiPolicy.php',
    'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/app/Policies/SikayetHatirlatmaPolicy.php'
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        
        // We will find any instance of 'Müşteri Şžikayeti Kurulu' or 'Müşteri Şžikayeti Çözüm Lideri'
        // Since we know the letters "Şž" are between "Müşteri " and "ikayeti", we can do a regex replacement!
        $pattern = '/(Müşteri\s+)(.*?)(ikayeti)/u';
        // But wait, the encoding of "Şž" might not be valid UTF-8, causing the /u modifier to fail.
        // Let's do it byte by byte.
        // The word "Müşteri" might also have encoding. Let's just find "ikayeti" and replace the prefix.
        
        $c = preg_replace('/MÃ¼ÅŸteri\s+ÅžÂžikayeti/', 'Müşteri Şikayeti', $c);
        $c = preg_replace('/MÃ¼ÅŸteri\s+Åžžikayeti/', 'Müşteri Şikayeti', $c);
        
        $c = str_replace('Şž', 'Ş', $c);
        $c = str_replace('şž', 'ş', $c);
        $c = str_replace("\xC5\x9E\xC5\xBE", 'Ş', $c); // Şž in hex
        $c = str_replace("\xC5\x9F\xC5\xBE", 'ş', $c); // şž in hex
        
        file_put_contents($f, $c);
        echo "Processed $f\n";
    }
}
