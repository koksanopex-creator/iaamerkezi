<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = 0;
$iaas = \App\Models\Iaa::whereNull('onaya_gonderilme_tarihi')->get();
foreach($iaas as $iaa) {
    if ($iaa->onaylanma_tarihi) {
        $log = \App\Models\IaaLog::where('iaa_id', $iaa->id)
            ->whereIn('eylem', ['Bölüm Onayına Gönderildi', 'Bölüm Onayına Gönderildi (İadeli)', 'Yönetici Onayına Gönderildi'])
            ->latest()->first();
        if($log) {
            $iaa->onaya_gonderilme_tarihi = $log->created_at;
        } else {
            // Check created_at or onaylanma_tarihi
            $iaa->onaya_gonderilme_tarihi = $iaa->created_at; 
            // the user complained that 30.03.2026 was the approval date, so submission must be before it. 
        }
    } else {
         $log = \App\Models\IaaLog::where('iaa_id', $iaa->id)
            ->whereIn('eylem', ['Bölüm Onayına Gönderildi', 'Bölüm Onayına Gönderildi (İadeli)', 'Yönetici Onayına Gönderildi'])
            ->latest()->first();
        if($log) {
             $iaa->onaya_gonderilme_tarihi = $log->created_at;
        }
    }
    
    // As per user's logic, "projenin onaylandigi tarih bolum kalite yoneticisinin ve direktorun onayladigi tarihlerden olusmali." That is handled by real logic, but for OLD data:
    $iaa->save();
    $count++;
}
echo "Migration successful, total updated: $count\n";
