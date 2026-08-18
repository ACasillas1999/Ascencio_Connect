<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "env(DB_DATABASE): " . env('DB_DATABASE') . "\n";
echo "config(database.connections.mysql.database): " . config('database.connections.mysql.database') . "\n";

try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306", "root", "");
    $dbs = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Databases en MySQL 127.0.0.1: " . implode(', ', $dbs) . "\n";
} catch (\Exception $e) {
    echo "Error PDO: " . $e->getMessage() . "\n";
}