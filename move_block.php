<?php
$file = "c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/resources/views/admin/sikayetler/show.blade.php";
$content = file_get_contents($file);

$startStr = "                    {{-- 3.5. ZİYARET BİLGİLERİ (Varsa) --}}";
$endStr = "                    @endif\n\n                    <div class=\"bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100\">\n                        <div class=\"p-6\">\n                            <h3 class=\"text-lg font-bold text-gray-900 mb-4 flex items-center border-b pb-2\">";

$posStart = strpos($content, $startStr);
$posEnd = strpos($content, $endStr);

if ($posStart === false || $posEnd === false) {
    echo "Could not find blocks\n";
    exit;
}

// Extract block
$block = substr($content, $posStart, $posEnd - $posStart);

// Remove block from original
$content = str_replace($block, "", $content);

// Update colors in block
$block = str_replace("'Tamamlandı' => 'emerald',", "'Tamamlandı' => 'blue',", $block);
$block = str_replace("'Revizyon Bekliyor' => 'amber',", "'Revizyon Bekliyor', 'Revize' => 'orange',", $block);
$block = str_replace("'Beklemede' => 'blue',", "'Beklemede' => 'yellow',", $block);
$block = str_replace("'Onaylandı' => 'indigo',", "'Onaylandı' => 'green',", $block);
$block = str_replace("text-emerald-700", "text-blue-700", $block);
$block = str_replace("text-blue-700", "text-yellow-700", $block); // wait, text-blue-700 will be replaced to yellow!
$block = str_replace("text-amber-700", "text-orange-700", $block);
$block = str_replace("text-indigo-700", "text-green-700", $block);
$block = str_replace("text-blue-500", "text-yellow-600", $block);
$block = str_replace("text-amber-500", "text-orange-600", $block);

// Also add mb-6 to the block's main div so it has margin at the bottom
$block = str_replace("overflow-hidden shadow-sm sm:rounded-xl border", "overflow-hidden shadow-sm sm:rounded-xl border mb-6", $block);

// Find insert position
$insertPosStr = "                    {{-- MÜŞTERİ HATIRLATMA GEÇMİŞİ --}}";
$posInsert = strpos($content, $insertPosStr);

if ($posInsert === false) {
    echo "Could not find insert pos\n";
    exit;
}

$content = substr_replace($content, $block . "\n" . $insertPosStr, $posInsert, strlen($insertPosStr));

file_put_contents($file, $content);
echo "Successfully moved and updated colors\n";
?>
