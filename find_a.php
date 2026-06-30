<?php
$file = 'C:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/routes/web.php';
$lines = file($file);
foreach ($lines as $i => $line) {
    if (strpos($line, 'Ã') !== false) {
        echo ($i+1) . ": " . trim($line) . "\n";
    }
}
