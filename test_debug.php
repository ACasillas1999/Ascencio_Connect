<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

header('Content-Type: text/plain; charset=utf-8');

// Clear compiled views
$viewPath = storage_path('framework/views');
$files = glob($viewPath . '/*');
$count = 0;
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
        $count++;
    }
}
echo "Cleared $count compiled views.\n";

// Clear route cache
try {
    \Artisan::call('route:clear');
    echo "Route cache cleared.\n";
} catch (\Exception $e) {
    echo "Route clear: " . $e->getMessage() . "\n";
}

// Now test
$act = \App\Models\Actividad::find(3);
if ($act) {
    echo "\nActividad ID: " . $act->ID . "\n";
    echo "getRouteKey(): " . $act->getRouteKey() . "\n";
    echo "URL: " . route('actividades.show', $act->ID) . "\n";
}
