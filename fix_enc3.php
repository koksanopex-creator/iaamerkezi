<?php

$dir = new RecursiveDirectoryIterator('c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/app');
$ite = new RecursiveIteratorIterator($dir);

foreach ($ite as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $c = file_get_contents($file->getPathname());
        
        $replaced = false;
        
        if (strpos($c, 'Şž') !== false) {
            $c = str_replace('Şž', 'Ş', $c);
            $replaced = true;
        }
        if (strpos($c, 'şž') !== false) {
            $c = str_replace('şž', 'ş', $c);
            $replaced = true;
        }
        if (strpos($c, 'ŞÂž') !== false) {
            $c = str_replace('ŞÂž', 'Ş', $c);
            $replaced = true;
        }
        if (strpos($c, 'şÂž') !== false) {
            $c = str_replace('şÂž', 'ş', $c);
            $replaced = true;
        }

        if ($replaced) {
            file_put_contents($file->getPathname(), $c);
            echo "Fixed " . $file->getPathname() . "\n";
        }
    }
}
