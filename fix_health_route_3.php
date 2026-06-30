<?php
$f = 'routes/web.php';
$lines = file($f);
$newLines = [];
$healthLines = [];

$inSuperAdmin = false;

foreach ($lines as $line) {
    if (strpos($line, 'sistem-sagligi') !== false) {
        $healthLines[] = $line;
        continue;
    }
    
    // Check if it's the start of the Superadmin block
    if (strpos($line, "Route::middleware(['role:Superadmin'])->group(function ()") !== false) {
        // Insert the health block right before this!
        $newLines[] = "Route::middleware(['role:Superadmin|Müşteri Saha Temsilcisi'])->group(function () {\n";
        $newLines[] = "    Route::get('sistem-sagligi', [HealthCheckController::class, 'index'])->name('health.index');\n";
        $newLines[] = "    Route::get('sistem-sagligi/init', [HealthCheckController::class, 'init'])->name('health.init');\n";
        $newLines[] = "    Route::post('sistem-sagligi/tarama', [HealthCheckController::class, 'scan'])->name('health.scan');\n";
        $newLines[] = "    Route::post('sistem-sagligi/blade-kontrol', [HealthCheckController::class, 'checkBladeRoutes'])->name('health.check_blade');\n";
        $newLines[] = "    Route::get('sistem-sagligi/kalibrasyon-gunlugu', \App\Livewire\Admin\CalibrationLogs::class)->name('health.logs');\n";
        $newLines[] = "});\n\n";
    }
    
    // Skip corrupted header comments
    if (strpos($line, 'PANEL') !== false && strpos($line, 'S') !== false && strpos($line, '//') !== false) {
        continue; // Skip the "SİSTEM SAĞLIK PANELİ" comment
    }
    
    $newLines[] = $line;
}

$c = implode("", $newLines);
if (substr($c, 0, 3) === "\xEF\xBB\xBF") {
    $c = substr($c, 3);
}
file_put_contents($f, $c);
