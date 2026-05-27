<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "Actividades en BD real:\n";
$acts = \App\Models\Actividad::all();
foreach($acts as $a) {
    echo $a->ID . " - " . $a->Actividad . "\n";
}
