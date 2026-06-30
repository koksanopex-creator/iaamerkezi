<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Spatie\Permission\Models\Role;
use App\Models\User;

echo "ROLES AND GUARDS:\n";
foreach (Role::all() as $role) {
    echo "- Name: {$role->name}, Guard: {$role->guard_name}\n";
}

echo "\nCURRENT LOGGED IN USER (IF ANY):\n";
if (Auth::check()) {
    $user = Auth::user();
    echo "User: {$user->name} (ID: {$user->id})\n";
    echo "Roles: " . $user->roles->map(fn($r) => "{$r->name} ({$r->guard_name})")->implode(', ') . "\n";
    
    $checkRoles = ['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı'];
    foreach ($checkRoles as $r) {
        echo "Has Role '$r'? " . ($user->hasRole($r) ? 'Evet' : 'Hayır') . "\n";
    }
} else {
    echo "No user logged in.\n";
}
