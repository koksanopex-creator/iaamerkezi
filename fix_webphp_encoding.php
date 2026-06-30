<?php
$file = 'C:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/routes/web.php';
$content = file_get_contents($file);

$replacements = [
    'Ã„Â±' => 'ı',
    'Ã„Â°' => 'İ',
    'ÃƒÂ§' => 'ç',
    'Ãƒâ€¡' => 'Ç',
    'ÃƒÂ¶' => 'ö',
    'Ãƒâ€“' => 'Ö',
    'ÃƒÂ¼' => 'ü',
    'ÃƒÅ“' => 'Ü',
    'Ã…Å¸' => 'ş',
    'Ã…ÂžÃ‚Âž' => 'Ş', 
    'Ã…Âž' => 'Ş',
    'Ã„Å¸' => 'ğ',
    'Ãƒâ€žÃ‚Âž' => 'Ğ', 
    'Ã„Âž' => 'Ğ'
];

$content = strtr($content, $replacements);
file_put_contents($file, $content);
echo "Fixed characters in web.php\n";
