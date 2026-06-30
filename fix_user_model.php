<?php
$f = 'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/app/Models/User.php';
$c = file_get_contents($f);

$pattern = '/(\/\/\s*5\.\s*DirektÃ¶r\s*Yetkisi\s*if\s*\(\$user->traitHasRole\(\'DirektÃ¶r\'\)\)\s*\{[^}]+\})/';

if (preg_match($pattern, $c, $matches)) {
    $replacement = $matches[1] . "
        // 5.5 MÃ¼ÅŸteri Saha Temsilcisi Yetkisi
        if (\$user->traitHasRole('MÃ¼ÅŸteri Saha Temsilcisi'))
        {
            \$yonetilenBolumler = \$user->sahaKaliteAnalistiOlduguBolumler()->pluck('bolumler.id')->toArray();
            \$bolumIds = array_merge(\$bolumIds, \$yonetilenBolumler);
        }";
        
    $c = preg_replace($pattern, $replacement, $c, 1);
    file_put_contents($f, $c);
    echo "Successfully updated User.php getAllowedBolumIds via regex\n";
} else {
    echo "Could not find target block in User.php\n";
}
