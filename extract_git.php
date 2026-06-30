<?php
$objectsDir = '.git/objects';
$dirs = glob($objectsDir . '/*', GLOB_ONLYDIR);

$latestTime = 0;
$latestContent = '';

foreach ($dirs as $dir) {
    if (basename($dir) === 'info' || basename($dir) === 'pack') continue;
    $files = glob($dir . '/*');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $uncompressed = @gzuncompress($content);
        if ($uncompressed === false) continue;
        
        if (strpos($uncompressed, 'blob ') === 0) {
            if (strpos($uncompressed, "route('admin.mavi-yaka.index')") !== false && strpos($uncompressed, "admin.musteri-saha-temsilcileri.index") !== false) {
                // Found a candidate for navigation.blade.php
                // The blob format is "blob <size>\0<content>"
                list($header, $blobContent) = explode("\0", $uncompressed, 2);
                
                $mtime = filemtime($file);
                if ($mtime > $latestTime) {
                    $latestTime = $mtime;
                    $latestContent = $blobContent;
                }
            }
        }
    }
}

if ($latestContent !== '') {
    file_put_contents('resources/views/layouts/navigation.blade.php', $latestContent);
    echo "Restored from git blob! Size: " . strlen($latestContent) . " bytes\n";
} else {
    echo "Not found in git objects.\n";
}
