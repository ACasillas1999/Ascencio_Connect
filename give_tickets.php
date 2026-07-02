<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Participante;
use App\Models\PremioEvento;
use App\Models\Canje;

$evento_id = 1;

$boleto = PremioEvento::where('ID_Evento', $evento_id)->where('NombrePremio', 'LIKE', '%Boleto%')->first();

if (!$boleto) {
    echo "No se encontro el premio Boleto";
    exit;
}

// Obtener 5 participantes
$participantes = Participante::where('ID_Evento', $evento_id)->take(5)->get();

if ($participantes->isEmpty()) {
    echo "No hay participantes en el evento $evento_id";
    exit;
}

foreach ($participantes as $index => $p) {
    // Dar entre 1 y 3 boletos
    $cantidad = rand(1, 3);
    Canje::create([
        'ID_Evento' => $evento_id,
        'ID_Participante' => $p->ID,
        'ID_Premio' => $boleto->ID,
        'Cantidad' => $cantidad,
        'Fecha' => now()
    ]);
    echo "Se le dieron $cantidad boletos a {$p->Nombre}<br>";
}

echo "<br>Pruebas creadas exitosamente.";
