<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

try {
    if (!\Illuminate\Support\Facades\Schema::hasColumn('usuarios', 'ID_Evento')) {
        \DB::statement("ALTER TABLE `usuarios` ADD COLUMN `ID_Evento` BIGINT UNSIGNED NULL");
        \DB::statement("ALTER TABLE `usuarios` ADD CONSTRAINT `fk_evento_usuario` FOREIGN KEY (`ID_Evento`) REFERENCES `evento`(`ID`) ON DELETE SET NULL");
        echo "Columna ID_Evento agregada y FK establecida.";
    } else {
        echo "La columna ID_Evento ya existe.";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
