<?php
$f = 'routes/web.php';
$c = file_get_contents($f);

// Find the position of Route::middleware(['role:Superadmin'])->group(function ()
$pos = strpos($c, "Route::middleware(['role:Superadmin'])->group(function ()");

if ($pos !== false) {
    // We will replace the whole block by extracting the sistem-sagligi routes
    $pattern = "/Route::middleware\(\['role:Superadmin'\]\)->group\(function \(\)\s*\{\s*\/\/\s*S.*?PANEL.*?\s*(Route::get\('sistem-sagligi'.*?;)\s*(Route::get\('sistem-sagligi\/init'.*?;)\s*(Route::post\('sistem-sagligi\/tarama'.*?;)\s*(Route::post\('sistem-sagligi\/blade-kontrol'.*?;)\s*(Route::get\('sistem-sagligi\/kalibrasyon-gunlugu'.*?;)/su";
    
    $replacement = "Route::middleware(['role:Superadmin|Müşteri Saha Temsilcisi'])->group(function () {
        // SİSTEM SAĞLIK PANELİ
        $1
        $2
        $3
        $4
        $5
    });

    Route::middleware(['role:Superadmin'])->group(function ()
    {";

    // Wait, the regex might fail because of the corrupted strings like SÃ„Â°STEM SAÃƒâ€žÃ‚ÂžLIK PANELÃ„Â°
    // Let's just use string replacement
}
