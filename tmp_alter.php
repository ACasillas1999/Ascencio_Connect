<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    Illuminate\Support\Facades\DB::statement('ALTER TABLE canjes ADD COLUMN Entregado TINYINT(1) DEFAULT 0 AFTER Fecha');
    echo 'Success';
} catch (Exception $e) {
    echo $e->getMessage();
}
