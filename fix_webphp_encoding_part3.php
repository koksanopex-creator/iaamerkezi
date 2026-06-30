<?php
$file = 'C:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/routes/web.php';
$content = file_get_contents($file);

$replacements = [
    'MÜÃ…ÂžÃ‚ÂžTERİ' => 'MÜŞTERİ',
    'Ã…ÂžÃ‚ÂžİKAYET' => 'ŞİKAYET',
    'ERİÃ…ÂžÃ‚ÂžİMİ' => 'ERİŞİMİ',
    'GİRİÃ…ÂžÃ‚Âž' => 'GİRİŞ',
    'Ã…ÂžÃ‚Âžikayet' => 'Şikayet',
    'İÃ…ÂžÃ‚ÂžLEMLERİ' => 'İŞLEMLERİ',
    'AKIÃ…ÂžÃ‚Âž' => 'AKIŞ',
    'Ã…ÂžÃ‚ÂžABLONLARI' => 'ŞABLONLARI',
    'BAÃ…ÂžÃ‚ÂžKAN' => 'BAŞKAN',
    'DIÃ…ÂžÃ‚Âž' => 'DIŞ',
    'MÃ¼ÅŸteri' => 'Müşteri',
    'Ã§Ã¶kmesini' => 'çökmesini',
    'Ã¶nlemek' => 'önlemek',
    'iÃ§in' => 'için',
    'geÃ§ici' => 'geçici',
    'Ã…ÂžÃ‚Âž' => 'Ş'
];

$content = str_replace(array_keys($replacements), array_values($replacements), $content);
file_put_contents($file, $content);
echo "Done\n";
