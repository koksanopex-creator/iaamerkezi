<?php
$f = 'C:\Users\celal.karaman\.gemini\antigravity-ide\brain\329774d1-a5d4-4d8b-9ba9-8d653fbb02d7\.system_generated\logs\transcript.jsonl';
$lines = file($f);
$out = [];
foreach ($lines as $line) {
    if (strpos($line, 'navigation.blade.php') !== false || strpos($line, 'x-dropdown-link') !== false) {
        $out[] = $line;
    }
}
file_put_contents('found_history.txt', implode("\n", $out));
echo "Found " . count($out) . " lines.\n";
