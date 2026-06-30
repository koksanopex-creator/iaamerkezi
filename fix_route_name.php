<?php
$f = 'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/resources/views/admin/musteri_saha_temsilcileri/index.blade.php';
$c = file_get_contents($f);
$c = str_replace('saha-kalite-analistleri', 'musteri-saha-temsilcileri', $c);
file_put_contents($f, $c);
echo "Replaced in $f\n";
