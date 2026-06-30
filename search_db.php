<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$tables = \DB::select('SHOW TABLES');
$dbName = env('DB_DATABASE');
$tableKey = "Tables_in_{$dbName}";

$found = false;
foreach ($tables as $table) {
    $tableName = $table->$tableKey ?? current((array)$table);
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing($tableName);
    
    foreach ($columns as $column) {
        try {
            $results = \DB::table($tableName)
                ->where($column, 'like', '%admin-yonetim%')
                ->limit(5)
                ->get();
            
            if ($results->count() > 0) {
                echo "FOUND in table $tableName, column $column\n";
                echo json_encode($results, JSON_PRETTY_PRINT) . "\n";
                $found = true;
            }
        } catch (\Exception $e) {
        }
    }
}
if (!$found) echo "Not found in DB.\n";
