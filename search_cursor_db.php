<?php
$dir = getenv('APPDATA') . '\\Cursor\\User\\workspaceStorage';
if (!is_dir($dir)) {
    echo "No Cursor workspaceStorage found.\n";
    exit;
}
$folders = glob($dir . '/*');
$found = false;
foreach ($folders as $folder) {
    $dbPath = $folder . '/state.vscdb';
    if (file_exists($dbPath)) {
        try {
            $pdo = new PDO('sqlite:' . $dbPath);
            $stmt = $pdo->query("SELECT * FROM ItemTable WHERE [key] LIKE '%history%' OR [key] LIKE '%file%'");
            if ($stmt) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $val = $row['value'];
                    if (strpos($val, 'navigation.blade.php') !== false && strpos($val, 'Müşteri Saha Temsilcisi') !== false) {
                        echo "Found in $dbPath\n";
                        file_put_contents('recovered_nav.txt', $val);
                        $found = true;
                    }
                }
            }
        } catch (Exception $e) {
            // Ignore locked dbs
        }
    }
}
if (!$found) echo "Not found in Cursor SQLite.\n";
