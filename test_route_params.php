<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('actividades.show');
if ($route) {
    echo "Ruta actividades.show:\n";
    echo "URI: " . $route->uri() . "\n";
    echo "Parametros: " . implode(', ', $route->parameterNames()) . "\n";
} else {
    echo "Ruta actividades.show no encontrada!\n";
}
