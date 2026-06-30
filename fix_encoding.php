<?php

$dir = new RecursiveDirectoryIterator('c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/app');
$ite = new RecursiveIteratorIterator($dir);

foreach ($ite as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        // Let's replace Şž with Ş
        // Ş is \u015E
        // We will just do a string replacement
        $search = 'Şžikayeti';
        $replace = 'Şikayeti';
        
        // Also fix any other lowercase ones
        $search2 = 'Şž';
        $replace2 = 'Ş';
        
        $search3 = 'şž';
        $replace3 = 'ş';

        if (strpos($content, $search) !== false || strpos($content, $search2) !== false || strpos($content, $search3) !== false) {
            $newContent = str_replace([$search, $search2, $search3], [$replace, $replace2, $replace3], $content);
            file_put_contents($file->getPathname(), $newContent);
            echo "Fixed " . $file->getPathname() . "\n";
        }
    }
}
