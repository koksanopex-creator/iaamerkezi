<?php
$f = 'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/app/Livewire/Admin/SikayetlerTablosu.php';
$c = file_get_contents($f);

// We need to inject the Müşteri Saha Temsilcisi check into applyAuthorizationFilters
$search = "} elseif (\$user->hasRole('Bölüm Kalite Yöneticisi')) {";

// The replacement text (checking for Müşteri Saha Temsilcisi before Bölüm Kalite Yöneticisi)
$replace = "} elseif (\$user->hasRole('Müşteri Saha Temsilcisi')) {
            \$yonettigiBolumIds = \$user->sahaKaliteAnalistiOlduguBolumler()->pluck('bolumler.id')->toArray();
            if (empty(\$yonettigiBolumIds)) {
                \$query->whereRaw('1 = 0');
            } else {
                \$query->whereHas('sikayetKategori', function (\$q) use (\$yonettigiBolumIds) {
                    \$q->whereIn('bolum_id', \$yonettigiBolumIds);
                });
            }
        } elseif (\$user->hasRole('Bölüm Kalite Yöneticisi')) {";

// Handle Turkish character encoding mismatch gracefully by matching the actual bytes
$search_bytes = "} elseif (\$user->hasRole('BÃ¶lÃ¼m Kalite YÃ¶neticisi')) {";
$replace_bytes = "} elseif (\$user->hasRole('MÃ¼ÅŸteri Saha Temsilcisi')) {
            \$yonettigiBolumIds = \$user->sahaKaliteAnalistiOlduguBolumler()->pluck('bolumler.id')->toArray();
            if (empty(\$yonettigiBolumIds)) {
                \$query->whereRaw('1 = 0');
            } else {
                \$query->whereHas('sikayetKategori', function (\$q) use (\$yonettigiBolumIds) {
                    \$q->whereIn('bolum_id', \$yonettigiBolumIds);
                });
            }
        } elseif (\$user->hasRole('BÃ¶lÃ¼m Kalite YÃ¶neticisi')) {";

if (strpos($c, $search_bytes) !== false) {
    $c = str_replace($search_bytes, $replace_bytes, $c);
    file_put_contents($f, $c);
    echo "Successfully updated SikayetlerTablosu for Müşteri Saha Temsilcisi filter (bytes matching).\n";
} else if (strpos($c, $search) !== false) {
    $c = str_replace($search, $replace, $c);
    file_put_contents($f, $c);
    echo "Successfully updated SikayetlerTablosu for Müşteri Saha Temsilcisi filter (string matching).\n";
} else {
    echo "Could not find the target code in SikayetlerTablosu.php.\n";
}
