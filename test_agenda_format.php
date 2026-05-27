<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
header('Content-Type: text/plain; charset=utf-8');
$agenda = \App\Models\Agenda::limit(5)->get();
foreach($agenda as $a) {
    echo "ID: {$a->ID}, Horario: {$a->Horario}, Fecha: {$a->Fecha->format('Y-m-d')}\n";
}
