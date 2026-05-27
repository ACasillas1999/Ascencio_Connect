<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "Actividad 7: ";
$act = \App\Models\Actividad::find(7);
echo $act ? $act->Actividad : "No encontrada";
echo "\n\nRutas actividades:\n";
foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
    if (strpos($route->uri(), 'actividades') !== false) {
        echo $route->methods()[0] . ' ' . $route->uri() . ' -> ' . $route->getActionName() . "\n";
    }
}
