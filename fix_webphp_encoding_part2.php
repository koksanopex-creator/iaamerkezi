<?php
$file = 'C:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/routes/web.php';
$content = file_get_contents($file);

$replacements = [
    'Ã…ÂžÃ‚Âž' => 'Ş',
    'MÃ¼ÅŸteri' => 'Müşteri',
    'Ã§Ã¶kmesini' => 'çökmesini',
    'Ã¶nlemek' => 'önlemek',
    'iÃ§in' => 'için',
    'geÃ§ici' => 'geçici',
];

$content = strtr($content, $replacements);
file_put_contents($file, $content);
echo "Fixed remaining characters in web.php\n";
