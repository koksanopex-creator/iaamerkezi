<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$superadmin = \App\Models\User::role('Superadmin')->first();
if (!$superadmin) {
    die("Superadmin bulunamadı\n");
}
Illuminate\Support\Facades\Auth::login($superadmin);

$request = Illuminate\Http\Request::create('/admin/iaa-yonetim', 'GET');
try {
    $response = $kernel->handle($request);
    echo $response->getContent();
} catch (\Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
