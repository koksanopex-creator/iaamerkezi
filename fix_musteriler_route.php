<?php
$f = 'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/routes/web.php';
$c = file_get_contents($f);

// Find the Route::get('/musteriler', MusteriYonetimi::class) block
$pattern = "/(Route::get\('\/musteriler',\s*MusteriYonetimi::class\)\s*->name\('musteriler\.index'\)\s*->middleware\(\['role:[^']+)('\)]\);)/";

if (preg_match($pattern, $c, $matches)) {
    $matchedText = $matches[0];
    if (strpos($matchedText, 'Müşteri Saha Temsilcisi') === false && strpos($matchedText, 'MÃ¼ÅŸteri Saha Temsilcisi') === false) {
        $replacement = $matches[1] . '|Müşteri Saha Temsilcisi' . $matches[2];
        $c = str_replace($matchedText, $replacement, $c);
        file_put_contents($f, $c);
        echo "Successfully added Müşteri Saha Temsilcisi to musteriler middleware.\n";
    } else {
        echo "Müşteri Saha Temsilcisi is already in the middleware.\n";
    }
} else {
    echo "Could not find the target route in web.php.\n";
}
