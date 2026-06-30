<?php
$filePath = 'c:/Users/celal.karaman/Desktop/Projelerim/iaa_projesi/resources/views/layouts/navigation.blade.php';
$content = file_get_contents($filePath);

// Convert doubly encoded UTF-8 back to raw bytes (which is the actual UTF-8)
$fixed = mb_convert_encoding($content, 'ISO-8859-1', 'UTF-8');

// There might be a mix of Windows-1254 and Windows-1252 because Powershell on Windows.
// Let's test if the output contains 'Müşteri'.
if (strpos($fixed, 'Müşteri') !== false || strpos($fixed, 'Yönetim') !== false) {
    file_put_contents($filePath, $fixed);
    echo "Fixed using ISO-8859-1\n";
} else {
    $fixed1254 = mb_convert_encoding($content, 'Windows-1254', 'UTF-8');
    if (strpos($fixed1254, 'Müşteri') !== false || strpos($fixed1254, 'Yönetim') !== false) {
        file_put_contents($filePath, $fixed1254);
        echo "Fixed using Windows-1254\n";
    } else {
        $fixed1252 = mb_convert_encoding($content, 'Windows-1252', 'UTF-8');
        if (strpos($fixed1252, 'Müşteri') !== false || strpos($fixed1252, 'Yönetim') !== false) {
            file_put_contents($filePath, $fixed1252);
            echo "Fixed using Windows-1252\n";
        } else {
            echo "Could not fix automatically. Checking manual string replacements.\n";
            // Manual fixes for common corrupted texts if conversion fails
            $replacements = [
                'MÃ¼ÅŸteri' => 'Müşteri',
                'YÃ¶netim' => 'Yönetim',
                'Ã‡alÄ±ÅŸma' => 'Çalışma',
                'AlanÄ±' => 'Alanı',
                'BÃ¶lÃ¼m' => 'Bölüm',
                'YÃ¶neticisi' => 'Yöneticisi',
                'DirektÃ¶r' => 'Direktör',
                'AtamalarÄ±' => 'Atamaları',
                'Åžikayet' => 'Şikayet',
                'RaporlarÄ±' => 'Raporları',
                'Ã‡Ã¶zÃ¼m' => 'Çözüm',
                'Ãœyeleri' => 'Üyeleri',
                'GÃ¶rev' => 'Görev',
                'GÃ¶r' => 'Gör',
                'ÅžÂžikayet' => 'Şikayet',
                'Ãœst' => 'Üst',
                'gÃ¶rÃ¼r' => 'görür',
                'Ä°ade' => 'İade',
                'Ä°ÅŸlemde' => 'İşlemde',
                'TakÄ±m' => 'Takım',
                'AÃ§Ä±k' => 'Açık',
                'TÃ¼m' => 'Tüm',
                'GÃ¶revlerim' => 'Görevlerim',
                'GÃ¼nlÃ¼k' => 'Günlük',
                'Lider YardÄ±mcÄ±sÄ±' => 'Lider Yardımcısı',
                'Ã‡Ã¶kmesini' => 'Çökmesini',
                'YÃ¶netici' => 'Yönetici'
            ];
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);
            file_put_contents($filePath, $content);
            echo "Fixed using string replacements.\n";
        }
    }
}
