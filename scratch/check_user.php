<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('name', 'like', '%Beyza%')->first();
if ($user) {
    echo "User: " . $user->name . "\n";
    echo "Roles: " . $user->getRoleNames()->implode(', ') . "\n";
    echo "Permissions: " . $user->getPermissionNames()->implode(', ') . "\n";
} else {
    echo "User not found.\n";
}
