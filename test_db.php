<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

header('Content-Type: text/plain; charset=utf-8');

$evento = \App\Models\Evento::find(1);
echo "Evento ID: {$evento->ID}\n";

$actividades = $evento->actividades()->orderBy('Actividad')->get();
echo "Count de actividades: " . $actividades->count() . "\n";

foreach ($actividades as $act) {
    echo "Model: " . get_class($act) . "\n";
    echo "Actividad ID: {$act->ID}\n";
    echo "Actividad Nombre: {$act->Actividad}\n";
    echo "Tabla: {$act->getTable()}\n";
    
    // Check if it has any relations loaded
    echo "Relations: " . json_encode(array_keys($act->getRelations())) . "\n";
}

$agenda = $evento->agenda()->orderBy('Fecha')->orderBy('Horario')->get();
echo "\nCount de agenda: " . $agenda->count() . "\n";
foreach ($agenda as $ag) {
    echo "Agenda ID: {$ag->ID}\n";
}
