<?php
$f = 'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/app/Policies/MusteriSikayetiPolicy.php';
$c = file_get_contents($f);

// Find the block using regex to avoid whitespace issues
$pattern = '/\/\/\s*2\.5\s*MÃ¼ÅŸteri\s*Saha\s*Temsilcisi[^{]*\{[^{]*\{[^}]*\}[^}]*\}[^}]*\}/';

if (preg_match($pattern, $c, $matches)) {
    $matchedText = $matches[0];
    $replacement = "// 2.5 MÃ¼ÅŸteri Saha Temsilcisi (Sorumlu olduÄŸu BÃ–LÃœMLERDEKÄ° ÅŸikayetleri gÃ¶rÃ¼r)
        if (\$user->hasRole(['MÃ¼ÅŸteri Saha Temsilcisi'])) {
            \$allowedBolumIds = \$user->sahaKaliteAnalistiOlduguBolumler()->pluck('bolumler.id')->toArray();
            if (\$sikayet->sikayetKategori && in_array(\$sikayet->sikayetKategori->bolum_id, \$allowedBolumIds)) {
                return true;
            }
        }";
    $c = str_replace($matchedText, $replacement, $c);
    file_put_contents($f, $c);
    echo "Successfully updated MusteriSikayetiPolicy via regex.\n";
} else {
    echo "Could not find the pattern in MusteriSikayetiPolicy.php.\n";
}
