<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = \App\Models\MailLog::orderBy('created_at', 'desc')->limit(10)->get();
foreach($logs as $l) {
    echo "=================================================\n";
    echo "To: " . $l->to_email . "\n";
    echo "Subject: " . $l->subject . "\n";
    echo "Status: " . $l->status . "\n";
    echo "Body: \n" . strip_tags($l->body) . "\n";
}
