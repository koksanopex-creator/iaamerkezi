<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$iaaId = 33;
$stepId = 2;

\App\Models\IaaZiyaretPlani::where('iaa_id', $iaaId)->delete();
\App\Models\IaaProgressUpdate::where('iaa_talep_id', $iaaId)->where('iaa_workflow_step_id', $stepId)->delete();
\App\Models\Iaa::find($iaaId)->update(['visit_planned' => false, 'current_step_id' => 2]); // Set current_step_id to 2

echo "Deleted step 2 progress and visit for IAA 33\n";
