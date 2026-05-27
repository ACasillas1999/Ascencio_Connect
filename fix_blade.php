<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

header('Content-Type: text/plain; charset=utf-8');

echo "=== ACTIVIDADES (tabla actividades) ===\n";
$rows = \DB::select("SELECT * FROM actividades");
foreach ($rows as $r) {
    echo "  ID={$r->ID} | ID_Evento={$r->ID_Evento} | Actividad={$r->Actividad}\n";
}

echo "\n=== AGENDA (tabla agenda) ===\n";
$rows = \DB::select("SELECT * FROM agenda");
foreach ($rows as $r) {
    echo "  ID={$r->ID} | ID_Evento={$r->ID_Evento} | Actividad={$r->Actividad} | Fecha={$r->Fecha} | Horario={$r->Horario}\n";
}

echo "\n=== CONCLUSION ===\n";
echo "El link usa route('actividades.show', \$act->ID)\n";
echo "Si el ID de la actividad es 3, la URL deberia ser /actividades/3\n";
echo "Pero el browser muestra /actividades/7\n";
echo "Hay algun registro de agenda con ID=7?\n";
