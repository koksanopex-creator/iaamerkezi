<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bolum = \App\Models\Bolum::where('ad', 'Kapak')->with('director')->first();
if($bolum) {
    $liderler = \App\Models\User::role('Bölüm Lideri')->where('bolum_id', $bolum->id)->pluck('name')->toArray();
    echo "Liderler: " . implode(', ', $liderler) . "\n";
    echo "Direktör: " . ($bolum->director ? $bolum->director->name : 'Yok') . "\n";
} else {
    echo "Bolum bulunamadi\n";
}
