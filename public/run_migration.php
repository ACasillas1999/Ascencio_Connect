<?php
$env = parse_ini_file(__DIR__ . '/../.env');
$host = $env['DB_HOST'] ?? '127.0.0.1';
$db = $env['DB_DATABASE'] ?? 'gpoascen_congresos';
$user = $env['DB_USERNAME'] ?? 'root';
$pass = $env['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("ALTER TABLE canjes ADD COLUMN Entregado TINYINT(1) NOT NULL DEFAULT 0 AFTER Cantidad");
    echo "Exito: Columna Entregado agregada correctamente con PDO raw.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Exito: La columna Entregado ya existia.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
