<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::role('Arabuluculuk Personel')->first();
if (!$user) die("No user found\n");

\Illuminate\Support\Facades\Auth::login($user);
$request = \Illuminate\Http\Request::create('/admin/iaa-yonetim', 'GET');
try {
    $response = $kernel->handle($request);
    echo $response->getContent();
} catch (\Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
