<?php
$f = 'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/routes/web.php';
$c = file_get_contents($f);

$pattern = '/(Route::get\(\'\/musteriler\',\s*MusteriYonetimi::class\)\s*->name\(\'musteriler.index\'\)\s*->middleware\(\[\'role:.*?)(\'\]\);)/s';

if (preg_match($pattern, $c, $matches)) {
    if (strpos($matches[1], 'Saha Temsilcisi') === false) {
        $replacement = $matches[1] . '|Müşteri Saha Temsilcisi' . $matches[2];
        $c = preg_replace($pattern, $replacement, $c, 1);
        file_put_contents($f, $c);
        echo "Successfully updated routes/web.php\n";
    } else {
        echo "Already present in routes/web.php\n";
    }
} else {
    echo "Could not find the target route block in web.php\n";
}
