<?php
$routes = collect(app('router')->getRoutes())->map(function($r) { return $r->getName(); })->filter()->toArray();
$files = \File::allFiles(resource_path('views'));
$errors = [];
foreach($files as $file) {
    $content = file_get_contents($file->getPathname());
    if (preg_match_all('/route\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
        foreach($matches[1] as $routeName) {
            if (!in_array($routeName, $routes) && strpos($routeName, '$') === false && !str_starts_with($routeName, 'ignition.')) {
                $errors[] = 'File: ' . $file->getRelativePathname() . ' -> Missing Route: ' . $routeName;
            }
        }
    }
}
$errors = array_unique($errors);
if (empty($errors)) {
    echo "No broken routes found in views!\n";
} else {
    echo implode("\n", $errors) . "\n";
}
