<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckBladeRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'health:check-blade-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan all blade files to find route() calls pointing to non-existent routes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning blade files for broken routes...');
        
        $routes = collect(app('router')->getRoutes())->map(function($r) { return $r->getName(); })->filter()->toArray();
        $files = File::allFiles(resource_path('views'));
        $errors = [];
        
        $this->output->progressStart(count($files));

        foreach($files as $file) {
            $content = file_get_contents($file->getPathname());
            // Match route('name') or route("name")
            if (preg_match_all('/route\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
                foreach($matches[1] as $routeName) {
                    // Ignore routes with variables inside string or third-party routes like ignition
                    if (!in_array($routeName, $routes) && strpos($routeName, '$') === false && !str_starts_with($routeName, 'ignition.')) {
                        $errors[] = [
                            'file' => $file->getRelativePathname(),
                            'route' => $routeName
                        ];
                    }
                }
            }
            $this->output->progressAdvance();
        }
        
        $this->output->progressFinish();
        
        // Remove duplicates
        $errors = collect($errors)->unique(function ($item) {
            return $item['file'] . $item['route'];
        })->values()->all();

        if (empty($errors)) {
            $this->info('Success! No broken routes found in views.');
            return Command::SUCCESS;
        }

        $this->error('Found ' . count($errors) . ' broken route references in views:');
        
        $this->table(
            ['Blade File', 'Missing Route Name'],
            $errors
        );

        return Command::FAILURE;
    }
}
