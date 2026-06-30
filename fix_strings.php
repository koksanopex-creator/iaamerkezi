<?php
$files = [
    'resources/views/layouts/navigation.blade.php',
    'resources/views/dashboard.blade.php',
    'app/Policies/MusteriSikayetiPolicy.php',
    'app/Policies/SikayetHatirlatmaPolicy.php',
    'app/Http/Controllers/Admin/MusteriSahaTemsilcisiController.php',
    'resources/views/admin/musteri_saha_temsilcileri/index.blade.php',
    'resources/views/dashboard/partials/musteri-saha-temsilcisi.blade.php',
    'app/Services/Dashboard/MusteriSahaTemsilcisiDashboardService.php',
    'app/Http/Controllers/DashboardController.php',
    'routes/web.php'
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        
        // Replace corrupted sequences with the correct Turkish characters
        $c = str_replace(
            ['MÃ¼ÅŸteri', 'Åžikayeti', 'Ã‡Ã¶zÃ¼m', 'DirektÃ¶r', 'BÃ¶lÃ¼m', 'YÃ¶neticisi', 'Ä°', 'ÅŸ', 'Ã§', 'Ã¼', 'Ä±', 'ÄŸ', 'Ã¶', 'Ãœ', 'Ã‡', 'Äž', 'Åž', 'Ã–', 'BaÅŸkanÄ±', 'Ãœyesi', 'Ä°ÅŸler', 'GÃ¶rebilir', 'Ä°adesi', 'Analizi', 'GerÃ§ekleÅŸtirmeniz', 'bulunmaktadÄ±r', 'LÃ¼tfen', 'detaylarÄ±', 'Ziyaret PlanlarÄ±m', 'PUANINIZ', 'yÃ¶netin', 'GÃ¶revlerinizi', 'gÃ¶rÃ¼ntÃ¼leyin', 'PerformansÄ±nÄ±zÄ±', 'sÃ¼reÃ§lerini', 'Ä±', 'Ã', 'Â', 'Å'],
            ['Müşteri', 'Şikayeti', 'Çözüm', 'Direktör', 'Bölüm', 'Yöneticisi', 'İ', 'ş', 'ç', 'ü', 'ı', 'ğ', 'ö', 'Ü', 'Ç', 'Ğ', 'Ş', 'Ö', 'Başkanı', 'Üyesi', 'İşler', 'Görebilir', 'İadesi', 'Analizi', 'Gerçekleştirmeniz', 'bulunmaktadır', 'Lütfen', 'detayları', 'Ziyaret Planlarım', 'PUANINIZ', 'yönetin', 'Görevlerinizi', 'görüntüleyin', 'Performansınızı', 'süreçlerini', 'ı', 'i', '', 'Ş'],
            $c
        );
        file_put_contents($f, $c);
        echo "Fixed $f\n";
    }
}
