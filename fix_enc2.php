<?php
$files = [
    'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/app/Models/User.php',
    'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/app/Policies/MusteriSikayetiPolicy.php',
    'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/app/Policies/SikayetHatirlatmaPolicy.php'
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        
        $c = str_replace('ÅžÂžikayeti', 'Åžikayeti', $c);
        $c = str_replace('Åžžikayeti', 'Åžikayeti', $c);
        
        file_put_contents($f, $c);
        echo "Processed $f\n";
    }
}
