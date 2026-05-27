<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Clear views and opcache
\Artisan::call('view:clear');
if (function_exists('opcache_reset')) opcache_reset();

echo "Caches cleared. Now visit /eventos/1 and inspect the HTML source of the Prueba link.";
