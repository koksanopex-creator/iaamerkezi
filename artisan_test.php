<?php
$user = \App\Models\User::role('Superadmin')->first();
\Auth::login($user);
$request = \Illuminate\Http\Request::create('/admin/iaa-yonetim', 'GET');
$kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);
try {
    $response = $kernel->handle($request);
    echo $response->getContent();
} catch (\Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
