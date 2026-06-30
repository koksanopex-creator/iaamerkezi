<?php
$f = 'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/app/Models/User.php';
$c = file_get_contents($f);

// Find the line index of 5. Direktör Yetkisi
$lines = explode("\n", $c);
$insertIdx = -1;
for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], '6. BÃ¶lÃ¼m Lider YardÄ±mcÄ±sÄ±') !== false) {
        $insertIdx = $i;
        break;
    }
}

if ($insertIdx !== -1) {
    $insertCode = "
        // 5.5 MÃ¼ÅŸteri Saha Temsilcisi Yetkisi
        if (\$user->traitHasRole('MÃ¼ÅŸteri Saha Temsilcisi'))
        {
            \$yonetilenBolumler = \$user->sahaKaliteAnalistiOlduguBolumler()->pluck('bolumler.id')->toArray();
            \$bolumIds = array_merge(\$bolumIds, \$yonetilenBolumler);
        }
";
    array_splice($lines, $insertIdx, 0, explode("\n", $insertCode));
    file_put_contents($f, implode("\n", $lines));
    echo "Successfully updated User.php\n";
} else {
    echo "Could not find insertion point\n";
}
