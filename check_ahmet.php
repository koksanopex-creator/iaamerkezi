<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'ahmet.gulluce5@koksan.com')->first();
if (!$user) {
    echo "Kullanıcı bulunamadı.\n";
    exit;
}

echo "Ahmet'in ID'si: " . $user->id . "\n";
echo "Bölüm ID: " . $user->bolum_id . "\n";

$notifs = \Illuminate\Support\Facades\DB::table('notifications')
    ->where('data', 'like', '%"user_id":' . $user->id . '%')
    ->orWhere('notifiable_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->get();

echo "=== AHMET İLE İLGİLİ BİLDİRİMLER ===\n";
foreach ($notifs as $n) {
    $target = \App\Models\User::find($n->notifiable_id);
    $targetName = $target ? $target->name . ' (' . $target->email . ')' : 'Bilinmeyen (ID: ' . $n->notifiable_id . ')';
    echo "Tarih: {$n->created_at}\n";
    echo "Kime Gitti: {$targetName}\n";
    echo "Bildirim Tipi: {$n->type}\n";
    echo "Detay: " . substr($n->data, 0, 150) . "...\n";
    echo "---------------------------\n";
}
