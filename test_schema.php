<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
header('Content-Type: text/plain; charset=utf-8');

$columns = \Illuminate\Support\Facades\Schema::getColumnListing('agenda');
echo "Agenda Columns: " . implode(', ', $columns) . "\n";

$columns = \Illuminate\Support\Facades\Schema::getColumnListing('evento');
echo "Evento Columns: " . implode(', ', $columns) . "\n";
