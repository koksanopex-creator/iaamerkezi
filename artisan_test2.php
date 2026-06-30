<?php
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$roles = \Spatie\Permission\Models\Role::pluck('name')->toArray();
$routes = [
    'admin.iaa-yonetim.index',
    'admin.arabuluculuk.index',
    'admin.raporlar.index',
    'yonetim.index',
    // ... we can just check 'admin.iaa-yonetim.index' for all roles
];

$testRoute = 'admin.iaa-yonetim.index';

foreach ($roles as $role) {
    $user = \App\Models\User::role($role)->first();
    if (!$user) continue;

    \Illuminate\Support\Facades\Auth::login($user);
    $request = \Illuminate\Http\Request::create('/admin/iaa-yonetim', 'GET');
    
    try {
        $response = $kernel->handle($request);
    } catch (\Throwable $e) {
        if (strpos($e->getMessage(), 'admin-yonetim') !== false) {
            echo "ROLE: $role -> " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n\n";
        }
    }
}
