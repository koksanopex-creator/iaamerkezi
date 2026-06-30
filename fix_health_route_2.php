<?php
$f = 'routes/web.php';
$c = file_get_contents($f);

// Strip BOM
if (substr($c, 0, 3) === "\xEF\xBB\xBF") {
    $c = substr($c, 3);
}

$c = str_replace(
    "Route::middleware(['role:Superadmin'])->group(function ()",
    "Route::middleware(['role:Superadmin|Müşteri Saha Temsilcisi'])->group(function () {
        Route::get('sistem-sagligi', [HealthCheckController::class, 'index'])->name('health.index');
        Route::get('sistem-sagligi/init', [HealthCheckController::class, 'init'])->name('health.init');
        Route::post('sistem-sagligi/tarama', [HealthCheckController::class, 'scan'])->name('health.scan');
        Route::post('sistem-sagligi/blade-kontrol', [HealthCheckController::class, 'checkBladeRoutes'])->name('health.check_blade');
        Route::get('sistem-sagligi/kalibrasyon-gunlugu', \App\Livewire\Admin\CalibrationLogs::class)->name('health.logs');
    });

    Route::middleware(['role:Superadmin'])->group(function ()",
    $c
);

// Now remove the original ones
$c = preg_replace("/\/\/ S.*S.*A.*L.*K.*P.*?Route::get\('sistem-sagligi',.*?health\.logs'\);\s*/s", "", $c);

file_put_contents($f, $c);
