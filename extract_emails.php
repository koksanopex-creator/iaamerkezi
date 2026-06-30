<?php
$logFile = __DIR__.'/storage/logs/laravel.log';
$lines = file($logFile);
$total = count($lines);
$startIndex = max(0, $total - 2000); // look at last 2000 lines
$emails = [];
$currentEmail = '';
$isCapturing = false;

for($i = $startIndex; $i < $total; $i++) {
    $line = $lines[$i];
    if (strpos($line, 'To: ') === 0) {
        if ($isCapturing && $currentEmail != '') {
            $emails[] = $currentEmail;
        }
        $isCapturing = true;
        $currentEmail = $line;
    } else if ($isCapturing) {
        if (strpos($line, '[2026-') === 0 && strpos($line, 'local.') !== false) {
            $emails[] = $currentEmail;
            $isCapturing = false;
            $currentEmail = '';
        } else {
            $currentEmail .= $line;
        }
    }
}
if ($isCapturing && $currentEmail != '') {
    $emails[] = $currentEmail;
}

foreach($emails as $index => $email) {
    if (strpos($email, 'ahmet.gulluce1') !== false || strpos($email, 'serkan.atak') !== false || strpos($email, 'senol.kanat') !== false) {
        echo "=== EMAIL " . ($index + 1) . " ===\n";
        echo strip_tags($email);
        echo "\n";
    }
}
