<?php
$f = 'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/routes/web.php';
$c = file_get_contents($f);

// Find the Route::resource('sikayetler' block
$pattern = "/(Route::resource\('sikayetler',\s*SikayetController::class\)\s*->names\('sikayetler'\)\s*->parameters\(\['sikayetler'\s*=>\s*'sikayet'\]\)\s*->middleware\('role:[^']+)/";

if (preg_match($pattern, $c, $matches)) {
    $matchedText = $matches[0];
    if (strpos($matchedText, 'Müşteri Saha Temsilcisi') === false) {
        $replacement = $matchedText . '|Müşteri Saha Temsilcisi';
        $c = str_replace($matchedText, $replacement, $c);
        file_put_contents($f, $c);
        echo "Successfully added Müşteri Saha Temsilcisi to sikayetler middleware.\n";
    } else {
        echo "Müşteri Saha Temsilcisi is already in the middleware.\n";
    }
} else {
    echo "Could not find the target route in web.php.\n";
}
