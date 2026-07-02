<?php

$rolesToAppend = '|Müşteri Şikayeti Kurulu - Yurt İçi|Müşteri Şikayeti Kurulu - Yurt Dışı|Müşteri Şikayeti Kurulu Yöneticisi|Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi|Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı';

$file = __DIR__ . '/routes/web.php';
$content = file_get_contents($file);
$content = str_replace('Müşteri Şikayeti Kurulu|', 'Müşteri Şikayeti Kurulu' . $rolesToAppend . '|', $content);
$content = str_replace("Müşteri Şikayeti Kurulu']", "Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı']", $content);
file_put_contents($file, $content);

$file = __DIR__ . '/resources/views/layouts/navigation.blade.php';
$content = file_get_contents($file);
$content = str_replace("Müşteri Şikayeti Kurulu',", "Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı',", $content);
file_put_contents($file, $content);

echo "done\n";
